<?php
require dirname(__DIR__).'/core/bootstrap.php';header('Content-Type: application/json; charset=UTF-8');header('Cache-Control: no-store');
function respond(int $status,array $data): never {http_response_code($status);echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
if(!is_post()){header('Allow: POST');respond(405,['valid'=>false,'error'=>'Use POST.']);}
if(!$secure&&!in_array($_SERVER['REMOTE_ADDR']??'',['127.0.0.1','::1'],true))respond(400,['valid'=>false,'error'=>'HTTPS obrigatório.']);
if(!rate_limit('license-api',90,60))respond(429,['valid'=>false,'error'=>'Limite temporário.']);
$key=$_POST['key']??'';$host=$_POST['domain']??'';
if(!is_string($key)||strlen($key)>150||!is_string($host)||strlen($host)>253)respond(400,['valid'=>false]);
try{$host=domain($host);}catch(InvalidArgumentException){respond(400,['valid'=>false]);}
$l=one('SELECT l.id,l.product_id,l.domain,l.expires_at,l.status,u.active FROM licenses l JOIN users u ON u.id=l.user_id WHERE l.key_hash=?',[hash('sha256',$key)]);
$valid=$l&&$l['active']&&$l['status']==='active'&&hash_equals($l['domain'],$host)&&(!$l['expires_at']||$l['expires_at']>=date('Y-m-d'));
if(!$valid)respond(403,['valid'=>false,'error'=>'Licença inválida, suspensa, vencida ou domínio divergente.']);
run('INSERT INTO audit (user_id,action,details,created_at) VALUES (NULL,?,?,?)',['licenca_validada','Licença #'.$l['id'].' '.$host,date('Y-m-d H:i:s')]);
respond(200,['valid'=>true,'product_id'=>(int)$l['product_id'],'domain'=>$l['domain'],'expires_at'=>$l['expires_at']]);
