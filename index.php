<?php
include('config/db.php'); // Apenas para buscar os planos
date_default_timezone_set('America/Sao_Paulo'); 

// Busca os planos do banco de dados
$stmt_plans = $pdo->query("SELECT * FROM plans ORDER BY plan_id");
$plans = $stmt_plans->fetchAll();

// Divide os planos para fácil exibição
$plano_basico = $plans[0] ?? null;
$plano_pro = $plans[1] ?? null;
$plano_empresarial = $plans[2] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos e Preços - Sistema Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 0 1.25rem rgba(30,34,40,.04); border: none; border-radius: 0.5rem; }
        /* Adiciona espaço no topo para a barra de navegação fixa */
        .main-container { padding-top: 100px; } 
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-size: 1.5rem; font-weight: 700;">
            Acnoo Admin
        </a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
            </a>
        </div>
    </div>
</nav>
<div class="container main-container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Planos e Preços</h1>
        <p class="lead text-muted">Escolha o plano que melhor se adapta à sua equipe.</p>
    </div>

    <div class="row justify-content-center">

        <?php if ($plano_basico): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 border border-secondary">
                <div class="card-header text-center bg-light">
                    <h4 class="h5 mb-0 py-2"><?php echo htmlspecialchars($plano_basico['plan_name']); ?></h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        <?php echo htmlspecialchars($plano_basico['price_description']); ?>
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_basico['feature_1']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_basico['feature_2']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_basico['feature_3']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_basico['feature_4']); ?></li>
                    </ul>
                    <a href="register_organization.php?plan_id=<?php echo $plano_basico['plan_id']; ?>" class="btn btn-secondary w-100 mt-auto">Assinar Agora</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($plano_pro): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100 border border-primary border-3">
                <div class="card-header text-center bg-primary text-white">
                    <h4 class="h5 mb-0 py-2"><?php echo htmlspecialchars($plano_pro['plan_name']); ?></h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        <?php echo htmlspecialchars($plano_pro['price_description']); ?>
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_pro['feature_1']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_pro['feature_2']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_pro['feature_3']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_pro['feature_4']); ?></li>
                    </ul>
                    <a href="register_organization.php?plan_id=<?php echo $plano_pro['plan_id']; ?>" class="btn btn-primary w-100 mt-auto">Assinar Agora</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($plano_empresarial): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 border border-secondary">
                <div class="card-header text-center bg-light">
                    <h4 class="h5 mb-0 py-2"><?php echo htmlspecialchars($plano_empresarial['plan_name']); ?></h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        <?php echo htmlspecialchars($plano_empresarial['price_description']); ?>
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_empresarial['feature_1']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_empresarial['feature_2']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_empresarial['feature_3']); ?></li>
                        <li class="list-group-item border-0 px-0"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($plano_empresarial['feature_4']); ?></li>
                    </ul>
                    <a href="register_organization.php?plan_id=<?php echo $plano_empresarial['plan_id']; ?>" class="btn btn-secondary w-100 mt-auto">Assinar Agora</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>