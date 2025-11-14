<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Dashboard (Administrador)";

// Verificação de segurança
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
$id_admin_logado = $_SESSION['user_id'];

// Mensagem de sucesso/erro
$message = "";
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "<div class='alert alert-success mt-3'>Transação registrada com sucesso!</div>";
} elseif (isset($_GET['status']) && $_GET['status'] == 'error') {
     $message = "<div class='alert alert-danger mt-3'>Erro ao registrar transação.</div>";
}


// --- QUERIES PARA OS CARDS DE ESTATÍSTICAS (KPIs) - Filtrado por gerência ---

// 1. Total de Operadores (Usuários) sob o Admin
$stmt_total_users = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE manager_id = ? AND role = 'usuario'");
$stmt_total_users->execute([$id_admin_logado]);
$total_users = $stmt_total_users->fetchColumn();

// 2. Total de Sub-Admins sob o Admin
$stmt_total_subadmins = $pdo->prepare("SELECT COUNT(*) FROM sub_administradores WHERE manager_id = ? AND role = 'sub_adm'");
$stmt_total_subadmins->execute([$id_admin_logado]);
$total_subadmins = $stmt_total_subadmins->fetchColumn();

// Lista de IDs (Operadores e Sub-Admins) para incluir nos relatórios
$manager_ids = [$id_admin_logado]; // O próprio admin
$stmt_subadmins_ids = $pdo->prepare("SELECT id FROM sub_administradores WHERE manager_id = ? AND role = 'sub_adm'");
$stmt_subadmins_ids->execute([$id_admin_logado]);
while ($row = $stmt_subadmins_ids->fetch(PDO::FETCH_ASSOC)) {
    $manager_ids[] = $row['id'];
}
$manager_ids_str = implode(',', array_fill(0, count($manager_ids), '?'));
$manager_ids_params = $manager_ids;


// Obter IDs de todos os operadores gerenciados (direta ou indiretamente via sub-admins)
$user_ids = [];
$users_query = "SELECT id FROM usuarios WHERE manager_id IN (" . $manager_ids_str . ") AND role = 'usuario'";
$stmt_users = $pdo->prepare($users_query);
$stmt_users->execute($manager_ids_params);
while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
    $user_ids[] = $row['id'];
}
$user_ids_str = implode(',', array_fill(0, count($user_ids), '?'));
$user_ids_params = $user_ids;


// 3. Lucro Bruto Total (Filtrado pelos operadores gerenciados)
$total_lucro_bruto = 0;
if (!empty($user_ids_params)) {
    $query_lucro = "SELECT SUM(lucro_diario) FROM relatorios WHERE id_usuario IN (" . $user_ids_str . ")";
    $stmt_lucro = $pdo->prepare($query_lucro);
    $stmt_lucro->execute($user_ids_params);
    $total_lucro_bruto = $stmt_lucro->fetchColumn() ?? 0;
}

// 4. Lucro Líquido do Admin (Soma de comissao_sub_adm de seus operadores e sub-admins)
$lucro_liquido_admin = 0;
if (!empty($user_ids_params)) {
    $query_comissao = "SELECT SUM(comissao_sub_adm) FROM relatorios WHERE id_usuario IN (" . $user_ids_str . ")";
    $stmt_comissao = $pdo->prepare($query_comissao);
    $stmt_comissao->execute($user_ids_params);
    $lucro_liquido_admin = $stmt_comissao->fetchColumn() ?? 0;
}

// --- DADOS PARA OS GRÁFICOS (Últimos 7 dias) - Lucro Líquido do Admin ---
$data_corte = date('Y-m-d', strtotime('-7 days'));
$data_admin_grafico = [];
$labels = [];

