<?php
/**
 * --------------------------------------------------------------------------------------------------
 * 这是一个自由软件！
 * Release Date: 2015-10-16
 */
class Mysql {
    private $dbhost; // 数据库主机
    private $dbuser; // 数据库用户名
    private $dbpass; // 数据库用户名密码
    private $dbname; // 数据库名
    private $link; // 数据库连接标识
    private $prefix; // 数据库前缀
    private $charset; // 数据库编码，GBK,UTF8,gb2312
    private $pconnect; // 持久链接,1为开启,0为关闭
    private $sql; // sql执行语句
    private $result; // 执行query命令的结果资源标识
    private $error_msg; // 数据库错误提示
                        
    // 构造函数
    function Mysql($dbhost, $dbuser, $dbpass, $dbname = '', $prefix, $charset = 'utf8', $pconnect = 0) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
        $this->prefix = $prefix;
        $this->charset = strtolower(str_replace('-', '', $charset));
        $this->pconnect = $pconnect;
        $this->connect();
    }
    
    // 数据库连接
    function connect() {
        if ($this->pconnect) {
            if (!$this->link = @mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpass)) {
                $this->error('Can not pconnect to mysql server');
                return false;
            }
        } else {
            if (!$this->link = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass, true)) {
                $this->error('Can not connect to mysql server');
                return false;
            }
        }
        
        if ($this->version() > '4.1') {
            if ($this->charset) {
                $this->query("SET character_set_connection=" . $this->charset . ", character_set_results=" . $this->charset .
                         ", character_set_client=binary");
            }
            
            if ($this->version() > '5.0.1') {
                $this->query("SET sql_mode=''");
            }
        }
        
        if (mysql_select_db($this->dbname, $this->link) === false) {
            $this->error("NO THIS DBNAME:" . $this->dbname);
            return false;
        }
    }
    
    // 数据库执行语句，可执行查询添加修改删除等任何sql语句
    function query($sql) {
        $this->sql = $sql;
        $query = mysql_query($this->sql, $this->link);
        return $query;
    }
    
    // 取得前一次 MySQL 操作所影响的记录行数
    function affected_rows() {
        return mysql_affected_rows();
    }
    
    // 返回结果集中一个字段的值
    function result($row = 0) {
        return @ mysql_result($this->result, $row);
    }
    
    // 返回结果集中行的数目
    function num_rows($query) {
        return @ mysql_num_rows($query);
    }
    
    // 返回结果集中字段的数
    function num_fields($query) {
        return mysql_num_fields($query);
    }
    
    // 释放结果内存
    function free_result() {
        return mysql_free_result($this->result);
    }
    
    // 返回上一步 INSERT 操作产生的 ID
    function insert_id() {
        return mysql_insert_id();
    }
    
    // 获取下一个自增(id)值
    function auto_id($table) {
        return $this->get_one("SELECT auto_increment FROM information_schema.`TABLES` WHERE  TABLE_SCHEMA='" . $this->dbname . "' AND TABLE_NAME = '" . trim($this->table($table), '`') . "'");
    }
    
    // 从结果集中取得一行作为数字数组
    function fetch_row($query) {
        return mysql_fetch_row($query);
    }
    
    // 从结果集中取得一行作为关联数组
    function fetch_assoc($query) {
        return mysql_fetch_assoc($query);
    }
    
    // 从结果集取得的行生成的数组
    function fetch_array($query) {
        return mysql_fetch_array($query);
    }
    
    // 返回 MySQL 服务器的信息
    function version() {
        if (empty($this->version)) {
            $this->version = mysql_get_server_info($this->link);
        }
        return $this->version;
    }
    
    // 关闭 MySQL 连接
    function close() {
        return mysql_close($this->link);
    }
    
    // 将指定的表名加上前缀后返回
    function table($str) {
        return '`' . $this->prefix . $str . '`';
    }
    
    // 查询全部
    function select_all($table) {
        return $this->query("SELECT * FROM " . $table);
    }
    
    // 判断表是否存在
    function table_exist($table) {
        if($this->num_rows($this->query("SHOW TABLES LIKE '" . trim($this->table($table), '`') . "'")) == 1)
            return true;
    }
    
    // 判断字段是否存在
    function field_exist($table, $field) {
        $sql = "SHOW COLUMNS FROM " . $this->table($table);
        $query = $this->query($sql);
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))   {
            $array[] = $row['Field'];
        }
        
        if (in_array($field, $array))
            return true;
    }
    
    // 验证信息是否已经存在
    function value_exist($table, $field, $value) {
        $sql = "SELECT $field FROM " . $this->table($table) . " WHERE $field = '$value'";
        $number = $this->num_rows($this->query($sql));
        
        if ($number > 0)
            return true;
    }
    
    // 条件查询
    function select($table, $columnName = "*", $condition = '', $debug = '') {
        $condition = $condition ? ' Where ' . $condition : NULL;
        if ($debug) {
            echo "SELECT $columnName FROM $table $condition";
        } else {
            $query = $this->query("SELECT $columnName FROM $table $condition");
            return $query;
        }
    }
    
    // 删除数据
    function delete($table, $condition="where", $url = '') {
        return $this->query("DELETE FROM $table WHERE $condition");
    }

    // 仿真 Adodb 函数
    function get_one($sql, $limited = false) {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        }
        $res = $this->query($sql);
        if ($res !== false) {
            $row = mysql_fetch_row($res);
            if ($row !== false) {
                return $row[0];
            } else {
                return '';
            }
        } else {
            return false;
        }
    }
    
    // 转义特殊字符
    function escape_string($string) {
        if (PHP_VERSION >= '4.3') {
            return mysql_real_escape_string($string);
        } else {
            return mysql_escape_string($string);
        }
    }
    
    // 循环读取结果集并储存至数组
    function fetch_array_all($table, $order_by = '') {
        $order_by = $order_by ? " ORDER BY " . $order_by : '';
        $query = $this->query("SELECT * FROM " . $table . $order_by);
        $data = array();
        while ($row = $this->fetch_assoc($query)) {
            $data[] = $row;
        }
        return $data;
    }
    // 循环读取结果集并储存至数组
    function get_all($sql) {
        $query = $this->query($sql);
        if ($query !== false){
            $data = array();
            while ($row = $this->fetch_assoc($query)) {
                $data[] = $row;
            }
            return $data;
        }else{
            return false;
        }

    }
    function select_one($table, $columnName = "id", $condition = '') {
        $condition = $condition ? ' Where ' . $condition : NULL;
        return $this->get_1("SELECT $columnName FROM $table $condition limit 1");
        
    }
    // 条件查询
    function select_row($table, $columnName = "*", $condition = '', $debug = '') {
        $condition = $condition ? ' Where ' . $condition : NULL;
        if ($debug) {
            echo "SELECT $columnName FROM $table $condition";
        } else {
            $query = $this->query("SELECT $columnName FROM $table $condition limit 1");
            if ($query !== false)            {
                return mysql_fetch_assoc($query);
            }else{
                return false;
            }
        }
    }
    // 条件查询
    function select_rows($table, $columnName = "*", $condition = '', $debug = '') {
        $condition = $condition ? ' Where ' . $condition : NULL;
        if ($debug) {
            echo "SELECT $columnName FROM $table $condition";
        } else {

            $query = $this->query("SELECT $columnName FROM $table $condition");
             if ($query !== false){
                $data = array();
                while ($row = $this->fetch_assoc($query)) {
                    $data[] = $row;
                }
                return $data;
            }else{
                return false;
            }
        }
    }
    function get_row($sql, $limited = false){
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $query = $this->query($sql);
        if ($query !== false)
        {
            return mysql_fetch_assoc($query);
        }
        else
        {
            return false;
        }
    }
    function count($table,$condition){
        $condition = $condition ? ' Where ' . $condition : NULL;
        return intval($this->get_1("SELECT count(*) FROM $table $condition"));
    }

    function insert($table, $array) {
        $keys = array();
        $values = array();
        foreach ($array as $key => $value) {
            $keys[] = "`" . $key . "`";
            $values[] = "'" . $value . "'";
        }
        $sql = @"INSERT INTO {$table} (" . implode(",", $keys) . ")VALUES(" . implode(",", $values) . ")";
        $this->query($sql);
        $num = $this->insert_id();
        return $num;
    }

    function update($table, $array, $limit = NULL,$check = false) {
        $limit = $limit ? ' Where ' . $limit : "where";
        if($check){
           if($this->check($table,$limit)<=0){
                return $this->insert($table,$array);
           }
        }
        $kvs = array();
        foreach ((array) $array as $key => $value) {
            $kvs[] = "`{$key}` = '{$value}'";
        }
        $sql = @"UPDATE {$table} SET " . implode(",", $kvs) . " " . $limit;
        $this->query($sql);
        $num = $this->affected_rows();
        return $num;
       
            
        
    }

    function check($table, $limit = "") {
        $limit = $limit ? ' Where ' . $limit : NULL;
        $sql = @"SELECT 1 FROM {$table} {$limit}";
        $num = intval($this->get_1($sql));
        return $num;
    }

    function get_1($sql, $limited = false)
    {
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }
        $query = $this->query($sql);
        if ($query !== false)
        {
            $row = mysql_fetch_row($query);

            if ($row !== false)
            {
                return $row[0];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    
    // 返回错误信息
    function error($msg = '') {
        $msg = $msg ? "BaiduCMS Error: $msg" : '<b>MySQL server error report</b><br>' . $this->error_msg;
        exit($msg);
    }
    
    // 数据库导入
    function fn_execute($sql) {
        $sqls = $this->fn_split($sql);
        if (is_array($sqls)) {
            foreach ($sqls as $sql) {
                if (trim($sql) != '')
                    $this->query($sql);
            }
        } else {
            $this->query($sqls);
        }
        return true;
    }
 
    // 数据分离
    function fn_split($sql) {
        if ($this->version() > '4.1' && $this->sqlcharset)
            $sql = preg_replace("/TYPE=(InnoDB|MyISAM)( DEFAULT CHARSET=[^; ]+)?/", "TYPE=\\1 DEFAULT CHARSET=" . $this->sqlcharset, $sql);
        
        $sql = str_replace("\r", "\n", $sql);
        $ret = array ();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return ($ret);
    }
}
?>