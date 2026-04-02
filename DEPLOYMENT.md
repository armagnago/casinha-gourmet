# Guia de Deploy - Loja Virtual de Doces e Salgados

## 📋 Índice
1. [Pré-requisitos](#pré-requisitos)
2. [Configuração do GitHub](#configuração-do-github)
3. [Configuração da Locaweb](#configuração-da-locaweb)
4. [Variáveis de Ambiente](#variáveis-de-ambiente)
5. [Deploy do Banco de Dados](#deploy-do-banco-de-dados)
6. [Deploy da Aplicação](#deploy-da-aplicação)
7. [Configuração do Domínio](#configuração-do-domínio)
8. [Troubleshooting](#troubleshooting)

---

## Pré-requisitos

Antes de começar, você precisará de:

- **Conta GitHub** - Para versionamento de código
- **Conta Locaweb** - Para hospedagem
- **Node.js 22+** - Instalado localmente
- **Git** - Para controle de versão
- **Stripe Account** - Para processamento de pagamentos (opcional)
- **Domínio próprio** - Para acessar a loja (opcional)

---

## Configuração do GitHub

### 1. Criar um Repositório

```bash
# Inicializar git no projeto local
cd /home/ubuntu/loja_doces_salgados
git init
git add .
git commit -m "Initial commit: Loja Virtual de Doces e Salgados"
```

### 2. Criar Repositório no GitHub

1. Acesse [github.com/new](https://github.com/new)
2. Nomeie o repositório: `loja-doces-salgados`
3. Escolha "Private" ou "Public" conforme sua preferência
4. Clique em "Create repository"

### 3. Adicionar Remoto e Push

```bash
git remote add origin https://github.com/SEU_USUARIO/loja-doces-salgados.git
git branch -M main
git push -u origin main
```

---

## Configuração da Locaweb

### 1. Criar Aplicação Node.js

1. Acesse o painel da Locaweb
2. Vá para "Aplicações" → "Criar Aplicação"
3. Escolha "Node.js" como runtime
4. Selecione a versão 22 ou superior
5. Nomeie a aplicação: `loja-doces-salgados`

### 2. Conectar Repositório GitHub

1. Na seção "Deployment", escolha "GitHub"
2. Autorize o acesso ao GitHub
3. Selecione o repositório `loja-doces-salgados`
4. Escolha a branch `main`
5. Clique em "Conectar"

### 3. Configurar Build

Na seção "Build & Deploy":

```
Build Command: pnpm install && pnpm build
Start Command: pnpm start
Node Version: 22.13.0
```

---

## Variáveis de Ambiente

### 1. Configurar Secrets na Locaweb

Acesse "Configurações" → "Variáveis de Ambiente" e adicione:

#### Banco de Dados
```
DATABASE_URL=mysql://usuario:senha@host:porta/database
```

#### Autenticação OAuth
```
VITE_APP_ID=seu_app_id
OAUTH_SERVER_URL=https://api.manus.im
VITE_OAUTH_PORTAL_URL=https://oauth.manus.im
JWT_SECRET=sua_chave_secreta_jwt
```

#### Stripe (Pagamentos)
```
STRIPE_SECRET_KEY=sk_live_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_xxxxxxxxxxxxx
```

#### Email (Notificações)
```
SMTP_HOST=smtp.seuhost.com
SMTP_PORT=587
SMTP_USER=seu_email@dominio.com
SMTP_PASSWORD=sua_senha_smtp
OWNER_EMAIL=proprietario@docesesalgados.com.br
```

#### Manus APIs
```
BUILT_IN_FORGE_API_URL=https://api.manus.im
BUILT_IN_FORGE_API_KEY=sua_chave_api
VITE_FRONTEND_FORGE_API_URL=https://api.manus.im
VITE_FRONTEND_FORGE_API_KEY=sua_chave_api_frontend
```

#### Informações do Proprietário
```
OWNER_NAME=Seu Nome
OWNER_OPEN_ID=seu_open_id
```

#### Analytics (Opcional)
```
VITE_ANALYTICS_ENDPOINT=https://analytics.manus.im
VITE_ANALYTICS_WEBSITE_ID=seu_website_id
```

#### Configuração da Loja
```
VITE_APP_TITLE=Doces & Salgados
VITE_APP_LOGO=https://seu-cdn.com/logo.png
```

---

## Deploy do Banco de Dados

### 1. Criar Banco de Dados na Locaweb

1. Vá para "Banco de Dados" → "Criar Banco"
2. Escolha "MySQL 8.0" ou superior
3. Nomeie: `loja_doces_salgados`
4. Anote as credenciais (usuário, senha, host, porta)

### 2. Executar Migrações

```bash
# Localmente, com acesso ao banco remoto
DATABASE_URL="mysql://usuario:senha@host:porta/loja_doces_salgados" \
pnpm drizzle-kit migrate
```

Ou via SSH na Locaweb:

```bash
ssh seu_usuario@seu_host
cd /var/www/loja-doces-salgados
DATABASE_URL="mysql://usuario:senha@localhost/loja_doces_salgados" \
pnpm drizzle-kit migrate
```

---

## Deploy da Aplicação

### 1. Deploy Automático

Após conectar o GitHub, qualquer push para a branch `main` acionará o deploy automático:

```bash
git add .
git commit -m "Atualização: nova funcionalidade"
git push origin main
```

### 2. Monitorar Deploy

1. Acesse o painel da Locaweb
2. Vá para "Deployments"
3. Acompanhe o progresso do build e deploy
4. Verifique os logs em caso de erro

### 3. Verificar Saúde da Aplicação

```bash
curl https://seu-dominio.com/api/health
```

---

## Configuração do Domínio

### 1. Apontar Domínio para Locaweb

Se você tem um domínio próprio:

1. Acesse o painel do seu registrador de domínios
2. Configure os nameservers para:
   ```
   ns1.locaweb.com.br
   ns2.locaweb.com.br
   ```

3. Ou configure os registros DNS (A records):
   ```
   A: seu-ip-locaweb
   ```

### 2. Configurar SSL/TLS

Na Locaweb, SSL é geralmente automático. Verifique em "Configurações" → "SSL".

### 3. Testar Acesso

```bash
curl https://seu-dominio.com
```

---

## Troubleshooting

### Erro: "DATABASE_URL não definida"

**Solução:** Verifique se a variável de ambiente está configurada corretamente na Locaweb.

```bash
# Verificar variáveis
ssh seu_usuario@seu_host
echo $DATABASE_URL
```

### Erro: "Port already in use"

**Solução:** A aplicação tenta usar uma porta já ocupada. Configure a porta via variável:

```
PORT=3000
```

### Erro: "Cannot find module 'stripe'"

**Solução:** Execute `pnpm install` no servidor:

```bash
ssh seu_usuario@seu_host
cd /var/www/loja-doces-salgados
pnpm install
```

### Erro: "Webhook signature verification failed"

**Solução:** Verifique se `STRIPE_WEBHOOK_SECRET` está correto e atualizado.

### Aplicação lenta

**Solução:** Verifique os logs e considere:
- Aumentar recursos (RAM, CPU)
- Otimizar queries do banco de dados
- Implementar caching

```bash
# Ver logs
ssh seu_usuario@seu_host
tail -f /var/log/loja-doces-salgados/app.log
```

---

## Monitoramento e Manutenção

### Logs

```bash
# SSH na Locaweb
ssh seu_usuario@seu_host

# Ver logs em tempo real
tail -f /var/log/loja-doces-salgados/app.log

# Ver últimas 100 linhas
tail -100 /var/log/loja-doces-salgados/app.log

# Buscar erros
grep ERROR /var/log/loja-doces-salgados/app.log
```

### Backups do Banco de Dados

```bash
# Fazer backup
mysqldump -u usuario -p -h host loja_doces_salgados > backup.sql

# Restaurar backup
mysql -u usuario -p -h host loja_doces_salgados < backup.sql
```

### Atualizar Aplicação

```bash
# Fazer alterações localmente
git add .
git commit -m "Descrição da mudança"
git push origin main

# Locaweb fará deploy automaticamente
```

---

## Checklist de Deploy

- [ ] Repositório GitHub criado e conectado
- [ ] Banco de dados MySQL criado na Locaweb
- [ ] Todas as variáveis de ambiente configuradas
- [ ] Migrações do banco executadas
- [ ] SSL/TLS configurado
- [ ] Domínio apontando para Locaweb
- [ ] Teste de acesso funcionando
- [ ] Stripe configurado (se usando pagamentos)
- [ ] Email de notificações testado
- [ ] Backups automatizados configurados

---

## Suporte

Para dúvidas ou problemas:

1. Consulte a [documentação da Locaweb](https://www.locaweb.com.br/documentacao/)
2. Verifique os logs da aplicação
3. Contate o suporte da Locaweb

---

**Última atualização:** 22 de março de 2026
