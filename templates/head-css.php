<?php
// /templates/head-css.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- CORREÇÃO CRÍTICA ---
// Inclui a conexão com o banco de dados e o logger.
// Como este arquivo está em /templates/, usamos __DIR__ . '/..' para voltar à raiz.
include_once(__DIR__ . '/../config/db.php');
include_once(__DIR__ . '/../config/logger.php');
// --- FIM DA CORREÇÃO ---

// Define o título da página
$title = "Dashboard CPA - ";
if (isset($page_title)) {
    $title .= $page_title;
} else {
    $title .= "Página Inicial";
}
?>

<meta charset="utf-8" />
<title><?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="assets/images/favicon.ico"> <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" /> <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" /> ```

---

#### Arquivo 3: `login.php`
Vamos substituir o seu `login.php` antigo para que ele use o novo layout "Attex" (vindo de `teste/auth-login.php`), mas mantendo a sua lógica de PHP.

**Substitua o seu `login.php` por isto:**

```php
<?php
// /login.php
session_start();
// Se já estiver logado, redireciona para o index
if (isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

// Inclui o DB e o Logger
include('config/db.php');
include('config/logger.php');

$page_title = "Login";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 1. Tenta encontrar na tabela de usuários
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nome'];
        $_SESSION['role'] = $user['role']; // 'usuario'
        
        log_acao('Login', 'Usuário logado com sucesso.', $user['id']);
        header('Location: index.php');
        exit;
    }

    // 2. Se não encontrou, tenta na tabela de sub-administradores
    $stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha, $admin['senha'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['nome'];
        $_SESSION['role'] = $admin['role']; // 'admin', 'sub_adm' ou 'super_adm'
        
        log_acao('Login', 'Administrador logado com sucesso.', $admin['id']);
        header('Location: index.php');
        exit;
    }

    // 3. Se chegou aqui, o login falhou
    $error_message = "E-mail ou senha inválidos.";
    log_acao('Login Falhou', 'Tentativa de login com e-mail: ' . $email, null);
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <?php include 'templates/title-meta.php'; ?>
    <?php include 'templates/head-css.php'; ?>
    </head>

<body class="authentication-bg">

    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header py-4 text-center bg-primary">
                            <a href="index.php">
                                <span class="text-white fs-3">DashCPA Login</span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Entrar</h4>
                                <p class="text-muted mb-4">Digite seu e-mail e senha para acessar o painel.</p>
                            </div>

                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <form action="login.php" method="POST">

                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="email" name="email" id="email" required="" placeholder="Digite seu e-mail">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="senha" id="password" class="form-control" placeholder="Digite sua senha">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary" type="submit"> Entrar </button>
                                </div>

                            </form>
                        </div> </div>
                    </div> </div>
            </div>
        </div>
    <footer class="footer footer-alt">
        <?php echo date('Y'); ?> © DashCPA
    </footer>

    <script src="assets/js/vendor.min.js"></script> <script src="assets/js/app.js"></script> </body>
</html>