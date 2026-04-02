<?php
/**
 * CART PAGE - Shopping Cart with Checkout Form
 * Casinha Gourmet - Loja Virtual
 * 
 * Features:
 * - Display items from localStorage cart
 * - Quantity controls (+/-)
 * - Remove items from cart
 * - Calculate totals
 * - Checkout form with validation
 * - Submit order to database
 */

require_once 'database.php';

// Handle form submission
$order_success = false;
$order_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $conn = getConnection();
    
    // Get form data
    $customer_name = sanitize($_POST['customer_name'] ?? '');
    $customer_email = sanitize($_POST['customer_email'] ?? '');
    $customer_phone = sanitize($_POST['customer_phone'] ?? '');
    $customer_address = sanitize($_POST['customer_address'] ?? '');
    $delivery_date = sanitize($_POST['delivery_date'] ?? '');
    $special_instructions = sanitize($_POST['special_instructions'] ?? '');
    $cart_data = $_POST['cart_data'] ?? '[]';
    
    // Decode cart data
    $cart_items = json_decode($cart_data, true);
    
    // Validate required fields
    $errors = [];
    if (empty($customer_name)) {
        $errors[] = 'Nome completo é obrigatório';
    }
    if (empty($customer_email)) {
        $errors[] = 'Email é obrigatório';
    } elseif (!validateEmail($customer_email)) {
        $errors[] = 'Email inválido';
    }
    if (empty($customer_phone)) {
        $errors[] = 'WhatsApp é obrigatório';
    }
    if (empty($delivery_date)) {
        $errors[] = 'Data de entrega é obrigatória';
    }
    if (empty($cart_items) || count($cart_items) === 0) {
        $errors[] = 'Carrinho vazio';
    }
    
    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            // Generate unique order number
            $order_number = 'CG' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            
            // Insert order
            $stmt = $conn->prepare("
                INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_address, total, status, payment_status, delivery_date, special_instructions)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?)
            ");
            $stmt->bind_param('sssssdss', 
                $order_number, 
                $customer_name, 
                $customer_email, 
                $customer_phone, 
                $customer_address, 
                $total, 
                $delivery_date, 
                $special_instructions
            );
            
            if ($stmt->execute()) {
                $order_id = $conn->insert_id;
                
                // Insert order items
                $item_stmt = $conn->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                foreach ($cart_items as $item) {
                    $product_id = (int)$item['id'];
                    $product_name = $item['name'];
                    $product_price = (float)$item['price'];
                    $quantity = (int)$item['quantity'];
                    $subtotal = $product_price * $quantity;
                    
                    $item_stmt->bind_param('isssdi', 
                        $order_id, 
                        $product_id, 
                        $product_name, 
                        $product_price, 
                        $quantity, 
                        $subtotal
                    );
                    $item_stmt->execute();
                }
                
                $order_success = true;
                $order_number = $order_number;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = 'Erro ao processar pedido: ' . $e->getMessage();
        }
    }
    
    closeConnection($conn);
}

