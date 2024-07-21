<?php
global $_GET;
$_GET = $_GET ?? [];
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            } 
        }
        return $headers;
    }
}
//findout real url ($_GET['x-real-url'])
(function() {
    global $_GET;
    $requestUri = $_SERVER['REQUEST_URI'];
    $realurlMatches = [];
    preg_match('~((\?|&)x-real-url=(.*)$)~iU', $_SERVER['REQUEST_URI'], $realurlMatches);

    $realurl = null;
    if(count($realurlMatches) >= 3) {
        // Prüfen, ob ein '?' vor dem ersten '&' vorkommt
        if (preg_match('/^[^&]*\?/', $realurlMatches[3])) {
            // Wenn ja, gib den gesamten String zurück
            $realurl =  $realurlMatches[3];
            $requestUri = str_replace($realurlMatches[0], '', $_SERVER['REQUEST_URI']);
            $_SERVER['REQUEST_URI'] =$requestUri;

        } else {
            // Wenn nein, extrahiere alles bis zum ersten '&'
            preg_match('/^[^&]*/', $realurlMatches[3], $matches);
            $realurl =  $matches[0];
        }
    }
    // var_dump( $realurl);
    // var_dump($realurlMatches);
    // var_dump( parse_url($requestUri));
    if(empty($realurl)) {
        @list('query' => $query, 'path' => $path) = parse_url($requestUri);
        parse_str($query, $_GET);
    } else {

        $_GET['x-real-url'] = $realurl ;
    }
    
})();


