# ATUALIZAÇÃO: /admin e gerenciamento completo

Esta versão é para publicar na RAIZ do repositório. Leia primeiro `ADMINISTRACAO.md`, com as migrações adicionais, Storage e função segura de clientes. O painel agora está em `/admin`, com login administrativo próprio.

# S7VEN Premium — GitHub Pages

Esta versão utiliza Inter por padrão, com Manrope e Plus Jakarta Sans opcionais, todas servidas localmente, fontes maiores, interface com mais espaço, páginas próprias de cadastro/login/recuperação e central responsiva. O ZIP não contém PHP e não precisa de build.

## 1. Colocar no GitHub

1. Extraia o ZIP no computador.
2. Envie o conteúdo extraído para a raiz do repositório: `index.html`, demais `.html`, `config.js`, pastas `assets`, `vendor` e `backend`, `.nojekyll` e documentação.
3. Preserve o arquivo `CNAME` já existente, se você usa `s7ven.pro`. Este ZIP não o substitui.
4. Em **Settings → Pages**, use a publicação da branch `main`, pasta `/(root)` (ou a branch que você utiliza).
5. Acesse a raiz do site: não existe `instalar.php` nesta versão.
6. Se os arquivos novos não aparecerem, faça uma atualização forçada no navegador após o deploy terminar.

Arquivos antigos de PHP não são utilizados. Faça backup antes de substituí-los. Nunca envie arquivos de configuração privados, bancos, senhas ou chaves de serviço ao GitHub.

## 2. Importante: contas reais no GitHub

O GitHub Pages hospeda o frontend; não executa PHP ou banco. Esta entrega inclui uma integração completa de autenticação/dados para um projeto **Supabase seu**, que precisa ser ativada por você.

Sem configuração, a página comercial, catálogo, filtros, prévias, formulários e briefing em TXT funcionam. Cadastro, login, recuperação por e-mail, gravação de pedidos e chamados ficam indisponíveis. Existe uma prévia visual da central explicitamente identificada, sem criar contas ou salvar senhas. Não há login falso por localStorage.

### Ativar Supabase

1. Acesse https://supabase.com/ e crie um projeto para a S7VEN. Você mantém a propriedade do projeto. Planos, quotas, cobrança e limites de e-mail dependem do provedor; confira no seu painel.
2. No **SQL Editor**, abra uma consulta e execute todo o arquivo `backend/supabase.sql`. Use um projeto novo/dedicado: a migração não foi feita para sobrescrever tabelas de outro sistema.
3. Em **Authentication**, habilite o provedor de e-mail e confirmação de cadastro. Defina senha mínima de 12 caracteres também no provedor. Configure um SMTP de sua empresa para envio confiável em produção; o envio padrão do Supabase tem limites próprios.
4. Em **Authentication → URL Configuration**, configure a **Site URL** com seu endereço real. Se usa domínio próprio: `https://s7ven.pro`. Se usa GitHub com subpasta: `https://SEU-USUARIO.github.io/SEU-REPOSITORIO/`.
5. Cadastre nas **Redirect URLs** os caminhos completos:
   - `https://SEU-ENDERECO/painel.html`
   - `https://SEU-ENDERECO/nova-senha.html`
   Substitua SEU-ENDERECO pelo domínio e, quando houver, a pasta do repositório. Use a URL real final; evite curingas amplos para produção.
6. Copie a **Project URL** e a chave **Publishable** (prefixo `sb_publishable_...`). Projetos legados podem usar a chave pública `anon`. Em `config.js`, preencha `supabaseUrl` e `supabasePublishableKey` com esses dois valores.
7. **Nunca use `service_role`, `sb_secret_...`, senha de banco ou chave secreta em config.js.** A chave pública identifica o projeto. As regras RLS do SQL protegem os dados de cada usuário no servidor.
8. Envie o `config.js` atualizado ao GitHub. Abra `cadastro.html`, crie sua conta e confirme o e-mail.
9. Pelo SQL Editor do Supabase, libere SOMENTE seu usuário como administrador:

```sql
insert into public.app_admins(user_id)
select id from auth.users where email = 'SEU_EMAIL_DE_ADMIN';
```

10. Entre pelo `login.html`. A central exibirá o link Gestão da empresa. A gestão também está em `admin.html`; essa página não dá permissão por si só. As regras do banco exigem que o usuário esteja em `app_admins`.
11. Teste com uma segunda conta de cliente para confirmar o fluxo de cadastro, confirmação, pedidos e suporte no seu domínio.

