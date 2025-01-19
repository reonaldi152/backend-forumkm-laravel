<?php

namespace App\Helper;

class InputValidationHelper
{
    // Helper for validate input email
    public static function validate_input_email($email){
        $email = trim($email);
        $email = strtolower($email);

        // use FILTER_VALIDATE_EMAIL
        $value = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(!$value){
            return false;
        }

        // use preg_match to validate email format with regex
        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($regex, $email)) {
            return null;
        }

        // return email if valid
        return $email;
    }

    // Helper for validate input text
    public static function validate_input_text($string, $htmlspecialchars = false, $no_backslash = true)
    {
        if ($string === '' || $string === null) {
            return null;
        }

        if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
            $val = filter_var($string, FILTER_SANITIZE_ADD_SLASHES);
        } else {
            $val = filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);
        }

        if ($no_backslash) {
            $val = stripslashes($val);
        }

        if ($htmlspecialchars) {
            $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
        }

        return $val;
    }
}