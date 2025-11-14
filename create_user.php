<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Apenas Gerentes podem criar usuários
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// Mensagem de erro
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'error_email') {
        $message = "<div class='alert alert-danger'>Erro: Este Email já está em uso no sistema.</div>";
    }
    if ($_GET['status'] == 'error_username') {
        $message = "<div class='alert alert-danger'>Erro: Este Nome de Usuário (username) já está em uso.</div>";
    }
}

// Carregar lista de gerentes (Admin/Sub-Admin) para vincular o novo OPERADOR
$admins_list = [];
if ($role == 'super_adm') {
    // Super Admin vê todos os gerentes
    $stmt_admins = $pdo->query("SELECT id_sub_adm, nome, role, username FROM sub_administradores WHERE role IN ('admin', 'sub_adm') ORDER BY nome");
    $admins_list = $stmt_admins->fetchAll();
}
// Se for Admin/Sub-Admin, ele é o único gerente de vínculo, então não precisa de lista

include('templates/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h4 class="mb-0">Criar Nova Conta</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    
                    <form action="process_create_user.php" method="POST">
                        
                        <?php if ($role == 'super_adm'): ?>
                            <div class="mb-3">
                                <label for="tipo_conta" class="form-label">Tipo de Conta a Criar</label>
                                <select class="form-select" name="tipo_conta" id="tipo_conta">
                                    <option value="usuario">Operador</option>
                                    <option value="sub_adm">Gerente (Sub-Administrador)</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        <?php elseif ($role == 'admin'): ?>
                            <div class="mb-3">
                                <label for="tipo_conta" class="form-label">Tipo de Conta a Criar</label>
                                <select class="form-select" name="tipo_conta" id="tipo_conta">
                                    <option value="usuario">Operador</option>
                                    <option value="sub_adm">Gerente (Sub-Administrador)</option>
                                </select>
                            </div>
                        <?php else: // Sub-Admin não vê opções, cria apenas 'usuario' ?>
                            <input type="hidden" name="tipo_conta" value="usuario">
                            <h5 class="text-center text-muted-foreground">Criando Novo Operador (Usuário)</h5>
                        <?php endif; ?>

                        <hr style="border-top-color: hsl(var(--border));">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3" id="campo_comissao">
                            <label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label>
                            <!-- Note: Este campo será ignorado para Operadores na nova lógica 40/10/50, mas mantido para Admins, caso o Super Admin queira dar comissão personalizada a eles -->
                            <input type="number" step="0.01" class="form-control" name="percentual_comissao" value="0.00" required>
                        </div>

                        <div class="mb-3" id="campo_username">
                            <label for="username" class="form-label">Nome de Usuário (Username)</label>
                            <input type="text" class="form-control" name="username" placeholder="Ex: novogerente (para login e link)">
                        </div>

                        <?php if ($role == 'super_adm'): ?>
                        <div class="mb-3" id="campo_vinculo">
                            <label for="id_sub_adm" class="form-label">Vincular Operador a (Gerente)</label>
                            <select class="form-select" name="id_sub_adm">
                                <option value="">Nenhum (Será vinculado diretamente ao Super Admin)</option>
                                <?php foreach ($admins_list as $admin): ?>
                                    <option value="<?php echo $admin['id_sub_adm']; ?>">
                                        <?php echo htmlspecialchars($admin['nome']) . " (" . $admin['username'] . ") - " . $admin['role']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-success w-100">Criar Conta e Gerar Senha</button>
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

    function toggleFields() {
        var tipoConta = $('#tipo_conta').val();

        // Camada de segurança para Super Admin (único que vê o dropdown)
        if (userRole === 'super_adm') {
            
            if (tipoConta === 'usuario') {
                $('#campo_username').hide();
                $('#campo_username input').prop('required', false);
                
                $('#campo_comissao input').val('0.00').prop('required', true); // Sugere 40% para Operador (embora a lógica use 40% fixo)
                
                $('#campo_vinculo').show(); 
            } else if (tipoConta === 'admin' || tipoConta === 'sub_adm') {
                $('#campo_username').show();
                $('#campo_username input').prop('required', true);
                
                $('#campo_comissao input').val('0.00').prop('required', true); // Sugere 10% para Gerente/Admin
                
                $('#campo_vinculo').hide(); // Gerentes não são vinculados a ninguém (Super Admin é o topo)
            }
        
        } else { // Admin / Sub-Admin
             // Admin/Sub-Admin sempre cria Operador (usuario)
            if (tipoConta === 'usuario') {
                $('#campo_username').hide();
                $('#campo_username input').prop('required', false);
                $('#campo_comissao input').val('0.00').prop('required', true);
                // Campo vínculo é desnecessário pois o operador é vinculado automaticamente a este admin/sub-admin
            } else { // Criando Gerente (Sub-Admin, só permitido se $role == 'admin')
                 $('#campo_username').show();
                 $('#campo_username input').prop('required', true);
                 $('#campo_comissao input').val('0.00').prop('required', true);
            }
        }
    }

    // Executa a função quando a página carrega
    toggleFields();

    // Executa a função quando o dropdown muda
    $('#tipo_conta').change(toggleFields);
});
</script>