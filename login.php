<?php
session_start();
include('config/db.php');
include('config/logger.php'); 

$login_error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // --- 1. VERIFICAÇÃO DE GERENTE (platform_owner, super_adm, admin, sub_adm) ---
    $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ? AND senha = ?");
    $stmt->execute([$email, $senha]);
    $admin_user = $stmt->fetch();

    if ($admin_user) {
        $org_id_check = $admin_user['org_id'];

        // **** VERIFICA O STATUS DA ORGANIZAÇÃO ****
        $stmt_org = $pdo->prepare("SELECT status FROM organizations WHERE org_id = ?");
        $stmt_org->execute([$org_id_check]);
        $org_status = $stmt_org->fetchColumn();

        if ($org_status == 'active') {
            $_SESSION['id'] = $admin_user['id_sub_adm'];
            $_SESSION['role'] = $admin_user['role'];
            $_SESSION['org_id'] = $admin_user['org_id'];
            
            if ($admin_user['role'] == 'platform_owner') header('Location: platform_owner.php');
            elseif ($admin_user['role'] == 'super_adm') header('Location: dashboard_superadmin.php');
            elseif ($admin_user['role'] == 'admin') header('Location: dashboard_admin.php');
            elseif ($admin_user['role'] == 'sub_adm') header('Location: dashboard_subadmin.php');
            exit;
        } else {
            $login_error = "<div class='alert alert-danger text-center'>Sua organização está <strong>{$org_status}</strong>. Contate o suporte.</div>";
            log_action($pdo, 'LOGIN_FAIL_SUSPENDED', "Login bloqueado (Status: {$org_status}) para o email: $email (Org ID: $org_id_check).");
        }
    }

    // --- 2. VERIFICAÇÃO DE USUÁRIO (Operador) ---
    if (!$admin_user) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
        $stmt->execute([$email, $senha]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $org_id_check = $usuario['org_id'];

            // **** VERIFICA O STATUS DA ORGANIZAÇÃO ****
            $stmt_org = $pdo->prepare("SELECT status FROM organizations WHERE org_id = ?");
            $stmt_org->execute([$org_id_check]);
            $org_status = $stmt_org->fetchColumn();

            if ($org_status == 'active') {
                $_SESSION['id'] = $usuario['id_usuario'];
                $_SESSION['role'] = 'usuario';
                $_SESSION['org_id'] = $usuario['org_id'];
                header('Location: dashboard_usuario.php');
                exit;
            } else {
                $login_error = "<div class='alert alert-danger text-center'>Sua organização está <strong>{$org_status}</strong>. Contate o suporte.</div>";
                log_action($pdo, 'LOGIN_FAIL_SUSPENDED', "Login bloqueado (Status: {$org_status}) para o email: $email (Org ID: $org_id_check).");
            }
        }
    }

    if (empty($login_error)) {
        $login_error = "<div class='alert alert-danger text-center'>Credenciais inválidas!</div>";
        log_action($pdo, 'LOGIN_FAIL', "Tentativa de login falhou (credenciais inválidas) para o email: $email.");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($login_error)) echo $login_error; ?>
                <form action="login.php" method="POST">
                    <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label for="senha" class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <hr>
                <div class="text-center">
                    <p class="mb-0">Não tem uma conta?</p>
                    <a href="index.php" class="btn btn-success w-100">
                        <i class="fas fa-gem me-2"></i> Ver Planos e Registrar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>