<?php include('templates/header.php'); ?>
<div class="container mt-5">
    <h3>Inserir Transação</h3>
    <form action="process_transaction.php" method="POST">
        <div class="mb-3">
            <label for="usuario_id" class="form-label">ID do Usuário</label>
            <input type="number" class="form-control" id="usuario_id" name="usuario_id" required>
        </div>
        <div class="mb-3">
            <label for="tipo_transacao" class="form-label">Tipo de Transação</label>
            <select class="form-control" id="tipo_transacao" name="tipo_transacao">
                <option value="deposito">Depósito</option>
                <option value="saque">Saque</option>
                <option value="bau">Bau</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="number" class="form-control" id="valor" name="valor" required>
        </div>
        <button type="submit" class="btn btn-success">Enviar</button>
    </form>
</div>
<?php include('templates/footer.php'); ?>
