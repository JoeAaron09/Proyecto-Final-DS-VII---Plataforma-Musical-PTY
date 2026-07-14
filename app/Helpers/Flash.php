<?php
namespace App\Helpers;
final class Flash {
    public static function set(string $type,string $msg):void{$_SESSION['flash']=[$type,$msg];
    } 
    public static function pull():?array{$f=$_SESSION['flash']??null;unset($_SESSION['flash']);
    return $f;
    }
}
