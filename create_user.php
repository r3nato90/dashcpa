<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Verificação Multi-Tenant
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

// --- **** 1. VERIFICAR LIMITES DO PLANO (PARA A INTERFACE) **** ---
$stmt_plan = $pdo->prepare("
    SELECT 
        o.max_users, o.max_admins,
        (SELECT COUNT(*) FROM usuarios WHERE org_id = o.org_id) as current_users,
        (SELECT COUNT(*) FROM sub_administradores WHERE org_id = o.org_id AND role IN ('admin', 'sub_adm')) as current_admins
    FROM organizations o WHERE o.org_id = ?
");
$stmt_plan->execute([$org_id]);
$plan = $stmt_plan->fetch();

$limite_usuarios_atingido = ($plan['current_users'] >= $plan['max_users']);
$limite_admins_atingido = ($plan['current_admins'] >= $plan['max_admins']);
$form_disabled = false; 
// --- **** FIM DA VERIFICAÇÃO DE LIMITE **** ---


// Mensagem de erro
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'error_email') $message = "<div class='alert alert-danger'>Erro: Este Email já está em uso.</div>";
    if ($_GET['status'] == 'error_username') $message = "<div class='alert alert-danger'>Erro: Este Nome de Usuário já está em uso.</div>";
    if ($_GET['status'] == 'limit_users') {
        $message = "<div class='alert alert-danger'>Você atingiu o limite de <strong>{$plan['max_users']}</strong> usuários (Operadores) do seu plano.</div>";
        $form_disabled = true;
    }
    if ($_GET['status'] == 'limit_admins') {
        $message = "<div class='alert alert-danger'>Você atingiu o limite de <strong>{$plan['max_admins']}</strong> gerentes (Admins/Sub-Admins) do seu plano.</div>";
        $form_disabled = true;
    }
}

// --- **** INÍCIO DA CORREÇÃO (Hierarquia N1/N2) **** ---

// Carregar lista de gerentes para o dropdown "Vincular a"
$dropdown_admins_list = [];

