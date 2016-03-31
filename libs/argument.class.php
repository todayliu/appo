<?php
/**
 * --------------------------------------------------------------------------------------------------
 * 这是一个自由软件！
 * Release Date: 2015-10-16
 */

class ArgumentType{
	public static $NUMBER  = 0;
	public static $CHAR = 1;
	public static $BOOL = 2;
	public static $SQL = 3;
}
class Argument{
	    public static function addslashes_deep($value) {
        if (empty($value)) {
            return $value;
        }
        
        if (is_array($value)) {
            foreach ((array) $value as $k => $v) {
                unset($value[$k]);
                $k = addslashes($k);
                if (is_array($v)) {
                    $value[$k] = $this->addslashes_deep($v);
                } else {
                    $value[$k] = addslashes($v);
                }
            }
        } else {
            $value = addslashes($value);
        }
        
        return $value;
    }
    

    public static function getSql($key,$default=''){
        if(isset($_GET[$key])){
            return mysql_real_escape_string(htmlentities(strip_tags(trim($_GET[$key])),ENT_QUOTES,"UTF-8"));
        }
        return $default;
    }
    public static function getInt($key,$default=0){
        if(isset($_GET[$key])){
            return intval($_GET[$key]);
        }
        return $default;
    }

    public static function getFloat($key,$default=0.0){
        if(isset($_GET[$key])){
            return floatval($_GET[$key]);
        }
        return $default;
    }

    public static function getDouble($key,$default=0.0){
        if(isset($_GET[$key])){
            return doubleval($_GET[$key]);
        }
        return $default;
    }
        public static function postSql($key,$default=''){
        if(isset($_POST[$key])){
            return mysql_real_escape_string(htmlentities(strip_tags(trim($_POST[$key])),ENT_QUOTES,"UTF-8"));
        }
        return $default;
    }
    public static function postInt($key,$default=0){
        if(isset($_POST[$key])){
            return intval($_POST[$key]);
        }
        return $default;
    }

    public static function postFloat($key,$default=0.0){
        if(isset($_POST[$key])){
            return floatval($_POST[$key]);
        }
        return $default;
    }

    public static function postDouble($key,$default=0.0){
        if(isset($_POST[$key])){
            return doubleval($_POST[$key]);
        }
        return $default;
    }
    /**
     * +----------------------------------------------------------
     * 递归方式的对变量中的特殊字符去除转义
     * +----------------------------------------------------------
     */
    public static function stripslashes_deep($value) {
        if (empty($value)) {
            return $value;
        }
        
        if (is_array($value)) {
            foreach ((array) $value as $k => $v) {
                unset($value[$k]);
                $k = stripslashes($k);
                if (is_array($v)) {
                    $value[$k] = self::stripslashes_deep($v);
                } else {
                    $value[$k] = stripslashes($v);
                }
            }
        } else {
            $value = stripslashes($value);
        }
        return $value;
    }
    
    /**
     * +----------------------------------------------------------
     * html安全过滤器
     * +----------------------------------------------------------
     */
    public static function filter($value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = htmlspecialchars($v, ENT_NOQUOTES);
            }
        } else {
            // 参数ENT_NOQUOTES代表不转义任何引号，避免与addslashes冲突
            $value = htmlspecialchars($value, ENT_NOQUOTES);
        }
        
        return $value;
    }
}