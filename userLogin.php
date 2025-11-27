<?php
session_start();

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['login_erro'])) {
    $rawMsg = $_SESSION['login_erro'];
    $toastMessage = strip_tags($rawMsg); 
    $toastType = 'error';
    
    unset($_SESSION['login_erro']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/loginStyle.css">
    <title>Login</title>
</head>
<body>
    <div id="toast-container"></div>
    <main>
        <div class="div-login-left">
            <img src="assets/img/LogoCompletaBranca.svg" alt="logo">
        </div>
        <div class="div-login-right">
            <form action="actions/loginVerification.php" method="post" autocomplete="off" id="form-login">
                <h2>Login</h2>
                <div class="input-grupo">
                    <input type="text" name="usuario" id="iusuario" placeholder=" " required>
                    <label for="usuario">Usuário</label>
                </div>
                <span class="error-text" id="error-usuario">Informe seu nome de usuário.</span>
                <div class="input-grupo">
                    <input type="password" name="senha" id="isenha" placeholder=" " required>
                    <label for="senha">Senha</label>
                </div>
                <span class="error-text" id="error-senha">Informe sua senha.</span>
                <button class="btn-login" type="submit" name="btn-entrar">Entrar</button>
            </form>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-login');
            const inputs = form.querySelectorAll('input, select');
            
            form.setAttribute('novalidate', true);

            function validarCampo(campo) {
                let errorSpan = campo.closest('.input-grupo') ? campo.closest('.input-grupo').nextElementSibling : campo.nextElementSibling;
                
                if (errorSpan && !errorSpan.classList.contains('error-text')) {
                     errorSpan = document.getElementById('error-' + campo.id.substring(1));
                }

                if (campo.hasAttribute('required') && !campo.value.trim()) {
                    campo.classList.add('invalid');
                    campo.classList.remove('valid');
                    if (errorSpan && errorSpan.classList.contains('error-text')) {
                        errorSpan.style.display = 'block';
                    }
                    return false;
                } else {
                    campo.classList.remove('invalid');
                    campo.classList.remove('valid');
                    if (errorSpan && errorSpan.classList.contains('error-text')) {
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
                    if (this.classList.contains('invalid')) {
                        validarCampo(this);
                    }
                });
            });

            form.addEventListener('submit', function(event) {
                
                let valido = true;
                inputs.forEach(input => {
                    if (!validarCampo(input)) {
                        valido = false;
                    }
                });

                if (!valido) {
                    event.preventDefault(); 
                    showToast("Preencha todos os campos obrigatórios!", "warning");
                }
            });

            const phpMessage = <?php echo json_encode($toastMessage); ?>;
            const phpType = <?php echo json_encode($toastType); ?>;

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