<?php

if (!is_callable('mb_strlen')) {
    function mb_strlen($str,$encoding=null) {
        return strlen($str);
    }
}

if (!is_callable('mb_strtoupper')) {
    function mb_strtoupper($str,$encoding=null) {
        return strtoupper($str);
    }
}

if (!is_callable('mb_strtolower')) {
    function mb_strtolower($str,$encoding=null) {
        return strtolower($str);
    }
}