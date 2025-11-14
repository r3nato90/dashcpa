<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Apenas Gerentes (Super e Sub) podem ver
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_admin_logado = $_SESSION['id'];

// Define o mês e ano atual (ex: 2025-11)
$ano_mes_atual = date('Y-m');
// Define o nome do mês formatado (ex: novembro de 2025)
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');
$nome_mes_atual = strftime('%B de %Y');

$message = "";

// --- LÓGICA DE SALVAR META (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'definir_meta') {
    if (isset($_POST['valor_meta']) && is_numeric($_POST['valor_meta']) && $_POST['valor_meta'] > 0) {
        $valor_meta = (float)$_POST['valor_meta'];
        
        try {
            // Usa INSERT ... ON DUPLICATE KEY UPDATE para salvar ou atualizar a meta do mês
            $stmt_save = $pdo->prepare("
                INSERT INTO metas_mensais (id_admin, ano_mes, valor_meta)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE valor_meta = VALUES(valor_meta)
            ");
            $stmt_save->execute([$id_admin_logado, $ano_mes_atual, $valor_meta]);
            $message = "<div class='alert alert-success'>Meta de R$ " . number_format($valor_meta, 2, ',', '.') . " definida com sucesso!</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erro ao salvar a meta: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Por favor, insira um valor numérico válido para a meta.</div>";
    }
}

// --- BUSCAR DADOS DA META ATUAL ---
$stmt_meta = $pdo->prepare("SELECT valor_meta FROM metas_mensais WHERE id_admin = ? AND ano_mes = ?");
$stmt_meta->execute([$id_admin_logado, $ano_mes_atual]);
$meta_atual = $stmt_meta->fetch(PDO::FETCH_ASSOC);
$valor_meta_atual = $meta_atual ? (float)$meta_atual['valor_meta'] : 0;

// --- BUSCAR PROGRESSO ATUAL (LUCRO LÍQUIDO DO ADMIN NO MÊS) ---
$query_progress = "";
$params_progress = [
    $ano_mes_atual . '-01 00:00:00', // Início do mês
    $ano_mes_atual . '-31 23:59:59'  // Fim do mês
];

if ($role == 'super_adm') {
    // Super Admin: Soma comissao_admin (50%)
    $query_progress = "SELECT SUM(comissao_admin) as progresso FROM relatorios WHERE data BETWEEN ? AND ?";
} else {
    // Gerente (Admin/Sub-Admin): Soma comissao_sub_adm (10%) dos seus usuários
    $query_progress = "
        SELECT SUM(r.comissao_sub_adm) as progresso 
        FROM relatorios r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE u.id_sub_adm = ? AND (r.data BETWEEN ? AND ?)
    ";
    array_unshift($params_progress, $id_admin_logado); // Adiciona id_admin_logado no início
}

$stmt_progress = $pdo->prepare($query_progress);
$stmt_progress->execute($params_progress);
$progresso_atual = $stmt_progress->fetchColumn() ?? 0;

// Calcular porcentagem
$progresso_percentual = 0;
if ($valor_meta_atual > 0) {
    $progresso_percentual = ($progresso_atual / $valor_meta_atual) * 100;
}

// --- BUSCAR HISTÓRICO DE METAS ---
// (Esta query é complexa, une metas e relatórios)
$query_historico = "";
$params_historico = [$id_admin_logado];

if ($role == 'super_adm') {
    $query_historico = "
        SELECT 
            m.ano_mes, 
            m.valor_meta,
            (SELECT SUM(comissao_admin) 
             FROM relatorios 
             WHERE DATE_FORMAT(data, '%Y-%m') = m.ano_mes
            ) as lucro_realizado
        FROM metas_mensais m
        WHERE m.id_admin = ?
        ORDER BY m.ano_mes DESC
    ";
} else {
    $query_historico = "
        SELECT 
            m.ano_mes, 
            m.valor_meta,
            (SELECT SUM(r.comissao_sub_adm) 
             FROM relatorios r
             JOIN usuarios u ON r.id_usuario = u.id_usuario
             WHERE u.id_sub_adm = m.id_admin AND DATE_FORMAT(r.data, '%Y-%m') = m.ano_mes
            ) as lucro_realizado
        FROM metas_mensais m
        WHERE m.id_admin = ?
        ORDER BY m.ano_mes DESC
    ";
}
$stmt_historico = $pdo->prepare($query_historico);
$stmt_historico->execute($params_historico);
$historico_metas = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);


