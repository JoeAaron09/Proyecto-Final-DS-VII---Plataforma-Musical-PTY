<?php
namespace App\Models;
use App\Core\Model;
final class User extends Model {
 public function findByEmail(string $email):?array{$s=$this->db->prepare('SELECT u.*,r.nombre rol FROM usuarios u JOIN roles r ON r.id=u.rol_id WHERE u.correo=? LIMIT 1');$s->execute([$email]);return $s->fetch()?:null;}
 public function all():array{return $this->db->query('SELECT u.id,u.nombre,u.correo,r.nombre rol,u.estado,u.creado_en FROM usuarios u JOIN roles r ON r.id=u.rol_id ORDER BY u.id DESC')->fetchAll();}
 public function create(array $d):void{$s=$this->db->prepare('INSERT INTO usuarios(nombre,correo,password,rol_id,estado) VALUES(?,?,?,?,1)');$s->execute([$d['nombre'],$d['correo'],password_hash($d['password'],PASSWORD_DEFAULT),$d['rol_id']]);}
 public function update(int $id,array $d):void{$sql='UPDATE usuarios SET nombre=?,correo=?,rol_id=?,estado=?';$p=[$d['nombre'],$d['correo'],$d['rol_id'],$d['estado']];if(!empty($d['password'])){$sql.=',password=?';$p[]=password_hash($d['password'],PASSWORD_DEFAULT);} $sql.=' WHERE id=?';$p[]=$id;$this->db->prepare($sql)->execute($p);}
 public function find(int $id):?array{$s=$this->db->prepare('SELECT * FROM usuarios WHERE id=?');$s->execute([$id]);return $s->fetch()?:null;}
 public function roles():array{return $this->db->query('SELECT * FROM roles ORDER BY id')->fetchAll();}
}
