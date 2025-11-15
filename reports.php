<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

$usuarios_list = []; $admins_list = []; $relatorios = [];
$filtros_aplicados = ['date_start' => date('Y-m-d'), 'date_end' => date('Y-m-d'), 'user_ids' => [], 'admin_id' => ''];

// ... (Lógica de carregar filtros salvos ou POST) ...
if (isset($_GET['report_id'])) {
    $report_id = (int)$_GET['report_id'];
    $query_saved = "SELECT * FROM saved_reports WHERE id_report_salvo = ? AND org_id = ?";
    $params_saved = [$report_id, $org_id];
    if ($role != 'super_adm') {
        $query_saved .= " AND id_salvo_por = ?"; 
        $params_saved[] = $id_logado;
    }
    $stmt_saved = $pdo->prepare($query_saved);
    $stmt_saved->execute($params_saved);
    $saved_report = $stmt_saved->fetch();
    if ($saved_report) $filtros_aplicados = json_decode($saved_report['filtros'], true);
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'filtrar') {
    $filtros_aplicados['date_start'] = $_POST['date_start']; 
    $filtros_aplicados['date_end'] = $_POST['date_end'];
    $filtros_aplicados['user_ids'] = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];
    // Apenas Super-Admin e Admin (N1) podem filtrar por gerente
    if (in_array($role, ['super_adm', 'admin']) && isset($_POST['admin_id'])) {
        $filtros_aplicados['admin_id'] = $_POST['admin_id'];
    }
}
// ... (Fim da lógica de filtro) ...


// --- Listar Usuários e Gerentes para os Filtros (DENTRO DA ORG) ---
if ($role == 'super_adm') {
    // Super-Adm vê Admins (N1) e Sub-Admins (N2)
    $stmt_admins = $pdo->prepare("SELECT id_sub_adm, nome, username FROM sub_administradores WHERE org_id = ? AND role IN ('admin', 'sub_adm') ORDER BY nome");
    $stmt_admins->execute([$org_id]);
    $admins_list = $stmt_admins->fetchAll();
    
    // Super-Adm vê todos os usuários
    $query_users = "SELECT id_usuario, nome FROM usuarios WHERE org_id = ?";
    $params_users = [$org_id];
    if (!empty($filtros_aplicados['admin_id'])) {
        $query_users .= " AND id_sub_adm = ?"; $params_users[] = $filtros_aplicados['admin_id'];
    }
    $query_users .= " ORDER BY nome";
    $stmt_users = $pdo->prepare($query_users);
    $stmt_users->execute($params_users);
    $usuarios_list = $stmt_users->fetchAll();
} 
elseif ($role == 'admin') {
     // Admin (N1) vê seus Sub-Admins (N2)
    $stmt_admins = $pdo->prepare("SELECT id_sub_adm, nome, username FROM sub_administradores WHERE org_id = ? AND role = 'sub_adm' AND parent_admin_id = ? ORDER BY nome");
    $stmt_admins->execute([$org_id, $id_logado]);
    $admins_list = $stmt_admins->fetchAll(); // Lista de Sub-Admins filhos

    // Admin (N1) vê Usuários (N3) ligados aos seus N2 E os ligados a ele mesmo (N1)
    $query_users = "SELECT u.id_usuario, u.nome FROM usuarios u 
                    LEFT JOIN sub_administradores s ON u.id_sub_adm = s.id_sub_adm
                    WHERE u.org_id = ? AND (s.parent_admin_id = ? OR u.id_sub_adm = ?)";
    $params_users = [$org_id, $id_logado, $id_logado];
    
    if (!empty($filtros_aplicados['admin_id'])) { // Se o Admin (N1) filtrou por um Sub-Adm (N2)
        $query_users = "SELECT id_usuario, nome FROM usuarios WHERE org_id = ? AND id_sub_adm = ?";
        $params_users = [$org_id, $filtros_aplicados['admin_id']];
    }
    $query_users .= " ORDER BY nome";
    $stmt_users = $pdo->prepare($query_users);
    $stmt_users->execute($params_users);
    $usuarios_list = $stmt_users->fetchAll();
}
else { // sub_adm (N2)
    // Sub-Adm (N2) vê apenas seus Usuários (N3)
    $stmt_users = $pdo->prepare("SELECT id_usuario, nome FROM usuarios WHERE id_sub_adm = ? AND org_id = ? ORDER BY nome");
    $stmt_users->execute([$id_logado, $org_id]);
    $usuarios_list = $stmt_users->fetchAll();
}

