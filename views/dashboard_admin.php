<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
$message = (isset($_GET['status']) && $_GET['status'] == 'success') ? "<div class='alert alert-success'>Relatório enviado com sucesso!</div>" : "";

include('templates/header.php');
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-5">
            <h3>Enviar Relatório (Admin)</h3>
            <?php echo $message; ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="process_transaction.php" method="POST">
                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Usuário</label>
                             <select class="form-control" name="usuario_id" required>
                                <option value="">Selecione um usuário...</option>
                                <?php
                                // Admin lista TODOS os usuários
                                $stmt_users = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome");
                                foreach ($stmt_users->fetchAll() as $user) {
                                    echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>";
                                }
                                ?>
                            </select>
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
                        <button type="submit" class="btn btn-success w-100">Enviar Relatório</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <h2>Relatórios Recentes (Todos Usuários)</h2>
            <table id="relatoriosTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Depósito</th>
                        <th>Saque</th>
                        <th>Baú</th>
                        <th>Lucro</th>
                        <th>Com. Usuário</th>
                        <th>Com. Sub-ADM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $stmt = $pdo->query("
                            SELECT r.*, u.nome 
                            FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario 
                            ORDER BY r.data DESC LIMIT 10
                        ");
                        while ($row = $stmt->fetch()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['nome']) . "</td>
                                    <td>R$ " . number_format($row['valor_deposito'], 2, ',', '.') . "</td>
                                    <td>R$ " . number_format($row['valor_saque'], 2, ',', '.') . "</td>
                                    <td>R$ " . number_format($row['valor_bau'], 2, ',', '.') . "</td>
                                    <td>R$ " . number_format($row['lucro_diario'], 2, ',', '.') . "</td>
                                    <td>R$ " . number_format($row['comissao_usuario'], 2, ',', '.') . "</td>
                                    <td>R$ " . number_format($row['comissao_sub_adm'], 2, ',', '.') . "</td>
                                  </tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>