if (!empty($user_ids)) {
    $user_ids_in_clause = implode(',', $user_ids);
    $query_grafico = "
        SELECT 
            DATE(data) as dia,
            SUM(comissao_sub_adm) as lucro_admin
        FROM relatorios 
        WHERE id_usuario IN ($user_ids_in_clause) AND data >= ?
        GROUP BY dia
        ORDER BY dia ASC
    ";
    $stmt_grafico = $pdo->prepare($query_grafico);
    $stmt_grafico->execute([$data_corte]);
    $dados_grafico_bruto = $stmt_grafico->fetchAll(PDO::FETCH_ASSOC);

    // Preenche os dados para os últimos 7 dias (incluindo dias sem dados com valor 0)
    for ($i = 6; $i >= 0; $i--) {
        $dia = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('d/m', strtotime($dia));
        
        $found = false;
        foreach ($dados_grafico_bruto as $d) {
            if ($d['dia'] == $dia) {
                $data_admin_grafico[] = (float)$d['lucro_admin'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data_admin_grafico[] = 0;
        }
    }
} else {
     // Preenche com 0 se não houver usuários gerenciados
     for ($i = 6; $i >= 0; $i--) {
        $labels[] = date('d/m', strtotime("-$i days"));
        $data_admin_grafico[] = 0;
    }
}


$labels_json = json_encode($labels);
$data_admin_grafico_json = json_encode($data_admin_grafico);

// --- TABELA DE MELHORES OPERADORES (Top 5) ---
$ranking_users = [];
if (!empty($user_ids_params)) {
    $query_ranking_users = "
        SELECT 
            u.nome, 
            SUM(r.comissao_usuario) AS comissao_total
        FROM usuarios u
        JOIN relatorios r ON r.id_usuario = u.id
        WHERE u.id IN (" . $user_ids_str . ")
        GROUP BY u.id, u.nome
        ORDER BY comissao_total DESC
        LIMIT 5
    ";
    $stmt_ranking = $pdo->prepare($query_ranking_users);
    $stmt_ranking->execute($user_ids_params);
    $ranking_users = $stmt_ranking->fetchAll(PDO::FETCH_ASSOC);
}

// --- DADOS DA META MENSAL ---
$ano_mes_atual = date('Y-m');
$stmt_meta = $pdo->prepare("SELECT valor_meta FROM metas_mensais WHERE id_admin = ? AND ano_mes = ?");
$stmt_meta->execute([$id_admin_logado, $ano_mes_atual]);
$meta_atual = $stmt_meta->fetch(PDO::FETCH_ASSOC);
$valor_meta_atual = $meta_atual ? (float)$meta_atual['valor_meta'] : 0;
$progresso_percentual = $valor_meta_atual > 0 ? ($lucro_liquido_admin / $valor_meta_atual) * 100 : 0;

include('header.php'); 
?>

<h2 class="mb-4">Visão Geral da Minha Gerência</h2>

<!-- Linha de KPIs e Meta -->
<div class="row">
    <!-- Lucro Líquido (Seu) -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-wallet fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-white">Lucro Líquido (Minha Parte)</h5>
                        <h1 class="display-6 mb-0">R$ <?php echo number_format($lucro_liquido_admin, 2, ',', '.'); ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Operadores -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-info text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-white">Operadores (Diretos/Indiretos)</h5>
                        <h1 class="display-6 mb-0"><?php echo $total_users; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Sub-Admins -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-warning text-dark shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users-cog fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-dark">Sub-Admins Gerenciados</h5>
                        <h1 class="display-6 mb-0"><?php echo $total_subadmins; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meta Mensal -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card shadow-sm border-l-4 border-l-primary h-100">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Meta Mensal</h5>
                    <a href="metas.php" class="btn btn-sm btn-outline-primary">Ajustar</a>
                 </div>
                 <?php if ($valor_meta_atual > 0): ?>
                    <h1 class="display-6 mb-2 text-primary">R$ <?php echo number_format($valor_meta_atual, 2, ',', '.'); ?></h1>
                    <div class="progress" style="height: 1.5rem;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: <?php echo min($progresso_percentual, 100); ?>%;" 
                             aria-valuenow="<?php echo $progresso_percentual; ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <b><?php echo number_format(min($progresso_percentual, 100), 1, ',', '.'); ?>%</b>
                        </div>
                    </div>
                    <small class="text-muted">Progresso: R$ <?php echo number_format($lucro_liquido_admin, 2, ',', '.'); ?></small>
                <?php else: ?>
                    <h1 class="display-6 mb-2 text-muted">Não Definida</h1>
                    <p class="text-muted">Defina uma meta em Metas Mensais.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Lucro Líquido e Ranking de Operadores -->
<div class="row">
    <!-- Gráfico de Lucro Líquido (Linha) -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Minha Comissão Líquida (Últimos 7 dias)</h5>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="adminProfitChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking de Operadores -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Top 5 Operadores por Comissão (Total)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Operador</th>
                            <th class="text-end">Comissão (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ranking_users)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Nenhum operador com dados.</td></tr>
                        <?php endif; ?>
                        <?php $rank = 1; foreach ($ranking_users as $user): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($user['nome']); ?></td>
                            <td class="text-end">R$ <?php echo number_format($user['comissao_total'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados injetados do PHP
    const labels = <?php echo $labels_json; ?>;
    const dataAdminProfit = <?php echo $data_admin_grafico_json; ?>;

    // --- Gráfico de Linha (Lucro Admin) ---
    const ctxLine = document.getElementById('adminProfitChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Minha Comissão Líquida',
                    data: dataAdminProfit,
                    borderColor: 'rgba(40, 167, 69, 1)', // Verde escuro para lucro
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valor (R$)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'R$ ' + context.parsed.y.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php 
include('footer.php');
?>