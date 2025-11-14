<?php
session_start();

// Se o usuário já estiver logado, redireciona para o dashboard correto
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'super_adm') {
        header('Location: dashboard_superadmin.php');
        exit;
    } elseif ($_SESSION['role'] == 'admin') {
        header('Location: dashboard_admin.php');
        exit;
    } elseif ($_SESSION['role'] == 'sub_adm') {
        header('Location: dashboard_subadmin.php');
        exit;
    } elseif ($_SESSION['role'] == 'usuario') {
        header('Location: dashboard_usuario.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Bem-vindo ao Sistema de Dashboard</h4>
            </div>
            <div class="card-body">
                <p class="text-center">Faça login para acessar o painel.</p>
                <div class="d-flex justify-content-between">
                    <a href="login.php" class="btn btn-primary w-100">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>