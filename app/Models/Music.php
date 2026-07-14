<?php
namespace App\Models;
use App\Core\Model;
final class Music extends Model {
 public function songs():array{return $this->db->query("SELECT c.*,g.nombre genero,a.nombre artista,al.nombre album FROM canciones c LEFT JOIN generos g ON g.id=c.genero_id LEFT JOIN artistas a ON a.id=c.artista_id LEFT JOIN albumes al ON al.id=c.album_id WHERE c.estado=1 ORDER BY c.id DESC")->fetchAll();}
 public function play(int $songId,int $userId):array{$cfg=require dirname(__DIR__,2).'/config.php';$s=$this->db->prepare("SELECT tipo_usuario FROM usuarios WHERE id=?");$s->execute([$userId]);$type=$s->fetchColumn();if($type!=='premium'){$q=$this->db->prepare("SELECT COUNT(*) FROM reproducciones WHERE usuario_id=? AND YEAR(fecha_hora)=YEAR(CURRENT_DATE()) AND MONTH(fecha_hora)=MONTH(CURRENT_DATE())");$q->execute([$userId]);$count=(int)$q->fetchColumn();if($count>=$cfg['free_monthly_limit'])return ['ok'=>false,'message'=>'Alcanzaste el límite mensual gratuito. Activa Premium para continuar.'];}
 $this->db->prepare('INSERT INTO reproducciones(cancion_id,usuario_id,nacionalidad_usuario,fecha_hora) SELECT ?,id,nacionalidad,NOW() FROM usuarios WHERE id=?')->execute([$songId,$userId]);return ['ok'=>true,'message'=>'Reproducción registrada.'];}
 public function top10():array{return $this->db->query("SELECT c.nombre cancion,COALESCE(a.nombre,b.nombre,'Sin artista') artista,COUNT(r.id) total FROM reproducciones r JOIN canciones c ON c.id=r.cancion_id LEFT JOIN artistas a ON a.id=c.artista_id LEFT JOIN bandas b ON b.id=c.banda_id WHERE r.fecha_hora>=DATE_SUB(NOW(),INTERVAL 30 DAY) GROUP BY c.id ORDER BY total DESC LIMIT 10")->fetchAll();}
 public function artistNow():?array{$s=$this->db->query("SELECT a.nombre artista,COUNT(r.id) total FROM artistas a JOIN canciones c ON c.artista_id=a.id JOIN reproducciones r ON r.cancion_id=c.id WHERE r.fecha_hora>=DATE_SUB(NOW(),INTERVAL 30 DAY) GROUP BY a.id ORDER BY total DESC LIMIT 1");return $s->fetch()?:null;}
 public function events():array{return $this->db->query("SELECT e.*,l.nombre local_nombre FROM eventos e LEFT JOIN locales l ON l.id=e.local_id WHERE e.estado=1 AND e.fecha>=CURRENT_DATE() ORDER BY e.fecha,e.hora")->fetchAll();}
}
