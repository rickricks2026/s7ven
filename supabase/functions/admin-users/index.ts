// Função de servidor. As chaves de serviço ficam apenas nas variáveis do Supabase.
import { createClient } from 'npm:@supabase/supabase-js@2.119.0';
const url=Deno.env.get('SUPABASE_URL')!;
const serviceKey=Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
const site=(Deno.env.get('PUBLIC_SITE_URL')||'').replace(/\/$/,'');
const allowed=(Deno.env.get('S7VEN_ALLOWED_ORIGINS')||site).split(',').map(x=>x.trim()).filter(Boolean);
Deno.serve(async(req:Request)=>{
 const origin=req.headers.get('Origin')||'';
 const cors={'Access-Control-Allow-Origin':allowed.includes(origin)?origin:'null','Access-Control-Allow-Headers':'authorization, x-client-info, apikey, content-type','Access-Control-Allow-Methods':'POST, OPTIONS','Vary':'Origin'};
 const send=(status:number,data:unknown)=>new Response(JSON.stringify(data),{status,headers:{...cors,'Content-Type':'application/json','Cache-Control':'no-store'}});
 if(req.method==='OPTIONS')return new Response(null,{status:204,headers:cors});
 if(req.method!=='POST')return send(405,{error:'Use POST.'});
 if(origin&&!allowed.includes(origin))return send(403,{error:'Origem não autorizada.'});
 try{
  const token=(req.headers.get('Authorization')||'').replace(/^Bearer\s+/i,'');if(!token)return send(401,{error:'Autenticação necessária.'});
  const db=createClient(url,serviceKey,{auth:{persistSession:false,autoRefreshToken:false}});
  const {data:identity,error:ae}=await db.auth.getUser(token);if(ae||!identity.user)return send(401,{error:'Sessão inválida.'});
  const uid=identity.user.id;
  const [{data:admin},{data:profile}]=await Promise.all([db.from('app_admins').select('user_id').eq('user_id',uid).maybeSingle(),db.from('profiles').select('active').eq('id',uid).maybeSingle()]);
  if(!admin||!profile?.active)return send(403,{error:'Permissão administrativa necessária.'});
  const size=Number(req.headers.get('content-length')||0);if(size>8192)return send(413,{error:'Requisição muito grande.'});
  const body=await req.json();const action=body.action;
  if(action==='list'){
   const page=Math.max(1,Math.min(100,Number(body.page)||1));const {data,error}=await db.auth.admin.listUsers({page,perPage:100});if(error)throw error;
   return send(200,{users:data.users.map(u=>({id:u.id,email:u.email,name:u.user_metadata?.display_name||'',blocked:Boolean(u.banned_until&&new Date(u.banned_until)>new Date()),created_at:u.created_at})),page,hasMore:data.users.length===100});
  }
  const id=String(body.id||'');
  if(action==='create'){
   const name=String(body.name||'').trim(),email=String(body.email||'').trim(),password=String(body.password||'');
   if(!name||name.length>100||!/^\S+@\S+\.\S+$/.test(email)||password.length<12||password.length>200)return send(400,{error:'Informe nome, e-mail e senha de pelo menos 12 caracteres.'});
   const {data,error}=await db.auth.admin.createUser({email,password,email_confirm:true,user_metadata:{display_name:name},app_metadata:{s7ven_admin_created:true}});if(error)throw error;
   await db.from('audit_logs').insert({actor_id:uid,action:'create_user',entity:'auth.users',entity_id:data.user.id});return send(200,{ok:true});
  }
  if(!/^[a-f0-9-]{36}$/i.test(id))return send(400,{error:'Cliente inválido.'});
  const {data:target,error:te}=await db.auth.admin.getUserById(id);if(te||!target.user)return send(404,{error:'Cliente não encontrado.'});
  if(action==='update'){
   const name=String(body.name||'').trim(),email=String(body.email||'').trim();if(!name||name.length>100||!/^\S+@\S+\.\S+$/.test(email))return send(400,{error:'Nome ou e-mail inválido.'});
   const {data:targetAdmin}=await db.from('app_admins').select('user_id').eq('user_id',id).maybeSingle();
   if(targetAdmin&&(body.blocked||email!==target.user.email))return send(400,{error:'Contas administrativas não podem ser bloqueadas ou ter o acesso alterado nesta tela.'});
   const blocked=body.blocked===true;
   // Bloquear acesso aos dados antes de bloquear o login no Auth.
   if(blocked){const {error}=await db.from('profiles').update({active:false}).eq('id',id);if(error)throw error;}
   const {error}=await db.auth.admin.updateUserById(id,{email,user_metadata:{...target.user.user_metadata,display_name:name},ban_duration:blocked?'876000h':'none'});if(error)throw error;
   const {error:pe}=await db.from('profiles').update({display_name:name,email,active:!blocked}).eq('id',id);if(pe)throw pe;
   await db.from('audit_logs').insert({actor_id:uid,action:blocked?'block_user':'update_user',entity:'auth.users',entity_id:id});return send(200,{ok:true});
  }
  if(action==='recovery'){
   if(!site.startsWith('https://'))return send(503,{error:'O responsável precisa configurar PUBLIC_SITE_URL no servidor.'});
   const {error}=await db.auth.resetPasswordForEmail(target.user.email!,{redirectTo:site+'/nova-senha.html'});if(error)throw error;
   await db.from('audit_logs').insert({actor_id:uid,action:'recover_user',entity:'auth.users',entity_id:id});return send(200,{ok:true});
  }
  return send(400,{error:'Ação inválida.'});
 }catch(error){console.error('admin-users:',error instanceof Error?error.message:'error');return send(400,{error:'Não foi possível concluir. Verifique os dados e os logs da função no provedor.'});}
});