// --- Construção da Query do Relatório ---
$query = "SELECT r.*, u.nome AS nome_usuario, u.id_sub_adm 
          FROM relatorios r 
          JOIN usuarios u ON r.id_usuario = u.id_usuario 
          WHERE r.org_id = ? AND (r.data BETWEEN ? AND ?)";
$date_end_query = date('Y-m-d', strtotime($filtros_aplicados['date_end'] . ' +1 day'));
$params = [$org_id, $filtros_aplicados['date_start'], $date_end_query];

// **** CORREÇÃO (Hierarquia N1->N2->N3) ****
if ($role == 'sub_adm') {
    // Sub-Adm (N2) vê relatórios dos seus usuários (N3)
    $query .= " AND u.id_sub_adm = ?"; $params[] = $id_logado;
} 
elseif ($role == 'admin') {
    // Admin (N1) vê relatórios de usuários (N3) ligados aos seus N2 E os ligados a ele mesmo
    $query .= " AND (u.id_sub_adm = ? OR u.id_sub_adm IN (SELECT id_sub_adm FROM sub_administradores WHERE parent_admin_id = ?))";
    $params[] = $id_logado;
    $params[] = $id_logado;
}
// Super-Adm (Dono) não precisa de filtro de ID

// Filtro de dropdown (N1 ou N2)
if (($role == 'super_adm' || $role == 'admin') && !empty($filtros_aplicados['admin_id'])) {
    $query .= " AND u.id_sub_adm = ?"; $params[] = $filtros_aplicados['admin_id'];
}
// Filtro de usuário (N3)
if (!empty($filtros_aplicados['user_ids'])) {
    $in_placeholders = implode(',', array_fill(0, count($filtros_aplicados['user_ids']), '?'));
    $query .= " AND r.id_usuario IN ($in_placeholders)"; $params = array_merge($params, $filtros_aplicados['user_ids']);
}
$query .= " ORDER BY r.data DESC";
$stmt_report = $pdo->prepare($query);
$stmt_report->execute($params);
$relatorios = $stmt_report->fetchAll();

// --- Cálculo dos Totais (Com comissao_admin) ---
$totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0, 'lucro_super_admin' => 0];
foreach ($relatorios as $r) {
    $totais['deposito'] += $r['valor_deposito']; 
    $totais['saque'] += $r['valor_saque']; 
    $totais['bau'] += $r['valor_bau'];
    $totais['lucro'] += $r['lucro_diario']; 
    $totais['comissao_user'] += $r['comissao_usuario']; 
    $totais['comissao_sub'] += $r['comissao_sub_adm'];
    $totais['comissao_admin'] += $r['comissao_admin'];
    // Lucro líquido (o que sobra pro Super-Admin)
    $totais['lucro_super_admin'] += ($r['lucro_diario'] - $r['comissao_usuario'] - $r['comissao_sub_adm'] - $r['comissao_admin']);
}

