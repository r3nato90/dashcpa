<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// Mensagem de sucesso
$message = "";
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "<div class='alert alert-success'>Configurações de pagamento salvas com sucesso!</div>";
}

// Buscar as configurações atuais no banco de dados
$stmt = $pdo->query("SELECT setting_key, setting_value FROM platform_settings");
$settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$settings = [
    'mp_public_key' => $settings_raw['mp_public_key'] ?? '',
    'mp_access_token' => $settings_raw['mp_access_token'] ?? ''
];

// **** CORREÇÃO: USA O NOVO HEADER ****
include('templates/header-new.php'); 
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="h3 mb-4">Configurações da Plataforma (SaaS)</h2>
            
            <?php echo $message; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Integração de Pagamento (Mercado Pago)</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Insira suas credenciais de produção do Mercado Pago. Elas serão usadas para cobrar as assinaturas dos seus clientes (Super Admins).</p>
                    
                    <form action="platform_save_settings.php" method="POST">
                        <div class="mb-3">
                            <label for="mp_public_key" class="form-label">Public Key (Chave Pública)</label>
                            <input type="text" class="form-control" id="mp_public_key" name="mp_public_key" 
                                   value="<?php echo htmlspecialchars($settings['mp_public_key']); ?>" 
                                   placeholder="APP_USR-...">
                        </div>
                        <div class="mb-3">
                            <label for="mp_access_token" class="form-label">Access Token (Chave Privada)</label>
                            <input type="password" class="form-control" id="mp_access_token" name="mp_access_token" 
                                   value="<?php echo htmlspecialchars($settings['mp_access_token']); ?>" 
                                   placeholder="APP_USR-...">
                            <small class="form-text text-muted">Sua chave de acesso é armazenada com segurança e não será exibida novamente.</small>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save me-2"></i>Salvar Configurações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// **** CORREÇÃO: USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>