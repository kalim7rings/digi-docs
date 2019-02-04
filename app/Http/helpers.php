<?php
if ( !function_exists('app')) {

    function app($settings = [])
    {
        return \App\App::instance($settings);
    }
}

if ( !function_exists('url')) {

    function url($url = '', $includeBaseUrl = true)
    {
        return app()->url($url, $includeBaseUrl);
    }
}

if ( !function_exists('soap_response')) {

    function soap_response($data)
    {
        //echo json_last_error();
        return json_decode(simplexml_load_string($data), true);
    }
}

if ( !function_exists('parse_xml_response')) {
    function parse_xml_response($xml)
    {
        libxml_use_internal_errors(true);
        function normalizeSimpleXML($obj, &$result)
        {
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

        $xml = simplexml_load_string($xml);

        if ($xml === false) {
            return false;
        }

        normalizeSimpleXML($xml, $result);

        return $result;
    }
}

if (! function_exists('mix')) {
    function mix($path, $manifestDirectory = '')
    {
        return app()->mix($path, $manifestDirectory);
    }
}

if ( !function_exists('session_get')) {

    function session_get($key)
    {
        return $_SESSION[$key] ?? '';
    }
}

if ( !function_exists('email_mask')) {

    function email_mask($data)
    {
        $email = explode('@', $data);

        return substr($email[0], 0, 3) . str_repeat('x', strlen($email[0]) - 3) . '@' . $email[1];
    }
}

if ( !function_exists('number_mask')) {

    function number_mask($data)
    {
        $len = strlen($data);

        return str_repeat('x', $len - 4) . substr($data, - 4);
    }
}
