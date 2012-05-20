<?php
// web_request code modified from Jeremy Saintot's example: http://www.php.net/manual/en/function.fsockopen.php#101872

// http_chunk_decode and is_hex modified from Marques Johansson's example: http://www.php.net/manual/en/function.http-chunked-decode.php#89786


function web_request( 
    $verb = 'GET',             /* HTTP Request Method (GET and POST supported) */ 
    $ip,                       /* Target IP/Hostname */ 
    $port = 80,                /* Target TCP port */ 
    $uri = '/',                /* Target URI */ 
    $getdata = array(),        /* HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $postdata = array(),       /* HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $cookie = array(),         /* HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $custom_headers = array(), /* Custom HTTP headers ie. array('Referer: http://localhost/ */ 
    $timeout = 10000,           /* Socket timeout in milliseconds */ 
    $req_hdr = false,          /* Include HTTP request headers */ 
    $res_hdr = false           /* Include HTTP response headers */ 
    ) 
{ 
    $ret = ''; 
    $verb = strtoupper($verb); 
    $cookie_str = ''; 
    $getdata_str = count($getdata) ? '?' : ''; 
    $postdata_str = ''; 

    foreach ($getdata as $k => $v) {
        if ($getdata_str != '?')
            $getdata_str.="&";
        $getdata_str .= urlencode($k) .'='. urlencode($v); 
    }

    foreach ($postdata as $k => $v) 
        $postdata_str .= urlencode($k) .'='. urlencode($v) .'&'; 

    foreach ($cookie as $k => $v) 
        $cookie_str .= urlencode($k) .'='. urlencode($v) .'; '; 

    $crlf = "\r\n"; 
    $req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf; 
    $req .= 'Host: '. $ip . $crlf; 
    $req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf; 
    $req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf; 
    $req .= 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4' . $crlf; 
    $req .= 'Accept-Encoding: deflate' . $crlf; 
    $req .= 'Accept-Charset:windows-1251,utf-8;q=0.7,*;q=0.3' . $crlf; 

    //$req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf;
    //$req .= 'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.3' . $crlf;
    //$req .= 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4' . $crlf;
    //$req .= 'Cache-Control: no-cache' . $crlf;
    //$req .= 'Cookie: SESSe7410a6b84c65c5bae6c1d93f3bd1c41=6d3dadfa817f734eaa2dfa968a33cc39; has_js=1; __utma=5945707.235451808.1337358983.1337410478.1337410478.4; __utmb=5945707.3.9.1337423469611; __utmc=5945707; __utmz=5945707.1337358983.1.1.utmcsr=vk.com|utmccn=(referral)|utmcmd=referral|utmcct=/im' . $crlf;
    //$req .= 'Pragma: no-cache' . $crlf;
    //$req .= 'Referer: http://www.infodozer.com/ru/tokico' . $crlf;
    //req .= 'User-Agent: Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/12.04 Chromium/18.0.1025.151 Chrome/18.0.1025.151 Safari/535.19' . $crlf;
    //echo "\n$req\n";
    foreach ($custom_headers as $k => $v) 
        $req .= $k .': '. $v . $crlf; 
        
    if (!empty($cookie_str)) 
        $req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf; 
        
    if ($verb == 'POST' && !empty($postdata_str)) 
    { 
        $postdata_str = substr($postdata_str, 0, -1); 
        $req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf; 
        $req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf; 
        $req .= $postdata_str; 
    } 
    else $req .= $crlf; 
    
    if ($req_hdr) 
        $ret .= $req; 
    
    if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false) 
        return "Error $errno: $errstr\n"; 
    
    stream_set_timeout($fp, 0, $timeout * 1000); 
    
    fputs($fp, $req); 
    while ($line = fgets($fp)) {
        $ret .= $line; 
    }
    fclose($fp); 
    
    if (!$res_hdr) 
        $ret = substr($ret, strpos($ret, "\r\n\r\n") + 4); 
    
    return http_chunk_decode($ret); 
}

/** 
 * dechunk an http 'transfer-encoding: chunked' message 
 * 
 * @param string $chunk the encoded message 
 * @return string the decoded message.  If $chunk wasn't encoded properly it will be returned unmodified. 
 */ 
function http_chunk_decode($chunk) { 
    $pos = 0; 
    $len = strlen($chunk); 
    $dechunk = null; 

    while(($pos < $len) 
        && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) 
    { 
        if (! is_hex($chunkLenHex)) { 
            trigger_error('Value is not properly chunk encoded', E_USER_WARNING); 
            return $chunk; 
        } 

        $pos = $newlineAt + 1; 
        $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n")); 
        $dechunk .= substr($chunk, $pos, $chunkLen); 
        $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1; 
    } 
    return $dechunk; 
} 

/** 
 * determine if a string can represent a number in hexadecimal 
 * 
 * @param string $hex 
 * @return boolean true if the string is a hex, otherwise false 
 */ 
function is_hex($hex) { 
    // regex is for weenies 
    $hex = strtolower(trim(ltrim($hex,"0"))); 
    if (empty($hex)) { $hex = 0; }; 
    $dec = hexdec($hex); 
    return ($hex == dechex($dec)); 
} 

?>