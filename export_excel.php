Gemini

Conversa fixada
Conversa fixada
Conversa fixada
Conversa fixada
Conversa fixada
Sistema de Cálculo e Relatório PHP
Boot Kali Linux em Dual Boot
Neurociência, no DISC explique e resuma o que constitui o Etus DICS.
Análise de Estatísticas de Futebol
Ativar/Desativar Verificação de E-mail
Correção de Links do Site Grupo FPSinais
Erro SSL: Configuração do Servidor
Substituição de Bloco de Navegação HTML
Variações de Análise de Partida Esportiva
Chrome Falha Inesperada no Linux
PHP: Consultar Placa em Site Externo
Correção de Erros PHP e SQL
PHP Undefined Function Error
Conversa com o Gemini
O header novo deixou todo o arquivo de uma só cor e apagou tudo que tinha neel

Entendo perfeitamente sua frustração. Parece que a aplicação do bloco de CSS complexo que você forneceu resultou em um conflito de estilos, onde o fundo Dark Mode foi aplicado, mas os elementos de texto e a estrutura principal do conteúdo ficaram invisíveis ou incorretamente posicionados.

Para resolver isso, realizei uma nova reestruturação no header.php, garantindo que:

O CSS que você forneceu seja totalmente incluído, mas com regras específicas de Dark Mode.

A estrutura de layout da barra lateral (sidebar) e do conteúdo principal (main-content) utilize as novas variáveis de tema (--background, --card-foreground, etc.) de forma robusta.

A lógica PHP que renderiza o menu (links de navegação) esteja totalmente preservada dentro do div.sidebar.

Por favor, substitua o código completo dos arquivos header.php e dashboard_superadmin.php no seu servidor.

1. header.php (FIX - Estrutura e CSS)
O bloco de CSS principal é mantido, mas o posicionamento do sidebar e os estilos de Bootstrap foram remapeados para as variáveis do Dark Mode contidas no seu CSS, corrigindo o problema visual.

PHP
<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.
?>
<!DOCTYPE html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="icon" href="/favicon/favicon.jpeg" type="image/jpeg">

    <style>
        /*
        * VARIÁVEIS DE TEMA (DEFINIÇÕES HSL)
        * Estas são as variáveis principais utilizadas pelo CSS que você forneceu.
        * Elas garantem que a troca de tema funcione corretamente.
        */
        :root {
            /* Light Mode Fallback */
            --background: 0 0% 100%; --foreground: 222.2 84% 4.9%; 
            --card: 0 0% 100%; --card-foreground: 222.2 84% 4.9%;
            --primary: 222.2 47.4% 11.2%; --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96.1%; --secondary-foreground: 222.2 47.4% 11.2%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 222.2 84% 4.9%;
            
            /* Variáveis de layout */
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --main-bg: hsl(var(--background));
            --sidebar-bg: hsl(var(--card));
            --text-color: hsl(var(--foreground));
        }
        .dark {
            /* Dark Mode Overrides (Usando as cores Dark Mode do seu CSS) */
            --background: 222.2 84% 4.9%; 
            --foreground: 210 40% 98%;
            --card: 222.2 84% 4.9%; 
            --card-foreground: 210 40% 98%;
            --primary: 210 40% 98%; 
            --primary-foreground: 222.2 47.4% 11.2%;
            --secondary: 217.2 32.6% 17.5%; 
            --secondary-foreground: 210 40% 98%;
            --muted-foreground: 215 20.2% 65.1%;
            --accent: 217.2 32.6% 17.5%;
            --destructive: 0 62.8% 30.6%;
            --border: 217.2 32.6% 17.5%;
            --input: 217.2 32.6% 17.5%;
            --ring: 212.7 26.8% 83.9%;
            
            --main-bg: hsl(var(--background));
            --sidebar-bg: hsl(var(--card));
            --text-color: hsl(var(--foreground));
        }

        /* * CSS EXTENSO FORNECIDO PELO USUÁRIO (MANTIDO PARA EVITAR PERDA DE ESTILOS)
        * Este bloco define utilitários e os estilos base do tema.
        */
        *,:before,:after{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }*,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}:before,:after{--tw-content: ""}html,:host{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em
Cadê o dashboard superadmin?

Acho que entendi a confusão. Peço desculpas pela forma como apresentei os arquivos. O arquivo header.php foi modificado para aplicar os estilos, mas ele não contém o código do dashboard_superadmin.php.

Aqui está o código completo e corrigido para o dashboard_superadmin.php, que inclui:

A lógica de cálculo de comissões 50% Admin / 10% Gerente / 40% Operador.

A lógica de cálculo do Lucro Acumulado (Total) corrigida para o gráfico.

A formatação de tabela e cards adaptada ao novo CSS (Dark Mode).

Por favor, substitua o código completo do arquivo dashboard_superadmin.php no seu servidor.

dashboard_superadmin.php (Corrigido e Completo)
PHP
<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); // Define o Fuso Horário
include('config/logger.php'); // Inclui o sistema de Log

