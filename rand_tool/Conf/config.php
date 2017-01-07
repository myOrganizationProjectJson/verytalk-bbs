<?php
require ('../config/config_global.php');
$configs=$_config['db']['1'];
$dbhost=$configs['dbhost'];
$dbuser=$configs['dbuser'];
$dbpw=$configs['dbpw'];
$dbname=$configs['dbname'];
return array(
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => "$dbhost", // 服务器地址
    'DB_NAME'   => "$dbname", // 数据库名
    'DB_USER'   => "$dbuser", // 用户名
    'DB_PWD'    => "$dbpw", // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'pre_', // 数据库表前缀 
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 调试模式
);
?>