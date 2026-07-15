<?php
namespace App\Config;
use PDO; use PDOException;
final class Database { private static ?PDO $pdo=null; public static function connection():PDO { if(self::$pdo)return self::$pdo; $c=require dirname(__DIR__,2).'/config.php';$d=$c['db']; try{ self::$pdo=new PDO("mysql:host={$d['host']};dbname={$d['name']};charset={$d['charset']}",$d['user'],$d['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); return self::$pdo;}catch(PDOException $e){throw new \RuntimeException('No fue posible conectar con la base de datos.',0,$e);}}}
