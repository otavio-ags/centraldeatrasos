<?php

include_once('includes/auth_check.php';)
include_once('config/conexao.php');

$turmas = [];

try{
    $sqlTurmas = "SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma";
    $stmtTurmas = $conn -> query($sqlTurmas);
    $turmas = $stmtTurmas -> fetchALL(PDO::FETCH_ASSOC);
} catch (PDOException $e){
    $_SESSION['erro_db'] = "Erro ao carregar turmas: " . $e -> getMessage();
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAluno = filter_input(INPUT_POST, 'select-nome-aluno', FILTER_VALIDATE_INT);
    $horarioChegada = filter_input(INPUT_POST, 'horario-chegada', FILTER_SANITIZE_SPECIAL_CHARS);
    $dataAtraso = filter_input(INPUT_POST, 'data-atraso', FILTER_SANITIZE_SPECIAL_CHARS);
    $motivoAtraso = filter_input(INPUT_POST, 'select-motivo-atraso', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if ($idAluno && $horarioChegada && $dataAtraso && $motivoAtraso) {
        
        try {
            $sqlInsert = "INSERT INTO registros_atrasos 
                          (id_aluno, horario_chegada, data_atraso, motivo_atraso, registrado_em)
                          VALUES (:aluno, :horario, :data, :motivo, NOW())"; 

            $stmtInsert = $conn->prepare($sqlInsert);

            $stmtInsert->bindParam(':aluno', $idAluno, PDO::PARAM_INT);
            $stmtInsert->bindParam(':horario', $horarioChegada);
            $stmtInsert->bindParam(':data', $dataAtraso);
            $stmtInsert->bindParam(':motivo', $motivoAtraso);
            
            $stmtInsert->execute();

            $_SESSION['registro_status'] = "<p style='color: green;'> Registro de atraso salvo com sucesso!</p>";
        
            header('Location: registerDelay.php?success=1'); 
            exit; 

        } catch (PDOException $e) {
            $_SESSION['registro_status'] = "<p style='color: red;'> Erro ao registrar atraso: " . $e->getMessage() . "</p>";
            header('Location: registerDelay.php'); 
            exit;
        }

    } else {
        $_SESSION['registro_status']= "<p style='color: orange;'> Por favor, preencha todos os campos obrigatórios.</p>";
        header('Location: registerDelay.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Atrasos</title>
    <link rel="stylesheet" href="assets/css/registerDelay.css">
    <link rel="stylesheet" href="assets/css/dynamismRegisterDelay.css">    
</head>
<body>
    
    <div id="toast-container"></div>

    <main class="container">
             <section class="lado-esquerdo">
                 <div class="items-register">
                    <h2>Registro de Atraso</h2>
                    
                    <form action="actions/registerDelay.php" method="post" id="form-registro">
                        
                        <label for="turma">Turma do aluno</label><br>
                        <select name="select-turma" id="iselect-turma" required>
                            <option value="" disabled selected hidden>Selecione a turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo htmlspecialchars($turma['id_turma']); ?>">
                                    <?php echo htmlspecialchars($turma['nome_turma']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-text" id="error-turma">Selecione uma turma válida.</span>
                        <br>
                        
                        <label for="nome">Nome do aluno</label><br>
                        <select name="select-nome-aluno" id="iselect-nome-aluno" required>
                            <option value="" disable selected hidden>Selecione o aluno</option>
                        </select>
                        <span class="error-text" id="error-aluno">Selecione um aluno.</span>
                        <br>
                        
                        <label for="horario-chegada">Horário de chegada</label><br>
                        <input type="time" name="horario-chegada" id="ihorario-chegada" required>
                        <span class="error-text" id="error-hora">Informe o horário.</span>
                        <br>
                        
                        <label for="data-atraso">Data do atraso</label><br>
                        <input type="date" name="data-atraso" id="idata-atraso" required>
                        <span class="error-text" id="error-data">Informe a data.</span>
                        <br>
                        
                        <label for="motivo-atraso">Motivo do atraso</label><br>
                        <select name="select-motivo-atraso" id="iselect-motivo-atraso" required>
                            <option value="" disabled selected hidden>Selecione o motivo</option>
                            <option value="Atraso no transporte coletivo">Atraso no transporte coletivo</option>
                            <option value="Trânsito">Trânsito</option>
                            <option value="Problemas familiares">Problemas familiares</option>
                            <option value="Outros">Outros</option>
                        </select>
                        <span class="error-text" id="error-motivo">Selecione um motivo.</span>
                        <br>
                        
                        <button class="btn-register-delay" type="submit" id="btn-submit">Registrar Atraso</button>
                    </form>
                 </div>
             </section>
             <section class="lado-direito">
                <img src="img/LogoCompletaBranca.svg" alt="logo-site" class="logo-register">
             </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectTurma = document.getElementById('iselect-turma');
            const selectAluno = document.getElementById('iselect-nome-aluno');
            
            selectTurma.focus();

            selectTurma.addEventListener('change', function() {
                const idTurma = this.value;

                validarCampo(this);

                selectAluno.innerHTML = '<option value="" disabled selected hidden>Carregando alunos...</option>';
                selectAluno.disabled = true;

                fetch('actions/buscar_alunos.php?id_turma=' + idTurma)
                    .then(response => response.json())
                    .then(alunos => {
                        selectAluno.innerHTML = '<option value="" disabled selected hidden>Selecione o aluno</option>';
                        
                        if (alunos.length > 0) {
                            alunos.forEach(aluno => {
                                const option = document.createElement('option');
                                option.value = aluno.id_aluno;
                                option.textContent = aluno.nome;
                                selectAluno.appendChild(option);
                            });
                            selectAluno.disabled = false;
                            
                            selectAluno.focus(); 
                        } else {
                            selectAluno.innerHTML = '<option value="" disabled selected hidden>Nenhum aluno encontrado.</option>';
                            selectAluno.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        selectAluno.innerHTML = '<option value="" disabled selected hidden>Erro ao carregar.</option>';
                    });
            });
            
            const form = document.getElementById('form-registro');
            form.setAttribute('novalidate', true);
            const inputs = form.querySelectorAll('input, select');

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

            function validarCampo(campo) {
                const errorSpan = campo.nextElementSibling;
                
                if (campo.hasAttribute('required') && !campo.value) {
                    campo.classList.add('invalid');
                    campo.classList.remove('valid');

                    if(errorSpan && errorSpan.classList.contains('error-text')) {
                        errorSpan.style.display = 'block';
                    }
                    return false;
                } else {
                    campo.classList.remove('invalid');
                    campo.classList.add('valid');
                    if(errorSpan && errorSpan.classList.contains('error-text')) {
                        errorSpan.style.display = 'none';
                    }
                    return true;
                }
            }

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