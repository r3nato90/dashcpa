<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// **** INÍCIO DA LÓGICA DE FILTRO ****
$filtro_org_id = $_POST['filtro_org_id'] ?? '';
$filtro_nome = $_POST['filtro_nome'] ?? '';
$filtro_email = $_POST['filtro_email'] ?? '';
$filtro_role = $_POST['filtro_role'] ?? ''; // Novo filtro

$all_admins = [];
$all_users = [];
$is_post = $_SERVER["REQUEST_METHOD"] == "POST"; // Verifica se um filtro foi aplicado

// Só executa as queries se um filtro foi aplicado (requisição POST)
if ($is_post) {
    
    // --- Query 1: Gerentes (Admins, Sub-Admins, Super-Admins) ---
    // (Só busca Gerentes se o filtro de role for 'gerentes' ou 'todos')
    if ($filtro_role == 'gerentes' || $filtro_role == '') {
        $params_admins = [];
        $query_admins = "
            SELECT s.*, o.org_name 
            FROM sub_administradores s
            JOIN organizations o ON s.org_id = o.org_id
            WHERE s.role != 'platform_owner'
        ";
        if (!empty($filtro_org_id)) {
            $query_admins .= " AND s.org_id = ?";
            $params_admins[] = $filtro_org_id;
        }
        if (!empty($filtro_nome)) {
            $query_admins .= " AND s.nome LIKE ?";
            $params_admins[] = '%' . $filtro_nome . '%';
        }
        if (!empty($filtro_email)) {
            $query_admins .= " AND s.email LIKE ?";
            $params_admins[] = '%' . $filtro_email . '%';
        }
        $query_admins .= " ORDER BY o.org_name, s.nome";
        $stmt_admins = $pdo->prepare($query_admins);
        $stmt_admins->execute($params_admins);
        $all_admins = $stmt_admins->fetchAll();
    }

    // --- Query 2: Operadores (Usuários) ---
    // (Só busca Usuários se o filtro de role for 'usuario' ou 'todos')
    if ($filtro_role == 'usuario' || $filtro_role == '') {
        $params_users = [];
        $query_users = "
            SELECT u.*, o.org_name 
            FROM usuarios u
            JOIN organizations o ON u.org_id = o.org_id
            WHERE 1=1
        ";
        if (!empty($filtro_org_id)) {
            $query_users .= " AND u.org_id = ?";
            $params_users[] = $filtro_org_id;
        }
        if (!empty($filtro_nome)) {
            $query_users .= " AND u.nome LIKE ?";
            $params_users[] = '%' . $filtro_nome . '%';
        }
        if (!empty($filtro_email)) {
            $query_users .= " AND u.email LIKE ?";
            $params_users[] = '%' . $filtro_email . '%';
        }
        $query_users .= " ORDER BY o.org_name, u.nome";
        $stmt_users = $pdo->prepare($query_users);
        $stmt_users->execute($params_users);
        $all_users = $stmt_users->fetchAll();
    }
}
// **** FIM DA LÓGICA DE FILTRO ****

// Busca organizações para o modal E para o filtro
$stmt_orgs = $pdo->query("SELECT org_id, org_name FROM organizations ORDER BY org_name");
$organizations = $stmt_orgs->fetchAll();

