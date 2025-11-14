<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído

// Apenas Gerentes podem processar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Coletar dados comuns
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $tipo_conta = $_POST['tipo_conta'] ?? 'usuario'; // Default 'usuario' (para Sub-Admin)

    // 2. Gerar senha aleatória
    $senha_aleatoria = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 12);
    
    // Salva os detalhes para a página de sucesso
    $_SESSION['new_user_details'] = [
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha_aleatoria
    ];

    try {
        // --- ROTA 1: CRIANDO UM USUÁRIO (OPERADOR) ---
        if ($tipo_conta == 'usuario') {
            
            // Determinar o vínculo (Gerente/Admin)
            $id_sub_adm = null;
            if ($role == 'super_adm') {
                // Super Admin pode escolher o gerente na criação
                $id_sub_adm = (!empty($_POST['id_sub_adm'])) ? (int)$_POST['id_sub_adm'] : null;
            } else {
                // Admin ou Sub-Admin vincula a si mesmo
                $id_sub_adm = $id_logado; 
            }

            // Verificar se o email já existe em ambas as tabelas
            $stmt_check1 = $pdo->prepare("SELECT email FROM usuarios WHERE email = ?");
            $stmt_check1->execute([$email]);
            $stmt_check2 = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ?");
            $stmt_check2->execute([$email]);

            if ($stmt_check1->fetch() || $stmt_check2->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha ao criar usuário. Email duplicado: $email.");
                header('Location: create_user.php?status=error_email');
                exit;
            }

            // Inserir na tabela 'usuarios'
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nome, email, senha, percentual_comissao, id_sub_adm) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $email, $senha_aleatoria, $percentual_comissao, $id_sub_adm]);
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'USER_CREATE', "Usuário '{$nome}' (ID: {$new_id}) foi criado. Gerente ID: {$id_sub_adm}.");
        } 
        
        // --- ROTA 2: CRIANDO UM GERENTE (ADMIN / SUB-ADMIN) ---
        else if (in_array($tipo_conta, ['admin', 'sub_adm'])) {
            
            // Permissão: Sub-Admin não pode criar outros gerentes
            if ($role == 'sub_adm') {
                throw new Exception("Permissão negada (Sub-Adm tentando criar gerente).");
            }
            // Permissão: Admin só pode criar Sub-Adm (não outro Admin)
            if ($role == 'admin' && $tipo_conta == 'admin') {
                // Admin só pode criar Sub-Admin. SuperAdmin pode criar Admin.
                throw new Exception("Admins só podem criar Sub-Admins.");
            }

            $username = $_POST['username'];
            $_SESSION['new_user_details']['username'] = $username; // Adiciona para a pág. de sucesso

            // Verificar se o email ou username já existe
            $stmt_check_email_sub = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ?");
            $stmt_check_email_sub->execute([$email]);
            $stmt_check_user = $pdo->prepare("SELECT username FROM sub_administradores WHERE username = ?");
            $stmt_check_user->execute([$username]);
            $stmt_check_email_user = $pdo->prepare("SELECT email FROM usuarios WHERE email = ?");
            $stmt_check_email_user->execute([$email]);

            if ($stmt_check_email_sub->fetch() || $stmt_check_email_user->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha ao criar gerente. Email duplicado: $email.");
                header('Location: create_user.php?status=error_email');
                exit;
            }
            if ($stmt_check_user->fetch()) {
                log_action($pdo, 'ERROR_CREATE', "Falha ao criar gerente. Username duplicado: $username.");
                header('Location: create_user.php?status=error_username');
                exit;
            }

            // Inserir na tabela 'sub_administradores'
            $stmt = $pdo->prepare("
                INSERT INTO sub_administradores (nome, email, username, senha, role, percentual_comissao) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $email, $username, $senha_aleatoria, $tipo_conta, $percentual_comissao]);
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'MANAGER_CREATE', "Gerente '{$nome}' (ID: {$new_id}) foi criado com role '{$tipo_conta}'.");
        }
        
        // 5. Redirecionar para a página de sucesso
        header('Location: create_user_success.php');
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_CREATE', "Erro crítico ao criar conta: " . $e->getMessage());
        unset($_SESSION['new_user_details']);
        echo "Erro ao criar usuário: " . $e->getMessage();
    }
} else {
    header('Location: create_user.php');
    exit;
}
?>