<?php
session_start();

if(isset($_POST['btn-entrar']) && !empty($_POST['usuario']) && !empty($_POST['senha'])){

    include_once('../config/conexao.php');

    $usuarioLogin = $_POST['usuario'];
    $senhaLogin = $_POST['senha'];
    
    $slqSelect = "SELECT id_usuario, nome_usuario, tipo_usuario, senha_hash FROM usuarios_cadastrados WHERE nome_usuario = ? COLLATE utf8mb4_bin";

    try {
        $stmt = $conn -> prepare($slqSelect);
        $stmt -> execute([$usuarioLogin]);

        $usuarioBanco = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        if($usuarioBanco && password_verify($senhaLogin, $usuarioBanco['senha_hash'])){
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $usuarioBanco['id_usuario'];
            $_SESSION['nome_usuario'] = $usuarioBanco['nome_usuario'];
            $_SESSION['tipo_usuario'] = $usuarioBanco['tipo_usuario'];

            header('Location: ../home.php');
            exit;
        } else {
            $_SESSION['login_erro'] = "Usuário ou senha inválidos.";
            header("Location: ../userLogin.php");
            exit;
        }
    
    } catch(PDOException $e) {
        $_SESSION['login_erro'] = "Erro no sistema: " . $e->getMessage();
        header('Location: ../userLogin.php');
        exit;
    }
} else{
    $_SESSION['login_erro'] = "Preencha todos os campos.";
    header('Location: ../userLogin.php');
    exit;
}

?>