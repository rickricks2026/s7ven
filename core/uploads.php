<?php
if(!defined('S7VEN'))exit;
function save_upload(?int $product,?int $ticket): void {
if(!isset($_FILES['file'])||$_FILES['file']['error']===UPLOAD_ERR_NO_FILE)return;
$f=$_FILES['file'];if($f['error']!==UPLOAD_ERR_OK||$f['size']>5*1024*1024)throw new InvalidArgumentException('Arquivo inválido ou maior que 5 MB.');
$name=basename(str_replace('\\','/',$f['name']));$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
if(!in_array($ext,['zip','pdf','txt','png','jpg','jpeg'],true)||strlen($name)>180)throw new InvalidArgumentException('Use ZIP, PDF, TXT, PNG ou JPG com nome até 180 caracteres.');
if(!is_uploaded_file($f['tmp_name']))throw new InvalidArgumentException('Upload inválido.');
$data=file_get_contents($f['tmp_name']);if($data===false)throw new RuntimeException('Falha na leitura do upload.');
$s=db()->prepare('INSERT INTO files (product_id,ticket_id,uploaded_by,name,size,sha256,data,created_at) VALUES (?,?,?,?,?,?,?,?)');
foreach([$product,$ticket,user()['id'],$name,strlen($data),hash('sha256',$data),$data,date('Y-m-d H:i:s')] as $i=>$v)$s->bindValue($i+1,$v,$i===6?PDO::PARAM_LOB:($v===null?PDO::PARAM_NULL:(is_int($v)?PDO::PARAM_INT:PDO::PARAM_STR)));$s->execute();
}
