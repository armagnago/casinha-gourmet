# 🚀 Guia de Deploy na Locaweb

Este guia passo a passo mostra como fazer o deploy da Casinha Gourmet na Locaweb.

## 📋 Pré-requisitos

- Conta ativa na Locaweb
- Domínio configurado (casinhagourmet.com)
- Acesso SSH ou FTP
- Repositório GitHub criado

## 🔧 Opção 1: Deploy via Git (Recomendado)

### Passo 1: Criar Repositório no GitHub

```bash
# No seu computador local
cd casinha-gourmet
git init
git add .
git commit -m "Initial commit - Casinha Gourmet"
git remote add origin https://github.com/seu-usuario/casinha-gourmet.git
git branch -M main
git push -u origin main
```

### Passo 2: Configurar Git Deploy na Locaweb

1. Acesse o **Painel de Controle Locaweb**
2. Vá para **Hospedagem** → **Gerenciador de Hospedagem**
3. Clique em **Git Deploy**
4. Preencha os campos:
   - **Repositório:** `https://github.com/seu-usuario/casinha-gourmet.git`
   - **Branch:** `main`
   - **Diretório:** `/public_html`

### Passo 3: Configurar Variáveis de Ambiente

Na Locaweb, vá para **Hospedagem** → **Variáveis de Ambiente**:

```
DB_HOST=seu-host-mysql.locaweb.com.br
DB_USER=seu_usuario_db
DB_PASS=sua_senha_db
DB_NAME=casinha_gourmet
DB_PORT=3306

APP_URL=https://casinhagourmet.com
APP_DESIGN=design1
DEBUG_MODE=false

SMTP_HOST=smtp.seuhost.com
SMTP_PORT=587
SMTP_USER=seu-email@dominio.com
SMTP_PASS=sua-senha-smtp
OWNER_EMAIL=proprietario@casinhagourmet.com

STRIPE_PUBLIC_KEY=pk_live_xxxxx
STRIPE_SECRET_KEY=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

AWS_ACCESS_KEY=sua_access_key
AWS_SECRET_KEY=sua_secret_key
AWS_BUCKET=casinha-gourmet
AWS_REGION=us-east-1
```

### Passo 4: Criar Banco de Dados

1. Acesse **Hospedagem** → **Banco de Dados**
2. Clique em **Criar Novo Banco de Dados**
3. Nome: `casinha_gourmet`
4. Usuário: `casinha_user`
5. Senha: (gere uma senha forte)

### Passo 5: Importar Schema

1. Acesse **phpMyAdmin** (fornecido pela Locaweb)
2. Selecione o banco `casinha_gourmet`
3. Vá para **Importar**
4. Selecione o arquivo `database/schema.sql`
5. Clique em **Executar**

### Passo 6: Deploy

```bash
# No painel Locaweb, clique em "Deploy Now"
# Ou faça um push para o GitHub:
git push origin main
```

## 📦 Opção 2: Deploy via FTP

### Passo 1: Preparar Arquivos

```bash
# No seu computador local
# Excluir arquivos desnecessários
rm -rf .git .env
cp .env.example .env

# Compactar
zip -r casinha-gourmet.zip . -x "*.git*" ".env"
```

### Passo 2: Fazer Upload via FTP

1. Abra seu cliente FTP (FileZilla, WinSCP, etc.)
2. Conecte-se à Locaweb com suas credenciais
3. Navegue para `/public_html`
4. Faça upload do arquivo `casinha-gourmet.zip`

### Passo 3: Descompactar no Servidor

1. Via FTP, clique com botão direito em `casinha-gourmet.zip`
2. Selecione **Extrair**
3. Ou via SSH:

```bash
cd /home/seu-usuario/public_html
unzip casinha-gourmet.zip
rm casinha-gourmet.zip
```

### Passo 4: Configurar Permissões

```bash
ssh seu-usuario@seu-servidor.locaweb.com.br

# Definir permissões
chmod -R 755 /home/seu-usuario/public_html
chmod -R 777 /home/seu-usuario/public_html/public/uploads
chmod -R 777 /home/seu-usuario/public_html/logs

# Criar arquivo .env
cp .env.example .env
nano .env  # Editar com suas configurações
```

### Passo 5: Criar Banco de Dados

Mesmo procedimento da Opção 1, Passo 4 e 5.

## 🔐 Configuração de Segurança

### 1. HTTPS (SSL/TLS)

Na Locaweb:
1. Vá para **Hospedagem** → **Certificados SSL**
2. Clique em **Gerar Certificado Grátis** (Let's Encrypt)
3. Selecione seu domínio
4. Clique em **Gerar**

### 2. Arquivo .htaccess

Verifique se o arquivo `public/.htaccess` está presente e contém:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 3. Configurar PHP

Na Locaweb, vá para **Hospedagem** → **Configurações PHP**:
- Versão PHP: 7.4 ou superior
- Extensions necessárias:
  - `mysqli`
  - `curl`
  - `json`
  - `openssl`

## 🧪 Testes Pós-Deploy

### 1. Verificar Conexão com Banco de Dados

Acesse: `https://casinhagourmet.com/public/index.php`

Se a página carregar sem erros, o banco está conectado.

### 2. Verificar Designs

Acesse: `https://casinhagourmet.com/public/design-selector.php`

Você deve ver os 4 designs disponíveis.

### 3. Testar Stripe

1. Vá para o painel administrativo
2. Tente criar um pedido de teste
3. Verifique se o Stripe recebe o evento

### 4. Verificar Email

1. Crie um novo pedido
2. Verifique se o email foi recebido em `OWNER_EMAIL`

## 🔄 Atualizações Futuras

Para fazer atualizações:

### Via Git:
```bash
git add .
git commit -m "Descrição da mudança"
git push origin main
# Na Locaweb, clique em "Deploy Now"
```

### Via FTP:
1. Faça upload dos arquivos alterados
2. Mantenha o arquivo `.env` intacto

## 🐛 Troubleshooting

### Erro: "Conexão com banco de dados recusada"

```bash
# Verificar credenciais
nano .env

# Testar conexão
mysql -h seu-host-mysql -u seu_usuario -p seu_banco
```

### Erro: "Permissão negada"

```bash
# Corrigir permissões
chmod -R 755 /home/seu-usuario/public_html
chmod -R 777 /home/seu-usuario/public_html/public/uploads
```

### Erro: "Reescrita de URLs não funciona"

1. Verifique se mod_rewrite está ativado
2. Verifique o arquivo `.htaccess`
3. Contate o suporte Locaweb

### Erro: "Stripe webhook não recebe eventos"

1. Verifique o URL do webhook: `https://casinhagourmet.com/api/webhook.php`
2. Verifique o `STRIPE_WEBHOOK_SECRET`
3. Teste manualmente no painel Stripe

## 📞 Suporte Locaweb

- **Chat:** https://www.locaweb.com.br/suporte
- **Email:** suporte@locaweb.com.br
- **Telefone:** 0800 592 0000

## 📚 Documentação Útil

- [Documentação Locaweb](https://www.locaweb.com.br/documentacao)
- [Documentação Stripe](https://stripe.com/docs)
- [Documentação PHP](https://www.php.net/manual/pt_BR/)
- [Documentação MySQL](https://dev.mysql.com/doc/)

---

**Última atualização:** 23 de março de 2026

Sucesso no seu deploy! 🚀
