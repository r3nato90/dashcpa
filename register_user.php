<?php
session_start(); 
include('config/db.php');

$id_sub_adm_ref = null;
$ref_username = "";

// Lógica 1: Captura o username de referência (ref) da URL
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $ref_username = $_GET['ref'];
    
    // Busca o ID do admin/sub-admin pelo username
    $stmt_ref = $pdo->prepare("SELECT id_sub_adm FROM sub_administradores WHERE username = ?");
    $stmt_ref->execute([$ref_username]);
    $admin_ref = $stmt_ref->fetch();
    
    if ($admin_ref) {
        $id_sub_adm_ref = $admin_ref['id_sub_adm'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $percentual_comissao = 25; // Comissão padrão
    
    $id_sub_adm = null;

    // Lógica 2: Verifica o campo manual (se o link não foi usado)
    if ($id_sub_adm_ref) {
        // Se veio pelo link, usa o ID do link
        $id_sub_adm = $id_sub_adm_ref;
    } elseif (!empty($_POST['manager_username'])) {
        // Se digitou no campo, busca pelo username
        $manager_username = $_POST['manager_username'];
        $stmt_man = $pdo->prepare("SELECT id_sub_adm FROM sub_administradores WHERE username = ?");
        $stmt_man->execute([$manager_username]);
        $manager = $stmt_man->fetch();
        if ($manager) {
            $id_sub_adm = $manager['id_sub_adm'];
        }
    }

    // Registrando o Usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, percentual_comissao, id_sub_adm) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha, $percentual_comissao, $id_sub_adm]);

    $_SESSION['id'] = $pdo->lastInsertId();
    $_SESSION['role'] = 'usuario'; 
    header('Location: dashboard_usuario.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Registrar Usuário</h4>
                <?php if($id_sub_adm_ref): ?>
                    <p class="mb-0 small">Indicação de: <?php echo htmlspecialchars($ref_username); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="register_user.php<?php echo ($ref_username ? '?ref=' . $ref_username : ''); ?>" method="POST">
                    
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

                    <?php if (!$id_sub_adm_ref): // Só mostra o campo manual se NÃO veio por link ?>
                    <div class="mb-3">
                        <label for="manager_username" class="form-label">Nome de Usuário do seu Gerente (Opcional)</label>
                        <input type="text" class="form-control" name="manager_username" placeholder="Ex: joaogerente">
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>