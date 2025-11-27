<?php

include_once('includes/auth_check.php');
include_once('config/conexao.php');

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['registro_status'])) {
    $rawMsg = $_SESSION['registro_status'];
    $toastMessage = strip_tags($rawMsg); 

    if (strpos(strtolower($rawMsg), 'sucesso') !== false) {
        $toastType = 'success';
    } else {
        $toastType = 'error';
    }

    unset($_SESSION['registro_status']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $novoUsuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
    $novaSenha = $_POST['senha'];
    $tipoUsuario = $_POST['select-tipo-usuario'];
    $idResponsavel = $_POST['admin-select'];

    if (!empty($novoUsuario) && !empty($novaSenha) && !empty($tipoUsuario) && !empty($idResponsavel)) {
        
        if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9])/', $novaSenha)) {
             $_SESSION['registro_status'] = "A senha deve conter maiúscula, minúscula, número e caractere especial.";
             header("Location: userRegister.php");
             exit;
        }

        $checkSql = "SELECT id_usuario FROM usuarios_cadastrados WHERE nome_usuario = ?";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->execute([$novoUsuario]); 
        
        if ($stmtCheck->rowCount() > 0) {
            $_SESSION['registro_status'] = "Erro: O usuário '$novoUsuario' já está cadastrado.";
            header("Location: userRegister.php");
            exit;
        } else {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO usuarios_cadastrados (nome_usuario, senha_hash, tipo_usuario, cadastrado_por) VALUES (?, ?, ?, ?)";
            
            try {
                $stmt = $conn->prepare($sqlInsert);
                
                if ($stmt->execute([$novoUsuario, $senhaHash, $tipoUsuario, $idResponsavel])) {
                    $_SESSION['registro_status'] = "Usuário cadastrado com sucesso!";
                    header("Location: userRegister.php");
                    exit;
                } 
            } catch (PDOException $e) {
                $_SESSION['registro_status'] = "Erro ao cadastrar: " . $e->getMessage();
                header("Location: userRegister.php");
                exit;
            }
        }
        $stmtCheck = null;

    } else {
        $_SESSION['registro_status'] = "Por favor, preencha todos os campos!";
        header("Location: userRegister.php");
        exit;
    }
}
        
$sqlSelect = 'SELECT id_usuario, nome_usuario FROM usuarios_cadastrados WHERE tipo_usuario = "usuario-admin"';
$result = $conn->query($sqlSelect); 

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="assets/css/registerStyle.css">
    <link rel="stylesheet" href="assets/css/dynamismUserRegister.css">
</head>
<body>
    <div id="toast-container"></div>
    <main>
        <div class="div-cadastro">
            <div class="logo">
                <img src="assets/LogoUnica.svg" alt="Logo Única">
            </div>
            <form action="userRegister.php" method="post" autocomplete="off" id="form-registro">
                <h2>Cadastro de Usuário</h2>
                <div class="input-grupo">
                    <label for="usuario">Usuário</label>
                    <input type="text" name="usuario" id="iusuario" placeholder="Crie seu usuário" required>
                    <span class="error-text">Informe um nome de usuário.</span>
                </div>
                <div class="input-grupo">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="isenha" placeholder="Crie sua senha" required>
                    <span class="error-text">Senha fraca: use maiúscula, minúscula, número e símbolo.</span>
                </div>
                <div class="input-grupo">
                    <label for="tipo-usuario">Tipo de Usuário</label>
                    <select name="select-tipo-usuario" id="itipo-usuario" required>
                        <option value="" disable select hidden>Selecione o tipo</option>
                        <option value="Usuário Comum">Usuário Comum</option>
                        <option value="Usuário Admin">Usuário Admin</option>
                    </select>
                    <span class="error-text">Selecione o tipo de usuário.</span>
                </div>
                <div class="input-grupo">
                    <label for="admin-responsavel">Responsável pelo cadastro</label>
                    <select class="select-admin" name="admin-select" id="iadmin-select" required>
                        <option value="" disabled selected hidden>Selecione um usuário</option>
                        <?php
                        if($result && $result->rowCount() > 0){
                            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value="' . $row['id_usuario'] . '">' . $row['nome_usuario'] . '</option>';
                            }
                        } else {
                            echo '<option value="" disabled>Nenhum usuário encontrado</option>';
                        }
                        ?>
                    </select>
                    <span class="error-text">Selecione o responsável.</span>
                </div>
                <button class="btn-register" type="submit">Enviar</button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-registro');
            form.setAttribute('novalidate', true);
            
            const inputs = form.querySelectorAll('input, select');
            
            if(inputs.length > 0) {
                inputs[0].focus();
            }

            function validarCampo(campo) {
                const errorSpan = campo.nextElementSibling;
                let valido = true;

                if (campo.hasAttribute('required') && !campo.value) {
                    valido = false;
                }

                if (valido && campo.id === 'isenha') {
                    const senha = campo.value;
                    const regexSenha = /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9])/;
                    
                    if (!regexSenha.test(senha)) {
                        valido = false;
                    }
                }

                if (!valido) {
                    campo.classList.add('invalid');
                    campo.classList.remove('valid'); 
                    
                    if(errorSpan && errorSpan.classList.contains('error-text')) {
                        errorSpan.style.display = 'block';
                    }
                    return false;
                } else {
                    campo.classList.remove('invalid');
                    
                    if(errorSpan && errorSpan.classList.contains('error-text')) {
                        errorSpan.style.display = 'none';
                    }
                    return true;
                }
            }

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validarCampo(this);
                });
                input.addEventListener('input', function() {
                    if (this.classList.contains('invalid') || this.id === 'isenha') {
                        validarCampo(this);
                    }
                });
            });

            form.addEventListener('submit', function(event) {
                let formValido = true;
                
                inputs.forEach(input => {
                    if (!validarCampo(input)) {
                        formValido = false;
                    }
                });

                if (!formValido) {
                    event.preventDefault();
                    showToast("Verifique os campos obrigatórios e a senha!", "warning");
                }
            });

            const phpMessage = "<?php echo $toastMessage; ?>";
            const phpType = "<?php echo $toastType; ?>";

            if (phpMessage) {
                showToast(phpMessage, phpType);
            }

            function showToast(message, type) {
                const toast = document.getElementById("toast-container");
                
                toast.textContent = message;
                toast.className = "show " + type; 

                setTimeout(function(){ 
                    toast.className = toast.className.replace("show", ""); 
                }, 3500);
            }
        });
    </script>
</body>
</html>

<?php
$conn = null;
?>