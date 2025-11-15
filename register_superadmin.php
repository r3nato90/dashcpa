<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('config/db.php');

// Define o fuso horário padrão para todas as operações
date_default_timezone_set('America/Sao_Paulo');

$message = "<div class='alert alert-warning'><strong>Atenção:</strong> Este arquivo é um risco de segurança. Apague-o do servidor imediatamente após criar o primeiro Super Admin.</div>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $role = 'super_adm'; // Papel fixo

    // Verificar se o email ou username já existe
    $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $message .= "<div class='alert alert-danger'>Este Email ou Nome de Usuário já está registrado!</div>";
    } else {
        try {
            // Registrando o Super Administrador
            $stmt = $pdo->prepare("INSERT INTO sub_administradores (nome, email, username, senha, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $username, $senha, $role]);
            $message .= "<div class='alert alert-success'>Super Admin registrado com sucesso! <strong>APAGUE ESTE ARQUIVO AGORA.</strong></div>";
        } catch (PDOException $e) {
            $message .= "<div class='alert alert-danger'>Erro ao registrar: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Super Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-danger text-white text-center">
                <h4>Registrar Super Admin (MASTER)</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="register_superadmin.php" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Usuário (login)</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Criar Super Admin</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>