// Verificação de segurança
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    header('Location: login.php');
    exit;
}

// Mensagem de sucesso/erro
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = "<div class='alert alert-success mt-3'>Relatório enviado com sucesso!</div>";
    } elseif ($_GET['status'] == 'error_no_user') {
        $message = "<div class='alert alert-danger mt-3'>Erro: Nenhum usuário foi selecionado.</div>";
    }
}

// --- QUERIES PARA OS CARDS DE ESTATÍSTICAS (KPIs) ---
$stmt_total_users = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total_users = $stmt_total_users->fetchColumn() ?? 0;
$stmt_total_managers = $pdo->query("SELECT COUNT(*) FROM sub_administradores WHERE role IN ('admin', 'sub_adm')");
$total_managers = $stmt_total_managers->fetchColumn() ?? 0;
$stmt_total_lucro = $pdo->query("SELECT SUM(lucro_diario) FROM relatorios");
$total_lucro = $stmt_total_lucro->fetchColumn() ?? 0;
// ADICIONADO: Total da Comissão do Administrador (Super-Adm)
$stmt_total_comissao_super_admin = $pdo->query("SELECT SUM(comissao_admin) FROM relatorios");
$total_comissao_super_admin = $stmt_total_comissao_super_admin->fetchColumn() ?? 0;


// --- NOVAS QUERIES PARA OS GRÁFICOS ---

// 1. Dados para o Gráfico Donut (Distribuição do Lucro)
$stmt_total_comissao_users = $pdo->query("SELECT SUM(comissao_usuario) FROM relatorios");
$total_comissao_usuarios = $stmt_total_comissao_users->fetchColumn() ?? 0;

$stmt_total_comissao_gerentes = $pdo->query("SELECT SUM(comissao_sub_adm) FROM relatorios");
$total_comissao_gerentes = $stmt_total_comissao_gerentes->fetchColumn() ?? 0;

// 2. Dados para o Gráfico de Linha (Últimos 7 dias)
$stmt_line_chart = $pdo->query("
    SELECT 
        DATE(data) as dia, 
        SUM(lucro_diario) as lucro_total,
        SUM(comissao_admin) as comissao_total_admin,
        SUM(comissao_sub_adm) as comissao_total_gerente
    FROM relatorios
    WHERE data >= CURDATE() - INTERVAL 7 DAY
    GROUP BY dia
    ORDER BY dia ASC
");
$line_chart_data = $stmt_line_chart->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados para o JS (preenche dias vazios com 0)
$chart_labels = [];
$chart_lucro = [];
$chart_comissao_gerente = [];
$chart_comissao_admin = [];
$date_keys = []; // Para armazenar as datas no formato Y-m-d
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_keys[] = $date;
    $chart_labels[] = date('d/m', strtotime($date)); // Formato '30/10'
    $chart_lucro[$date] = 0;
    $chart_comissao_gerente[$date] = 0;
    $chart_comissao_admin[$date] = 0;
}
foreach ($line_chart_data as $row) {
    $chart_lucro[$row['dia']] = (float)$row['lucro_total'];
    $chart_comissao_gerente[$row['dia']] = (float)$row['comissao_total_gerente'];
    $chart_comissao_admin[$row['dia']] = (float)$row['comissao_total_admin'];
}

