<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Verificação Multi-Tenant
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role']; // Role de quem está LOGADO
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

// --- **** 1. VERIFICAR LIMITES DO PLANO **** ---
$stmt_plan = $pdo->prepare("
    SELECT 
        o.max_users, 
        o.max_admins,
        (SELECT COUNT(*) FROM usuarios WHERE org_id = o.org_id) as current_users,
        (SELECT COUNT(*) FROM sub_administradores WHERE org_id = o.org_id AND role IN ('admin', 'sub_adm')) as current_admins
    FROM organizations o
    WHERE o.org_id = ?
");
$stmt_plan->execute([$org_id]);
$plan = $stmt_plan->fetch();

if (!$plan) {
    log_action($pdo, 'ERROR_CREATE', "Falha ao criar conta. Organização (ID: $org_id) não encontrada.");
    header('Location: create_user.php?status=error_org_not_found');
    exit;
}

$tipo_conta = $_POST['tipo_conta'] ?? 'usuario'; // Tipo de conta a SER CRIADA

// --- **** 2. ENFORÇAR OS LIMITES **** ---
if ($tipo_conta == 'usuario') {
    if ($plan['current_users'] >= $plan['max_users']) {
        log_action($pdo, 'LIMIT_REACHED', "Falha ao criar usuário. Limite de usuários ({$plan['max_users']}) atingido.");
        header('Location: create_user.php?status=limit_users');
        exit;
    }
} else { // admin ou sub_adm
    if ($plan['current_admins'] >= $plan['max_admins']) {
        log_action($pdo, 'LIMIT_REACHED', "Falha ao criar gerente. Limite de gerentes ({$plan['max_admins']}) atingido.");
        header('Location: create_user.php?status=limit_admins');
        exit;
    }
}
// --- **** FIM DA VERIFICAÇÃO DE LIMITE **** ---


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $senha_aleatoria = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 12);
    
    $_SESSION['new_user_details'] = ['nome' => $nome, 'email' => $email, 'senha' => $senha_aleatoria];

    try {
        if ($tipo_conta == 'usuario') {
            $id_sub_adm_vinculado = null;
            
            if ($role == 'sub_adm') {
                // Se um N2 cria um N3, ele é vinculado ao N2
                $id_sub_adm_vinculado = $id_logado;
            } 
            elseif ($role == 'admin' || $role == 'super_adm') {
                // Se N1 ou Dono criam N3, eles usam o dropdown
                $id_sub_adm_vinculado = (!empty($_POST['id_sub_adm'])) ? (int)$_POST['id_sub_adm'] : null;
            }

            // (Validação de segurança: Dono/N1 só podem vincular a quem eles gerenciam - Omitida por simplicidade, mas o 'edit_user' já tem)

            // Verifica email DENTRO da organização
            $stmt_check1 = $pdo->prepare("SELECT email FROM usuarios WHERE email = ? AND org_id = ?");
            $stmt_check1->execute([$email, $org_id]);
            $stmt_check2 = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? AND org_id = ?");
            $stmt_check2->execute([$email, $org_id]);

            if ($stmt_check1->fetch() || $stmt_check2->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha (email duplicado): $email.");
                header('Location: create_user.php?status=error_email');
                exit;
            }

            // Insere com org_id
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (org_id, nome, email, senha, percentual_comissao, id_sub_adm) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$org_id, $nome, $email, $senha_aleatoria, $percentual_comissao, $id_sub_adm_vinculado]);
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'USER_CREATE', "Usuário '{$nome}' (ID: {$new_id}) foi criado. Gerente ID: {$id_sub_adm_vinculado}.");
        } 
        
        else if (in_array($tipo_conta, ['admin', 'sub_adm'])) {
            if ($role == 'sub_adm') throw new Exception("Permissão negada (Sub-Adm tentando criar gerente).");
            if ($role == 'admin' && $tipo_conta == 'admin') throw new Exception("Admins (N1) não podem criar outros Admins (N1).");

            $username = $_POST['username'];
            $_SESSION['new_user_details']['username'] = $username; 

            // Verifica email/username DENTRO da organização
            $stmt_check_email = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ? AND org_id = ?");
            $stmt_check_email->execute([$email, $org_id]);
            $stmt_check_email_user = $pdo->prepare("SELECT email FROM usuarios WHERE email = ? AND org_id = ?");
            $stmt_check_email_user->execute([$email, $org_id]);
            if ($stmt_check_email->fetch() || $stmt_check_email_user->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha (email duplicado): $email.");
                header('Location: create_user.php?status=error_email');
                exit;
            }
            $stmt_check_user = $pdo->prepare("SELECT username FROM sub_administradores WHERE username = ? AND org_id = ?");
            $stmt_check_user->execute([$username, $org_id]);
            if ($stmt_check_user->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha (username duplicado): $username.");
                header('Location: create_user.php?status=error_username');
                exit;
            }

            // --- **** INÍCIO DA CORREÇÃO (BUG Hierarquia) **** ---
            
            $parent_admin_id = null;
            // Se o usuário logado for um 'admin' (N1) e ele está criando um 'sub_adm' (N2)
            if ($role == 'admin' && $tipo_conta == 'sub_adm') {
                $parent_admin_id = $id_logado; // O N1 logado é o pai
            }
            // Se o 'super_adm' (Dono) cria, o parent_admin_id fica NULL (ligado ao Dono)

            $stmt = $pdo->prepare("
                INSERT INTO sub_administradores (org_id, nome, email, username, senha, role, percentual_comissao, parent_admin_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$org_id, $nome, $email, $username, $senha_aleatoria, $tipo_conta, $percentual_comissao, $parent_admin_id]);
            
            // --- **** FIM DA CORREÇÃO **** ---
            
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'MANAGER_CREATE', "Gerente '{$nome}' (ID: {$new_id}, Role: {$tipo_conta}) foi criado. Pai ID: {$parent_admin_id}.");
        }
        
        header('Location: create_user_success.php');
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_CREATE', "Erro crítico ao criar conta: " . $e->getMessage());
        unset($_SESSION['new_user_details']);
        // Redireciona de volta com a mensagem de erro
        header('Location: create_user.php?status=error_general&msg=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: create_user.php');
    exit;
}
?>