include('templates/header-new.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Gerenciamento Global de Contas</h2>
        <button type="button" class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
            <i class="fas fa-plus-circle me-2"></i> Adicionar Nova Conta
        </button>
    </div>
    
    <?php
    // Mensagens de Sucesso/Erro
    if (isset($_GET['status']) && $_GET['status'] == 'user_updated') {
        echo "<div class='alert alert-success'>Usuário atualizado e movido com sucesso!</div>";
    }
    if (isset($_SESSION['new_user_details'])) {
        $details = $_SESSION['new_user_details'];
        unset($_SESSION['new_user_details']);
        echo "<div class='alert alert-warning'>
                <strong>Conta Criada com Sucesso!</strong><br>
                Anote a senha gerada para '{$details['nome']}' (Email: {$details['email']}):<br>
                <strong>Senha: {$details['senha']}</strong>
              </div>";
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtrar Contas</h5>
        </div>
        <div class="card-body">
            <form action="platform_manage_users.php" method="POST">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filtro_org_id" class="form-label">Filtrar por Empresa</label>
                        <select class="form-control" name="filtro_org_id">
                            <option value="">Todas as Empresas</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['org_id']; ?>" <?php echo ($filtro_org_id == $org['org_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($org['org_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_nome" class="form-label">Filtrar por Nome</label>
                        <input type="text" class="form-control" name="filtro_nome" value="<?php echo htmlspecialchars($filtro_nome); ?>" placeholder="Buscar por nome...">
                    </div>
                    <div class="col-md-2">
                        <label for="filtro_email" class="form-label">Filtrar por Email</label>
                        <input type="text" class="form-control" name="filtro_email" value="<?php echo htmlspecialchars($filtro_email); ?>" placeholder="Buscar por email...">
                    </div>
                    <div class="col-md-2">
                        <label for="filtro_role" class="form-label">Tipo de Conta</label>
                        <select class="form-control" name="filtro_role">
                            <option value="" <?php echo ($filtro_role == '') ? 'selected' : ''; ?>>Todos os Tipos</option>
                            <option value="gerentes" <?php echo ($filtro_role == 'gerentes') ? 'selected' : ''; ?>>Gerentes (Todos)</option>
                            <option value="usuario" <?php echo ($filtro_role == 'usuario') ? 'selected' : ''; ?>>Operador (Usuário)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$is_post): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i> Use os filtros acima para buscar contas.
        </div>
    <?php else: ?>

        <?php if ($filtro_role == 'gerentes' || $filtro_role == ''): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Gerentes (Super Admins, Admins, Sub-Admins)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Organização (Cliente)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_admins)): ?>
                                <tr><td colspan="5" class="text-center">Nenhum gerente encontrado com este filtro.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($all_admins as $admin): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($admin['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($admin['role']); ?></span></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($admin['org_name']); ?></span>
                                    (ID: <?php echo $admin['org_id']; ?>)
                                </td>
                                <td>
                                    <a href="platform_edit_subadmin.php?id=<?php echo $admin['id_sub_adm']; ?>" class="btn btn-primary btn-sm">
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
        <?php endif; // Fim do if (filtro_role gerentes) ?>

        <?php if ($filtro_role == 'usuario' || $filtro_role == ''): ?>
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Operadores (Usuários)</h5>
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
                                <tr><td colspan="4" class="text-center">Nenhum usuário encontrado com este filtro.</td></tr>
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
        <?php endif; // Fim do if (filtro_role usuario) ?>
    
    <?php endif; // Fim do if (is_post) ?>
    </div>

<div class="modal fade" id="modalCriarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Criar Nova Conta Global</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <p>Este formulário cria um usuário e gera uma senha aleatória. Você define a qual organização e qual o papel dele.</p>
                <form action="platform_create_user.php" method="POST">
                    <div class="mb-3"><label for="nome" class="form-label">Nome Completo</label><input type="text" class="form-control" name="nome" required></div>
                    <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                    <hr>
                    <div class="mb-3">
                        <label for="org_id" class="form-label">Vincular à Organização (Empresa)</label>
                        <select class="form-control" name="org_id" required>
                            <option value="">Selecione a empresa...</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['org_id']; ?>"><?php echo htmlspecialchars($org['org_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_conta" class="form-label">Papel (Role)</label>
                        <select class="form-control" name="tipo_conta" id="tipo_conta" required>
                            <option value="usuario">Usuário (Operador)</option>
                            <option value="sub_adm">Sub-Admin (Gerente N2)</option>
                            <option value="admin">Admin (Gerente N1)</option>
                            <option value="super_adm">Super-Admin (Dono da Empresa)</option>
                        </select>
                    </div>
                    <div class="mb-3"><label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="0.00" required></div>
                    
                    <button type="submit" class="btn btn-success w-100">Criar Conta e Gerar Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include('templates/footer-new.php'); 
?>