A integração com e-mails/autenticação de um projeto real ainda não foi ativada nesta entrega, porque não foram fornecidos Project URL/chave pública nem acesso ao seu serviço. A aparência e a autorização do banco foram testadas localmente. Os limites e a configuração de e-mail do seu projeto devem ser conferidos na ativação.

## 3. O que está implementado

### Site público
- Landing page, catálogo com busca/categorias, portfólio conceitual por versão.
- Detalhes de produtos, planos, FAQ e briefing.
- Conteúdo publicado pelo admin aparece na seção Novos capítulos.
- Páginas de cadastro, login, recuperação e troca de senha.

### Com Supabase configurado
- Cadastro com nome/e-mail/senha, confirmação por e-mail, login, sessão e logout.
- Recuperação de senha por e-mail e definição de nova senha.
- Perfil próprio com edição de nome.
- Solicitação de produtos: preço e nome são obtidos no banco, não aceitos do navegador.
- Pedidos com status e proposta, licenças com domínio/prazo/status.
- Chamados com mensagens entre cliente e equipe e encerramento pelo admin.
- Briefing de cliente autenticado salvo na gestão; visitante baixa o TXT.
- Admin: visão geral, catálogo/preços/visibilidade, pedidos, registros de licenças, suporte, briefings e CMS (projetos, notícias, depoimentos e demos).
- Admin é autorizado no banco, sem senha padrão ou papel definido por metadados do cliente.

### Limites claros
- Não há cobrança automática por cartão/Pix. Pedidos pagos/entregues são confirmados pela equipe após conferência externa. A função do servidor exige esse passo.
- Licenças são registros de serviço; não há anti-pirataria, chave de ativação ou validação incorporada a um launcher/template. A integração com seus produtos é um projeto separado.
- Não há envio de itens para warehouse ou acesso a MuServer.
- Não inclui softwares comerciais, launchers compilados, anti-hack, APIs de entrega e inclui downloads privados por licença após configurar o Storage. Não coloque links públicos de arquivos vendidos no catálogo.
- A conversa dos chamados é por texto. Entregas e atualizações privadas são feitas na biblioteca de arquivos do produto.
- O bloqueio e a gestão de contas são feitos pela função segura admin-users, após a implantação. Não existe exclusão definitiva de contas pelo painel.
- Sem e-mail configurado no provedor, a recuperação/confirmação não estará operacional. O frontend não inventa envio bem-sucedido.
- Os conceitos Eclipse, Legacy, Ascension e Genesis não são clientes reais. Depoimentos reais são cadastrados pelo CMS com autorização.
- Revise a privacidade com seus contatos e prazos antes de liberar contas ao público.

## 4. Arquivos

- `index.html`: apresentação.
- `solucoes.html`: catálogo e busca.
- `login.html`, `cadastro.html`, `recuperar.html`, `nova-senha.html`: autenticação.
- `painel.html`: central do cliente.
- `admin.html`: gestão com autorização no banco.
- `contato.html`, `privacidade.html`: briefing e transparência.
- `assets/style.css`: design e regras responsivas.
- `assets/app.js`: interações, auth e dados.
- `assets/data.js`: catálogo/conceitos de apresentação sem backend.
- `assets/icons.js`: ícones SVG de interface.
- `vendor/supabase.js`: SDK incluído localmente, sem CDN em runtime.
- `config.js`: URL e chave PÚBLICA do projeto.
- `backend/supabase.sql`: schema, funções, triggers, grants e políticas RLS.

Não é preciso rodar npm. Como os scripts são módulos, abra pelo site GitHub Pages ou por um servidor HTTP local; clicar no HTML via file:// pode bloquear módulos no navegador.

## 5. Validação

- Sintaxe JavaScript e referências de arquivos locais.
- Revisão visual em desktop e viewport mobile de 390 px.
- Testes PostgreSQL em PGlite: migração, perfil automático, RLS/grants, isolamento de usuários, elevação de privilégio negada, preço/status no servidor, pedidos, suporte, fechamento de chamado, registros de licença, briefings e CMS público/rascunho.
- Não houve teste de envio de e-mail ou login contra um projeto Supabase real do usuário, pois ainda falta a configuração.

## Documentação do provedor

- https://supabase.com/docs/guides/auth
- https://supabase.com/docs/reference/javascript/auth-signup
- https://supabase.com/docs/reference/javascript/auth-signinwithpassword
- https://supabase.com/docs/guides/database/secure-data
- https://docs.github.com/en/pages/getting-started-with-github-pages/configuring-a-publishing-source-for-your-github-pages-site
