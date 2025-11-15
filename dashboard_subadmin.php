<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'sub_adm' || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$id_sub_adm_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') $message = "<div class='alert alert-success'>Relatório enviado com sucesso!</div>";
    elseif ($_GET['status'] == 'error_invalid_input') $message = "<div class='alert alert-danger'>Erro: Verifique os dados.</div>";
}

// **** MODIFICADO: Busca DENTRO da organização ****
$stmt_admin_user = $pdo->prepare("SELECT username FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ?"); 
$stmt_admin_user->execute([$id_sub_adm_logado, $org_id]);
$admin_user = $stmt_admin_user->fetch();
$admin_username = $admin_user ? $admin_user['username'] : '';

// **** MODIFICADO: Busca DENTRO da organização ****
$stmt_linked_users = $pdo->prepare("SELECT id_usuario, nome FROM usuarios WHERE id_sub_adm = ? AND org_id = ? ORDER BY nome");
$stmt_linked_users->execute([$id_sub_adm_logado, $org_id]);
$linked_users = $stmt_linked_users->fetchAll();

// --- Lógica do Filtro de Data e Usuário ---
$date_start = date('Y-m-d');
$date_end = date('Y-m-d');
$selected_user_id = ''; 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'filtrar') {
    if (!empty($_POST['date_start'])) $date_start = $_POST['date_start'];
    if (!empty($_POST['date_end'])) $date_end = $_POST['date_end'];
    if (isset($_POST['user_id_filter']) && !empty($_POST['user_id_filter'])) {
        $selected_user_id = $_POST['user_id_filter'];
    }
}
$date_end_query = $date_end . ' 23:59:59';

include('templates/header.php'); 
?>

<div class="container-fluid">
    <div class="alert alert-info shadow-sm">
        <strong>Seu link de registro de usuário:</strong><br>
        <input type="text" class="form-control form-control-sm mt-1" value="<?php echo $site_url; ?>/register_user.php?ref=<?php echo $admin_username; ?>" readonly>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-12">
            <h3>Enviar Relatório (Sub-Admin)</h3>
            <?php echo $message; ?>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <form action="process_transaction.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Selecione o Usuário Vinculado</label>
                        <select class="form-control" name="usuario_id" required>
                            <option value="">Escolha um usuário...</option>
                            <?php foreach ($linked_users as $user) echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <input type="hidden" name="data_relatorio" value="<?php echo date('Y-m-d'); ?>">
                    <div class="mb-3"><label for="deposito" class="form-label">DEPÓSITO</label><input type="number" step="0.01" class="form-control" name="deposito" required></div>
                    <div class="mb-3"><label for="saque" class="form-label">SAQUE</label><input type="number" step="0.01" class="form-control" name="saque" required></div>
                    <div class="mb-3"><label for="bau" class="form-label">BAÚ (Saldo Final)</label><input type="number" step="0.01" class="form-control" name="bau" required></div>
                    <button type="submit" class="btn btn-success w-100" <?php echo (empty($linked_users)) ? 'disabled' : ''; ?>>
                        <?php echo (empty($linked_users)) ? 'Vincule um usuário primeiro' : 'Enviar Relatório'; ?>
                    </button>
                </form>
            </div></div>
        </div>
        <div class="col-lg-7 col-md-12">
            <h3>Relatórios dos Seus Usuários</h3>
            <form action="dashboard_subadmin.php" method="POST" class="mb-3 p-3 border rounded bg-light shadow-sm">
                <input type="hidden" name="action" value="filtrar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4"><label for="date_start" class="form-label small mb-1">De:</label><input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>"></div>
                    <div class="col-md-4"><label for="date_end" class="form-label small mb-1">Até:</label><input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>"></div>
                    <div class="col-md-4"><label for="user_id_filter" class="form-label small mb-1">Filtrar Usuário:</label>
                         <select name="user_id_filter" id="user_id_filter" class="form-control form-control-sm">
                            <option value="">Todos os Usuários</option>
                            <?php foreach ($linked_users as $user): ?>
                                <option value="<?php echo $user['id_usuario']; ?>" <?php echo ($user['id_usuario'] == $selected_user_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2"><button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button></div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Usuário</th><th>Data</th><th>Depósito</th><th>Saque</th><th>Baú</th>
                            <th>Lucro Total</th><th>Com. Usuário</th><th>Sua Comissão</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // **** MODIFICADO: Adiciona AND r.org_id = ? ****
                            $query_reports = "
                                SELECT r.*, u.nome AS nome_usuario FROM relatorios r 
                                JOIN usuarios u ON r.id_usuario = u.id_usuario
                                WHERE u.id_sub_adm = ? AND r.org_id = ? AND (r.data BETWEEN ? AND ?)
                            ";
                            $params_reports = [$id_sub_adm_logado, $org_id, $date_start, $date_end_query];
                            if (!empty($selected_user_id)) {
                                $query_reports .= " AND r.id_usuario = ?";
                                $params_reports[] = $selected_user_id; 
                            }
                            $query_reports .= " ORDER BY r.data DESC";
                            $stmt_reports = $pdo->prepare($query_reports);
                            $stmt_reports->execute($params_reports); 
                            $relatorios = $stmt_reports->fetchAll();

                            $total_lucro_geral = 0; $total_comissao_usuario = 0; $total_comissao_admin = 0;
                            if (count($relatorios) == 0) {
                                echo "<tr><td colspan='8' class='text-center'>Nenhum relatório encontrado.</td></tr>";
                            } else {
                                foreach ($relatorios as $r) {
                                    $total_lucro_geral += $r['lucro_diario'];
                                    $total_comissao_usuario += $r['comissao_usuario'];
                                    $total_comissao_admin += $r['comissao_sub_adm'];
                                    echo "<tr>
                                            <td>" . htmlspecialchars($r['nome_usuario']) . "</td>
                                            <td>" . date('d/m/Y H:i', strtotime($r['data'])) . "</td>
                                            <td>R$ " . number_format($r['valor_deposito'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($r['valor_saque'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($r['valor_bau'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($r['lucro_diario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($r['comissao_usuario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($r['comissao_sub_adm'], 2, ',', '.') . "</td> 
                                          </tr>";
                                }
                            }
                        ?>
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr>
                            <td colspan="5" class="text-end"><strong>TOTAIS:</strong></td>
                            <td class="text-success fw-bold">R$ <?php echo number_format($total_lucro_geral, 2, ',', '.'); ?></td>
                            <td class="text-success fw-bold">R$ <?php echo number_format($total_comissao_usuario, 2, ',', '.'); ?></td>
                            <td class="text-success fw-bold">R$ <?php echo number_format($total_comissao_admin, 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>