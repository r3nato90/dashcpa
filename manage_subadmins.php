<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Gerenciar Administradores";
$breadcrumb_active = "Gerenciar Admins";

// Verificação de segurança: Apenas Super Admin e Admin podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];

// Mensagens de status
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') {
        $message = "<div class='alert alert-success mt-3'>Administrador/Sub-Admin atualizado com sucesso!</div>";
    } elseif ($_GET['status'] == 'deleted') {
        $message = "<div class='alert alert-success mt-3'>Administrador/Sub-Admin apagado com sucesso!</div>";
    } elseif ($_GET['status'] == 'error_permission') {
        $message = "<div class='alert alert-danger mt-3'>Erro de Permissão: Você não pode realizar esta ação.</div>";
    }
}

// 1. Query para buscar administradores e sub-administradores
$query = "
    SELECT s.*, m.nome AS nome_manager, m.role AS role_manager 
    FROM sub_administradores s
    LEFT JOIN sub_administradores m ON s.manager_id = m.id
";
$params = [];
$where_clauses = [];

if ($role_logado == 'super_adm') {
    // Super Admin vê todos exceto a si mesmo (se for o único Super Admin)
    $where_clauses[] = "s.id != ?";
    $params[] = $id_logado;
} elseif ($role_logado == 'admin') {
    // Admin vê apenas os Sub-Admins sob sua gerência
    $where_clauses[] = "s.role = 'sub_adm'";
    $where_clauses[] = "s.manager_id = ?";
    $params[] = $id_logado;
}

// Adiciona as cláusulas WHERE
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY s.role DESC, s.nome ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$gerentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Título dinâmico
$h2_title = $role_logado == 'super_adm' ? "Gerenciar Admins e Sub-Admins" : "Gerenciar Sub-Admins";

include('header.php'); 
?>

<h2 class="mb-4"><?php echo $h2_title; ?></h2>

<!-- Botões de Ação -->
<div class="mb-4">
    <a href="register_admin.php" class="btn btn-primary <?php echo ($role_logado != 'super_adm') ? 'd-none' : ''; ?>">
         <i class="fas fa-plus-circle me-2"></i> Criar Novo Administrador (Admin)
    </a>
    <a href="register_subadmin.php" class="btn btn-secondary">
         <i class="fas fa-plus-circle me-2"></i> Criar Novo Sub-Admin
    </a>
</div>

<?php echo $message; // Exibe feedback de status ?>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Comissão (%)</th>
                    <th>Gerente Superior</th>
                    <th>Ações</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gerentes)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Nenhum administrador ou sub-admin encontrado.</td></tr>
                <?php endif; ?>
                <?php foreach ($gerentes as $gerente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($gerente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($gerente['email']); ?></td>
                    <td><span class="badge bg-<?php echo ($gerente['role'] == 'admin' ? 'warning' : 'info'); ?> text-dark">
                        <?php echo strtoupper(str_replace('_', ' ', $gerente['role'])); ?>
                    </span></td>
                    <td><?php echo number_format($gerente['comissao'], 2, ',', '.'); ?>%</td>
                    <td>
                        <?php 
                            if ($gerente['role'] === 'admin' && $gerente['manager_id'] === null) {
                                echo "<span class='text-primary'>Super Admin (Global)</span>";
                            } elseif ($gerente['manager_id'] && $gerente['role_manager']) {
                                echo htmlspecialchars($gerente['nome_manager']) . " (" . strtoupper(str_replace('_', ' ', $gerente['role_manager'])) . ")";
                            } else {
                                echo "<span class='text-muted'>N/A</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="edit_subadmin.php?id=<?php echo $gerente['id']; ?>" class="btn btn-primary btn-sm me-2">Editar</a>
                        <a href="delete_subadmin.php?id=<?php echo $gerente['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja APAGAR este Gerente? Todos os usuários e relatórios associados a ele precisarão ser reatribuídos ou serão perdidos.');">
                            Apagar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>