<?php
$page_title = "Registro";
include('config/db.php');
include('config/logger.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Define o papel como 'usuario' por padrão
    $role = 'usuario';

    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>Por favor, preencha todos os campos.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $message = "<div class='alert alert-danger'>Formato de email inválido.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>As senhas não coincidem.</div>";
    } else {
        // Hash da senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Verifica se o email já existe em ambas as tabelas
            $stmt_check = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? UNION ALL SELECT email FROM usuarios WHERE email = ?");
            $stmt_check->execute([$email, $email]);

            if ($stmt_check->fetch()) {
                $message = "<div class='alert alert-warning'>Este email já está registrado.</div>";
            } else {
                // Insere na tabela 'usuarios'
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $hashed_password, $role]);

                // Registra a ação de registro
                log_acao("Novo usuário ('usuario') registrado: " . htmlspecialchars($nome) . " com email: " . htmlspecialchars($email));

                // Redireciona para a página de sucesso
                header('Location: create_user_success.php?name=' . urlencode($nome));
                exit;
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao registrar. Tente novamente mais tarde.</div>";
            error_log("Erro de registro de usuário: " . $e->getMessage());
        }
    }
}

// Inclui o cabeçalho (apenas a estrutura básica)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashCPA - Registro</title>
    <!-- Incluindo Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Incluindo Font Awesome (Ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
            margin-bottom: 50px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center my-3">Registro de Novo Usuário</h3>
                        <h6 class="text-center text-muted mb-4">Crie sua conta de usuário padrão.</h6>
                        
                        <?php echo $message; ?>

                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-success">Registrar Conta</button>
                            </div>
                            <p class="text-center">
                                Já tem uma conta? <a href="login.php">Faça login</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Incluindo Bootstrap JS (CDN) e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>