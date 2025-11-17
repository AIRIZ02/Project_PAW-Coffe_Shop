<?php
$config = include __DIR__ . '/../db_config.php';

$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];

$cmd = "mysql -u $user ".($pass?"-p$pass":"")." $db < ".__DIR__."/../database/db.sql";
system($cmd);

echo "AUTO IMPORT DB DONE\n";
?>
