# S7VEN — GitHub Pages

Site estático em HTML5, Tailwind compilado e JavaScript. Sem PHP, npm, build ou instalação de dependências.

## Publicar no GitHub
1. Extraia o ZIP no seu computador.
2. No repositório do site, use **Add file → Upload files** e envie o conteúdo extraído.
3. `index.html`, `style.css`, `tailwind.css` e `app.js` precisam ficar na RAIZ do repositório, e não dentro de outra pasta. Envie também `.nojekyll` se aparecer.
4. Confirme o envio com **Commit changes**.
5. Abra **Settings → Pages**. Em **Build and deployment**, escolha **Deploy from a branch**.
6. Selecione a branch que recebeu os arquivos (normalmente `main`) e a pasta **/(root)**. Salve.
7. Aguarde a publicação e abra o endereço informado em Pages.

Se o repositório já tiver os arquivos PHP antigos, remova-os do repositório após fazer backup. Eles não são usados por esta versão. Não envie `storage/config.php`, bancos, senhas, ZIPs ou arquivos de teste.

## Domínio próprio
Se `s7ven.pro` já estiver configurado em Pages, mantenha o campo **Custom domain**, os registros DNS e o arquivo `CNAME` existente. Este pacote não inclui um CNAME para evitar substituir sua configuração. Habilite **Enforce HTTPS** quando disponível. Não altere o DNS para cPanel ao usar esta versão.

## O que funciona
- Landing page responsiva com a identidade S7VEN.
- Catálogo e detalhes das soluções.
- Portfólio conceitual filtrável por versão e prévias visuais.
- Planos e tabela comparativa.
- Briefing com download em TXT; o formulário não envia e-mail.
- Painéis demonstrativos de cliente e administração, identificados na interface.
- Edição local de catálogo, comunicado, registros de licença e chamados para experimentar a interface.

## Limites
Os painéis NÃO têm autenticação e NÃO são áreas privadas. Dados demonstrativos ficam somente no armazenamento deste navegador: não são compartilhados entre usuários ou dispositivos e podem ser apagados pelo navegador. Não insira senhas, dados de clientes ou licenças reais. As edições na demonstração não atualizam os arquivos do repositório nem a página para outros visitantes.

Não há backend, banco, cobrança, ativação real de licença, entrega de produtos ou integração com MuServer. Para operar esses recursos, utilize a versão PHP em uma hospedagem compatível ou integre este frontend a uma API externa autenticada. Nunca coloque chaves de serviço, senhas de banco ou credenciais administrativas no JavaScript do GitHub.

Os projetos mostrados são conceitos demonstrativos, e os planos estão sob orçamento. Barlow é carregada pelo Google Fonts, com fallback Arial. O Tailwind já está compilado localmente.

## Documentação oficial
- https://docs.github.com/en/pages/getting-started-with-github-pages/configuring-a-publishing-source-for-your-github-pages-site
- https://docs.github.com/en/pages/getting-started-with-github-pages/about-github-pages
