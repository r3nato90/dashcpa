<?php
session_start(); 
include('config/db.php');
include('config/logger.php'); // Incluído
date_default_timezone_set('America/Sao_Paulo');

$ref_username = "";
$gerente_info = null;
$message = "";

// 1. Validar o link de referência (ref)
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $ref_username = $_GET['ref'];
    
    // Busca o gerente (Admin/Sub-Admin) pelo username
    $stmt_ref = $pdo->prepare("SELECT id_sub_adm, org_id, nome FROM sub_administradores WHERE username = ?");
    $stmt_ref->execute([$ref_username]);
    $gerente_info = $stmt_ref->fetch();
    
    if (!$gerente_info) {
        $message = "<div class='alert alert-danger'>Link de referência inválido ou expirado.</div>";
        $ref_username = ""; // Limpa para esconder o nome
    }
} else {
    $message = "<div class='alert alert-danger'>Esta página só pode ser acessada através de um link de referência.</div>";
}

// 2. Verificar Limite do Plano (se o gerente foi encontrado)
if ($gerente_info) {
    $stmt_plan = $pdo->prepare("
        SELECT o.max_users, (SELECT COUNT(*) FROM usuarios WHERE org_id = o.org_id) as current_users
        FROM organizations o WHERE o.org_id = ?
    ");
    $stmt_plan->execute([$gerente_info['org_id']]);
    $plan = $stmt_plan->fetch();

    if ($plan && $plan['current_users'] >= $plan['max_users']) {
        $message = "<div class='alert alert-danger'>Esta organização atingiu o limite máximo de usuários (Operadores) do seu plano.</div>";
    }
}

// 3. Processar o Formulário
if ($_SERVER["REQUEST_METHOD"] == "POST" && $gerente_info && empty($message)) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha']; 
    $percentual_comissao = 25; // Comissão padrão
    
    $org_id = $gerente_info['org_id'];
    $id_sub_adm = $gerente_info['id_sub_adm'];

    try {
        // Verificar se o email já existe DENTRO da organização
        $stmt_check1 = $pdo->prepare("SELECT email FROM usuarios WHERE email = ? AND org_id = ?");
        $stmt_check1->execute([$email, $org_id]);
        $stmt_check2 = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? AND org_id = ?");
        $stmt_check2->execute([$email, $org_id]);

        if ($stmt_check1->fetch() || $stmt_check2->fetch()) {
            $message = "<div class='alert alert-danger'>Este email já está registrado nesta organização.</div>";
        } else {
            // Inserir o novo usuário com org_id e id_sub_adm
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (org_id, nome, email, senha, percentual_comissao, id_sub_adm) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$org_id, $nome, $email, $senha, $percentual_comissao, $id_sub_adm]);
            $new_user_id = $pdo->lastInsertId();

            // Log (o org_id será pego da sessão do novo usuário)
            // log_action($pdo, 'USER_REGISTER_PUBLIC', "Usuário '{$nome}' (ID: {$new_user_id}) registrou-se via link.");
            
            // Loga o novo usuário e redireciona
            $_SESSION['id'] = $new_user_id;
            $_SESSION['role'] = 'usuario'; 
            $_SESSION['org_id'] = $org_id;
            header('Location: dashboard_usuario.php'); 
            exit;
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erro ao registrar: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuário (Operador)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Registrar Novo Operador</h4>
                <?php if($gerente_info): ?>
                    <p class="mb-0 small">Você está sendo indicado por: <?php echo htmlspecialchars($gerente_info['nome']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php echo $message; ?>

                <?php if ($gerente_info && empty($message)): // Só mostra o form se o link for válido e o limite não foi atingido ?>
                <form action="register_user.php?ref=<?php echo htmlspecialchars($ref_username); ?>" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Seu Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Seu Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Crie uma Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Criar Conta</button>
                </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary w-100">Voltar ao Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>