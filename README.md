---Example usage
``composer require hasirciogli/session-wrapper``


```PHP


<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Hasirciogli\Hdb\Interfaces\Database\Config\DatabaseConfigInterface;
use Hasirciogli\SessionWrapper\Session;
use Hasirciogli\SessionWrapper\Storage\MysqlStorage;

class DatabaseConfig implements DatabaseConfigInterface
{
    const DB_HOST = "localhost";
    const DB_NAME = "reseller";
    const DB_USER = "root";
    const DB_PASS = "1234";

    public static function cfun()
    {
        return new DatabaseConfig();
    }
}


$SessionClass = new Session(DatabaseConfig::cfun());

$SessionClass->Get("key");
$SessionClass->Set("key", "value");


die(PHP_EOL);


```
