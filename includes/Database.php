<?php

class Database {
    
    private $db;
    
    public function __construct($host,$user,$pass,$name) {
        $this->db = mysqli_connect($host,$user,$pass,$name);
        if(mysqli_connect_error()) die('Could not connect: ' . mysqli_connect_error());
        return $this->db;
    }
    
    public function __destruct() {
        return $this->close();
    }
    
    public function query($query) {
        if($this->db) {
            return mysqli_query($this->db,$query);
        }
    }
    
    public function count($query) {
        if($this->db) {
            return mysqli_num_rows($query);
        }
    }
    
    public function fetch($query, $type = 'assoc') {
        if($this->db) {
            $function = 'mysqli_fetch_' . $type;
            if(!is_callable($function)) {
                $function = 'mysqli_fetch_assoc';
            }
            return $function($query);
        }
    }
    
    public function clear($query) {
        if($this->db) {
            return mysqli_free_result($query);
        }
    }
    
    public function close() {
        if($this->db) {
            mysqli_close($this->db);
            $this->db = false;
        }
    }
    
    public function escape($string) {
        if($this->db) {
            return mysqli_real_escape_string($this->db,$string);
        }
    }
    
    public function insert_id() {
        if($this->db) {
            return mysqli_insert_id($this->db);
        }
    }
    
    public function affected_rows() {
        if($this->db) {
            return mysqli_affected_rows($this->db);
        }
    }

}