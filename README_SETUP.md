# 🍰 Loja Virtual de Doces e Salgados - Guia de Configuração

Bem-vindo ao seu sistema de loja virtual! Este guia ajudará você a configurar e usar sua plataforma de vendas de doces e salgados.

## 📋 Índice

1. [Primeiros Passos](#primeiros-passos)
2. [Configuração Inicial](#configuração-inicial)
3. [Gerenciando Produtos](#gerenciando-produtos)
4. [Processando Encomendas](#processando-encomendas)
5. [Integração com Stripe](#integração-com-stripe)
6. [Notificações por Email](#notificações-por-email)
7. [Integração com Redes Sociais](#integração-com-redes-sociais)
8. [Upload de Imagens](#upload-de-imagens)

---

## Primeiros Passos

### Acesso ao Painel Administrativo

1. Acesse sua loja em: `https://seu-dominio.com/admin`
2. Você será redirecionado para fazer login
3. Após autenticar, você terá acesso ao painel completo

### Estrutura da Loja

```
🏠 Home
├── 📦 Catálogo
│   ├── 🍫 Bombons
│   ├── 🥚 Ovos de Páscoa
│   ├── 🎂 Tortas Doces
│   ├── 🧁 Bolos Especiais
│   ├── 🥟 Empadas
│   ├── 🥧 Quiches
│   └── 🥐 Pastéis
├── 🛒 Carrinho
├── 👤 Minha Conta
└── ⚙️ Admin (Apenas para Administradores)
```

---

## Configuração Inicial

### 1. Configurar Informações da Loja

No painel admin, vá para **Configurações** → **Loja**:

- **Nome da Loja:** Doces & Salgados
- **Email de Contato:** seu-email@dominio.com
- **Telefone/WhatsApp:** (11) 99999-9999
- **Endereço:** Rua Exemplo, 123, São Paulo, SP
- **Horário de Funcionamento:** Segunda a Sexta, 9h às 18h

### 2. Configurar Redes Sociais

Adicione os links das suas redes sociais:

- **Instagram:** https://instagram.com/seu_usuario
- **Facebook:** https://facebook.com/seu_usuario
- **WhatsApp:** https://wa.me/5511999999999

### 3. Personalizar Aparência

- **Logo:** Faça upload do seu logo
- **Cores:** Customize as cores da loja
- **Descrição:** Adicione uma descrição sobre sua confeitaria

---

## Gerenciando Produtos

### Adicionar Novo Produto

1. Vá para **Admin** → **Produtos**
2. Clique em **+ Novo Produto**
3. Preencha os campos:

| Campo | Descrição | Exemplo |
|-------|-----------|---------|
| **Categoria** | Selecione a categoria | Bombons |
| **Nome** | Nome do produto | Bombom de Chocolate Belga |
| **Slug** | URL-friendly name | bombom-chocolate-belga |
| **Descrição** | Descrição detalhada | Bombom feito com chocolate belga premium... |
| **Preço** | Preço em reais | 45.00 |
| **Quantidade Mínima** | Mínimo de unidades | 6 |
| **Imagem** | Foto do produto | [Upload] |

4. Clique em **Salvar**

### Editar Produto

1. Vá para **Admin** → **Produtos**
2. Clique no ícone ✏️ ao lado do produto
3. Modifique os campos desejados
4. Clique em **Salvar**

### Deletar Produto

1. Vá para **Admin** → **Produtos**
2. Clique no ícone 🗑️ ao lado do produto
3. Confirme a exclusão

---

## Processando Encomendas

### Visualizar Encomendas

1. Vá para **Admin** → **Encomendas**
2. Você verá todas as encomendas recebidas com:
   - Número do pedido
   - Nome do cliente
   - Email e WhatsApp
   - Total do pedido
   - Status atual

### Atualizar Status da Encomenda

Os status disponíveis são:

| Status | Descrição |
|--------|-----------|
| **pending** | Pedido recebido, aguardando confirmação |
| **confirmed** | Pedido confirmado com o cliente |
| **preparing** | Confeitaria está preparando o pedido |
| **ready** | Pedido pronto para retirada/entrega |
| **delivered** | Pedido entregue ao cliente |

Para atualizar:

1. Vá para **Admin** → **Encomendas**
2. Clique no status desejado para o pedido
3. O cliente receberá uma notificação automática

### Contatar Cliente

1. Clique no número do WhatsApp para abrir o chat
2. Ou envie um email clicando no endereço de email

---

## Integração com Stripe

### Configurar Pagamentos

1. Vá para **Configurações** → **Pagamentos**
2. Adicione suas chaves Stripe:
   - **Chave Pública:** pk_live_xxxxx
   - **Chave Secreta:** sk_live_xxxxx
   - **Webhook Secret:** whsec_xxxxx

### Testar Pagamentos

Use o cartão de teste:

```
Número: 4242 4242 4242 4242
Validade: 12/25
CVC: 123
```

### Verificar Pagamentos Recebidos

1. Acesse seu dashboard Stripe: https://dashboard.stripe.com
2. Vá para **Pagamentos** para ver todas as transações
3. Verifique o status de cada pagamento

---

## Notificações por Email

### Configurar Email

1. Vá para **Configurações** → **Email**
2. Configure seu servidor SMTP:
   - **Host:** smtp.seuhost.com
   - **Porta:** 587
   - **Usuário:** seu-email@dominio.com
   - **Senha:** sua-senha
   - **Email do Proprietário:** seu-email@dominio.com

### Notificações Automáticas

Você receberá emails quando:

- ✅ Uma nova encomenda for recebida
- ✅ Um pagamento for confirmado
- ✅ Um cliente cancelar um pedido
- ✅ Uma encomenda for entregue

---

## Integração com Redes Sociais

### Adicionar Links Sociais

1. Vá para **Configurações** → **Redes Sociais**
2. Adicione os URLs:
   - **Instagram**
   - **Facebook**
   - **TikTok** (opcional)
   - **Pinterest** (opcional)

### Compartilhar Produtos

Cada produto pode ser compartilhado diretamente:

- Clique no ícone de compartilhamento
- Escolha a rede social
- O link será compartilhado automaticamente

---

## Upload de Imagens

### Adicionar Imagem de Produto

1. No formulário de produto, clique em **Escolher Imagem**
2. Selecione uma imagem do seu computador
3. A imagem será armazenada em S3 automaticamente
4. Clique em **Salvar**

### Requisitos de Imagem

- **Formato:** JPG, PNG ou WebP
- **Tamanho Máximo:** 5MB
- **Dimensões Recomendadas:** 800x600px ou maior
- **Proporção:** Quadrada ou 4:3

### Otimizar Imagens

Para melhor performance, comprima suas imagens antes de fazer upload:

1. Use ferramentas como TinyPNG ou ImageOptim
2. Mantenha imagens menores que 2MB
3. Use formato WebP para melhor compressão

---

## Troubleshooting

### Problema: Não consigo fazer login

**Solução:**
1. Verifique se você está usando a URL correta
2. Certifique-se de que seu email está cadastrado
3. Clique em "Esqueci a Senha" para resetar
4. Limpe o cache do navegador

### Problema: Imagens não aparecem

**Solução:**
1. Verifique a conexão com a internet
2. Tente fazer upload novamente
3. Verifique o tamanho da imagem (máx. 5MB)
4. Tente outro formato (JPG em vez de PNG)

### Problema: Encomendas não aparecem

**Solução:**
1. Verifique se o banco de dados está conectado
2. Recarregue a página (F5)
3. Verifique se há encomendas no período selecionado
4. Contate o suporte técnico

### Problema: Emails não estão sendo enviados

**Solução:**
1. Verifique as credenciais SMTP
2. Verifique se o email do proprietário está correto
3. Verifique a pasta de spam
4. Teste a conexão SMTP

---

## Dicas Úteis

### 📸 Fotografia de Produtos

- Use boa iluminação natural
- Fotografe em fundo neutro
- Mostre o produto de diferentes ângulos
- Inclua detalhes (embalagem, tamanho, etc.)

### 💬 Descrição de Produtos

- Seja descritivo e atrativo
- Mencione ingredientes especiais
- Destaque diferenciais (sem glúten, vegano, etc.)
- Inclua informações de armazenamento

### 📱 WhatsApp

- Responda rápido às mensagens
- Confirme pedidos dentro de 24h
- Envie fotos do produto sendo preparado
- Atualize o cliente sobre a entrega

### 📊 Análise de Vendas

Verifique regularmente:
- Produtos mais vendidos
- Horários de pico
- Categorias populares
- Feedback dos clientes

---

## Contato e Suporte

Para dúvidas ou problemas:

- **Email:** suporte@docesesalgados.com.br
- **WhatsApp:** (11) 99999-9999
- **Instagram:** @docesesalgados

---

**Última atualização:** 22 de março de 2026

Aproveite sua loja virtual! 🎉
