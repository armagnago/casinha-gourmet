# 🚀 Guia Rápido de Início

## 5 Passos para Começar

### 1️⃣ Clonar o Repositório

```bash
git clone https://github.com/seu-usuario/casinha-gourmet.git
cd casinha-gourmet
```

### 2️⃣ Configurar Banco de Dados

```bash
# Criar banco de dados
mysql -u root -p < database/schema.sql

# Ou via phpMyAdmin:
# 1. Crie banco: casinha_gourmet
# 2. Importe: database/schema.sql
```

### 3️⃣ Configurar Variáveis de Ambiente

```bash
cp .env.example .env
nano .env  # Edite com suas configurações
```

**Configurações mínimas necessárias:**
```
DB_HOST=localhost
DB_USER=root
DB_PASS=sua_senha
DB_NAME=casinha_gourmet
```

### 4️⃣ Escolher Design

Acesse no navegador:
```
http://localhost/casinha-gourmet/public/design-selector.php
```

Escolha um dos 4 designs e clique em "Selecionar".

### 5️⃣ Acessar a Loja

```
http://localhost/casinha-gourmet/public/
```

---

## 📱 Estrutura de Designs

| Design | URL | Estilo |
|--------|-----|--------|
| Design 1 | `/designs/design1/` | Minimalista Moderno |
| Design 2 | `/designs/design2/` | Elegante Sofisticado |
| Design 3 | `/designs/design3/` | Colorido Vibrante |
| Design 4 | `/designs/design4/` | Clássico Vintage |

---

## 🔧 Configuração Avançada

### Stripe
```
STRIPE_PUBLIC_KEY=pk_live_xxxxx
STRIPE_SECRET_KEY=sk_live_xxxxx
```

### Email (SMTP)
```
SMTP_HOST=smtp.seuhost.com
SMTP_USER=seu-email@dominio.com
SMTP_PASS=sua-senha
```

### AWS S3
```
AWS_ACCESS_KEY=sua_key
AWS_SECRET_KEY=sua_secret
AWS_BUCKET=casinha-gourmet
```

---

## 📚 Documentação

- **README.md** - Documentação completa
- **DEPLOY_LOCAWEB.md** - Como fazer deploy
- **DESIGNS_COMPARISON.md** - Comparação dos designs

---

## ✅ Checklist de Configuração

- [ ] Banco de dados criado
- [ ] Arquivo `.env` configurado
- [ ] Design escolhido
- [ ] Página inicial acessível
- [ ] Stripe configurado (opcional)
- [ ] Email configurado (opcional)
- [ ] S3 configurado (opcional)

---

## 🆘 Problemas Comuns

**Erro: "Conexão com banco de dados recusada"**
```bash
# Verificar credenciais em .env
# Certificar que MySQL está rodando
mysql -u root -p -e "SELECT 1;"
```

**Erro: "Arquivo não encontrado"**
```bash
# Verificar se mod_rewrite está ativado
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Erro: "Permissão negada"**
```bash
chmod -R 755 /caminho/para/casinha-gourmet
chmod -R 777 /caminho/para/casinha-gourmet/public/uploads
```

---

## 🎯 Próximos Passos

1. Adicionar produtos ao catálogo
2. Configurar pagamentos com Stripe
3. Configurar notificações por email
4. Personalizar cores e tipografia
5. Fazer deploy na Locaweb

---

**Pronto para começar! 🎉**

Dúvidas? Consulte o README.md ou DEPLOY_LOCAWEB.md
