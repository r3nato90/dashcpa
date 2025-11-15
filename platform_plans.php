<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// **** USA O NOVO HEADER ****
include('templates/header-new.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Planos de Assinatura</h2>
        <a href="platform_manage_orgs.php" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-tasks me-2"></i> Controlar Limites de Clientes
        </a>
    </div>
    
    <p class="lead mb-4">Esta página é um visualizador dos seus pacotes de serviço. O controle real (definir os limites de cada cliente) é feito na página "Gerenciar Clientes".</p>

    <div class="row justify-content-center">

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 border border-secondary">
                <div class="card-header text-center bg-light">
                    <h4 class="h5 mb-0 py-2">Plano Básico</h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        R$ 49<span class="fs-6 text-muted">,90 /mês</span>
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>5</strong> Usuários (Operadores)
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>1</strong> Gerente (Admin)
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Relatórios Completos
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Suporte Básico
                        </li>
                    </ul>
                    <a href="platform_manage_orgs.php" class="btn btn-secondary w-100 mt-auto">Gerenciar Clientes</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100 border border-primary border-3">
                <div class="card-header text-center bg-primary text-white">
                    <h4 class="h5 mb-0 py-2">Plano Pro</h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        R$ 99<span class="fs-6 text-muted">,90 /mês</span>
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>25</strong> Usuários (Operadores)
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>5</strong> Gerentes (Admin/Sub-Admin)
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Relatórios Completos
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Suporte Prioritário
                        </li>
                    </ul>
                    <a href="platform_manage_orgs.php" class="btn btn-primary w-100 mt-auto">Gerenciar Clientes</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 border border-secondary">
                <div class="card-header text-center bg-light">
                    <h4 class="h5 mb-0 py-2">Plano Empresarial</h4>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h3 class="card-title text-center fw-bold">
                        Customizado
                    </h3>
                    <hr>
                    <ul class="list-group list-group-flush mb-4 flex-grow-1">
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Usuários (Operadores) Ilimitados
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Gerentes (Admin/Sub-Admin) Ilimitados
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Relatórios Completos
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Suporte Dedicado & Integrações
                        </li>
                    </ul>
                    <a href="platform_manage_orgs.php" class="btn btn-secondary w-100 mt-auto">Gerenciar Clientes</a>
                </div>
            </div>
        </div>

    </div>
    </div>

<?php 
// **** USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>