<?php
declare(strict_types=1);
define('S7VEN', true);
define('ROOT', dirname(__DIR__));
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', '0');
ini_set('session.use_strict_mode','1');
$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_name('s7ven_session');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header('Content-Type: text/html; charset=UTF-8');
set_exception_handler(function(Throwable $e): void { error_log('[S7VEN] '.$e->getMessage()); http_response_code(500); echo '<!doctype html><meta charset="utf-8"><title>S7VEN</title><p>Não foi possível concluir a operação. Consulte o log de erros da hospedagem.</p>'; });
$script = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
if(in_array(basename($script), ['admin','cliente','pages','api'],true)) $script=dirname($script);
$base = rtrim($script === '/' ? '' : $script,'/');
$cfg = is_file(ROOT.'/storage/config.php') ? require ROOT.'/storage/config.php' : null;
function e(mixed $s): string { return htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function url(string $path=''): string {global $base; return $base.'/'.ltrim($path,'/');}
function go(string $path): never {header('Location: '.url($path),true,303);exit;}
function token(): string {return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));}
function csrf(): void {echo '<input type="hidden" name="csrf" value="'.e(token()).'">';}
function verify_csrf(): void {if(!is_string($_POST['csrf']??null)||!hash_equals(token(),$_POST['csrf'])){http_response_code(403);exit('Formulário expirado. Recarregue a página.');}}
function is_post(): bool {return $_SERVER['REQUEST_METHOD']==='POST';}
function input(string $key,int $max=200): string { $v=$_POST[$key]??''; if(!is_string($v)||strlen($v)>$max)throw new InvalidArgumentException('Campo inválido: '.$key); return trim($v); }
function flash(string $s): void {$_SESSION['flash']=$s;}
function money(mixed $n): string {return $n===null?'Sob orçamento':'R$ '.number_format((float)$n,2,',','.');}
function datebr(?string $d): string {return $d ? date('d/m/Y',strtotime($d)) : 'Sem vencimento';}
function db(): PDO {global $cfg;static $pdo;if(!$pdo){if(!$cfg)throw new RuntimeException('Instalação pendente');$dsn=$cfg['driver']==='mysql'?'mysql:host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['database'].';charset=utf8mb4':'sqlite:'.$cfg['path'];$pdo=new PDO($dsn,$cfg['user']??null,$cfg['password']??null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);if($cfg['driver']==='sqlite'){$pdo->exec('PRAGMA foreign_keys=ON');$pdo->exec('PRAGMA busy_timeout=5000');}}return $pdo;}
function run(string $sql,array $args=[]): PDOStatement {$s=db()->prepare($sql);$s->execute($args);return $s;}
function one(string $sql,array $args=[]): ?array {return run($sql,$args)->fetch()?:null;}
function rows(string $sql,array $args=[]): array {return run($sql,$args)->fetchAll();}
function setting(string $key,string $default=''): string {if(!$GLOBALS['cfg'])return $default; $v=one('SELECT value FROM settings WHERE name=?',[$key]);return $v?$v['value']:$default;}
function user(): ?array {static $u=false;if($u===false){$u=isset($_SESSION['uid'])?one('SELECT id,name,email,role,active FROM users WHERE id=?',[$_SESSION['uid']]):null;if($u&&!$u['active']){$u=null;unset($_SESSION['uid']);}}return $u?:null;}
function auth(bool $admin=false): array {$u=user();if(!$u)go('login.php');if($admin&&$u['role']!=='admin'){http_response_code(403);exit('Acesso restrito.');}header('Cache-Control: no-store');return $u;}
function audit(string $action,string $details=''): void {run('INSERT INTO audit (user_id,action,details,created_at) VALUES (?,?,?,?)',[user()['id']??null,$action,$details,date('Y-m-d H:i:s')]);}
function safe_url(string $v): string {if($v==='')return '';if(!filter_var($v,FILTER_VALIDATE_URL)||strtolower((string)parse_url($v,PHP_URL_SCHEME))!=='https')throw new InvalidArgumentException('Use uma URL HTTPS válida.');return $v;}
function domain(string $v): string {$v=strtolower(trim($v));if(!filter_var($v,FILTER_VALIDATE_DOMAIN,FILTER_FLAG_HOSTNAME)||!str_contains($v,'.')||strlen($v)>253)throw new InvalidArgumentException('Informe apenas o domínio válido, sem https ou caminho.');return $v;}
function rate_limit(string $scope,int $limit=8,int $window=900): bool {$ip=hash('sha256',($_SERVER['REMOTE_ADDR']??'local').'|'.$scope);$since=date('Y-m-d H:i:s',time()-$window);run('DELETE FROM attempts WHERE created_at < ?', [date('Y-m-d H:i:s',time()-86400)]);$n=one('SELECT COUNT(*) AS n FROM attempts WHERE identity=? AND created_at>=?',[$ip,$since]);if((int)$n['n']>=$limit)return false;run('INSERT INTO attempts (identity,created_at) VALUES (?,?)',[$ip,date('Y-m-d H:i:s')]);return true;}
if(!$cfg && basename($_SERVER['SCRIPT_NAME']??'')!=='instalar.php') go('instalar.php');
function parse_price(string $v): string {$v=str_replace(['R$',' '],'',$v);if(str_contains($v,','))$v=str_replace(',','.',str_replace('.','',$v));if(!preg_match('/^\d{1,9}(\.\d{1,2})?$/',$v))throw new InvalidArgumentException('Valor inválido. Use por exemplo 299,90.');return number_format((float)$v,2,'.','');}
