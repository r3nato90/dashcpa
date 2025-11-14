<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído

$login_error = ""; // Variável para armazenar a mensagem de erro

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificando Admin, Super-Admin ou Sub-Admin
    $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ? AND senha = ?");
    $stmt->execute([$email, $senha]);
    $admin_user = $stmt->fetch();

    if ($admin_user) {
        $_SESSION['id'] = $admin_user['id_sub_adm'];
        $_SESSION['role'] = $admin_user['role']; // Pega a role do banco
        
        if ($admin_user['role'] == 'super_adm') {
            header('Location: dashboard_superadmin.php');
            exit;
        } elseif ($admin_user['role'] == 'admin') {
            header('Location: dashboard_admin.php');
            exit;
        } elseif ($admin_user['role'] == 'sub_adm') {
            header('Location: dashboard_subadmin.php');
            exit;
        }
    }

    // Verificando Usuário
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
    $stmt->execute([$email, $senha]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $_SESSION['id'] = $usuario['id_usuario'];
        $_SESSION['role'] = 'usuario';
        header('Location: dashboard_usuario.php');
        exit;
    }

    // Caso as credenciais sejam inválidas
    $login_error = "<div class='alert alert-danger text-center'>Credenciais inválidas!</div>";
    
    // **** LOG DE FALHA ****
    log_action($pdo, 'LOGIN_FAIL', "Tentativa de login falhou para o email: $email.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                
                <?php if (!empty($login_error)) echo $login_error; // Exibe o erro aqui ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>