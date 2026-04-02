<?php
/**
 * CATALOG PAGE - Products Listing
 * Casinha Gourmet - Loja Virtual
 * 
 * Features:
 * - Category filtering
 * - Product grid with responsive layout
 * - Add to cart functionality with localStorage
 * - Cart badge in header
 */

require_once 'database.php';

$conn = getConnection();

// Get active categories
$categories = getRows($conn, "SELECT * FROM categories WHERE is_active = TRUE ORDER BY display_order");

// Get filter parameter
$category_filter = isset($_GET['categoria']) ? sanitize($_GET['categoria']) : '';

// Build product query based on filter
if (!empty($category_filter)) {
    $products = getRows($conn, "
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.is_available = TRUE AND c.slug = ? 
        ORDER BY p.display_order
    ", [$category_filter], 's');
} else {
    $products = getRows($conn, "
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.is_available = TRUE 
        ORDER BY p.display_order
    ");
}

// Get store settings for header
$whatsapp = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'whatsapp_number'");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Casinha Gourmet</title>
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

        /* CATALOG CONTAINER */
        .catalog-container {
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

        /* CATEGORY FILTERS */
        .category-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .category-btn {
            padding: 0.8rem 1.5rem;
            background: #f9f9f9;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.95rem;
            color: #333;
            transition: all 0.3s;
            text-decoration: none;
        }

        .category-btn:hover {
            background: #f0f0f0;
            border-color: #d4a574;
        }

        .category-btn.active {
            background: #d4a574;
            color: #fff;
            border-color: #d4a574;
        }

        .category-btn.active:hover {
            background: #c89560;
        }

        /* PRODUCTS GRID */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        /* PRODUCT CARD */
        .product-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #999;
        }

        .product-info {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #000;
        }

        .product-category {
            font-size: 0.8rem;
            color: #999;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .product-price {
            font-size: 1.3rem;
            color: #d4a574;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .product-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            flex: 1;
        }

        .add-to-cart {
            width: 100%;
            padding: 0.8rem;
            background: #d4a574;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s;
            margin-top: auto;
        }

        .add-to-cart:hover {
            background: #c89560;
        }

        .add-to-cart:active {
            transform: scale(0.98);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        /* TOAST NOTIFICATION */
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

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .header-container {
                padding: 0 1rem;
            }

            nav {
                gap: 1rem;
            }

            .catalog-container {
                padding: 1rem;
            }

            .category-btn {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
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

    <!-- CATALOG CONTENT -->
    <main class="catalog-container">
        <h1 class="page-title">Nosso Catálogo</h1>

        <!-- Category Filters -->
        <div class="category-filters">
            <a href="catalog.php" class="category-btn <?php echo empty($category_filter) ? 'active' : ''; ?>">
                Todos
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?categoria=<?php echo htmlspecialchars($cat['slug']); ?>" 
               class="category-btn <?php echo $category_filter === $cat['slug'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if (!empty($product['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                    🍰
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                    <p class="product-description">
                        <?php 
                        $desc = $product['description'] ?? '';
                        echo htmlspecialchars(mb_strimwidth($desc, 0, 80, '...'));
                        ?>
                    </p>
                    <button class="add-to-cart" 
                            onclick="addToCart(<?php echo htmlspecialchars(json_encode([
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'price' => $product['price'],
                                'image' => $product['image_url'] ?? ''
                            ])); ?>)">
                        Adicionar ao Carrinho
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h3>Nenhum produto encontrado</h3>
            <p>Esta categoria ainda não tem produtos disponíveis.</p>
            <a href="catalog.php" class="category-btn" style="display:inline-block;margin-top:1rem;">
                Ver todos os produtos
            </a>
        </div>
        <?php endif; ?>
    </main>

    <!-- Toast Notification -->
    <div class="toast" id="toast">Produto adicionado ao carrinho!</div>

    <script>
        // Initialize cart from localStorage
        let cart = JSON.parse(localStorage.getItem('casinha_cart')) || [];
        updateCartBadge();

        // Add to cart function
        function addToCart(product) {
            // Check if product already exists in cart
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.image || '',
                    quantity: 1
                });
            }

            // Save to localStorage
            localStorage.setItem('casinha_cart', JSON.stringify(cart));
            
            // Update badge
            updateCartBadge();

            // Show toast notification
            showToast(product.name + ' adicionado ao carrinho!');
        }

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

        // Show toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>