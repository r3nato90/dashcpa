<?php
$page_title = "Criar Novo Sub Administrador";
include('config/db.php');
include('config/logger.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário logado tem permissão para criar sub administradores (apenas super_adm e admin)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'super_adm' && $_SESSION['role'] !== 'admin')) {
    header('Location: index.php');
    exit;
}

$message = "";
$nome = '';
$email = '';
$comissao = '';
$admin_id = $_SESSION['role'] === 'admin' ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $comissao = (float)($_POST['comissao'] ?? 0);
    $role = 'sub_adm'; // Define a role manualmente
    
    // Se for super_adm, ele pode definir a qual admin o sub_adm pertence
    $manager_id = $_SESSION['role'] === 'super_adm' ? ($_POST['manager_id'] ?? null) : $admin_id;

    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password) || $comissao == '' || $manager_id === null) {
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
                // Insere o novo sub administrador
                $stmt = $pdo->prepare("INSERT INTO sub_administradores (nome, email, senha, role, comissao, manager_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $hashed_password, $role, $comissao, $manager_id]);

                log_acao("Novo Sub Administrador ('sub_adm') criado por " . $_SESSION['username'] . ": " . htmlspecialchars($nome) . " com email: " . htmlspecialchars($email));

                $message = "<div class='alert alert-success'>Sub Administrador **" . htmlspecialchars($nome) . "** registrado com sucesso!</div>";
                // Limpa os campos após o sucesso
                $nome = $email = $password = $confirm_password = $comissao = '';
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao registrar. Tente novamente mais tarde.</div>";
            error_log("Erro de registro de sub admin: " . $e->getMessage());
        }
    }
}

// Query para buscar administradores (para super_adm)
$managers = [];
if ($_SESSION['role'] === 'super_adm') {
    $stmt_managers = $pdo->query("SELECT id, nome FROM sub_administradores WHERE role IN ('admin', 'super_adm') ORDER BY nome");
    $managers = $stmt_managers->fetchAll();
}

// Inclui o cabeçalho com a barra lateral
include('header.php'); 
?>
<h2 class="mb-4">Criar Novo Sub Administrador</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="register_subadmin.php">
            <?php if ($_SESSION['role'] === 'super_adm'): ?>
            <div class="mb-3">
                <label for="manager_id" class="form-label">Administrador Gerente</label>
                <select class="form-select" id="manager_id" name="manager_id" required>
                    <option value="">Selecione um Administrador</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo $manager['id']; ?>" <?php echo ($manager_id == $manager['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($manager['nome'] . ' (' . $manager['id'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: // Usuário logado é 'admin', o manager_id é fixo ?>
                 <input type="hidden" name="manager_id" value="<?php echo $_SESSION['user_id']; ?>">
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
                <a href="manage_subadmins.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Criar Sub Administrador
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
include('footer.php');
?>