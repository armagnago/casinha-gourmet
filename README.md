# 🍰 Casinha Gourmet - Loja Virtual de Doces e Salgados

Sistema completo de loja virtual em HTML/PHP com 4 opções de design, banco de dados MySQL, painel administrativo e integração com Stripe para pagamentos.

## 📋 Características

✅ **4 Designs Diferentes**
- Design 1: Minimalista Moderno
- Design 2: Elegante Sofisticado
- Design 3: Colorido Vibrante
- Design 4: Clássico Vintage

✅ **Funcionalidades Completas**
- Catálogo de produtos com 7 categorias
- Sistema de carrinho de compras
- Checkout com integração Stripe
- Painel administrativo
- Gerenciamento de produtos
- Visualização de encomendas
- Notificações por email

✅ **Tecnologias**
- PHP 7.4+
- MySQL/MariaDB
- HTML5 & CSS3
- JavaScript
- Stripe API
- AWS S3

## 🚀 Instalação Rápida

### 1. Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Composer (opcional)
- Git

### 2. Clonar o Repositório

```bash
git clone https://github.com/seu-usuario/casinha-gourmet.git
cd casinha-gourmet
```

### 3. Configurar Banco de Dados

```bash
# Criar banco de dados
mysql -u root -p < database/schema.sql

# Ou execute manualmente:
# 1. Abra o MySQL Workbench ou phpMyAdmin
# 2. Crie um novo banco de dados: casinha_gourmet
# 3. Importe o arquivo database/schema.sql
```

### 4. Configurar Variáveis de Ambiente

```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar .env com suas configurações
nano .env
```

**Configurações necessárias:**
```
DB_HOST=localhost
DB_USER=root
DB_PASS=sua_senha
DB_NAME=casinha_gourmet
```

### 5. Configurar Servidor Web

#### Apache (com mod_rewrite ativado)
```bash
# Ativar mod_rewrite
sudo a2enmod rewrite

# Reiniciar Apache
sudo systemctl restart apache2
```

#### Nginx
```nginx
server {
    listen 80;
    server_name casinhagourmet.com;
    
    root /var/www/casinha-gourmet/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### 6. Definir Permissões

```bash
chmod -R 755 casinha-gourmet
chmod -R 777 casinha-gourmet/uploads
chmod -R 777 casinha-gourmet/logs
```

## 🎨 Escolher Design

Acesse: `https://casinhagourmet.com/public/design-selector.php`

Escolha entre os 4 designs disponíveis e clique em "Selecionar".

## 📱 Estrutura de Pastas

```
casinha-gourmet/
├── config/
│   └── database.php          # Configuração do banco de dados
├── database/
│   └── schema.sql            # Schema do banco de dados
├── designs/
│   ├── design1/
│   │   └── index.php         # Design 1: Minimalista
│   ├── design2/
│   │   └── index.php         # Design 2: Elegante
│   ├── design3/
│   │   └── index.php         # Design 3: Colorido
│   └── design4/
│       └── index.php         # Design 4: Vintage
├── public/
│   ├── index.php             # Página principal
│   ├── design-selector.php   # Seletor de designs
│   ├── .htaccess             # Reescrita de URLs
│   └── uploads/              # Pasta de uploads
├── admin/
│   ├── index.php             # Painel administrativo
│   ├── products.php          # Gerenciar produtos
│   ├── orders.php            # Visualizar encomendas
│   └── settings.php          # Configurações
├── api/
│   ├── cart.php              # API do carrinho
│   ├── checkout.php          # API de checkout
│   ├── webhook.php           # Webhook do Stripe
│   └── orders.php            # API de encomendas
├── includes/
│   ├── header.php            # Header comum
│   ├── footer.php            # Footer comum
│   └── functions.php         # Funções auxiliares
├── .env.example              # Exemplo de variáveis
├── .gitignore                # Arquivos ignorados pelo Git
└── README.md                 # Este arquivo
```

## 🔧 Configuração Avançada

### Integração Stripe

1. Crie uma conta em [stripe.com](https://stripe.com)
2. Obtenha suas chaves (Public e Secret)
3. Configure no arquivo `.env`:

```
STRIPE_PUBLIC_KEY=pk_live_xxxxx
STRIPE_SECRET_KEY=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

### Integração S3 (AWS)

1. Crie um bucket S3 na AWS
2. Gere access keys
3. Configure no arquivo `.env`:

```
AWS_ACCESS_KEY=sua_access_key
AWS_SECRET_KEY=sua_secret_key
AWS_BUCKET=casinha-gourmet
AWS_REGION=us-east-1
```

### Email (SMTP)

Configure seu servidor SMTP no arquivo `.env`:

```
SMTP_HOST=smtp.seuhost.com
SMTP_PORT=587
SMTP_USER=seu-email@dominio.com
SMTP_PASS=sua-senha-smtp
OWNER_EMAIL=proprietario@casinhagourmet.com
```

## 🌐 Deploy na Locaweb

### Via Git (Recomendado)

1. **Criar repositório no GitHub:**
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/seu-usuario/casinha-gourmet.git
git push -u origin master
```

2. **Configurar na Locaweb:**
   - Acesse o painel de controle Locaweb
   - Vá para Hospedagem > Git Deploy
   - Cole a URL do seu repositório GitHub
   - Configure as variáveis de ambiente

3. **Deploy automático:**
```bash
# Na Locaweb, configure webhook do GitHub para deploy automático
```

### Via FTP

1. Compacte os arquivos:
```bash
zip -r casinha-gourmet.zip . -x "*.git*" ".env"
```

2. Faça upload via FTP para `/public_html/`

3. Configure o arquivo `.env` na Locaweb

## 🛡️ Segurança

- ✅ Validação de entrada em todos os formulários
- ✅ Proteção contra SQL Injection (prepared statements)
- ✅ Proteção contra XSS (htmlspecialchars)
- ✅ HTTPS obrigatório
- ✅ Senhas criptografadas (bcrypt)
- ✅ CSRF tokens em formulários
- ✅ Rate limiting em APIs

## 📊 Banco de Dados

### Tabelas Principais

- **users** - Usuários do sistema
- **categories** - Categorias de produtos
- **products** - Produtos da loja
- **orders** - Encomendas
- **order_items** - Itens das encomendas
- **store_settings** - Configurações da loja
- **notifications** - Notificações

## 🐛 Troubleshooting

### Erro: "Conexão com banco de dados recusada"
- Verifique as credenciais no `.env`
- Certifique-se de que MySQL está rodando
- Verifique se o banco de dados foi criado

### Erro: "Permissão negada" em uploads
```bash
chmod -R 777 public/uploads
```

### Erro: "Reescrita de URLs não funciona"
- Ative mod_rewrite no Apache: `sudo a2enmod rewrite`
- Reinicie o Apache: `sudo systemctl restart apache2`

### Erro: "Stripe webhook não recebe eventos"
- Verifique o STRIPE_WEBHOOK_SECRET
- Certifique-se de que o URL é acessível publicamente
- Verifique os logs do Stripe

## 📞 Suporte

Para dúvidas ou problemas:
- Email: suporte@casinhagourmet.com
- WhatsApp: (11) 99999-9999
- GitHub Issues: https://github.com/seu-usuario/casinha-gourmet/issues

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 🙏 Agradecimentos

Desenvolvido com ❤️ para confeitarias artesanais.

---

**Última atualização:** 23 de março de 2026

Aproveite sua loja virtual! 🎉
