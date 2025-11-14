<?php
// ATENÇÃO: ESTE ARQUIVO DEVE SER PROTEGIDO EM AMBIENTE DE PRODUÇÃO!
// Ele é projetado para ser usado UMA VEZ para criar o SUPER ADMIN inicial.
$page_title = "Registro de Super Admin";
include('config/db.php');
include('config/logger.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = "";

// A lógica de controle de acesso para este arquivo deve ser implementada no servidor
// para garantir que ele só possa ser acessado na primeira execução ou por usuários autorizados.
// Por simplicidade, assumimos que esta página é acessível para o primeiro registro
// e pode ser desativada manualmente depois.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'super_adm'; // Define a role manualmente

    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>Por favor, preencha todos os campos.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $message = "<div class='alert alert-danger'>Formato de email inválido.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>As senhas não coincidem.</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Verifica se já existe algum 'super_adm'
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sub_administradores WHERE role = 'super_adm'");
            $stmt_check->execute();
            $super_admin_count = $stmt_check->fetchColumn();

            // Permite o registro se for o primeiro 'super_adm' OU se a regra de segurança permitir
            if ($super_admin_count == 0) {
                // Verifica se o email já existe (em qualquer tabela)
                $stmt_check_email = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? UNION ALL SELECT email FROM usuarios WHERE email = ?");
                $stmt_check_email->execute([$email, $email]);

                if ($stmt_check_email->fetch()) {
                    $message = "<div class='alert alert-warning'>Este email já está registrado.</div>";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO sub_administradores (nome, email, senha, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nome, $email, $hashed_password, $role]);

                    log_acao("Novo Super Admin registrado: " . htmlspecialchars($nome) . " com email: " . htmlspecialchars($email));

                    $message = "<div class='alert alert-success'>Super Admin registrado com sucesso! <a href='login.php'>Fazer Login</a></div>";
                    // Após o primeiro registro, você deve desativar ou proteger este arquivo!
                }
            } else {
                 $message = "<div class='alert alert-warning'>Já existe um Super Admin registrado. Acesso negado.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao registrar. Tente novamente mais tarde.</div>";
            error_log("Erro de registro de Super Admin: " . $e->getMessage());
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
    <title>DashCPA - <?php echo $page_title; ?></title>
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
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Registro de Super Administrador</h4>
                    </div>
                    <div class="card-body">
                        <h6 class="text-center text-muted mb-4">Esta página é para configurar o primeiro usuário mestre.</h6>
                        
                        <?php echo $message; ?>

                        <form method="POST" action="register_superadmin.php">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
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
                                <button type="submit" class="btn btn-primary">Registrar Super Admin</button>
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