// Get store settings
$conn = getConnection();
$whatsapp = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'whatsapp_number'");
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Casinha Gourmet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        /* HEADER */
        header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #000;
            text-decoration: none;
        }

        .logo span {
            color: #d4a574;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #666;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #d4a574;
        }

        .cart-wrapper {
            position: relative;
            cursor: pointer;
        }

        .cart-icon {
            font-size: 1.5rem;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #d4a574;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        .cart-badge.hidden {
            display: none;
        }

        /* MAIN CONTENT */
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #000;
            text-align: center;
        }

        /* CART LAYOUT */
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        /* CART ITEMS */
        .cart-items {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .cart-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .cart-item:last-child {
            margin-bottom: 0;
        }

        .item-image {
            width: 100px;
            height: 100px;
            background: #e0e0e0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #999;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .item-price {
            color: #d4a574;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .item-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: auto;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: #d4a574;
            color: #fff;
            border-color: #d4a574;
        }

        .qty-value {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }

        .remove-btn {
            padding: 0.5rem 1rem;
            background: #fff;
            color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .remove-btn:hover {
            background: #dc3545;
            color: #fff;
        }

        /* EMPTY CART */
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 2rem;
        }

        .continue-shopping {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #d4a574;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .continue-shopping:hover {
            background: #c89560;
        }

        /* CHECKOUT FORM */
        .checkout-form {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .form-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #000;
            border-bottom: 2px solid #d4a574;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d4a574;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .order-summary {
            background: #fff;
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-row.total {
            border-top: 2px solid #d4a574;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #d4a574;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #c89560;
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* SUCCESS MESSAGE */
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            max-width: 600px;
            margin: 2rem auto;
        }

        .success-message h2 {
            margin-bottom: 1rem;
        }

        .success-message .order-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #155724;
            margin: 1rem 0;
            padding: 1rem;
            background: #fff;
            border-radius: 4px;
        }

        .success-message p {
            margin-bottom: 1rem;
        }

        /* ERRORS */
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .error-message ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        /* TOAST */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #333;
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 4px;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.success {
            background: #28a745;
        }

        .toast.error {
            background: #dc3545;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }

            .checkout-form {
                position: static;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                font-size: 1.8rem;
            }

            .header-container {
                padding: 0 1rem;
            }

            nav {
                gap: 1rem;
            }

            .cart-container {
                padding: 1rem;
            }

            .cart-item {
                flex-direction: column;
            }

            .item-image {
                width: 100%;
                height: 150px;
            }

            .item-actions {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">Casinha <span>Gourmet</span></a>
            <nav>
                <a href="index.php">Home</a>
                <a href="catalog.php">Catálogo</a>
                <a href="#contato">Contato</a>
                <div class="cart-wrapper" onclick="window.location.href='cart.php'">
                    <span class="cart-icon">🛒</span>
                    <span class="cart-badge hidden" id="cart-count">0</span>
                </div>
            </nav>
        </div>
    </header>

    <!-- CART CONTENT -->
    <main class="cart-container">
        <h1 class="page-title">Seu Carrinho</h1>

        <?php if ($order_success): ?>
        <!-- Success Message -->
        <div class="success-message">
            <h2>✅ Pedido Realizado com Sucesso!</h2>
            <p>Obrigado pela sua encomenda! Recebemos o seu pedido e em breve entraremos em contato.</p>
            <div class="order-number">Pedido: <?php echo htmlspecialchars($order_number); ?></div>
            <p>Você receberá um email de confirmação em breve.</p>
            <a href="catalog.php" class="continue-shopping">Continuar Comprando</a>
        </div>
        <?php else: ?>
        
        <!-- Cart Layout -->
        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items" id="cart-items">
                <!-- Cart items will be rendered by JavaScript -->
            </div>

            <!-- Checkout Form -->
            <div class="checkout-form">
                <h2 class="form-title">Finalizar Pedido</h2>
                
                <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" id="checkout-form">
                    <!-- Hidden cart data -->
                    <input type="hidden" name="cart_data" id="cart-data" value="[]">
                    
                    <!-- Order Summary -->
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal">R$ 0,00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="total">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="form-group">
                        <label>Nome completo <span class="required">*</span></label>
                        <input type="text" name="customer_name" required placeholder="Seu nome completo">
                    </div>

                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="customer_email" required placeholder="seu@email.com">
                    </div>

                    <div class="form-group">
                        <label>WhatsApp <span class="required">*</span></label>
                        <input type="tel" name="customer_phone" required placeholder="(11) 99999-9999">
                    </div>

                    <div class="form-group">
                        <label>Endereço</label>
                        <textarea name="customer_address" placeholder="Rua, número, complemento, bairro, cidade"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Data de entrega <span class="required">*</span></label>
                        <input type="date" name="delivery_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>

                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="special_instructions" placeholder="Alguma observação especial para o pedido"></textarea>
                    </div>

                    <button type="submit" name="submit_order" class="submit-btn" id="submit-btn">
                        Finalizar Pedido
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        // Cart state
        let cart = JSON.parse(localStorage.getItem('casinha_cart')) || [];
        
        // Initialize
        updateCartBadge();
        renderCart();

        // Update cart badge
        function updateCartBadge() {
            const badge = document.getElementById('cart-count');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Render cart items
        function renderCart() {
            const container = document.getElementById('cart-items');
            const cartDataInput = document.getElementById('cart-data');
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <h3>Seu carrinho está vazio</h3>
                        <p>Adicione produtos do nosso catálogo para fazer seu pedido.</p>
                        <a href="catalog.php" class="continue-shopping">Ver Catálogo</a>
                    </div>
                `;
                document.getElementById('submit-btn').disabled = true;
                cartDataInput.value = '[]';
                updateSummary();
                return;
            }

            document.getElementById('submit-btn').disabled = false;
            cartDataInput.value = JSON.stringify(cart);

            let html = '';
            let subtotal = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                html += `
                    <div class="cart-item">
                        <div class="item-image">
                            ${item.image ? `<img src="${item.image}" alt="${item.name}">` : '🍰'}
                        </div>
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">R$ ${item.price.toFixed(2).replace('.', ',')}</div>
                            <div class="item-actions">
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="updateQuantity(${index}, -1)">−</button>
                                    <span class="qty-value">${item.quantity}</span>
                                    <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                                </div>
                                <button class="remove-btn" onclick="removeItem(${index})">Remover</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            updateSummary();
        }

        // Update quantity
        function updateQuantity(index, change) {
            const newQuantity = cart[index].quantity + change;
            
            if (newQuantity < 1) {
                removeItem(index);
                return;
            }

            cart[index].quantity = newQuantity;
            saveCart();
        }

        // Remove item
        function removeItem(index) {
            cart.splice(index, 1);
            saveCart();
            showToast('Item removido do carrinho', 'success');
        }

        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem('casinha_cart', JSON.stringify(cart));
            updateCartBadge();
            renderCart();
        }

        // Update order summary
        function updateSummary() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const total = subtotal;

            document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            document.getElementById('total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Form validation
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                showToast('Carrinho vazio! Adicione produtos primeiro.', 'error');
                return;
            }
        });
    </script>
</body>
</html>