<?php
namespace App\Helpers;
use App\Core\HttpException;
final class Auth { 
    public static function user():?array{
        return $_SESSION['user']??null;
    } 
    public static function check():bool{
        return isset($_SESSION['user']);
        } 
    public static function id():?int{
        return self::user()['id']??null;
        } 
    public static function role():string{
        return self::user()['rol']??'';
     } 
    public static function requireLogin():void{
        if(!self::check()){self::go('/login');
        }
    }
     public static function requireAdmin(bool $allowOperator=true):void{self::requireLogin();
     $ok=self::role()==='Administrador'||($allowOperator&&self::role()==='Operador');
     if(!$ok){throw new HttpException(403, 'Acceso denegado.');
     }
    }
      public static function go(string $path):never{$c=require dirname(__DIR__,2).'/config.php';header('Location: '.$c['base_url'].$path);
      exit;
      } 
    }
