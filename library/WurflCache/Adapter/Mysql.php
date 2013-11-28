<?php
namespace WurflCache\Adapter;

    /**
     * Copyright (c) 2012 ScientiaMobile, Inc.
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU Affero General Public License as
     * published by the Free Software Foundation, either version 3 of the
     * License, or (at your option) any later version.
     * Refer to the COPYING.txt file distributed with this package.
     *
     * @category   WURFL
     * @package    \Wurfl\Storage
     * @copyright  ScientiaMobile, Inc.
     * @license    GNU Affero General Public License
     * @author     Fantayeneh Asres Gizaw
     * @version    $id$
     */
/**
 * WURFL Storage
 *
 * @package    \Wurfl\Storage
 */
class Mysql implements AdapterInterface
{
    private $defaultParams = array(
        "host"        => "localhost",
        "port"        => 3306,
        "db"          => "wurfl_persistence_db",
        "user"        => "",
        "pass"        => "",
        "table"       => "wurfl_object_cache",
        "keycolumn"   => "key",
        "valuecolumn" => "value"
    );

    private $link;
    private $host;
    private $db;
    private $user;
    private $pass;
    private $port;
    private $table;
    private $keycolumn;
    private $valuecolumn;

    public function __construct($params)
    {
        $currentParams = is_array($params) ? array_merge($this->defaultParams, $params) : $this->defaultParams;
        foreach ($currentParams as $key => $value) {
            $this->$key = $value;
        }
        $this->initialize();
    }
    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $return   = null;
        $objectId = $this->encode('', $key);
        $objectId = mysql_real_escape_string($objectId);

        $sql    = "select `$this->valuecolumn` from `$this->db`.`$this->table` where `$this->keycolumn`='$objectId'";
        $result = mysql_query($sql, $this->link);
        if (!is_resource($result)) {
            throw new Exception("MySql error " . mysql_error($this->link) . "in $this->db");
        }

        $row = mysql_fetch_assoc($result);

        if (is_array($row)) {
            $return = @unserialize($row['value']);
            if ($return === false) {
                $return = null;
            }
        }

        if (is_resource($result)) {
            mysql_free_result($result);
        }

        return $return;
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasItem($key)
    {
        return null;
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function setItem($key, $value)
    {
        $object   = mysql_real_escape_string(serialize($value));
        $objectId = $this->encode("", $key);
        $objectId = mysql_real_escape_string($objectId);
        $sql      = "delete from `$this->db`.`$this->table` where `$this->keycolumn`='$objectId'";
        $success  = mysql_query($sql, $this->link);
        if (!$success) {
            throw new Exception("MySql error " . mysql_error($this->link) . "deleting $objectId in $this->db");
        }

        $sql     = "insert into `$this->db`.`$this->table` (`$this->keycolumn`,`$this->valuecolumn`) VALUES ('$objectId','$object')";
        $success = mysql_query($sql, $this->link);
        if (!$success) {
            throw new Exception("MySQL error " . mysql_error($this->link) . "setting $objectId in $this->db");
        }

        return $success;
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     */
    public function touchItem($key)
    {
        return null;
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     */
    public function removeItem($key)
    {
        return null;
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        $sql     = "truncate table `$this->db`.`$this->table`";
        $success = mysql_query($sql, $this->link);
        if (mysql_error($this->link)) {
            throw new Exception("MySql error " . mysql_error($this->link) . " clearing $this->db.$this->table");
        }

        return $success;
    }

    /**
     * Remove expired items
     *
     * @return bool
     */
    public function clearExpired()
    {
        return null;
    }

    private function initialize()
    {
        $this->ensureModuleExistance();

        /* Initializes link to MySql */
        $this->link = mysql_connect("$this->host:$this->port", $this->user, $this->pass);
        if (mysql_error($this->link)) {
            throw new Exception("Couldn't link to `$this->host` (" . mysql_error($this->link) . ")");
        }

        /* Initializes link to database */
        $success = mysql_select_db($this->db, $this->link);
        if (!$success) {
            throw new Exception("Couldn't change to database `$this->db` (" . mysql_error($this->link) . ")");
        }

        /* Is Table there? */
        $test = mysql_query("SHOW TABLES FROM $this->db LIKE '$this->table'", $this->link);
        if (!is_resource($test)) {
            throw new Exception("Couldn't show tables from database `$this->db` (" . mysql_error($this->link) . ")");
        }

        // create table if it's not there.
        if (mysql_num_rows($test) == 0) {
            $sql     = "CREATE TABLE `$this->db`.`$this->table` (
                      `$this->keycolumn` varchar(255) collate latin1_general_ci NOT NULL,
                      `$this->valuecolumn` mediumblob NOT NULL,
                      `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
                      PRIMARY KEY  (`$this->keycolumn`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
            $success = mysql_query($sql, $this->link);
            if (!$success) {
                throw new Exception("Table $this->table missing in $this->db (" . mysql_error($this->link) . ")");
            }
        }

        if (is_resource($test)) {
            mysql_free_result($test);
        }
    }

    /**
     * Ensures the existance of the the PHP Extension mysql
     *
     * @throws Exception required extension is unavailable
     */
    private function ensureModuleExistance()
    {
        if (!extension_loaded("mysql")) {
            throw new Exception("The PHP extension mysql must be installed and loaded in order to use the mysql.");
        }
    }

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     * @param string $namespace
     * @param string $input
     *
     * @return string $input with the given $namespace as a prefix
     */
    private function encode($namespace, $input)
    {
        return implode(':', array('Wurfl', $namespace, $input));
    }
}