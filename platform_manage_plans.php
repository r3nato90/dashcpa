<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// Mensagens de status
$message = "";
if (isset($_GET['status']) && $_GET['status'] == 'plan_updated') {
    $message = "<div class='alert alert-success'>Plano atualizado com sucesso!</div>";
}

// Busca os planos do banco de dados
$stmt_plans = $pdo->query("SELECT * FROM plans ORDER BY plan_id");
$plans = $stmt_plans->fetchAll();

include('templates/header-new.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Gerenciar Planos de Assinatura</h2>
    </div>
    
    <?php echo $message; ?>

    <div class="alert alert-info">
        <strong>Instruções:</strong><br>
        1. Crie um "Link de Pagamento" (Assinatura Recorrente) dentro do seu painel do Mercado Pago para cada plano.<br>
        2. Copie o link de pagamento e cole no campo "Link de Pagamento (Checkout)" abaixo.
    </div>

    <div class="row justify-content-center">
        <?php foreach ($plans as $plan): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <form action="platform_save_plan.php" method="POST">
                <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                <div class="card shadow-sm h-100 <?php if($plan['plan_id'] == 2) echo 'border-primary border-3'; ?>">
                    <div class="card-header text-center <?php if($plan['plan_id'] == 2) echo 'bg-primary text-white'; else echo 'bg-light'; ?>">
                        <h4 class="h5 mb-0 py-2"><?php echo htmlspecialchars($plan['plan_name']); ?></h4>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-3">
                            <label class="form-label">Descrição do Preço</label>
                            <input type="text" name="price_description" class="form-control" value="<?php echo htmlspecialchars($plan['price_description']); ?>">
                        </div>
                        <hr>
                        <label class="form-label">Recursos (Features)</label>
                        <input type="text" name="feature_1" class="form-control form-control-sm mb-2" value="<?php echo htmlspecialchars($plan['feature_1']); ?>">
                        <input type="text" name="feature_2" class="form-control form-control-sm mb-2" value="<?php echo htmlspecialchars($plan['feature_2']); ?>">
                        <input type="text" name="feature_3" class="form-control form-control-sm mb-2" value="<?php echo htmlspecialchars($plan['feature_3']); ?>">
                        <input type="text" name="feature_4" class="form-control form-control-sm mb-3" value="<?php echo htmlspecialchars($plan['feature_4']); ?>">
                        
                        <label class="form-label">Limites Padrão (Novas Contas)</label>
                        <div class="row mb-3">
                            <div class="col-6"><label class="small">Max. Admins</label><input type="number" name="default_max_admins" class="form-control" value="<?php echo $plan['default_max_admins']; ?>"></div>
                            <div class="col-6"><label class="small">Max. Usuários</label><input type="number" name="default_max_users" class="form-control" value="<?php echo $plan['default_max_users']; ?>"></div>
                        </div>

                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link de Pagamento (Checkout)</label>
                            <input type="url" name="mercadopago_link" class="form-control" value="<?php echo htmlspecialchars($plan['mercadopago_link']); ?>" placeholder="https://mpago.la/...">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-auto">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
// **** USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>