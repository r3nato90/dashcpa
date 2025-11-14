<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// Inclui o autoload do Composer (NECESSÁRIO PARA Dompdf)
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Apenas Gerentes (todos os níveis) podem exportar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    die('Acesso negado.');
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// --- 1. Obter Filtros (Exatamente como em reports.php) ---
$filtros_aplicados = [
    'date_start' => $_GET['date_start'] ?? date('Y-m-d'),
    'date_end' => $_GET['date_end'] ?? date('Y-m-d'),
    'user_ids' => $_GET['user_ids'] ?? [],
    'admin_id' => $_GET['admin_id'] ?? '' 
];

// --- 2. Re-executar a Query (Exatamente como em reports.php) ---
$query = "SELECT r.*, u.nome AS nome_usuario 
          FROM relatorios r 
          JOIN usuarios u ON r.id_usuario = u.id_usuario 
          WHERE (r.data BETWEEN ? AND ?)";
$date_end_query = $filtros_aplicados['date_end'] . ' 23:59:59';
$params = [$filtros_aplicados['date_start'], $date_end_query];

if ($role == 'admin' || $role == 'sub_adm') {
    $query .= " AND u.id_sub_adm = ?"; $params[] = $id_logado;
}
if ($role == 'super_adm' && !empty($filtros_aplicados['admin_id'])) {
    $query .= " AND u.id_sub_adm = ?"; $params[] = $filtros_aplicados['admin_id'];
}
if (!empty($filtros_aplicados['user_ids'])) {
    $in_placeholders = implode(',', array_fill(0, count($filtros_aplicados['user_ids']), '?'));
    $query .= " AND r.id_usuario IN ($in_placeholders)"; $params = array_merge($params, $filtros_aplicados['user_ids']);
}
$query .= " ORDER BY r.data DESC";
$stmt_report = $pdo->prepare($query);
$stmt_report->execute($params);
$relatorios = $stmt_report->fetchAll(PDO::FETCH_ASSOC);

// --- 3. Calcular Totais (Exatamente como em reports.php) ---
$totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0];
foreach ($relatorios as $r) {
    $totais['deposito'] += $r['valor_deposito'];
    $totais['saque'] += $r['valor_saque'];
    $totais['bau'] += $r['valor_bau'];
    $totais['lucro'] += $r['lucro_diario'];
    $totais['comissao_user'] += $r['comissao_usuario'];
    $totais['comissao_sub'] += $r['comissao_sub_adm'];
    $totais['comissao_admin'] += $r['comissao_admin'];
}

// --- 4. Gerar o HTML para o PDF ---
$html = "
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        .container { width: 98%; margin: 0 auto; }
        h1 { text-align: center; }
        .info { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-success { color: #15803d; }
        .fw-bold { font-weight: bold; }
        tfoot tr { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Relatório Detalhado de Operações</h1>
        <div class='info'>
            Período: " . date('d/m/Y', strtotime($filtros_aplicados['date_start'])) . " a " . date('d/m/Y', strtotime($filtros_aplicados['date_end'])) . "
        </div>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Operador</th>
                    <th>Depósito</th>
                    <th>Saque</th>
                    <th>Baú</th>
                    <th>Lucro Bruto</th>
                    <th>Com. Operador (40%)</th>
                    <th>Com. Gerente (10%)</th>
                    <th>Com. Admin (50%)</th>
                </tr>
            </thead>
            <tbody>
";

foreach ($relatorios as $r) {
    $html .= "
        <tr>
            <td>" . date('d/m/Y H:i', strtotime($r['data'])) . "</td>
            <td>" . htmlspecialchars($r['nome_usuario']) . "</td>
            <td class='text-end'>" . number_format($r['valor_deposito'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['valor_saque'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['valor_bau'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['lucro_diario'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['comissao_usuario'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['comissao_sub_adm'], 2, ',', '.') . "</td>
            <td class='text-end'>" . number_format($r['comissao_admin'], 2, ',', '.') . "</td>
        </tr>
    ";
}

$html .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='2' class='text-end fw-bold'>SOMA TOTAL:</th>
                    <th class'text-end fw-bold'>" . number_format($totais['deposito'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold'>" . number_format($totais['saque'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold'>" . number_format($totais['bau'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold text-success'>" . number_format($totais['lucro'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold text-success'>" . number_format($totais['comissao_user'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold text-success'>" . number_format($totais['comissao_sub'], 2, ',', '.') . "</th>
                    <th class='text-end fw-bold text-success'>" . number_format($totais['comissao_admin'], 2, ',', '.') . "</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
";

// --- 5. Instanciar e Usar o Dompdf ---
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Opcional) Define o tamanho do papel e a orientação (paisagem)
$dompdf->setPaper('A4', 'landscape');

// Renderiza o HTML como PDF
$dompdf->render();

// Envia o PDF para o navegador
$filename = 'relatorio_cpa_' . date('Y-m-d') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]); // true = download, false = preview
exit;

?>