// (Usando header antigo, OK)
include('templates/header.php'); 
?>
<div class="container-fluid">
    <h2>Relatórios Detalhados</h2>
    <div class="card shadow-sm mb-4"><div class="card-header">Filtrar Relatórios</div>
        <div class="card-body"><form action="reports.php" method="POST"><input type="hidden" name="action" value="filtrar">
            <div class="row g-3">
                <div class="col-md-3"><label>Data Início</label><input type="date" class="form-control" name="date_start" value="<?php echo $filtros_aplicados['date_start']; ?>" required></div>
                <div class="col-md-3"><label>Data Fim</label><input type="date" class="form-control" name="date_end" value="<?php echo $filtros_aplicados['date_end']; ?>" required></div>
                <?php if ($role == 'super_adm' || $role == 'admin'): ?>
                <div class="col-md-3">
                    <label>Filtrar por <?php echo ($role == 'super_adm') ? 'Gerente (N1/N2)' : 'Sub-Gerente (N2)'; ?></label>
                    <select name="admin_id" class="form-control"><option value="">Todos</option><?php foreach ($admins_list as $admin): ?><option value="<?php echo $admin['id_sub_adm']; ?>" <?php echo ($filtros_aplicados['admin_id'] == $admin['id_sub_adm']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($admin['nome']); ?></option><?php endforeach; ?></select>
                </div>
                <?php endif; ?>
                <div class="col-md-3"><label>Filtrar por Usuário (N3)</label><select name="user_ids[]" id="user_ids" class="form-control" multiple style="height: 100px;"><?php foreach ($usuarios_list as $usuario): ?><option value="<?php echo $usuario['id_usuario']; ?>" <?php echo in_array($usuario['id_usuario'], $filtros_aplicados['user_ids']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($usuario['nome']); ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Aplicar Filtro</button></div>
            </div>
        </form></div>
    </div>
    <div class="card shadow-sm mb-4"><div class="card-header">Salvar Relatório Atual</div>
        <div class="card-body"><form action="save_report.php" method="POST">
            <input type="hidden" name="filtros_json" value="<?php echo htmlspecialchars(json_encode($filtros_aplicados)); ?>">
            <div class="row g-3">
                <div class="col-md-8"><label>Nome para Salvar</label><input type="text" class="form-control" name="nome_relatorio" required></div>
                <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-success w-100">Salvar Relatório</button></div>
            </div>
        </form></div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header">
            Resultados
            <?php if (isset($_GET['status'])) {
                if ($_GET['status'] == 'report_updated') echo "<span class='alert alert-success py-1 ms-3'>Relatório atualizado!</span>";
                if ($_GET['status'] == 'report_deleted') echo "<span class='alert alert-success py-1 ms-3'>Relatório apagado!</span>";
            } ?>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Data</th><th>Usuário</th><th>Depósito</th><th>Saque</th><th>Baú</th>
                        <th>Lucro (Total)</th><th>Com. User (N3)</th><th>Com. Sub (N2)</th>
                        <?php if ($role == 'super_adm' || $role == 'admin'): ?>
                            <th>Com. Admin (N1)</th>
                        <?php endif; ?>
                        <?php if ($role == 'super_adm'): ?>
                            <th>Lucro Líquido</th>
                        <?php endif; ?>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($relatorios)): ?>
                        <tr><td colspan="11" class="text-center">Nenhum relatório encontrado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($relatorios as $r): ?>
                    <?php
                        $lucro_liquido_linha = $r['lucro_diario'] - $r['comissao_usuario'] - $r['comissao_sub_adm'] - $r['comissao_admin'];
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['data'])); ?></td>
                        <td><?php echo htmlspecialchars($r['nome_usuario']); ?></td>
                        <td>R$ <?php echo number_format($r['valor_deposito'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($r['valor_saque'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($r['valor_bau'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($r['lucro_diario'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($r['comissao_usuario'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($r['comissao_sub_adm'], 2, ',', '.'); ?></td>
                        
                        <?php if ($role == 'super_adm' || $role == 'admin'): ?>
                            <td>R$ <?php echo number_format($r['comissao_admin'], 2, ',', '.'); ?></td>
                        <?php endif; ?>
                        <?php if ($role == 'super_adm'): ?>
                            <td class="text-success fw-bold">R$ <?php echo number_format($lucro_liquido_linha, 2, ',', '.'); ?></td>
                        <?php endif; ?>
                        
                        <td>
                            <a href="edit_report_entry.php?id=<?php echo $r['id_relatorio']; ?>" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                            <a href="delete_report_entry.php?id=<?php echo $r['id_relatorio']; ?>" class="btn btn-danger btn-sm" title="Apagar" onclick="return confirm('Tem certeza?');"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2" class="text-end">TOTAIS:</th>
                        <th>R$ <?php echo number_format($totais['deposito'], 2, ',', '.'); ?></th>
                        <th>R$ <?php echo number_format($totais['saque'], 2, ',', '.'); ?></th>
                        <th>R$ <?php echo number_format($totais['bau'], 2, ',', '.'); ?></th>
                        <th>R$ <?php echo number_format($totais['lucro'], 2, ',', '.'); ?></th>
                        <th>R$ <?php echo number_format($totais['comissao_user'], 2, ',', '.'); ?></th>
                        <th>R$ <?php echo number_format($totais['comissao_sub'], 2, ',', '.'); ?></th>
                        
                        <?php if ($role == 'super_adm' || $role == 'admin'): ?>
                            <th>R$ <?php echo number_format($totais['comissao_admin'], 2, ',', '.'); ?></th>
                        <?php endif; ?>
                        <?php if ($role == 'super_adm'): ?>
                            <th>R$ <?php echo number_format($totais['lucro_super_admin'], 2, ',', '.'); ?></th>
                        <?php endif; ?>
                        
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>