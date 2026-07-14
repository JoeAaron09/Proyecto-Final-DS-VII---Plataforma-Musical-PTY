<?php
namespace App\Core;
abstract class Controller {
    protected function view(string $view,array $data=[]):void{$config=require dirname(__DIR__,2).'/config.php';extract($data,EXTR_SKIP);
    $file=dirname(__DIR__).'/Views/'.$view.'.php';if(!is_file($file))throw new \RuntimeException('Vista inexistente: '.$view);
    require dirname(__DIR__).'/Views/layouts/header.php';
    require $file;
    require dirname(__DIR__).'/Views/layouts/footer.php';
    } 
    protected function redirect(string $path):never{$c=require dirname(__DIR__,2).'/config.php';
    header('Location: '.$c['base_url'].$path);
    exit;
    }
}
