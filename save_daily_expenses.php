<?php
session_start();
include('config/db.php');
header('Content-Type: application/json');

// Apenas Gerentes (Super e Sub) podem salvar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit;
}
$id_admin_logado = $_SESSION['id'];

// Obter dados do POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']);
    exit;
}

$data_selecionada = $data['data'] ?? null;
$gastos_proxy = $data['gastos_proxy'] ?? 0;
$gastos_numeros = $data['gastos_numeros'] ?? 0;

// Validação básica
if (empty($data_selecionada)) {
     echo json_encode(['status' => 'error', 'message' => 'Data não fornecida.']);
    exit;
}

// Usar INSERT ... ON DUPLICATE KEY UPDATE para salvar ou atualizar
// Isso garante que só exista um registro por dia por admin
try {
    $stmt = $pdo->prepare("
        INSERT INTO despesas_diarias (data, id_admin_logado, gastos_proxy, gastos_numeros)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            gastos_proxy = VALUES(gastos_proxy),
            gastos_numeros = VALUES(gastos_numeros)
    ");
    
    $stmt->execute([
        $data_selecionada,
        $id_admin_logado,
        $gastos_proxy,
        $gastos_numeros
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Salvo com sucesso!']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
}
?>