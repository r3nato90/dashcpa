<?php
$page_title = "Login";
include('config/db.php');
include('config/logger.php'); // Inclui o sistema de Log

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já estiver logado, redireciona para o index
if (isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = "<div class='alert alert-danger'>Por favor, preencha todos os campos.</div>";
    } else {
        // Tenta buscar o usuário na tabela de sub_administradores (super_adm, admin, sub_adm)
        $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Se não encontrado em sub_administradores, tenta na tabela de usuarios (usuario)
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        }

        if ($user && password_verify($password, $user['senha'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['nome'] ?? $user['email']; // Usa nome se existir, senão o email
            $_SESSION['role'] = $user['role'];

            // Registra a ação de login
            log_acao("Login bem-sucedido. Usuário: " . $_SESSION['username'] . " (" . $_SESSION['role'] . ")");

            // Redireciona para a página principal (que fará o redirecionamento específico por role)
            header('Location: index.php');
            exit;
        } else {
            // Falha no login
            $error_message = "<div class='alert alert-danger'>Email ou senha incorretos.</div>";
            // Registra a tentativa de login falhada
            log_acao("Tentativa de login falhada para o email: " . htmlspecialchars($email));
        }
    }
}

// Inclui o cabeçalho (apenas a estrutura básica, sem a barra lateral)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashCPA - Login</title>
    <!-- Incluindo Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Incluindo Font Awesome (Ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 100px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center my-3">Login no DashCPA</h3>
                        
                        <?php echo $error_message; ?>

                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                            </div>
                            <p class="text-center">
                                Não tem uma conta? <a href="register.php">Crie uma aqui</a>.
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