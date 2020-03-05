<?php

function geturl($url){
    $ch =curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $result =curl_exec($ch);
    curl_close($ch);
    $data=json_decode($result,true);
    return $result;
}