include('templates/header.php'); 
?>

<div class="container-fluid">

    <!-- Cabeçalho -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Metas Mensais</h1>
            <p class="text-muted-foreground">Defina e acompanhe suas metas de lucro líquido.</p>
        </div>
    </div>
    
    <?php echo $message; // Exibe feedback de salvar meta ?>

    <!-- Card da Meta Atual -->
    <div class="card shadow-sm mb-4 border-l-4 border-l-blue-500">
        <div class="card-body p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">
                    <i class="fas fa-bullseye text-blue-600 me-2"></i>
                    Meta de <?php echo ucwords($nome_mes_atual); ?>
                </h3>
                <i class="fas fa-trophy fa-2x text-muted-foreground opacity-50"></i>
            </div>
            
            <p class="text-muted-foreground">Acompanhe seu progresso mensal.</p>
            <hr class="my-3" style="border-top-color: hsl(var(--border));">

            <?php if ($valor_meta_atual > 0): ?>
                <!-- SE TEM META: MOSTRA O PROGRESSO -->
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="font-medium text-muted-foreground">Progresso</span>
                        <span class="font-bold text-success">
                            R$ <?php echo number_format($progresso_atual, 2, ',', '.'); ?>
                             / R$ <?php echo number_format($valor_meta_atual, 2, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="progress" style="height: 1.5rem;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?php echo $progresso_percentual; ?>%;" 
                             aria-valuenow="<?php echo $progresso_percentual; ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <b class="ms-2"><?php echo number_format($progresso_percentual, 1, ',', '.'); ?>%</b>
                        </div>
                    </div>
                </div>
                
                <!-- Formulário para ATUALIZAR a meta -->
                <form action="metas.php" method="POST" class="mt-4">
                    <input type="hidden" name="action" value="definir_meta">
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" name="valor_meta" class="form-control" 
                               placeholder="Atualizar valor da meta (ex: 5000)" 
                               value="<?php echo $valor_meta_atual; ?>" required>
                        <button type="submit" class="btn btn-primary">Atualizar Meta</button>
                    </div>
                </form>

            <?php else: ?>
                <!-- SE NÃO TEM META: MOSTRA MENSAGEM E FORMULÁRIO -->
                <div class="text-center p-4">
                    <div class="display-4 text-muted-foreground mb-3">
                        <i class="far fa-circle"></i>
                    </div>
                    <h4 class="text-muted-foreground">Nenhuma meta definida para este mês.</h4>
                </div>

                <form action="metas.php" method="POST" class="mt-3">
                    <input type="hidden" name="action" value="definir_meta">
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" name="valor_meta" class="form-control" 
                               placeholder="Digite o valor da meta (ex: 5000)" required>
                        <button type="submit" class="btn btn-primary">Definir Meta</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <!-- Histórico de Metas -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="text-2xl font-semibold leading-none tracking-tight">Histórico de Metas</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mês/Ano</th>
                        <th>Meta Definida</th>
                        <th>Lucro Realizado</th>
                        <th>Progresso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historico_metas)): ?>
                        <tr><td colspan="4" class="text-center text-muted-foreground p-3">Nenhum histórico encontrado.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach ($historico_metas as $meta): 
                        $ano_mes_formatado = date('m/Y', strtotime($meta['ano_mes'] . '-01'));
                        $lucro_realizado = (float)($meta['lucro_realizado'] ?? 0);
                        $meta_definida = (float)$meta['valor_meta'];
                        $progresso = 0;
                        if ($meta_definida > 0) {
                            $progresso = ($lucro_realizado / $meta_definida) * 100;
                        }
                    ?>
                    <tr>
                        <td><?php echo $ano_mes_formatado; ?></td>
                        <td>R$ <?php echo number_format($meta_definida, 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($lucro_realizado, 2, ',', '.'); ?></td>
                        <td>
                            <div class="progress" style="height: 1.25rem;">
                                <div class="progress-bar <?php echo $progresso >= 100 ? 'bg-success' : 'bg-warning'; ?>" role="progressbar" 
                                     style="width: <?php echo min($progresso, 100); ?>%;" 
                                     aria-valuenow="<?php echo $progresso; ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <small class="ms-1"><?php echo number_format($progresso, 1, ',', '.'); ?>%</small>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include('templates/footer.php'); ?>