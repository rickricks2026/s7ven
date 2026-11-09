<?php
// EXEMPLO DE REFERÊNCIA. O instalador grava o arquivo real em storage/config.php.
// Não coloque suas credenciais neste exemplo. Use instalar.php.
if(!defined('S7VEN')){http_response_code(404);exit;}
return ['driver'=>'mysql','host'=>'localhost','port'=>'3306','database'=>'USUARIO_BANCO','user'=>'USUARIO_SQL','password'=>'SENHA_SQL'];
