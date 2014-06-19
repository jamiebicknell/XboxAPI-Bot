<?php

class Database
{
    
    private $db;
    private $duration = 0;
    
    public function __construct($host, $user, $pass, $name)
    {
        $this->db = mysqli_connect($host, $user, $pass, $name);
        if (mysqli_connect_error()) {
            $this->error(mysqli_connect_error(), true);
        }
        return $this->db;
    }
    
    public function __destruct()
    {
        return $this->close();
    }
    
    private function error($string, $error = false)
    {
        $trace = end(debug_backtrace());
        $string .= ' for ' . $trace['class'] . '::' . $trace['function'] . '(),';
        $string .= ' called in ' . $trace['file'] . ' on line ' . $trace['line'];
        trigger_error($string, $error ? E_USER_ERROR : E_USER_WARNING);
        return false;
    }
    
    private function isDataBase($show_error = true)
    {
        if (is_a($this->db, 'mysqli')) {
            return true;
        }
        if ($show_error) {
            $this->error('No database connection active');
        }
        return false;
    }
    
    private function isResult($result, $show_error = true)
    {
        if (is_a($result, 'mysqli_result')) {
            return true;
        }
        if ($show_error) {
            $this->error('Not a database result');
        }
        return false;
    }
    
    public function query($string)
    {
        if ($this->isDataBase()) {
            $tmp = microtime(true);
            $result = mysqli_query($this->db, $string);
            $this->duration = number_format(microtime(true) - $tmp, 12, '.', '');
            if ($this->db->error) {
                $this->error($this->db->error);
            }
            return $result;
        }
    }
    
    public function count($result)
    {
        if ($this->isDataBase() && $this->isResult($result)) {
            return mysqli_num_rows($result);
        }
    }
    
    public function fetch($result, $type = 'assoc')
    {
        if ($this->isDataBase() && $this->isResult($result)) {
            $function = 'mysqli_fetch_' . $type;
            if (!is_callable($function)) {
                $function = 'mysqli_fetch_assoc';
            }
            return $function($result);
        }
    }
    
    public function single($sql)
    {
        $query = $this->query($sql);
        if ($this->isResult($query, false)) {
            if ($this->count($query) > 0) {
                $return = $this->fetch($query, 'row');
                $this->clear($query);
                return $return[0];
            }
        }
        return false;
    }
    
    public function insert($array, $table)
    {
        if ($this->isDataBase()) {
            if (is_array($array) && count($array) > 0) {
                $k = "`" . implode('`,`', array_keys($array)) . "`";
                $v = "'" . implode("','", array_map(array($this, 'escape'), array_values($array))) . "'";
                return $this->query("INSERT INTO `" . $table . "` (" . $k . ") VALUES (" . $v . ")");
            }
            $this->error('$array is not a valid array');
        }
    }
    
    public function update($array, $table, $where = '')
    {
        if ($this->isDataBase()) {
            if (is_array($array) && count($array) > 0) {
                $fields = array();
                foreach ($array as $k => $v) {
                    $fields[] = "`" . $k . "` = '" . $this->escape($v) . "'";
                }
                return $this->query("UPDATE `" . $table . "` SET " . implode(', ', $fields) . (($where) ? ' WHERE ' . $where : null));
            }
            $this->error('$array is not a valid array');
        }
    }
    
    public function clear($result)
    {
        if ($this->isDataBase(false) && $this->isResult($result, false)) {
            return mysqli_free_result($result);
        }
    }
    
    public function close()
    {
        if ($this->isDataBase(false)) {
            mysqli_close($this->db);
            $this->db = false;
        }
    }
    
    public function escape($string)
    {
        if ($this->isDataBase()) {
            return mysqli_real_escape_string($this->db, $string);
        }
    }
    
    public function insertId()
    {
        if ($this->isDataBase()) {
            return mysqli_insert_id($this->db);
        }
    }
    
    public function affectedRows()
    {
        if ($this->isDataBase()) {
            return mysqli_affected_rows($this->db);
        }
    }
    
    public function duration()
    {
        return $this->duration;
    }
}
