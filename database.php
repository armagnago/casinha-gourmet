<?php
/**
 * Configuração do Banco de Dados
 * Casinha Gourmet - Loja Virtual
 */

// Configurações de conexão
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'casinha_gourmet');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// Configurações da aplicação
define('APP_NAME', 'Casinha Gourmet');
define('APP_URL', getenv('APP_URL') ?: 'https://casinhagourmet.com');
define('APP_DESIGN', getenv('APP_DESIGN') ?: 'design1'); // design1, design2, design3, design4

// Configurações de email
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.seuhost.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: 'seu-email@dominio.com');
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'sua-senha');
define('OWNER_EMAIL', getenv('OWNER_EMAIL') ?: 'proprietario@casinhagourmet.com');

// Configurações Stripe
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: '');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: '');

// Configurações S3
define('AWS_ACCESS_KEY', getenv('AWS_ACCESS_KEY') ?: '');
define('AWS_SECRET_KEY', getenv('AWS_SECRET_KEY') ?: '');
define('AWS_BUCKET', getenv('AWS_BUCKET') ?: 'casinha-gourmet');
define('AWS_REGION', getenv('AWS_REGION') ?: 'us-east-1');

// Configurações de segurança
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Modo debug
define('DEBUG_MODE', getenv('DEBUG_MODE') ?: false);

// Função para conectar ao banco de dados
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        if ($conn->connect_error) {
            throw new Exception("Erro de conexão: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        } else {
            die("Erro ao conectar ao banco de dados. Contate o suporte.");
        }
    }
}

// Função para fechar conexão
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Função para preparar statement
function prepareStatement($conn, $query) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Erro ao preparar query: " . $conn->error);
    }
    return $stmt;
}

// Função para executar query
function executeQuery($conn, $query, $params = [], $types = '') {
    $stmt = prepareStatement($conn, $query);
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar query: " . $stmt->error);
    }
    
    return $stmt;
}

// Função para obter resultado
function getResult($conn, $query, $params = [], $types = '') {
    $stmt = executeQuery($conn, $query, $params, $types);
    return $stmt->get_result();
}

// Função para obter uma linha
function getRow($conn, $query, $params = [], $types = '') {
    $result = getResult($conn, $query, $params, $types);
    return $result->fetch_assoc();
}

// Função para obter todas as linhas
function getRows($conn, $query, $params = [], $types = '') {
    $result = getResult($conn, $query, $params, $types);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

// Função para obter última ID inserida
function getLastInsertId($conn) {
    return $conn->insert_id;
}

// Função para sanitizar entrada
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para gerar token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
