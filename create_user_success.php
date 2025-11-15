<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Apenas Gerentes podem ver esta página
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

// Verifica se os detalhes do novo usuário estão na sessão
if (!isset($_SESSION['new_user_details'])) {
    header('Location: manage_users.php');
    exit;
}

// Pega os detalhes e limpa da sessão
$details = $_SESSION['new_user_details'];
unset($_SESSION['new_user_details']);

include('templates/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg text-center">
                <div class="card-header bg-success text-white">
                    <h4>Conta Criada com Sucesso!</h4>
                </div>
                <div class="card-body">
                    <p>A conta para <strong><?php echo htmlspecialchars($details['nome']); ?></strong> foi criada.</p>
                    <p>Anote os dados de login e informe ao novo usuário, pois esta senha não poderá ser recuperada (apenas redefinida).</p>
                    
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Dados de Acesso:</h5>
                        <hr>
                        <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($details['email']); ?></p>
                        
                        <?php if (isset($details['username'])): ?>
                            <p class="mb-0"><strong>Username:</strong> <?php echo htmlspecialchars($details['username']); ?></p>
                        <?php endif; ?>

                        <p class="mb-0"><strong>Senha Gerada:</strong> <?php echo htmlspecialchars($details['senha']); ?></p>
                    </div>

                    <a href="manage_users.php" class="btn btn-primary mt-3">Ver Gerenciamento</a>
                    <a href="create_user.php" class="btn btn-success mt-3">Criar Outra Conta</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>