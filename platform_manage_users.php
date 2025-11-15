<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// Busca TODOS os usuários de TODAS as organizações
$stmt = $pdo->query("
    SELECT u.*, o.org_name 
    FROM usuarios u
    JOIN organizations o ON u.org_id = o.org_id
    ORDER BY o.org_name, u.nome
");
$all_users = $stmt->fetchAll();

include('templates/header-new.php'); // Usa o novo header
?>

<div class="container-fluid">

    <h2 class="h3 mb-4">Gerenciamento Global de Usuários (Operadores)</h2>
    
    <?php
    if (isset($_GET['status']) && $_GET['status'] == 'user_updated') {
        echo "<div class='alert alert-success'>Usuário atualizado e movido com sucesso!</div>";
    }
    ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Todos os Usuários da Plataforma</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nome do Usuário</th>
                            <th>Email</th>
                            <th>Organização (Cliente)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_users)): ?>
                            <tr><td colspan="4" class="text-center">Nenhum usuário encontrado na plataforma.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($all_users as $user): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['nome']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($user['org_name']); ?></span>
                                (ID: <?php echo $user['org_id']; ?>)
                            </td>
                            <td>
                                <a href="platform_edit_user.php?id=<?php echo $user['id_usuario']; ?>" class="btn btn-primary btn-sm">
                                    Editar / Mover
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// Usa o novo footer
include('templates/footer-new.php'); 
?>