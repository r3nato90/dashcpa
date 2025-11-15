<?php
session_start();
include('config/db.php');
include('config/logger.php');
date_default_timezone_set('America/Sao_Paulo'); 

$message = "";

// 1. Validar o Plano selecionado
if (!isset($_GET['plan_id']) || !is_numeric($_GET['plan_id'])) {
    header('Location: pricing.php'); // Se não escolher plano, volta para a página de planos
    exit;
}
$plan_id = (int)$_GET['plan_id'];

// 2. Buscar os detalhes do plano (limites e link de pagamento)
$stmt_plan = $pdo->prepare("SELECT * FROM plans WHERE plan_id = ?");
$stmt_plan->execute([$plan_id]);
$plan = $stmt_plan->fetch();

if (!$plan) {
    header('Location: pricing.php'); // Plano inválido
    exit;
}
$payment_link = $plan['mercadopago_link'];

// Lógica de processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $org_name = $_POST['org_name'];
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // Pega os limites do plano
    $plan_type = $plan['plan_name'];
    $max_admins = $plan['default_max_admins'];
    $max_users = $plan['default_max_users'];

    try {
        // 1. Validar se o Email ou Username já existem GLOBALMENTE
        $stmt_check = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? OR username = ?");
        $stmt_check->execute([$admin_email, $admin_username]);
        if ($stmt_check->fetch()) {
            throw new Exception("Este Email ou Nome de Usuário já está em uso na plataforma.");
        }

        $pdo->beginTransaction();

        // 3. Criar a Organização (Cliente) com status 'inactive'
        $stmt_org = $pdo->prepare("
            INSERT INTO organizations (org_name, plan_type, max_admins, max_users, status) 
            VALUES (?, ?, ?, ?, 'inactive')
        ");
        $stmt_org->execute([$org_name, $plan_type, $max_admins, $max_users]);
        $org_id = $pdo->lastInsertId();

        // 4. Criar a conta do Super Admin
        $stmt_admin = $pdo->prepare("
            INSERT INTO sub_administradores (org_id, nome, email, username, senha, role, percentual_comissao) 
            VALUES (?, ?, ?, ?, ?, 'super_adm', 0)
        ");
        $stmt_admin->execute([$org_id, $admin_name, $admin_email, $admin_username, $admin_password]);
        
        $pdo->commit();

        // Log
        log_action($pdo, 'ORG_REGISTER_PENDING', "Nova organização '{$org_name}' (ID: {$org_id}) foi criada por '{$admin_name}'. Aguardando pagamento.");
        
        // 5. Redirecionar para o Checkout do Mercado Pago
        if (empty($payment_link)) {
            // Fallback se você não cadastrou o link
            throw new Exception("Link de pagamento para este plano não configurado. Contate o suporte.");
        }
        header('Location: ' . $payment_link);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nova Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding: 2rem 0;">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
            <div class="card-header bg-success text-white text-center">
                <h4><i class="fas fa-building me-2"></i> Criar Conta (Plano: <?php echo htmlspecialchars($plan['plan_name']); ?>)</h4>
            </div>
            <div class="card-body p-4">
                <p class="text-center">Você está assinando o <strong><?php echo htmlspecialchars($plan['price_description']); ?></strong>. Preencha seus dados para criar a conta.</p>
                <?php echo $message; ?>
                
                <form action="register_organization.php?plan_id=<?php echo $plan_id; ?>" method="POST">
                    <fieldset class="border p-3 rounded mb-3">
                        <legend classs="float-none w-auto px-2 fs-6" style="font-size: 1rem; font-weight: 600;">Sobre sua Empresa</legend>
                        <div class="mb-3"><label for="org_name" class="form-label">Nome da Empresa</label><input type="text" class="form-control" name="org_name" required></div>
                    </fieldset>
                    
                    <fieldset class="border p-3 rounded mb-3">
                        <legend classs="float-none w-auto px-2 fs-6" style="font-size: 1rem; font-weight: 600;">Sobre Você (Super Admin)</legend>
                        <div class="mb-3"><label for="admin_name" class="form-label">Seu Nome Completo</label><input type="text" class="form-control" name="admin_name" required></div>
                        <div class="mb-3"><label for="admin_email" class="form-label">Seu Email (para login)</label><input type="email" class="form-control" name="admin_email" required></div>
                        <div class="mb-3"><label for="admin_username" class="form-label">Nome de Usuário (para links de convite)</label><input type="text" class="form-control" name="admin_username" required></div>
                        <div class="mb-3"><label for="admin_password" class="form-label">Sua Senha</label><input type="password" class="form-control" name="admin_password" required></div>
                    </fieldset>
                    
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fas fa-lock me-2"></i> Ir para Pagamento
                    </button>
                </form>
                
                <hr>
                <div class="text-center">
                    <a href="login.php" class="btn btn-outline-primary w-100">Voltar ao Login</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>