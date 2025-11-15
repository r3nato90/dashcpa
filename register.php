<?php
session_start(); // <-- ADICIONADO
include('config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $role = $_POST['role'];

    // Limpar mensagens de erro/sucesso antigas
    $message = "";

    try {
        if ($role == 'admin' || $role == 'sub_adm') {
            // Verificar se o email já existe
            $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "<div class='alert alert-danger'>Este email já está registrado!</div>";
            } else {
                // Inserir na tabela sub_administradores
                $stmt = $pdo->prepare("INSERT INTO sub_administradores (nome, email, senha, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $senha, $role]);
                
                $_SESSION['id'] = $pdo->lastInsertId();
                $_SESSION['role'] = $role;
                
                if ($role == 'admin') {
                    header('Location: dashboard_admin.php');
                } else {
                    header('Location: dashboard_subadmin.php');
                }
                exit;
            }
        } elseif ($role == 'usuario') {
            // Verificar se o email já existe
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "<div class='alert alert-danger'>Este email já está registrado!</div>";
            } else {
                // Inserir na tabela usuarios
                $percentual_comissao = 25; // Exemplo
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, percentual_comissao) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $senha, $percentual_comissao]);
                
                $_SESSION['id'] = $pdo->lastInsertId();
                $_SESSION['role'] = 'usuario';
                header('Location: dashboard_usuario.php');
                exit;
            }
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
    <title>Registrar - Sistema Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Registro</h4>
            </div>
            <div class="card-body">
                
                <?php if (!empty($message)) echo $message; // Exibe a mensagem de erro aqui ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Função</label>
                        <select class="form-control" name="role">
                            <option value="admin">Administrador</option>
                            <option value="sub_adm">Sub-Administrador</option>
                            <option value="usuario">Usuário</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>