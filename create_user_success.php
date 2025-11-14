<?php
$page_title = "Registro Concluído";
$nome = htmlspecialchars($_GET['name'] ?? 'Usuário');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashCPA - Registro Concluído</title>
    <!-- Incluindo Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Incluindo Font Awesome (Ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            margin-top: 100px;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 60px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="success-container">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h1 class="mb-3">Registro Concluído!</h1>
                    <p class="lead">Parabéns, <?php echo $nome; ?>! Sua conta foi criada com sucesso.</p>
                    <p class="mb-4">Você agora pode fazer login para acessar o sistema.</p>
                    <a href="login.php" class="btn btn-primary btn-lg">Fazer Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Incluindo Bootstrap JS (CDN) e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>