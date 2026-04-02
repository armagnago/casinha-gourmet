<?php
/**
 * Seletor de Design - Casinha Gourmet
 * Permite escolher entre 4 designs diferentes
 */

require_once '../database.php';

$conn = getConnection();

// Atualizar design se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['design'])) {
    $design = sanitize($_POST['design']);
    
    // Validar design
    $valid_designs = ['design1', 'design2', 'design3', 'design4'];
    if (in_array($design, $valid_designs)) {
        // Atualizar no banco
        executeQuery($conn, "
            UPDATE store_settings 
            SET setting_value = ? 
            WHERE setting_key = 'current_design'
        ", [$design], 's');
        
        $_SESSION['design_updated'] = true;
        header('Location: /');
        exit;
    }
}

// Obter design atual
$current_design = getRow($conn, "SELECT setting_value FROM store_settings WHERE setting_key = 'current_design'");
$current = $current_design['setting_value'] ?? 'design1';

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Design - Casinha Gourmet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        .header {
            text-align: center;
            color: #fff;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .designs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .design-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .design-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .design-card.active {
            border-color: #667eea;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .design-preview {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .design1 .design-preview {
            background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
        }

        .design2 .design-preview {
            background: linear-gradient(135deg, #f5e6d3 0%, #faf8f3 100%);
        }

        .design3 .design-preview {
            background: linear-gradient(135deg, #ff6b9d, #ffa500, #ffd700);
        }

        .design4 .design-preview {
            background: linear-gradient(135deg, #d4a574 0%, #c89560 50%, #a0826d 100%);
        }

        .design-info {
            padding: 1.5rem;
        }

        .design-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .design-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .design-features {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 1rem;
        }

        .design-features li {
            list-style: none;
            margin-bottom: 0.3rem;
        }

        .design-features li::before {
            content: '✓ ';
            color: #667eea;
            font-weight: bold;
        }

        .design-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.7rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-preview {
            background: #f0f0f0;
            color: #333;
        }

        .btn-preview:hover {
            background: #e0e0e0;
        }

        .btn-select {
            background: #667eea;
            color: #fff;
        }

        .btn-select:hover {
            background: #5568d3;
        }

        .design-card.active .btn-select {
            background: #4CAF50;
        }

        .design-card.active .btn-select:hover {
            background: #45a049;
        }

        .footer-note {
            text-align: center;
            color: #fff;
            margin-top: 2rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .designs-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Escolha o Design da Sua Loja</h1>
            <p>Selecione uma das 4 opções de design para sua Casinha Gourmet</p>
        </div>

        <div class="designs-grid">
            <!-- DESIGN 1 -->
            <div class="design-card design1 <?php echo $current === 'design1' ? 'active' : ''; ?>">
                <div class="design-preview">🎨</div>
                <div class="design-info">
                    <div class="design-name">Minimalista Moderno</div>
                    <div class="design-description">
                        Clean e elegante com cores neutras
                    </div>
                    <ul class="design-features">
                        <li>Cores neutras com acentos</li>
                        <li>Muito espaço em branco</li>
                        <li>Tipografia moderna</li>
                        <li>Profissional e minimalista</li>
                    </ul>
                    <div class="design-buttons">
                        <a href="/designs/design1/" class="btn btn-preview" target="_blank">Visualizar</a>
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="design" value="design1">
                            <button type="submit" class="btn btn-select" style="width: 100%;">
                                <?php echo $current === 'design1' ? '✓ Selecionado' : 'Selecionar'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DESIGN 2 -->
            <div class="design-card design2 <?php echo $current === 'design2' ? 'active' : ''; ?>">
                <div class="design-preview">✨</div>
                <div class="design-info">
                    <div class="design-name">Elegante Sofisticado</div>
                    <div class="design-description">
                        Luxuoso com cores pastel e tipografia serif
                    </div>
                    <ul class="design-features">
                        <li>Cores pastel sofisticadas</li>
                        <li>Tipografia serif elegante</li>
                        <li>Elementos decorativos</li>
                        <li>Design premium</li>
                    </ul>
                    <div class="design-buttons">
                        <a href="/designs/design2/" class="btn btn-preview" target="_blank">Visualizar</a>
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="design" value="design2">
                            <button type="submit" class="btn btn-select" style="width: 100%;">
                                <?php echo $current === 'design2' ? '✓ Selecionado' : 'Selecionar'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DESIGN 3 -->
            <div class="design-card design3 <?php echo $current === 'design3' ? 'active' : ''; ?>">
                <div class="design-preview">🎉</div>
                <div class="design-info">
                    <div class="design-name">Colorido Vibrante</div>
                    <div class="design-description">
                        Alegre e divertido com cores vibrantes
                    </div>
                    <ul class="design-features">
                        <li>Cores alegres e vibrantes</li>
                        <li>Design lúdico</li>
                        <li>Animações divertidas</li>
                        <li>Muito atrativo</li>
                    </ul>
                    <div class="design-buttons">
                        <a href="/designs/design3/" class="btn btn-preview" target="_blank">Visualizar</a>
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="design" value="design3">
                            <button type="submit" class="btn btn-select" style="width: 100%;">
                                <?php echo $current === 'design3' ? '✓ Selecionado' : 'Selecionar'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DESIGN 4 -->
            <div class="design-card design4 <?php echo $current === 'design4' ? 'active' : ''; ?>">
                <div class="design-preview">🏛️</div>
                <div class="design-info">
                    <div class="design-name">Clássico Vintage</div>
                    <div class="design-description">
                        Estilo vintage/retrô nostálgico
                    </div>
                    <ul class="design-features">
                        <li>Estilo vintage clássico</li>
                        <li>Cores quentes nostálgicas</li>
                        <li>Elementos decorativos</li>
                        <li>Tipografia tradicional</li>
                    </ul>
                    <div class="design-buttons">
                        <a href="/designs/design4/" class="btn btn-preview" target="_blank">Visualizar</a>
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="design" value="design4">
                            <button type="submit" class="btn btn-select" style="width: 100%;">
                                <?php echo $current === 'design4' ? '✓ Selecionado' : 'Selecionar'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p>Você pode mudar o design a qualquer momento. Clique em "Visualizar" para ver uma prévia antes de selecionar.</p>
        </div>
    </div>
</body>
</html>
