<?php

class CommonUtil {

    public static function arrayToReferences($arr)
    {
        $refs = array();

        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];

        return $refs;
    }

    /**
     * @param $timestamp int
     * @return string
     *
     * Converts UNIX timestamp into a format accepted by MySQL.
     */
    public static function sqlTimeStamp($timestamp)
    {
        return date("Y-m-d H:i:s", $timestamp);
    }

    public static function redirect($url)
    {
        header("Location: " . Config::SITE_BASE . $url);
    }

    public static function formatStringToUrl($str)
    {
        $str = preg_replace('/[ -]+/', '-', $str);
        $str = str_replace(
            array("Å","å","Ä","ä","Ö","ö"),
            array("a","a","a","a","o","o"),
            $str
        );
        $str = preg_replace('/[^a-zA-Z0-9-]+/', '', $str);
        $str = strtolower($str);

        return $str;
    }

    /**
     * @param $str string
     * @param $frombase int
     * @param $tobase int 
     *
     * @return string
     * 
     * Converts integers from base to another.
     */
    public static function baseConvert($str, $frombase=10, $tobase=36) { 
        $str = trim($str); 
        if (intval($frombase) != 10) { 
            $len = strlen($str); 
            $q = 0; 
            for ($i=0; $i<$len; $i++) { 
                $r = base_convert($str[$i], $frombase, 10); 
                $q = bcadd(bcmul($q, $frombase), $r); 
            } 
        } 
        else $q = $str; 

        if (intval($tobase) != 10) { 
            $s = ''; 
            while (bccomp($q, '0', 0) > 0) { 
                $r = intval(bcmod($q, $tobase)); 
                $s = base_convert($r, 10, $tobase) . $s; 
                $q = bcdiv($q, $tobase, 0); 
            } 
        } 
        else $s = $q; 

        return $s; 
    }
} 