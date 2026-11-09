<?php
if(!defined('S7VEN')){http_response_code(404);exit;}
function install_schema(PDO $p,string $driver): void {
$id=$driver==='mysql'?'BIGINT PRIMARY KEY AUTO_INCREMENT':'INTEGER PRIMARY KEY AUTOINCREMENT';
$blob=$driver==='mysql'?'LONGBLOB':'BLOB';
$tables=[
"settings (name VARCHAR(100) PRIMARY KEY,value TEXT NOT NULL)",
"users (id $id,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,role VARCHAR(20) NOT NULL DEFAULT 'client',active INTEGER NOT NULL DEFAULT 1,created_at VARCHAR(19) NOT NULL)",
"products (id $id,name VARCHAR(150) NOT NULL,category VARCHAR(40) NOT NULL,description TEXT NOT NULL,features TEXT NOT NULL,price DECIMAL(12,2) NULL,visible INTEGER NOT NULL DEFAULT 1,created_at VARCHAR(19) NOT NULL)",
"orders (id $id,user_id BIGINT NOT NULL,product_id BIGINT NOT NULL,product_name VARCHAR(150) NOT NULL,amount DECIMAL(12,2) NULL,status VARCHAR(30) NOT NULL DEFAULT 'requested',domain VARCHAR(253) NOT NULL,notes TEXT NOT NULL,created_at VARCHAR(19) NOT NULL,updated_at VARCHAR(19) NOT NULL)",
"licenses (id $id,user_id BIGINT NOT NULL,product_id BIGINT NOT NULL,order_id BIGINT NULL UNIQUE,domain VARCHAR(253) NOT NULL,key_hash VARCHAR(64) NOT NULL UNIQUE,status VARCHAR(20) NOT NULL DEFAULT 'active',expires_at VARCHAR(10) NULL,created_at VARCHAR(19) NOT NULL)",
"tickets (id $id,user_id BIGINT NOT NULL,subject VARCHAR(200) NOT NULL,status VARCHAR(20) NOT NULL DEFAULT 'open',created_at VARCHAR(19) NOT NULL,updated_at VARCHAR(19) NOT NULL)",
"messages (id $id,ticket_id BIGINT NOT NULL,user_id BIGINT NOT NULL,body TEXT NOT NULL,created_at VARCHAR(19) NOT NULL)",
"content (id $id,kind VARCHAR(30) NOT NULL,title VARCHAR(180) NOT NULL,body TEXT NOT NULL,version VARCHAR(30) NOT NULL DEFAULT '',link VARCHAR(1000) NOT NULL DEFAULT '',image_url VARCHAR(1000) NOT NULL DEFAULT '',published INTEGER NOT NULL DEFAULT 0,created_at VARCHAR(19) NOT NULL)",
"leads (id $id,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL,server_name VARCHAR(150) NOT NULL,version VARCHAR(40) NOT NULL,plan VARCHAR(100) NOT NULL,body TEXT NOT NULL,status VARCHAR(20) NOT NULL DEFAULT 'new',created_at VARCHAR(19) NOT NULL)",
"files (id $id,product_id BIGINT NULL,ticket_id BIGINT NULL,uploaded_by BIGINT NOT NULL,name VARCHAR(200) NOT NULL,size BIGINT NOT NULL,sha256 VARCHAR(64) NOT NULL,data $blob NOT NULL,created_at VARCHAR(19) NOT NULL)",
"audit (id $id,user_id BIGINT NULL,action VARCHAR(100) NOT NULL,details TEXT NOT NULL,created_at VARCHAR(19) NOT NULL)",
"attempts (id $id,identity VARCHAR(64) NOT NULL,created_at VARCHAR(19) NOT NULL)"
];foreach($tables as $t)$p->exec('CREATE TABLE '.$t.($driver==='mysql'?' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4':''));
foreach(['CREATE INDEX idx_attempts ON attempts (identity,created_at)','CREATE INDEX idx_license_owner ON licenses (user_id,product_id,status)','CREATE INDEX idx_ticket_owner ON tickets (user_id,status)','CREATE INDEX idx_order_owner ON orders (user_id,status)','CREATE INDEX idx_messages ON messages (ticket_id,id)','CREATE INDEX idx_files_product ON files (product_id)','CREATE INDEX idx_files_ticket ON files (ticket_id)','CREATE INDEX idx_content_public ON content (kind,published)'] as $sql)$p->exec($sql);
}
