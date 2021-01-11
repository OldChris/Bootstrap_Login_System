<?php 
$charset = 'utf8mb4';
$dsn = "mysql:host=$SessionDbHost;dbname=$SessionDbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,];

try
{
    $con = new PDO($dsn, $SessionDbUsername, $SessionDbPassword, $options);
    $con_status="OK";
}
catch (\PDOException $e)
{
    $con_status="FAILED " . $e->getMessage();
    LogFileSQL($con_status, "A system error occurred, please contact the administrator.");
    die;
}





?>