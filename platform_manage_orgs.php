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

// --- 1. BUSCAR TODAS AS ORGANIZAÇÕES (Clientes) ---
// **** MODIFICADO: Adicionado LEFT JOIN para buscar o nome do Super Admin (Responsável) ****
$stmt_orgs = $pdo->query("
    SELECT 
        o.*, 
        (SELECT COUNT(*) FROM usuarios WHERE org_id = o.org_id) as current_users,
        (SELECT COUNT(*) FROM sub_administradores WHERE org_id = o.org_id AND role IN ('admin', 'sub_adm')) as current_admins,
        s.nome AS super_admin_nome, 
        s.email AS super_admin_email
    FROM organizations o
    LEFT JOIN sub_administradores s ON o.super_admin_id = s.id_sub_adm
    ORDER BY o.org_name
");
$organizations = $stmt_orgs->fetchAll();

// --- 2. BUSCAR USUÁRIOS E GERENTES (Para os Detalhes) ---
$stmt_users = $pdo->query("SELECT id_usuario, org_id, nome, email FROM usuarios ORDER BY nome");
$users_list = $stmt_users->fetchAll();
$users_by_org = [];
foreach ($users_list as $user) {
    $users_by_org[$user['org_id']][] = $user;
}

$stmt_admins = $pdo->query("SELECT id_sub_adm, org_id, nome, email, role FROM sub_administradores WHERE role IN ('admin', 'sub_adm', 'super_adm') ORDER BY nome");
$admins_list = $stmt_admins->fetchAll();
$admins_by_org = [];
foreach ($admins_list as $admin) {
    if ($admin['role'] == 'platform_owner') continue;
    $admins_by_org[$admin['org_id']][] = $admin;
}

// --- 3. **** NOVO: Buscar Super Admins (Para o Dropdown do Modal) **** ---
$stmt_super_admins = $pdo->query("
    SELECT id_sub_adm, nome, email 
    FROM sub_administradores 
    WHERE role = 'super_adm' 
    ORDER BY nome
");
$all_super_admins = $stmt_super_admins->fetchAll();


include('templates/header-new.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Gerenciar Clientes (Organizações)</h2>
        <button type="button" class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarOrg">
            <i class="fas fa-plus-circle me-2"></i> Criar Nova Organização
        </button>
    </div>

    <?php
    // Mensagens de status (incluindo as de exclusão)
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'org_created') echo "<div class='alert alert-success'>Organização criada com sucesso!</div>";
        if ($_GET['status'] == 'org_updated') echo "<div class='alert alert-success'>Organização atualizada com sucesso!</div>";
        if ($_GET['status'] == 'status_updated') echo "<div class='alert alert-success'>Status da organização atualizado!</div>";
        if ($_GET['status'] == 'user_updated') echo "<div class='alert alert-success'>Usuário atualizado/movido com sucesso!</div>";
        if ($_GET['status'] == 'error') echo "<div class='alert alert-danger'>Ocorreu um erro.</div>";
        
        if ($_GET['status'] == 'org_deleted') echo "<div class='alert alert-success'>Organização e todos os dados associados foram apagados com sucesso.</div>";
        if ($_GET['status'] == 'error_self_delete') echo "<div class='alert alert-danger'>Erro: Você não pode apagar sua própria organização.</div>";
        if ($_GET['status'] == 'error_delete_failed') echo "<div class='alert alert-danger'>Erro ao apagar a organização.</div>";
    }
    ?>

    <div class="accordion" id="accordionOrgs">
        <?php foreach ($organizations as $org): ?>
            <div class="card shadow-sm mb-2"> <div class="card-header p-0" id="heading-<?php echo $org['org_id']; ?>">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-start w-100 p-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $org['org_id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $org['org_id']; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary" style="font-size: 1.2rem;"><?php echo htmlspecialchars($org['org_name']); ?></strong>
                                    <small class="text-muted">(ID: <?php echo $org['org_id']; ?>)</small>
                                </div>
                                <div>
                                    <?php 
                                    $status_class = 'bg-success'; // Ativo
                                    if ($org['status'] == 'inactive') $status_class = 'bg-secondary';
                                    if ($org['status'] == 'suspended') $status_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?> me-2"><?php echo htmlspecialchars($org['status']); ?></span>
                                    
                                    <span class="badge bg-info me-2">Gerentes: <?php echo $org['current_admins']; ?> / <?php echo $org['max_admins']; ?></span>
                                    <span class="badge bg-info me-2">Operadores: <?php echo $org['current_users']; ?> / <?php echo $org['max_users']; ?></span>
                                </div>
                            </div>
                        </button>
                    </h2>
                </div>

                <div id="collapse-<?php echo $org['org_id']; ?>" class="collapse" aria-labelledby="heading-<?php echo $org['org_id']; ?>" data-bs-parent="#accordionOrgs">
                    <div class="card-body">
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                    data-bs-target="#modalEditarOrg" 
                                    data-org-id="<?php echo $org['org_id']; ?>"
                                    data-org-name="<?php echo htmlspecialchars($org['org_name']); ?>"
                                    data-org-cpf-cnpj="<?php echo htmlspecialchars($org['cpf_cnpj']); ?>"
                                    data-org-super-admin-id="<?php echo $org['super_admin_id']; ?>"
                                    data-max-admins="<?php echo $org['max_admins']; ?>"
                                    data-max-users="<?php echo $org['max_users']; ?>">
                                Editar Empresa/Limites
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                    data-bs-target="#modalEditarStatus" data-org-id="<?php echo $org['org_id']; ?>"
                                    data-org-name="<?php echo htmlspecialchars($org['org_name']); ?>"
                                    data-org-status="<?php echo $org['status']; ?>">
                                Editar Status (Pagamento)
                            </button>

                            <?php 
                            if ($org['org_id'] != $_SESSION['org_id']): 
                            ?>
                                <a href="platform_delete_org.php?id=<?php echo $org['org_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('ATENÇÃO: Excluir a organização \'<?php echo htmlspecialchars(addslashes($org['org_name'])); ?>\' é IRREVERSÍVEL.\n\nTodos os dados desta empresa serão permanentemente apagados.\n\nDeseja continuar?');">
                                    <i class="fas fa-trash-alt"></i> Excluir Empresa
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($org['super_admin_nome']): ?>
                        <div class="alert alert-light">
                            <strong>Responsável (Super Admin):</strong> <?php echo htmlspecialchars($org['super_admin_nome']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($org['super_admin_email']); ?>
                        </div>
                        <?php endif; ?>

                        <hr>
                        
                        <h5 class="h6">Gerentes (Super Admins, Admins, Sub-Admins)</h5>
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr><th>Nome</th><th>Email</th><th>Role</th><th>Ações</th></tr>
                            </thead>
                            <tbody>
                                <?php if (isset($admins_by_org[$org['org_id']])): ?>
                                    <?php foreach ($admins_by_org[$org['org_id']] as $admin): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($admin['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo $admin['role']; ?></span></td>
                                            <td>
                                                <a href="platform_edit_subadmin.php?id=<?php echo $admin['id_sub_adm']; ?>" class="btn btn-outline-secondary btn-sm">Editar Gerente</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">Nenhum gerente encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <h5 class="h6 mt-4">Operadores (Usuários)</h5>
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr><th>Nome</th><th>Email</th><th>Ações</th></tr>
                            </thead>
                            <tbody>
                                <?php if (isset($users_by_org[$org['org_id']])): ?>
                                    <?php foreach ($users_by_org[$org['org_id']] as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <a href="platform_edit_user.php?id=<?php echo $user['id_usuario']; ?>" class="btn btn-outline-secondary btn-sm">Editar Usuário</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">Nenhum operador encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>

<div class="modal fade" id="modalCriarOrg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Criar Nova Organização (Cliente)</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <form action="platform_create_org.php" method="POST">
                    <div class="mb-3"><label for="org_name" class="form-label">Nome da Organização</label><input type="text" class="form-control" name="org_name" required></div>
                    <div class="mb-3"><label for="plan_type" class="form-label">Plano</label><select name="plan_type" class="form-control"><option value="pro">Pro</option><option value="basic">Básico</option></select></div>
                    <div class="row">
                        <div class="col-6"><label for="max_admins" class="form-label">Limite de Admins</label><input type="number" class="form-control" name="max_admins" value="1" required></div>
                        <div class="col-6"><label for="max_users" class="form-label">Limite de Usuários</label><input type="number" class="form-control" name="max_users" value="5" required></div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-success w-100">Salvar Organização</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarOrg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Organização: <span id="editOrgNameTitle"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <form action="platform_edit_org.php" method="POST">
                    <input type="hidden" id="editOrgId" name="org_id">
                    <div class="mb-3"><label for="org_name" class="form-label">Nome da Organização</label><input type="text" class="form-control" id="editOrgNameField" name="org_name" required></div>
                    
                    <div class="mb-3"><label for="cpf_cnpj" class="form-label">CPF / CNPJ</label><input type="text" class="form-control" id="editCpfCnpj" name="cpf_cnpj"></div>
                    
                    <div class="mb-3">
                        <label for="super_admin_id" class="form-label">Responsável (Super Admin)</label>
                        <select class="form-control" id="editSuperAdminId" name="super_admin_id">
                            <option value="">Nenhum (Desvincular)</option>
                            <?php foreach ($all_super_admins as $sa): ?>
                                <option value="<?php echo $sa['id_sub_adm']; ?>">
                                    <?php echo htmlspecialchars($sa['nome']); ?> (<?php echo htmlspecialchars($sa['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr><p><strong>Limites do Plano:</strong></p>
                    <div class="row">
                        <div class="col-6"><label for="max_admins" class="form-label">Limite de Admins</label><input type="number" class="form-control" id="editMaxAdmins" name="max_admins" required></div>
                        <div class="col-6"><label for="max_users" class="form-label">Limite de Usuários</label><input type="number" class="form-control" id="editMaxUsers" name="max_users" required></div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Status: <span id="statusOrgNameTitle"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <form action="platform_update_status.php" method="POST">
                    <input type="hidden" id="statusOrgId" name="org_id">
                    <p>Ao definir como 'Inativo' ou 'Suspenso', todos os usuários desta organização serão impedidos de fazer login.</p>
                    <div class="mb-3">
                        <label for="new_status" class="form-label">Status de Pagamento</label>
                        <select class="form-control" id="statusOrgStatus" name="new_status">
                            <option value="active">Ativo (Acesso liberado)</option>
                            <option value="inactive">Inativo (Acesso bloqueado)</option>
                            <option value="suspended">Suspenso (Acesso bloqueado)</option>
                        </select>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-warning w-100">Salvar Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include('templates/footer-new.php'); 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal 1: Editar Limites/Nome
    var editModal = document.getElementById('modalEditarOrg');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            // Pega os dados dos atributos data-*
            var orgId = button.getAttribute('data-org-id');
            var orgName = button.getAttribute('data-org-name');
            var orgCpfCnpj = button.getAttribute('data-org-cpf-cnpj');
            var orgSuperAdminId = button.getAttribute('data-org-super-admin-id');
            var maxAdmins = button.getAttribute('data-max-admins');
            var maxUsers = button.getAttribute('data-max-users');
            
            // Busca os campos do modal
            var modalTitle = editModal.querySelector('.modal-title #editOrgNameTitle');
            var modalOrgIdInput = editModal.querySelector('.modal-body #editOrgId');
            var modalOrgNameInput = editModal.querySelector('.modal-body #editOrgNameField');
            var modalCpfCnpjInput = editModal.querySelector('.modal-body #editCpfCnpj');
            var modalSuperAdminInput = editModal.querySelector('.modal-body #editSuperAdminId');
            var modalMaxAdminsInput = editModal.querySelector('.modal-body #editMaxAdmins');
            var modalMaxUsersInput = editModal.querySelector('.modal-body #editMaxUsers');
            
            // Preenche os campos do modal
            if (modalTitle) modalTitle.textContent = orgName;
            if (modalOrgIdInput) modalOrgIdInput.value = orgId;
            if (modalOrgNameInput) modalOrgNameInput.value = orgName;
            if (modalCpfCnpjInput) modalCpfCnpjInput.value = orgCpfCnpj;
            if (modalSuperAdminInput) modalSuperAdminInput.value = orgSuperAdminId;
            if (modalMaxAdminsInput) modalMaxAdminsInput.value = maxAdmins;
            if (modalMaxUsersInput) modalMaxUsersInput.value = maxUsers;
        });
    }

    // Modal 2: Editar Status (sem alterações)
    var statusModal = document.getElementById('modalEditarStatus');
    if (statusModal) {
        statusModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var orgId = button.getAttribute('data-org-id');
            var orgName = button.getAttribute('data-org-name');
            var orgStatus = button.getAttribute('data-org-status');
            
            var modalTitle = statusModal.querySelector('.modal-title #statusOrgNameTitle');
            var modalOrgIdInput = statusModal.querySelector('.modal-body #statusOrgId');
            var modalOrgStatusInput = statusModal.querySelector('.modal-body #statusOrgStatus');

            if (modalTitle) modalTitle.textContent = orgName;
            if (modalOrgIdInput) modalOrgIdInput.value = orgId;
            if (modalOrgStatusInput) modalOrgStatusInput.value = orgStatus;
        });
    }
});
</script>