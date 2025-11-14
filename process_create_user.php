<?php
// Este arquivo foi substituído e deve ser considerado obsoleto.
// A lógica de criação de usuários/admins está agora nos arquivos específicos:
// - register.php (para o primeiro usuário padrão, acessível publicamente)
// - register_admin.php (para criação de Admin)
// - register_subadmin.php (para criação de Sub Admin)
// - register_user.php (para criação de Usuário Comissionado)

session_start();
header('Location: create_user.php'); // Redireciona para o menu de criação correto
exit;
?>