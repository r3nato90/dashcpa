<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $org_id = (int)$_POST['org_id'];
    $tipo_conta = $_POST['tipo_conta'];
    $percentual_comissao = $_POST['percentual_comissao'];
    
    // Geração de Senha Aleatória (como em create_user.php)
    $senha_aleatoria = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 12);
    
    // Salva na sessão para mostrar na tela de sucesso
    $_SESSION['new_user_details'] = ['nome' => $nome, 'email' => $email, 'senha' => $senha_aleatoria];

    try {
        // Validação da Role (segurança)
        if (!in_array($tipo_conta, ['usuario', 'sub_adm', 'admin', 'super_adm'])) {
            throw new Exception("Role inválida selecionada.");
        }

        // Verifica se o email já existe GLOBALMENTE (em qualquer das tabelas)
        $stmt_check1 = $pdo->prepare("SELECT email FROM usuarios WHERE email = ?");
        $stmt_check1->execute([$email]);
        $stmt_check2 = $pdo->prepare("SELECT email FROM sub_administradores WHERE email = ?");
        $stmt_check2->execute([$email]);

        if ($stmt_check1->fetch() || $stmt_check2->fetch()) {
            log_action($pdo, 'ERROR_CREATE_GLOBAL', "Falha (email duplicado global): $email.");
            unset($_SESSION['new_user_details']);
            header('Location: platform_manage_users.php?status=error_email_exists');
            exit;
        }

        if ($tipo_conta == 'usuario') {
            // Insere na tabela usuarios
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (org_id, nome, email, senha, percentual_comissao, id_sub_adm) 
                VALUES (?, ?, ?, ?, ?, NULL) -- Criado pelo Platform Owner, fica desvinculado
            ");
            $stmt->execute([$org_id, $nome, $email, $senha_aleatoria, $percentual_comissao]);
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'USER_CREATE_GLOBAL', "Usuário '{$nome}' (ID: {$new_id}) foi criado pelo Platform Owner.");
        
        } else {
             // Insere na tabela sub_administradores
            $username_gerado = strtolower(str_replace(' ', '', $nome)) . $org_id; // Gera um username
            
            $stmt = $pdo->prepare("
                INSERT INTO sub_administradores (org_id, nome, email, username, senha, role, percentual_comissao, parent_admin_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NULL) -- Criado pelo Platform Owner, fica desvinculado
            ");
            $stmt->execute([$org_id, $nome, $email, $username_gerado, $senha_aleatoria, $tipo_conta, $percentual_comissao]);
            $new_id = $pdo->lastInsertId();
            log_action($pdo, 'MANAGER_CREATE_GLOBAL', "Gerente '{$nome}' (ID: {$new_id}, Role: {$tipo_conta}) foi criado pelo Platform Owner.");
        }
        
        // Redireciona para a página de usuários (que mostrará a senha na sessão)
        header('Location: platform_manage_users.php');
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_CREATE_GLOBAL', "Erro crítico ao criar conta global: " . $e->getMessage());
        unset($_SESSION['new_user_details']);
        header('Location: platform_manage_users.php?status=error_general');
        exit;
    }
} else {
    header('Location: platform_manage_users.php');
    exit;
}
?>