if ($role == 'super_adm') {
    // Super-Admin (Dono) vê todos os Admins (N1) e Sub-Admins (N2)
    $stmt_admins = $pdo->prepare("SELECT id_sub_adm, nome, role FROM sub_administradores WHERE org_id = ? AND role IN ('admin', 'sub_adm') ORDER BY nome");
    $stmt_admins->execute([$org_id]);
    $dropdown_admins_list = $stmt_admins->fetchAll();
} 
elseif ($role == 'admin') {
    // Admin (N1) vê a si mesmo E seus Sub-Admins (N2)
    $stmt_admins = $pdo->prepare("
        SELECT id_sub_adm, nome, role FROM sub_administradores 
        WHERE org_id = ? AND (id_sub_adm = ? OR parent_admin_id = ?)
        ORDER BY nome
    ");
    $stmt_admins->execute([$org_id, $id_logado, $id_logado]);
    $dropdown_admins_list = $stmt_admins->fetchAll();
}
// Sub-Adm (N2) não vê esta lista, ele só pode criar usuários (N3) vinculados a ele mesmo.

// --- **** FIM DA CORREÇÃO **** ---

include('templates/header.php');
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white"><h4>Criar Nova Conta</h4></div>
                <div class="card-body">
                    <?php echo $message; ?>
                    
                    <div class="card bg-light border-info mb-3">
                        <div class="card-body small">
                            <h6 class="card-title text-info">Limites do Plano Atual</h6>
                            <p class="mb-1">
                                <strong>Gerentes (Admin/Sub-Admin):</strong> 
                                <?php echo $plan['current_admins']; ?> / <?php echo $plan['max_admins']; ?>
                                <?php if ($limite_admins_atingido) echo "<span class='badge bg-danger ms-2'>Limite Atingido</span>"; ?>
                            </p>
                            <p class="mb-0">
                                <strong>Usuários (Operadores):</strong> 
                                <?php echo $plan['current_users']; ?> / <?php echo $plan['max_users']; ?>
                                <?php if ($limite_usuarios_atingido) echo "<span class='badge bg-danger ms-2'>Limite Atingido</span>"; ?>
                            </p>
                        </div>
                    </div>

                    <form action="process_create_user.php" method="POST">
                        <?php if ($role == 'super_adm'): ?>
                            <div class="mb-3">
                                <label for="tipo_conta" class="form-label">Tipo de Conta a Criar</label>
                                <select class="form-control" name="tipo_conta" id="tipo_conta" <?php if ($form_disabled) echo 'disabled'; ?>>
                                    <option value="usuario">Usuário (Operador N3)</option>
                                    <option value="sub_adm">Sub-Administrador (Gerente N2)</option>
                                    <option value="admin">Administrador (Gerente N1)</option>
                                </select>
                            </div>
                        <?php elseif ($role == 'admin'): ?>
                            <div class="mb-3">
                                <label for="tipo_conta" class="form-label">Tipo de Conta a Criar</label>
                                <select class="form-control" name="tipo_conta" id="tipo_conta" <?php if ($form_disabled) echo 'disabled'; ?>>
                                    <option value="usuario">Usuário (Operador N3)</option>
                                    <option value="sub_adm">Sub-Administrador (Gerente N2)</option>
                                </select>
                            </div>
                        <?php else: // Sub-Admin (N2) ?>
                            <input type="hidden" name="tipo_conta" value="usuario">
                            <h5 class="text-center">Criando Novo Usuário (Operador N3)</h5>
                            <?php if ($limite_usuarios_atingido) $form_disabled = true; ?>
                        <?php endif; ?>
                        <hr>
                        <div class="mb-3"><label for="nome" class="form-label">Nome Completo</label><input type="text" class="form-control" name="nome" required <?php if ($form_disabled) echo 'disabled'; ?>></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" required <?php if ($form_disabled) echo 'disabled'; ?>></div>
                        <div class="mb-3" id="campo_comissao"><label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="40.00" required <?php if ($form_disabled) echo 'disabled'; ?>></div>
                        <div class="mb-3" id="campo_username" style="display: none;"><label for="username" class="form-label">Nome de Usuário (Username)</label><input type="text" class="form-control" name="username" placeholder="Ex: novogerente" <?php if ($form_disabled) echo 'disabled'; ?>></div>
                        
                        <div class="mb-3" id="campo_vinculo" style="display: none;">
                            <label for="id_sub_adm" class="form-label">Vincular Usuário a (Gerente)</label>
                            <select class="form-control" name="id_sub_adm" <?php if ($form_disabled) echo 'disabled'; ?>>
                                <option value="">Nenhum (Sem vínculo)</option>
                                <?php foreach ($dropdown_admins_list as $admin) echo "<option value='{$admin['id_sub_adm']}'>" . htmlspecialchars($admin['nome']) . " (" . $admin['role'] . ")</option>"; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100" <?php if ($form_disabled) echo 'disabled'; ?>>
                            <?php if ($form_disabled) echo 'Limite do Plano Atingido'; else echo 'Criar Conta e Gerar Senha'; ?>
                        </button>
                        <a href="index.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>
<script>
$(document).ready(function() {
    var userRole = '<?php echo $role; ?>';
    var limite_usuarios_atingido = <?php echo json_encode($limite_usuarios_atingido); ?>;
    var limite_admins_atingido = <?php echo json_encode($limite_admins_atingido); ?>;
    var form_disabled = <?php echo json_encode($form_disabled); ?>;

    function toggleFields() {
        if (form_disabled) return; 

        var tipoConta = $('#tipo_conta').val();

        $('#campo_username, #campo_vinculo').hide();
        $('#campo_username input').prop('required', false);
        $('#campo_comissao, #campo_comissao input').show().prop('required', true);
        $('button[type="submit"]').prop('disabled', false).text('Criar Conta e Gerar Senha');
        
        if (tipoConta === 'usuario') {
            if (limite_usuarios_atingido) {
                $('button[type="submit"]').prop('disabled', true).text('Limite de Usuários Atingido');
            }
            // Apenas Super-Admin (Dono) e Admin (N1) veem o dropdown de vínculo
            if (userRole === 'super_adm' || userRole === 'admin') { 
                $('#campo_vinculo').show(); 
            }
            
        } else if (tipoConta === 'admin' || tipoConta === 'sub_adm') {
            if (limite_admins_atingido) {
                $('button[type="submit"]').prop('disabled', true).text('Limite de Gerentes Atingido');
            }
            $('#campo_username').show().prop('required', true);
            $('#campo_vinculo').hide(); // Vínculo de gerente (parent_id) é definido no backend
        }
    }
    toggleFields();
    $('#tipo_conta').change(toggleFields);
});
</script>