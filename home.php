<?php

include_once('includes/auth_check.php');

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link rel="stylesheet" href="assets/css/homeStyle.css">
</head>
<body>
    <header>
        <nav>
            <img class="logo" src="assets/img/LogoUnicaMenor.svg" alt="">
            <ul class="nav-list">
                <li><a href="registerDelay.php" target="_blank">Registro de Atrasos</a></li>
                
                <?php
                if(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Usuario Admin'){
                ?>
                    <li><a href="userRegister.php" target="_blank">Cadastro de Usuário</a></li>
                <?php
                }
                ?>
                
                <li><a href="delayReports.php" target="_blank">Relatórios</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>
            O que é a Central de Atrasos?
        </h2>
        <p>
            A Central de Atrasos foi desenvolvida para transformar a gestão da pontualidade dos alunos, substituindo processos manuais e demorados. Nosso foco é simplificar a rotina, garantindo que o registro de atrasos seja uma tarefa rápida e precisa, centralizada na Secretaria.
        </p>
        <h2>
            Principais vantagens
        </h2>
        <ul>
            <li>
                <p><strong>Redução da Carga Operacional:</strong> Os professores são liberados da tarefa burocrática de registrar atrasos em sala de aula, podendo focar 100% no ensino. A Secretaria assume o registro, utilizando uma interface intuitiva e ágil.</p>
            </li>
            <li>
                <p><strong>Monitoramento Centralizado e Inteligente:</strong> Todos os dados de pontualidade dos alunos são reunidos em um único local, permitindo um melhor acompanhamento e a identificação rápida de padrões e reincidências.</p>
            </li>
            <li>
                <p><strong>Relatórios Imediatos e Prontos para Uso:</strong> Elimine o tempo gasto na compilação de dados. O sistema conta com uma aba dedicada à extração de relatórios detalhados, prontos para impressão e análise.</p>
            </li>
        </ul>
        <h2>
            Acesso: níveis de usuário
        </h2>
        <p>
            A segurança e a integridade dos dados são prioridades no Central de Atrasos. O acesso à plataforma é controlado para garantir que apenas o pessoal autorizado possa interagir com o registro e a gestão dos dados.
            Para utilizar qualquer funcionalidade, o acesso é obrigatório via login e senha individuais. Esta medida garante a rastreabilidade de todas as ações e mantém a segurança de todo o sistema.
            O cadastro e a manutenção de todas as contas de acesso são responsabilidade do Usuário Administrador (Admin). O Admin possui o controle total sobre a equipe que utilizará a plataforma.
        </p>
        <h2>
            Funcionalidades Usuário Comum
        </h2>
        <ul>
            <li>
                Permite o registro rápido e eficiente dos atrasos dos alunos, com campos para data, hora, motivo e outras informações relevantes.</p>
            </li>
            <li>
                Acesso ao histórico completo de atrasos registrados, facilitando o acompanhamento da pontualidade dos alunos ao longo do tempo.</p>
            </li>
            <li>
                Capacidade de imprimir relatórios básicos sobre os atrasos registrados, úteis para análises rápidas e tomadas de decisão.</p>
            </li>
        </ul>
        <h2>
            Funcionalidades Usuário Admin
        </h2>
        <ul>
            <li>
                <p>Todas as Funcionalidades do Usuário Comum</p>
            </li>
            <li>
                <p> Criação e exclusão de contas de usuários, permitindo o controle total sobre quem tem acesso ao sistema.</p>
            </li>
        </ul>
        <h2>
            Fluxo de Registo de Atrasos
        </h2>
        <p>
            O processo de registro de atrasos foi simplificado para ser concluído em segundos, garantindo precisão sem comprometer a fluidez da rotina escolar.
        </p>
        <ol>
            <li>
                <p>Ao receber o aluno atrasado, o funcionário navega até a aba "Registro de Atrasos" no sistema, inserindo as informações necessárias contidas na aba.</p>
            </li>
            <li>
                <p>
                    Após o registro, realizar a impressão do termo de autorização para que o aluno possa entrar na sala de aula.
                </p>
            </li>
            <li>
                <p>
                    Com a autorização de entrada em mãos, o aluno é liberado e pode se dirigir à sala. O professor recebe o aluno apenas mediante a apresentação deste comprovante, garantindo que o atraso foi formalmente registrado na Secretaria.
                </p>
            </li>
        </ol>
        <p>
            Este fluxo não só agiliza o processo na porta da escola, mas também assegura que os dados de pontualidade sejam coletados de forma completa e instantânea para futuras análises e relatórios.
        </p>
    </main>
</body>
</html>