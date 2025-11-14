<?php
session_start();
include('config/db.php');

$message = ""; // Para feedback

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username']; // Novo campo
    $senha = $_POST['senha'];
    $role = 'sub_adm'; // Esta página registra um 'sub_adm'

    // Verificar se o email ou username já existe
    $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $message = "<div class='alert alert-danger'>Este Email ou Nome de Usuário já está registrado!</div>";
    } else {
        try {
            // Registrando o Sub-Administrador com role e username
            $stmt = $pdo->prepare("INSERT INTO sub_administradores (nome, email, username, senha, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $username, $senha, $role]);

            $_SESSION['id'] = $pdo->lastInsertId();
            $_SESSION['role'] = $role;
            header('Location: dashboard_subadmin.php');
            exit;
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao registrar: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Sub-Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Registrar Sub-Administrador</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="register_subadmin.php" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Usuário (para link)</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>