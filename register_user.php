<?php
$page_title = "Criar Novo Usuário Comissionado";
include('config/db.php');
include('config/logger.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário logado tem permissão para criar usuários (super_adm, admin, sub_adm)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: index.php');
    exit;
}

$message = "";
$nome = '';
$email = '';
$comissao = '';
$manager_id = $_SESSION['user_id'];

// Se for super_adm, ele pode definir o manager_id (deve ser um 'admin' ou 'sub_adm')
if ($_SESSION['role'] === 'super_adm') {
    // A lista de managers (admins e sub_admins) será preenchida abaixo
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $comissao = (float)($_POST['comissao'] ?? 0);
    $role = 'usuario'; // Define a role manualmente
    $manager_id = $_POST['manager_id'] ?? $_SESSION['user_id']; // Sobrescreve se super_adm

    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password) || $comissao == '' || empty($manager_id)) {
        $message = "<div class='alert alert-danger'>Por favor, preencha todos os campos e selecione o Administrador Gerente.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $message = "<div class='alert alert-danger'>Formato de email inválido.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>As senhas não coincidem.</div>";
    } elseif ($comissao < 0 || $comissao > 100) {
        $message = "<div class='alert alert-danger'>A comissão deve ser um valor entre 0 e 100.</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Verifica se o email já existe (em qualquer tabela)
            $stmt_check = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? UNION ALL SELECT email FROM usuarios WHERE email = ?");
            $stmt_check->execute([$email, $email]);

            if ($stmt_check->fetch()) {
                $message = "<div class='alert alert-warning'>Este email já está registrado.</div>";
            } else {
                // Verifica se o manager_id é válido (existe e tem a role correta)
                $stmt_manager = $pdo->prepare("SELECT id, role FROM sub_administradores WHERE id = ? AND role IN ('super_adm', 'admin', 'sub_adm')");
                $stmt_manager->execute([$manager_id]);
                if (!$stmt_manager->fetch()) {
                     $message = "<div class='alert alert-danger'>Administrador Gerente inválido.</div>";
                } else {
                    // Insere o novo usuário
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, role, comissao, manager_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nome, $email, $hashed_password, $role, $comissao, $manager_id]);

                    log_acao("Novo usuário ('usuario') criado por " . $_SESSION['username'] . ": " . htmlspecialchars($nome) . " com email: " . htmlspecialchars($email) . " e manager_id: " . $manager_id);

                    $message = "<div class='alert alert-success'>Usuário **" . htmlspecialchars($nome) . "** registrado com sucesso!</div>";
                    // Limpa os campos após o sucesso
                    $nome = $email = $password = $confirm_password = $comissao = '';
                }
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao registrar. Tente novamente mais tarde.</div>";
            error_log("Erro de registro de usuário comissionado: " . $e->getMessage());
        }
    }
}

// Query para buscar managers (super_adm, admin, sub_adm)
$managers = [];
if ($_SESSION['role'] === 'super_adm') {
    // Super Adm vê todos
    $stmt_managers = $pdo->query("SELECT id, nome, role FROM sub_administradores WHERE role IN ('admin', 'sub_adm') ORDER BY nome");
    $managers = $stmt_managers->fetchAll();
} else {
    // Admin ou Sub Adm só veem a si mesmos como manager
    $stmt_managers = $pdo->prepare("SELECT id, nome, role FROM sub_administradores WHERE id = ?");
    $stmt_managers->execute([$_SESSION['user_id']]);
    $managers = $stmt_managers->fetchAll();
}


// Inclui o cabeçalho com a barra lateral
include('header.php'); 
?>
<h2 class="mb-4">Criar Novo Usuário Comissionado</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="register_user.php">
            <?php if ($_SESSION['role'] === 'super_adm'): ?>
            <div class="mb-3">
                <label for="manager_id" class="form-label">Administrador Gerente</label>
                <select class="form-select" id="manager_id" name="manager_id" required>
                    <option value="">Selecione um Administrador</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo $manager['id']; ?>" <?php echo (isset($manager_id) && $manager_id == $manager['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($manager['nome'] . ' (' . $manager['role'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: // Usuário logado é 'admin' ou 'sub_adm', o manager_id é fixo ?>
                 <input type="hidden" name="manager_id" value="<?php echo $_SESSION['user_id']; ?>">
                 <div class="alert alert-info">
                     Você está cadastrando este usuário sob sua gerência (ID: <?php echo $_SESSION['user_id']; ?>).
                 </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="comissao" class="form-label">Comissão Padrão (%)</label>
                <input type="number" step="0.01" class="form-control" id="comissao" name="comissao" value="<?php echo htmlspecialchars($comissao); ?>" required min="0" max="100">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="manage_users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Criar Usuário Comissionado
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
include('footer.php');
?>