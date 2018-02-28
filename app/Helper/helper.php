<?php
/**
 * Created by PhpStorm.
 * User: man.nv
 * Date: 2/7/18
 * Time: 12:43 PM
 */

if (!function_exists('get_web_page')) {
    function get_web_page($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => "",       // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);

        if ($err != 0) {
            app('log')->debug('CURL ERROR: ' . $errmsg);
            return '';
        }
        return $content;
    }
}

if (!function_exists('trim_space')) {
    function trim_space($str) {
        $str = preg_replace('!\s+!', ' ', $str);
        $str = str_replace(') .', ').', $str);
        $str = trim($str);
        return $str;
    }
}

function XML2JSON($xml) {

    function normalizeSimpleXML($obj, &$result) {
        $data = $obj;
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $res = null;
                normalizeSimpleXML($value, $res);
                if (($key == '@attributes') && ($key)) {
                    $result = $res;
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $data;
        }
    }
    $result = null;
    normalizeSimpleXML(simplexml_load_string($xml), $result);
    return json_encode($result);
}