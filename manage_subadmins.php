<?php
session_start();
include('config/db.php');

// **** ALTERADO: Apenas 'super_adm' pode gerenciar admins ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    header('Location: login.php');
    exit;
}
// ... (o resto do arquivo permanece o mesmo) ...
include('templates/header.php');

// Mensagem de sucesso
if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    echo "<div class='container mt-3'><div class='alert alert-success'>Sub-Administrador atualizado com sucesso!</div></div>";
}

// Buscar todos os sub-administradores e administradores
$stmt = $pdo->query("SELECT * FROM sub_administradores ORDER BY nome");
$sub_admins = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Gerenciar Gerentes e Admins</h2>
        <a href="create_user.php" class="btn btn-success btn-lg">
             <i class="fas fa-plus-circle me-2"></i> Criar Novo Gerente
        </a>
    </div>

    <p class="text-muted-foreground">Painel para editar dados, comissões e permissões de gerentes (Sub-Admins) e Administradores.</p>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Permissão (Role)</th>
                        <th>Comissão (%)</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sub_admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['nome']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td>
                            <span class="badge <?php echo $admin['role'] == 'super_adm' ? 'bg-danger' : 'bg-primary'; ?>">
                                <?php echo htmlspecialchars($admin['role']); ?>
                            </span>
                        </td>
                        <td><?php echo number_format($admin['percentual_comissao'], 2, ',', '.'); ?>%</td>
                        <td>
                            <a href="edit_subadmin.php?id=<?php echo $admin['id_sub_adm']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>