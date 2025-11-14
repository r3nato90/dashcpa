<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Apenas Gerentes (todos os níveis) podem ver esta página
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
include('templates/header.php');

// Mensagens de status
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') {
        echo "<div class='container mt-3'><div class='alert alert-success'>Usuário atualizado com sucesso!</div></div>";
    }
    if ($_GET['status'] == 'deleted') {
        echo "<div class='container mt-3'><div class='alert alert-success'>Usuário apagado com sucesso!</div></div>";
    }
}

// Query de busca (já filtrava por permissão corretamente)
$query = "
    SELECT u.*, s.nome AS nome_sub_adm 
    FROM usuarios u
    LEFT JOIN sub_administradores s ON u.id_sub_adm = s.id_sub_adm
";
$params = [];
if ($role == 'admin' || $role == 'sub_adm') {
    // Se for Admin ou Sub-Admin, mostra apenas os usuários vinculados a ele
    $query .= " WHERE u.id_sub_adm = ?";
    $params[] = $id_logado;
}
$query .= " ORDER BY u.nome";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Gerenciar Operadores</h2>
        <a href="create_user.php" class="btn btn-success btn-lg">
             <i class="fas fa-plus-circle me-2"></i> Criar Novo Operador
        </a>
    </div>

    <?php if ($role == 'super_adm'): ?>
        <p class="text-muted-foreground">Painel para editar, apagar e vincular operadores a gerentes.</p>
    <?php else: ?>
        <p class="text-muted-foreground">Painel para editar ou apagar os operadores vinculados à sua gerência.</p>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Comissão (%)</th>
                        <th>Vinculado a (Gerente)</th>
                        <th>Ações</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="5" class="text-center text-muted-foreground">Nenhum operador encontrado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo number_format($usuario['percentual_comissao'], 2, ',', '.'); ?>%</td>
                        <td><?php echo $usuario['nome_sub_adm'] ? htmlspecialchars($usuario['nome_sub_adm']) : "<span class='text-muted'>Nenhum</span>"; ?></td>
                        
                        <td>
                            <a href="edit_user.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-primary btn-sm me-2">Editar</a>
                            <a href="delete_user.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja APAGAR este usuário? Esta ação não pode ser desfeita e irá desvincular todos os relatórios dele.');">
                                Apagar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>