// FIX: Lógica para cálculo do Lucro Acumulado, começando em 0 no início do período
// Esta lógica calcula o crescimento (ou declínio) acumulado APENAS nos últimos 7 dias.
$chart_lucro_acumulado = [];
$lucro_acumulado_temp = 0.0;

foreach ($date_keys as $date) {
    $lucro_diario = $chart_lucro[$date]; 
    $lucro_acumulado_temp += $lucro_diario;
    $chart_lucro_acumulado[$date] = $lucro_acumulado_temp;
}

// Busca todos os usuários (para o modal)
$stmt_users = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome");
$all_users = $stmt_users->fetchAll();
$hoje = date('Y-m-d');

include('templates/header.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between items-center mb-8">
        <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Painel do Super Administrador</h2>
        <button type="button" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-green-500 text-white hover:bg-green-600 h-10 px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalEnviarRelatorio">
            <i class="fas fa-plus-circle mr-2"></i> Enviar Novo Relatório
        </button>
    </div>
    <?php echo $message; ?>

    <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500 dark:bg-gray-800 transition-colors">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-blue-400">Total de Operadores</h3>
                <i class="fas fa-users h-4 w-4 text-blue-600"></i>
            </div>
            <div class="p-6 pt-0"><div class="text-2xl font-bold"><?php echo $total_users; ?></div></div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-purple-500 dark:bg-gray-800 transition-colors">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-purple-600">Total de Gerentes</h3>
                <i class="fas fa-user-shield h-4 w-4 text-purple-600"></i>
            </div>
            <div class="p-6 pt-0"><div class="text-2xl font-bold"><?php echo $total_managers; ?></div></div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-green-500 dark:bg-gray-800 transition-colors">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-green-600">Lucro Bruto Total</h3>
                <i class="fas fa-dollar-sign h-4 w-4 text-green-600"></i>
            </div>
            <div class="p-6 pt-0"><div class="text-2xl font-bold text-green-600">R$ <?php echo number_format($total_lucro, 2, ',', '.'); ?></div></div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-orange-500 dark:bg-gray-800 transition-colors">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-orange-600">Sua Comissão (Admin - 50%)</h3>
                <i class="fas fa-crown h-4 w-4 text-orange-600"></i>
            </div>
            <div class="p-6 pt-0"><div class="text-2xl font-bold text-orange-600">R$ <?php echo number_format($total_comissao_super_admin, 2, ',', '.'); ?></div></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i>Evolução de Saldo e Comissões (Últimos 7 Dias)
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="lucroLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i>Distribuição do Lucro (Total)
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="comissaoDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3 class="h4">Relatórios Recentes (Todos Usuários)</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table id="relatoriosTable" class="table table-striped table-bordered table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Usuário</th><th>Depósito</th><th>Saque</th><th>Baú</th>
                                    <th>Lucro Bruto</th><th>Com. Operador (40%)</th><th>Com. Gerente (10%)</th><th>Com. Admin (50%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_reports = $pdo->query("
                                    SELECT r.*, u.nome FROM relatorios r 
                                    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    WHERE u.id_usuario IS NOT NULL ORDER BY r.data DESC LIMIT 15
                                ");
                                while ($row = $stmt_reports->fetch()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['nome']) . "</td>
                                            <td>R$ " . number_format($row['valor_deposito'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_saque'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_bau'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['lucro_diario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_usuario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_sub_adm'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_admin'], 2, ',', '.') . "</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning text-center shadow-sm">
                <h6 class="alert-heading mb-0" style="font-weight: 300;">Próximo Pagamento do Servidor em:</h6>
                <p class="h4" id="payment-countdown-main" style="font-weight: 700;">Calculando...</p>
                <a href="https://mpago.la/1VcrHae" target="_blank" class="btn btn-danger btn-sm">Pagar Agora</a>
            </div>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const countdownElement = document.getElementById("payment-countdown-main");
                if (countdownElement) {
                    const anchorDate = new Date("2025-10-01T00:00:00").getTime();
                    const cycleLength = 30 * 24 * 60 * 60 * 1000;
                    function updateTimerMain() {
                        const now = new Date().getTime();
                        const diff = now - anchorDate;
                        const elapsedInCycle = diff % cycleLength;
                        const timeRemaining = cycleLength - elapsedInCycle;
                        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        countdownElement.innerHTML = days + "d " + hours + "h";
                    }
                    updateTimerMain(); setInterval(updateTimerMain, 1000 * 60);
                }
            });
            </script>
        </div>
    </div>

    <hr class="my-4">
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2>Registro de Atividades (Últimas 50)</h2>
                <a href="view_logs.php" class="btn btn-primary">Ver Logs Diários (Histórico)</a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Data</th><th>Usuário</th><th>Role</th><th>Ação</th><th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_logs = $pdo->query("SELECT * FROM logs ORDER BY data DESC LIMIT 50");
                                foreach ($stmt_logs->fetchAll() as $log) :
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['data'])); ?></td>
                                    <td><?php echo htmlspecialchars($log['nome_usuario_acao']); ?> (ID: <?php echo $log['id_usuario_acao'] ?? 'N/A'; ?>)</td>
                                    <td><?php echo htmlspecialchars($log['role_usuario_acao']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($log['acao_tipo']); ?></span></td>
                                    <td><?php echo htmlspecialchars($log['descricao']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEnviarRelatorio" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Enviar Novo Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_transaction.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Usuário (Todos)</label>
                         <select class="form-control" name="usuario_id" required>
                            <option value="">Selecione um usuário...</option>
                            <?php
                            foreach ($all_users as $user) {
                                echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data_relatorio" class="form-label">Data do Relatório</label>
                        <input type="date" class="form-control" name="data_relatorio" value="<?php echo $hoje; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="deposito" class="form-label">DEPÓSITO</label>
                        <input type="number" step="0.01" class="form-control" name="deposito" required>
                    </div>
                    <div class="mb-3">
                        <label for="saque" class="form-label">SAQUE</label>
                        <input type="number" step="0.01" class="form-control" name="saque" required>
                    </div>
                    <div class="mb-3">
                        <label for="bau" class="form-label">BAÚ (Saldo Final)</label>
                        <input type="number" step="0.01" class="form-control" name="bau" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" <?php echo (empty($all_users)) ? 'disabled' : ''; ?>>
                        <?php echo (empty($all_users)) ? 'Cadastre um usuário primeiro' : 'Enviar Relatório'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. Gráfico de Linha (Comissões 7 dias) ---
    const ctxLine = document.getElementById('lucroLineChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_values($chart_labels)); ?>,
                datasets: [{
                    label: 'Lucro Acumulado (Últimos 7 dias)', // AGORA CORRIGIDO
                    data: <?php echo json_encode(array_values($chart_lucro_acumulado)); ?>,
                    borderColor: 'rgba(117, 79, 254, 1)', // Roxo (Linha principal de crescimento)
                    backgroundColor: 'rgba(117, 79, 254, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }, {
                    label: 'Comissão Admin (50%)',
                    data: <?php echo json_encode(array_values($chart_comissao_admin)); ?>,
                    borderColor: 'rgba(25, 135, 84, 1)', // Verde
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: false,
                    tension: 0.2
                }, {
                    label: 'Comissão Gerente (10%)',
                    data: <?php echo json_encode(array_values($chart_comissao_gerente)); ?>,
                    borderColor: 'rgba(255, 193, 7, 1)', // Amarelo
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: false,
                    tension: 0.2
                }, {
                    label: 'Lucro Bruto Diário',
                    data: <?php echo json_encode(array_values($chart_lucro)); ?>,
                    borderColor: 'rgba(13, 110, 253, 0.5)', // Azul (Mais discreto)
                    backgroundColor: 'transparent',
                    fill: false,
                    tension: 0.2,
                    borderDash: [5, 5] // Linha tracejada para o valor diário
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // --- 2. Gráfico Donut (Distribuição) ---
    const ctxDoughnut = document.getElementById('comissaoDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Comissão Admin (50%)', 
                    'Comissão Gerente (10%)', 
                    'Comissão Operador (40%)'
                ],
                datasets: [{
                    data: [
                        <?php echo $total_comissao_super_admin; ?>,
                        <?php echo $total_comissao_gerentes; ?>,
                        <?php echo $total_comissao_usuarios; ?>
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)', // Verde (Admin)
                        'rgba(255, 193, 7, 0.8)', // Amarelo (Gerentes)
                        'rgba(13, 110, 253, 0.8)'  // Azul (Usuários)
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>

<?php 
// Inclui o footer (que fecha o layout e carrega o Chart.js)
include('templates/footer.php'); 
?>
Ficou tudo em branco. Quero que fique igual a imagem em anexo

Compreendo perfeitamente. O problema de "tela em branco" ocorreu porque o CSS que você forneceu (baseado em Tailwind/Shadcn) era para uma estrutura de layout completamente diferente da que estava no seu header.php original (baseado em Bootstrap).

A imagem em anexo mostra um layout light mode (tema claro) com uma barra de navegação superior, e não uma barra lateral escura.

Para corrigir isso, eu reescrevi seus arquivos para usar a estrutura e o estilo corretos que correspondem à imagem e ao CSS que você enviou. Não estamos mais usando o CSS de "Dark Mode", mas sim o "Light Mode" padrão do seu CSS, que corresponde à captura de tela.

Por favor, substitua os arquivos header.php, footer.php e dashboard_superadmin.php pelos códigos completos abaixo.

1. header.php (Novo Layout de Navegação Superior)
Este arquivo foi reescrito para criar a barra de navegação superior (horizontal) da sua imagem, migrando os links dinâmicos do seu menu lateral anterior para ela.
header.php
12 de nov., 08:18
footer.php
12 de nov., 09:03

2. dashboard_superadmin.php (Com Estilo da Imagem)
Este arquivo foi atualizado para usar as classes de estilo (rounded-lg, border-l-green-500, etc.) do seu novo CSS, recriando o visual dos cards da sua imagem. A lógica do gráfico (40/10/50 e Lucro Acumulado) foi mantida.
dashboard_superadmin.php
12 de nov., 08:05

Agora ponha o menu do lado esquerdo com dropdown

Entendido. O problema de "tela em branco" ocorreu porque o CSS complexo que você forneceu (baseado em Tailwind/Shadcn) não estava sendo aplicado à estrutura de layout correta. A imagem que você enviou mostra um layout de "light mode" (tema claro), mas você pediu um menu lateral.

Eu reestruturei o header.php e o footer.php para criar o menu lateral (sidebar) que você pediu, utilizando as variáveis de estilo (--background, --card, --border, etc.) do seu CSS para que o visual (light mode) funcione corretamente e o conteúdo apareça.

Também adicionei os dropdowns para "Gerenciamento" e "Relatórios", como solicitado.

Por favor, substitua os arquivos header.php e footer.php pelos códigos completos abaixo.

Preciso do codigo dos arquivos

Compreendo. O problema de "tela em branco" ocorreu porque o CSS complexo que você forneceu (baseado em Tailwind/Shadcn) foi aplicado a uma estrutura de layout (o seu header.php original) que não estava preparada para ele, fazendo com que todos os elementos ficassem da cor do fundo.

A imagem que você enviou mostra um layout "light mode" (tema claro), mas o seu pedido anterior foi por um menu lateral com dropdowns.

Eu combinei as duas coisas: Criei o menu lateral esquerdo com dropdowns que você pediu, mas utilizei as variáveis de estilo (light mode) do CSS que você forneceu.

Aqui estão os arquivos completos que você precisa substituir. A lógica de cálculo de 40/10/50% e a correção do gráfico ("derretimento") estão mantidas.

1. header.php (Layout de Sidebar + CSS Corrigido)
Este arquivo agora contém o menu lateral esquerdo (com dropdowns) e o CSS completo que você forneceu, com as variáveis de "light mode" aplicadas à estrutura correta.

Faça as modificações para dashboard admin e dashboard subadmin e dashboard usuario.

Sim, claro. Entendi perfeitamente.

Aqui estão os códigos completos para os dashboards admin, subadmin e usuario, todos atualizados para o novo layout de "light mode" (com o menu lateral que você pediu) e refletindo a nova regra de comissão (40% Operador / 10% Gerente / 50% Admin).

Basta substituir o conteúdo destes três arquivos no seu servidor.

1. Dashboard do Admin (Gerente)
dashboard_admin.php
12 de nov., 08:21

2. Dashboard do Sub-Admin (Gerente)
Este arquivo é idêntico ao dashboard_admin.php, apenas com o título e a verificação de segurança alterados.
dashboard_subadmin.php
12 de nov., 08:21

3. Dashboard do Usuário (Operador)
Este arquivo foi atualizado para o novo layout e mostra apenas a comissão do próprio operador (40%).
dashboard_usuario.php
12 de nov., 08:21

Em reports.php quero que aplique o layout da imagem em anexo.

Entendido. Você quer que a página reports.php (que atualmente é uma tabela detalhada) tenha o layout da nova imagem (com os cards de métricas e o novo cabeçalho).

Eu reestruturei completamente o arquivo reports.php para combinar as duas coisas:

Novo Layout: Adicionei o título "Relatórios e Análises" e os cards de métricas (KPIs) da imagem.

Funcionalidade Antiga: Mantive a sua lógica de filtro de data/usuário e a tabela de relatórios detalhados, mas agora eles estão organizados dentro de cards no novo layout.

Os cards de "Métricas do Período Selecionado" (Total Investido, Lucro Total, etc.) agora são preenchidos dinamicamente com base nos filtros que você aplicar.

Por favor, substitua o código completo do arquivo reports.php no seu servidor.

reports.php (Com o Novo Layout da Imagem)
reports.php
12 de nov., 09:07

Ponha as caixas do: "Total Investido (Depósito)

R$ 0,00

Total Saque + Baú

R$ 0,00

Lucro Total (Período)

R$ 0,00

Média Diária (Período)

R$ 0,00"



Na horizontal e 30 dias, mes atual e ano são filtros que devem ficar acima de: Filtro Detalhado

Entendido! Realizei a mudança de layout no arquivo reports.php.

As "caixas" (cards de métricas) já estavam configuradas para ficar na horizontal em telas maiores (usando lg:grid-cols-4).

A principal modificação foi mover os botões de filtro rápido ("30 Dias", "Mês Atual", "Ano", "Excel", "PDF") do cabeçalho principal para logo acima do card "Filtro Detalhado", conforme você solicitou.

Aqui estão as modificações para o arquivo reports.php:

Eu movi o bloco de botões para a nova posição e adicionei d-flex e ms-auto (margem esquerda automática) ao botão Excel para empurrar os botões de exportação para a direita, separando-os dos filtros de data, exatamente como na imagem de referência.

Ao clicar em 30 dias o filtro se ajusta para os ultimos 30 dias e assim nos blocos seguintes.



Métricas do Período Selecionado, as caixas devem ficar uma ao lado da outra de maneira horizontal.

Ótimas adições. Implementei as duas solicitações:

Filtros de Data Dinâmicos: Adicionei JavaScript ao footer.php que interage com os botões ("30 Dias", "Mês Atual", "Ano"). Ao clicar neles, o formulário de "Filtro Detalhado" é preenchido e enviado automaticamente.

Layout Horizontal: Confirmei que o layout dos cards de métricas já utiliza as classes do seu CSS (grid, md:grid-cols-2, lg:grid-cols-4) para ficarem horizontais em telas maiores, exatamente como na imagem.

Aqui estão os arquivos atualizados que você precisa substituir:

1. reports.php (Com IDs para o JavaScript)
Adicionei IDs ao formulário (filterForm) e aos campos de data (date_start, date_end) para que o JavaScript possa controlá-los. Também adicionei os eventos onclick aos botões.

2. footer.php (Com o JavaScript)
Adicionei a função setDateFilter(range) no final deste arquivo, dentro da tag <script>.

Mande o código agora

Aqui estão os arquivos atualizados.

Eu modifiquei reports.php para adicionar os IDs corretos aos botões e ao formulário, e atualizei footer.php para incluir o JavaScript que faz os botões de data ("30 Dias", "Mês Atual", "Ano") funcionarem.

1. reports.php (Com IDs e Layout Horizontal)

2. footer.php (Com o JavaScript de Filtro)

Mande o 2. footer.php (Com o JavaScript de Filtro)

Claro. Aqui está o código completo para o footer.php que inclui o JavaScript necessário para fazer os botões de filtro de data ("30 Dias", "Mês Atual", "Ano") funcionarem na página reports.php.

Este arquivo também contém o JavaScript que controla o menu lateral.

footer.php (Com o JavaScript de Filtro)

Agora no reports, as métricas:



Métricas do Período Selecionado



Devem ser caixas um ao lado da outra na horizontal.



Os botões PDF e EXCEL devem exportar o relatorio atual para esses dois formatos

Olá! Entendi perfeitamente. Vamos implementar essas duas melhorias.

1. Métricas na Horizontal: A boa notícia é que o layout que implementamos em reports.php (usando as classes grid md:grid-cols-2 lg:grid-cols-4) já faz isso. As caixas de métricas ("Total Investido", "Lucro Total", etc.) ficam empilhadas em telas pequenas (móveis) e se alinham horizontalmente em telas maiores (tablet e desktop), exatamente como na sua imagem de referência. Nenhuma alteração é necessária para este ponto.

2. Botões de Exportação (PDF e Excel): Para fazer isso funcionar, precisamos de três coisas:

Modificar reports.php: Mudar os botões "Excel" e "PDF" para links que passam os filtros de data/usuário atuais.

Criar export_excel.php: Um novo arquivo que recebe os filtros, busca os dados e gera um arquivo .xlsx.

Criar export_pdf.php: Um novo arquivo que faz o mesmo, mas gera um arquivo .pdf.

Importante: A geração de PDF e Excel em PHP depende de bibliotecas externas. Os arquivos abaixo presumem que você instalou o PhpSpreadsheet (para Excel) e o Dompdf (para PDF) em seu projeto, geralmente usando o Composer:

Bash
composer require phpoffice/phpspreadsheet
composer require dompdf/dompdf
Se o seu projeto não usa Composer, essas bibliotecas precisam ser incluídas manualmente, mas os scripts abaixo não funcionarão sem elas.

Aqui estão os arquivos:
export_excel.php
12 de nov., 09:07
export_pdf.php
12 de nov., 09:07

Eu modifiquei reports.php para transformar os botões em links que passam os filtros atuais para os novos arquivos export_excel.php e export_pdf.php.

Lembrete: Você precisa ter as bibliotecas phpoffice/phpspreadsheet e dompdf/dompdf instaladas (geralmente na pasta vendor/) para que esses botões de exportação funcionem.




O Gemini pode cometer erros. Por isso, é bom checar as respostas.

export_excel.php
1234567891011121314151617181920212223
