<?php
/**
 * DESIGN 1 - MINIMALISTA MODERNO
 * Casinha Gourmet - Loja Virtual
 * 
 * Características:
 * - Clean e moderno
 * - Cores neutras com acentos
 * - Tipografia elegante
 * - Muito espaço em branco
 */

require_once 'database.php';

$conn = getConnection();

// Obter categorias
$categories = getRows($conn, "SELECT * FROM categories WHERE is_active = TRUE ORDER BY display_order");

// Obter produtos em destaque
$featured_products = getRows($conn, "
    SELECT * FROM products 
    WHERE is_available = TRUE AND is_featured = TRUE 
    ORDER BY display_order 
    LIMIT 6
");

// Obter configurações da loja
$store_name = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'store_name'");
$store_phone = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'store_phone'");
$whatsapp = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'whatsapp_number'");
$instagram = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'instagram_url'");
$facebook = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'facebook_url'");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casinha Gourmet - Loja Virtual</title>
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

        .cart-icon {
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* HERO SECTION */
        .hero {
            background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
            padding: 6rem 2rem;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #000;
        }

        .hero p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: #d4a574;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background 0.3s;
            margin: 0.5rem;
        }

        .cta-button:hover {
            background: #c89560;
        }

        .cta-button.secondary {
            background: #fff;
            color: #d4a574;
            border: 2px solid #d4a574;
        }

        .cta-button.secondary:hover {
            background: #f5f5f5;
        }

        /* CATEGORIES */
        .categories {
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 3rem;
            text-align: center;
            color: #000;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
        }

        .category-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 8px;
            background: #f9f9f9;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }

        .category-card:hover {
            background: #f0f0f0;
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .category-card h3 {
            font-size: 1rem;
            color: #000;
        }

        /* FEATURED PRODUCTS */
        .featured {
            background: #f9f9f9;
            padding: 4rem 2rem;
            margin-top: 4rem;
        }

        .products-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
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
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #000;
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
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background: #c89560;
        }

        /* FOOTER */
        footer {
            background: #1a1a1a;
            color: #fff;
            padding: 3rem 2rem;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: #d4a574;
        }

        .footer-section a {
            display: block;
            color: #ccc;
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #d4a574;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            display: inline-flex;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            background: #d4a574;
            border-radius: 50%;
            color: #fff;
            text-decoration: none;
            transition: background 0.3s;
        }

        .social-links a:hover {
            background: #c89560;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 2rem;
            color: #999;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            nav {
                gap: 1rem;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <a href="/" class="logo">Casinha <span>Gourmet</span></a>
            <nav>
                <a href="#catalogo">Catálogo</a>
                <a href="#sobre">Sobre</a>
                <a href="#contato">Contato</a>
                <a href="/admin">Admin</a>
                <div class="cart-icon">🛒</div>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1>Doces e Salgados Artesanais</h1>
            <p>Feitos com amor e ingredientes de qualidade premium</p>
            <a href="#catalogo" class="cta-button">Ver Catálogo</a>
            <a href="https://wa.me/<?php echo $whatsapp['setting_value'] ?? ''; ?>" class="cta-button secondary">WhatsApp</a>
        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="categories" id="catalogo">
        <h2 class="section-title">Nossas Categorias</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?categoria=<?php echo $cat['slug']; ?>" class="category-card">
                <div class="category-icon">🍰</div>
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="featured">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
            <h2 class="section-title">Produtos em Destaque</h2>
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">🍪</div>
                    <div class="product-info">
                        <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                        <div class="product-description"><?php echo substr(htmlspecialchars($product['description']), 0, 80) . '...'; ?></div>
                        <button class="add-to-cart">Adicionar ao Carrinho</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Sobre Nós</h3>
                <p>Casinha Gourmet é uma confeitaria artesanal dedicada a criar doces e salgados de qualidade premium.</p>
            </div>
            <div class="footer-section">
                <h3>Contato</h3>
                <a href="tel:<?php echo str_replace(['(', ')', ' ', '-'], '', $store_phone['setting_value'] ?? ''); ?>">
                    📞 <?php echo $store_phone['setting_value'] ?? ''; ?>
                </a>
                <a href="mailto:contato@casinhagourmet.com">📧 contato@casinhagourmet.com</a>
            </div>
            <div class="footer-section">
                <h3>Redes Sociais</h3>
                <div class="social-links">
                    <a href="<?php echo $instagram['setting_value'] ?? '#'; ?>" title="Instagram">📷</a>
                    <a href="<?php echo $facebook['setting_value'] ?? '#'; ?>" title="Facebook">👍</a>
                    <a href="https://wa.me/<?php echo $whatsapp['setting_value'] ?? ''; ?>" title="WhatsApp">💬</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Casinha Gourmet. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
