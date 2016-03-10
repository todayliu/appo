<?php

class Check {

    /**
     * 判断是否为数字
     */
    public static function is_number($number) {
        if (preg_match("/^[0-9]+$/", $number)) {
            return true;
        }
    }
    
    /**
     * 判断是否为字母
     */
    public static function is_letter($letter) {
        if (preg_match("/^[a-z]+$/", $letter)) {
            return true;
        }
    }
    
    /**
     * 判断是否为字母、中文、数字
     */
    public static function is_text($text) {
        if (preg_match("/^[\x{4e00}-\x{9fa5}0-9a-zA-Z_]*$/u", $text)) {
            return true;
        }
    }
    
    /**
     * 判断别名是否规范
     */
    public static function is_unique_id($unique) {
        if (preg_match("/^[a-zA-Z0-9-]+$/", $unique)) {
            return true;
        }
    }
    
    
    /**
     * 检查是否包含中文字符，防止垃圾信息
     */
    public static function if_include_chinese($value) {
        if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $value)) {
            return true;
        }
    }
    
    /**
     * 验证是否输入和输入长度
     */
    public static function length($value, $length) {
        if (strlen($value) > 0 && strlen($value) <= $length) {
            return true;
        }
    }
    
    /**
     * 判断验证码是否规范
     */
    public static function is_captcha($captcha) {
        if (preg_match("/^[A-Za-z0-9]{4}$/", $captcha)) {
            return true;
        }
    }

    /**
     * 判断是否为邮件地址
     */
    public static function is_email($email) {
        if (preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email)) {
            return true;
        }
    }
    
    /**
     * 限制密码长度为6-32位
     */
    public static function is_password($password) {
        if (preg_match("/^.{6,}$/", $password)) {
            return true;
        }
    }
    
    /**
     * 判断是否为手机号码
     */
    public static function is_telphone($mobile) {
        if (preg_match("/((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/", $mobile)) {
            return true;
        }
    }
    
    /**
     * 判断是否为QQ号码
     */
    public static function is_qq($qq) {
        if (preg_match("/^[1-9]*[1-9][0-9]*$/", $qq)) {
            return true;
        }
    }
    
    public static function data_encode($str,$key) {
        $str = base64_encode($str);
        $key = base64_encode($key);
        $code = '';
        $len = strlen($key);
        $strlen = strlen($str);
        for ($i=0; $i < $strlen; $i++) {
            $k = $i %  $len;
            $code .= $str[$i] ^ $key[$k];
        }
        return base64_encode($code);
    }
    public static function data_decode($str,$key) {
        $str = base64_decode($str);
        $key = base64_encode($key);
        $code = '';
        $len = strlen($key);
        $strlen = strlen($str);
        for ($i=0; $i < $strlen; $i++) {
            $k = $i %  $len;
            $code .= $str[$i] ^ $key[$k];
        }
        return base64_decode($code);
    }
}
?>