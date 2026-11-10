# S7VEN — Administração do site em /admin

## Situação desta entrega

O frontend está pronto para a raiz do repositório GitHub, e o painel está em `admin/index.html`.
A rota pública desejada é `https://s7ven.pro/admin` (GitHub Pages também pode normalizar para `/admin/`).

Os arquivos não contêm um banco externo ativo por conta própria. Enquanto `config.js` estiver vazio, login, cadastro e publicação pelo CMS NÃO estarão ativos. A tela informa isso sem criar um login falso no navegador.

## Ativação do backend

1. Criar/selecionar um projeto Supabase pertencente ao dono da S7VEN.
2. Em projeto novo, executar nesta ordem:
   - `backend/supabase.sql`
   - `backend/admin-upgrade.sql`
   - `backend/storage.sql`
3. Se a primeira migração já foi aplicada, não a execute de novo: faça backup e aplique apenas as migrações adicionais ainda não aplicadas. As migrações são identificáveis pelos arquivos; não foram feitas para sobrescrever bancos de outros sistemas.
4. Configurar autenticação por e-mail, confirmação e Site URL. Adicionar as URLs finais de `painel.html` e `nova-senha.html` à lista de redirecionamentos.
5. Preencher `config.js` somente com URL do projeto e chave PÚBLICA Publishable (ou anon). Não colocar service_role, secret key ou senha de banco no GitHub.
6. Criar a conta do dono, confirmar e-mail e conceder a permissão pelo SQL Editor:

```sql
insert into public.app_admins(user_id)
select id from auth.users where email='EMAIL_DO_DONO';
```

7. Implantar a função `supabase/functions/admin-users/index.ts` com nome `admin-users`.
8. Na função, configurar os segredos de servidor:
   - `PUBLIC_SITE_URL=https://s7ven.pro`
   - `S7VEN_ALLOWED_ORIGINS=https://s7ven.pro,https://www.s7ven.pro` (apenas os domínios realmente usados).
   As variáveis SUPABASE_URL e SUPABASE_SERVICE_ROLE_KEY são fornecidas pelo ambiente do Supabase; não as copie para config.js.
9. O código da função valida o JWT com Auth e verifica a permissão em app_admins + conta ativa antes de usar as APIs administrativas. A chave de serviço nunca é enviada ao navegador.
10. Configurar SMTP para confirmação e recuperação confiáveis. Os limites de e-mail e as condições do provedor devem ser verificados na conta do dono.

## O que pode ser administrado

### Site completo
- Identidade, nome e logotipo.
- Fontes, tamanho dos textos/títulos, espaçamento e cores principais.
- Título/descrição do site, destaque, botões, imagem e mockup da home.
- Menu: adicionar, remover, ordenar, ocultar e alterar destinos.
- Seções: mostrar/ocultar e editar títulos.
- Diferenciais técnicos: título, texto, ícone e ordem.
- Planos: nome, descrição, preço/condição, recursos, destaque e ordem.
- Perguntas e respostas.
- Contato comercial, WhatsApp, Discord, textos e rodapé.
- Textos de login/cadastro, pausa de cadastros, aviso público e manutenção.
- Texto de privacidade.
- Histórico de versões e restauração de publicações.

Os visitantes leem a configuração no banco. Alterar um título no painel publica para todos, não apenas para o navegador do dono. Controle de revisão evita que dois administradores sobrescrevam simultaneamente o trabalho um do outro.

### Operação
- Produtos: catálogo, preço, descrição, recursos, visibilidade.
- Pedidos: valor da proposta e status, com conferência manual de pagamento.
- Licenças: cliente, domínio, prazo e status.
- Downloads: arquivos privados por produto, liberados a contas com licença ativa.
- Suporte: conversa por texto, respostas e encerramento.
- Orçamentos: leitura de briefings enviados por clientes autenticados.
- CMS: notícias, projetos, demos e depoimentos publicados ou em rascunho.
- Imagens: upload de PNG/JPG/WebP até 5 MB, gerando URL para usar no CMS.
- Entregas: ZIP privado até 25 MB, associado ao produto.
- Clientes: listar com paginação de 100 por página, criar, editar nome/e-mail, bloquear/desbloquear e solicitar recuperação por e-mail.
- Auditoria: histórico de alterações de catálogo, pedidos, licenças, conteúdo e publicações.

## Limites

“Administrar o site” não inclui alterar o DNS, o GitHub, a Vercel ou a infraestrutura por esse painel. O site não aceita código PHP/JS livre do admin, para não executar scripts arbitrários contra contas de clientes. Não permite excluir o dono ou bloquear contas administrativas pelo gerenciador de clientes.

Cobrança automática, anti-hack, geração de launchers, proteção contra pirataria e entrega de itens no MuServer exigem integrações próprias e não são executadas por esta plataforma comercial. Confirmação de pagamento é manual.

Pausar cadastro é aplicado no banco; esconder um botão não é a única proteção. Bloquear cliente altera a conta Auth e o campo de atividade do perfil; as regras do banco impedem leitura com tokens antigos. Arquivos de entrega exigem licença ativa no Storage e no catálogo de arquivos.

O modo de manutenção é uma apresentação do frontend, não um bloqueio de rede a toda a API. Autorizações continuam sendo aplicadas no servidor.

## Publicação GitHub/Vercel

A versão pública precisa estar na RAIZ do repositório, não em `api/`. O arquivo `vercel.json` inclui a rota /admin. O GitHub Pages utiliza `admin/index.html` sem depender de PHP ou rewrite.

Não é preciso apagar o repositório. A atualização é um commit reversível; o histórico anterior permanece. Mantenha CNAME e configurações de domínio existentes, se houver. Se a Vercel tiver uma Root Directory personalizada nas configurações do projeto, ela também precisa apontar para a raiz desta versão.

## Validação realizada

- JavaScript e referências estáticas, incluindo base de URL em /admin/.
- Migrações PostgreSQL e regras RLS: isolamento entre clientes, negação de privilégios de admin, publicação global, histórico, conflito de revisão e bloqueio de links inseguros.
- Conta bloqueada sem leitura de pedidos e sem novas gravações.
- Pausa de cadastro aplicada no trigger de Auth.
- UI local com tipografia sem espaçamento negativo.

A validação no projeto Supabase real e o envio de e-mail só podem ser concluídos depois de conectar e configurar esse projeto. Não confundir preparação de código com ativação do serviço real.
