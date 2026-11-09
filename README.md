# S7VEN — ecossistema MuOnline

Frontend em HTML5, Tailwind CSS 3.4 e JavaScript sem framework. Publicação estática privada para revisão.

## Executar
Sirva a pasta `dist` por HTTP. Não exige Node no servidor. Para recompilar as utilidades Tailwind: `npm ci` e `npm run build:css`.

## Experiência implementada
- Landing page responsiva, mockup vetorial construído em CSS, serviços, planos e contato.
- Portfólio conceitual com filtros 97d, Season 2, Season 6 e Season 19+.
- Prévia dos conceitos em modal, detalhes dos produtos e briefing com download TXT.
- Área do cliente demonstrativa: visão geral, licenças e abertura/consulta de chamados.
- Administração demonstrativa: dashboard, inclusão/edição/ocultação/precificação de produtos, registro/suspensão de licenças, respostas e encerramento de chamados e comunicado da central.
- Dados demonstrativos persistidos em localStorage neste navegador. Não há login, faturamento, ativação real, envio de mensagens ou integração com MuServer.
- Projetos, domínio e métricas são exemplos. Nenhum depoimento ou cliente real foi inventado.

## Arquivos
- `dist/index.html`: estrutura semântica.
- `dist/style.css`: identidade visual e responsividade.
- `dist/tailwind.css`: Tailwind compilado localmente, sem CDN de runtime.
- `dist/app.js`: estado e interações da demonstração.
- `architecture/ARCHITECTURE.md`: contrato para backend, CMS e integração.
- `architecture/schema.sql`: modelo inicial de dados para SQL Server.

## Limites de produção
O frontend demonstra a experiência. A arquitetura PHP é uma especificação, não um backend instalado. Publicar para clientes reais exige autenticação, autorização por recurso, persistência no servidor, cobrança e validação de integrações conforme o documento técnico. Nunca proteger admin ou licença apenas no JavaScript.

Fontes Barlow e Barlow Condensed são carregadas pelo Google Fonts, com fallback local Arial. Sem imagens externas necessárias. Não existe integração anti-hack ou garantia de compatibilidade universal implementada.
