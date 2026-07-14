<?php
namespace App\Models;
use App\Core\Model;
final class Catalog extends Model {
 private array $allowed=['generos','artistas','bandas','albumes','canciones','locales','eventos','planes'];
 private function ok(string $t):string{if(!in_array($t,$this->allowed,true))throw new \InvalidArgumentException('Tabla inválida');return $t;}
 public function all(string $t):array{$t=$this->ok($t);return $this->db->query("SELECT * FROM {$t} ORDER BY id DESC")->fetchAll();}
 public function active(string $t):array{$t=$this->ok($t);return $this->db->query("SELECT * FROM {$t} WHERE estado=1 ORDER BY nombre")->fetchAll();}
 public function find(string $t,int $id):?array{$t=$this->ok($t);$s=$this->db->prepare("SELECT * FROM {$t} WHERE id=?");$s->execute([$id]);return $s->fetch()?:null;}
 public function save(string $t,array $data,?int $id=null):void{$t=$this->ok($t);$cols=array_keys($data);if($id){$set=implode(',',array_map(fn($c)=>"{$c}=?",$cols));$vals=array_values($data);$vals[]=$id;$this->db->prepare("UPDATE {$t} SET {$set} WHERE id=?")->execute($vals);}else{$marks=implode(',',array_fill(0,count($cols),'?'));$this->db->prepare("INSERT INTO {$t}(".implode(',',$cols).") VALUES({$marks})")->execute(array_values($data));}}
 public function delete(string $t,int $id):void{$t=$this->ok($t);$this->db->prepare("UPDATE {$t} SET estado=0 WHERE id=?")->execute([$id]);}
 public function dashboard():array{return [
  'artistas'=>(int)$this->db->query('SELECT COUNT(*) FROM artistas WHERE estado=1')->fetchColumn(),
  'canciones'=>(int)$this->db->query('SELECT COUNT(*) FROM canciones WHERE estado=1')->fetchColumn(),
  'eventos'=>(int)$this->db->query('SELECT COUNT(*) FROM eventos WHERE estado=1')->fetchColumn(),
  'usuarios'=>(int)$this->db->query('SELECT COUNT(*) FROM usuarios WHERE estado=1')->fetchColumn(),
 ];}
}
