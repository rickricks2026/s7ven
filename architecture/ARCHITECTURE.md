# Arquitetura de produção proposta

## Separação de responsabilidades
1. Frontend público: catálogo, planos, projetos publicados, notícias e formulário de orçamento.
2. Portal autenticado: serviços adquiridos, downloads autorizados, faturas, domínios, renovações e tickets próprios.
3. Admin autenticado: catálogo, clientes, licenças, vendas, helpdesk, CMS e auditoria.
4. API PHP 8.2+ com PDO SQLSRV, camadas Controller → Service → Repository. Dependências por Composer e configuração fora do diretório público.
5. SQL Server exclusivo da empresa. Integrações com bancos MuServer em adaptadores isolados, sem expor credenciais ao cliente ou à landing page.

A versão hospedada é estática. PHP/SQL Server exige uma hospedagem compatível (IIS/FastCGI ou servidor PHP apropriado); não é executado no host estático atual. Uma futura API externa deve configurar CORS por origem exata e sessões conforme seu domínio.

## Módulos, rotas e autorização
| Módulo | Rotas sugeridas | Permissão |
| --- | --- | --- |
| Sessão | POST /api/auth/login, /logout; GET /api/me | Sessão e rate limit |
| Produtos | GET /api/products; POST/PATCH /api/admin/products/{id} | Leitura pública só ativos; escrita admin |
| Pedidos | POST /api/orders; GET /api/orders/{id} | Cliente proprietário ou admin |
| Clientes | GET/PATCH /api/admin/customers/{id} | Admin autorizado |
| Licenças | GET /api/me/licenses; PATCH /api/admin/licenses/{id} | Cliente vê próprias; admin altera |
| Validação | POST /api/license/validate | Chave com escopo, nonce, rate limit e log |
| Renovações | POST /api/me/licenses/{id}/renewal | Proprietário; ativação após pagamento verificado |
| Helpdesk | POST /api/tickets; GET /api/tickets/{id}; POST /messages | Proprietário ou equipe autorizada |
| Uploads | POST /api/tickets/{id}/attachments | Participante, limite e validação de conteúdo |
| CMS | POST/PATCH /api/admin/content/{id} | Editor/admin, rascunho/publicado |
| Demos | GET /api/demos; PATCH /api/admin/demos/{id} | Leitura pública ativa; escrita editor |
| Auditoria | GET /api/admin/audit | Dono, sem edição pelo portal |

Nunca confiar em user_id, preço, role ou status de pagamento enviados pelo navegador. Toda autorização deve ser refeita na API, inclusive downloads e anexos. Sessões por cookies Secure, HttpOnly e SameSite; token CSRF nas mutações. Senhas com Argon2id, MFA para administradores, recuperação com token de uso único e expiração. Queries parametrizadas e whitelists para ordenação; não concatenar identificadores recebidos.

## Licenciamento
Chave aleatória com entropia forte; armazenar hash, mostrar segredo completo somente na emissão. Normalizar domínio (IDNA, minúsculas, sem porta/caminho) e comparar domínio exato. O cabeçalho Host sozinho não comprova propriedade: verificar desafio DNS/HTTP antes da liberação. Validar estado, produto, domínio e expiração; resposta assinada com validade curta e tolerância definida em contrato. Registrar ativações, suspensões e renovações em eventos imutáveis. Não incluir segredos do servidor na resposta. Cliente baixável nunca garante proteção absoluta contra adulteração.

## Vendas e renovações
Pedido com snapshot de preço e versão do produto. Integração futura com provedor de pagamento por webhook: verificar assinatura e prazo; chave idempotente para evitar dupla ativação. Valores em DECIMAL, moeda explícita. Falhas de pagamento não podem apagar histórico. Agendamento de lembretes e vencimentos por fila/cron com logs de execução. Nenhum pagamento real implementado nesta entrega.

## CMS completo previsto
Entidades de notícias, projetos, depoimentos e demos com título, slug, corpo, capa, status, ordem, autor, published_at e revisão. Projetos têm versão, categoria e screenshots; demos têm URL validada e allowlist de protocolo HTTPS. Depoimentos só serão publicados com conteúdo real e autorização. Upload de imagens com limites, re-encode, remoção de metadados e sem execução; arquivos de atualizações com checksum, controle de acesso e URLs temporárias. Editor com preview, validação, rascunho/publicação e histórico reversível.

## Helpdesk
Ticket tem prioridade, serviço, status e responsável. Mensagens separam resposta pública e nota interna. Atualizações e correções são anexos versionados com hash, autorização e download com expiração. Nunca usar o nome enviado como caminho de arquivo. Auditar envio e download, sem registrar credenciais.

## Integração MuOnline
Cada versão/GameServer precisa de adaptador validado em ambiente de homologação. Season não basta para determinar formato. Hex de 10/16 bytes, tamanho do warehouse, serial, checksum e moedas variam entre distribuições. Compra usa transação, bloqueio de linha, verificação de saldo, idempotência e política para conta online; entrega só com confirmação de compatibilidade e plano de rollback. Não escrever itens nem alterar saldo com base em uma prévia do frontend.

## Implantação e validação
Segredos por variáveis de ambiente, privilégios mínimos no banco, TLS, backups com restauração testada, logs estruturados e auditoria sem dados sensíveis. CSP com nonce após remover handlers inline no frontend de produção. Testes prioritários: isolamento entre contas; CSRF; XSS persistente; SQL injection; webhooks repetidos; duas compras simultâneas; licença expirada e domínio incorreto; download não autorizado; falha na entrega. Monitorar erros e latência. Contratar proteção anti-hack e testar compatibilidade como integração separada.
