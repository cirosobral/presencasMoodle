<?php 
require_once 'helpers.php';

// Define o locale e o fuso horário
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Bahia');

// Inicia a sessão
session_start();

// Define como serão exibidas as presenças
define("PRESENTE", '0');
define("AUSENTE", '2');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Contador de presença</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <style>
       .table-responsive {
            display: block;
            width: 100%;
            /* margin-left:15em; */
            overflow-x: scroll;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }

        .fixed-col {
            position: -webkit-sticky;
            position: sticky;
            left:0;
            top:auto;
            background-color: white;
        }

    </style>

    <!-- Compiled and minified JavaScript -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <?php if (isset($_SESSION['erro'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var Modalelem = document.querySelector('.modal');
            var instance = M.Modal.init(Modalelem);
            instance.open();
        });
    </script>
    <?php endif; ?>
</head>

<body>
    <div class="container">
        <div class="row">
            <h4>Calcula presenças</h4>
            <form class="col s12" action="verificaFaltas.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="input-field col s6">
                        <input id="input-data-inicio" type="date" name="dataInicio" value="<?php echo $_SESSION['dataInicio'] ?? ''; ?>">
                        <label for="input-data-inicio">Data de inicio</label>
                    </div>
                    <div class="input-field col s6">
                        <input id="input-data-fim" type="date" name="dataFim" value="<?php echo $_SESSION['dataFim'] ?? ''; ?>">
                        <label for="input-data-fim">Data de fim</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <input id="input-hora-inicio" type="time" name="horaInicio" value="<?php echo $_SESSION['horaInicio'] ?? ''; ?>">
                        <label for="input-hora-inicio">Hora de inicio</label>
                    </div>
                    <div class="input-field col s6">
                        <input id="input-hora-fim" type="time" name="horaFim" value="<?php echo $_SESSION['horaFim'] ?? ''; ?>">
                        <label for="input-hora-fim">Hora de fim</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <label>Dias de aulas síncornas</label>
                    </div>
                    <?php for ($i = 1 ; $i <= 6; $i++): ?>
                    <label class="col s2">
                        <input type="checkbox" name="diasSincrona[]" value="<?php echo $i; ?>"<?php echo @$_SESSION['diasSincrona'] && in_array($i, $_SESSION['diasSincrona']) ? ' checked' : ''; ?>/>
                        <span><?php echo utf8_encode(ucfirst(strftime('%A', 345600 + 86400 * $i))); ?></span>
                    </label>
                    <?php endfor; ?>
                </div>
                <div class="row">
                    <div class="col s12">
                        <label>Dias de aulas assíncornas</label>
                    </div>
                    <?php for ($i = 1 ; $i <= 6; $i++): ?>
                    <label class="col s2">
                        <input type="checkbox" name="diasAssincrona[]" value="<?php echo $i; ?>"<?php echo @$_SESSION['diasAssincrona'] && in_array($i, $_SESSION['diasAssincrona']) ? ' checked' : ''; ?>/>
                        <span><?php echo utf8_encode(ucfirst(strftime('%A', 345600 + 86400 * $i))); ?></span>
                    </label>
                    <?php endfor; ?>
                </div>
                <div class="file-field input-field">
                    <div class="waves-effect waves-light btn">
                        <span>
                            Arquivos
                        </span>
                        <input type="file" name="arquivo[]" multiple>
                    </div>
                    <div class="file-path-wrapper">
                        <input type="text" class="file-path validade" placeholder="Informe o(s) arquivo(s) de log">
                    </div>
                </div>

                <button class="waves-effect waves-light btn" type="submit">
                    Enviar
                    <i class="material-icons right">send</i>
                </button>
            </form>
        </div>
        <?php if (isset($_SESSION['presenca'])): ?>
        <h5>Lista de presenças</h5>
        <div class="row table-responsive">
            <table class="striped highlight">
                <thead>
                    <tr>
                        <th class="fixed-col">Aluno</th>
                        <?php foreach ($_SESSION['datasAulas'] as $data): ?>
                        <th><?php echo utf8_encode(strftime('%d/%m/%G', $data)); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['presenca'] as $aluno => $datas): ?>
                    <tr>
                        <td class="fixed-col"><?php echo $aluno; ?></td>
                        <?php foreach ($_SESSION['datasAulas'] as $key => $data): ?>
                        <td><?php echo naMesmaSemana($data, @$datas[intval($key / count($_SESSION['diasComAula']))]) ? PRESENTE : AUSENTE; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; if (isset($_SESSION['erro'])): ?>
        <!-- Modal Structure -->
        <div id="modal-erro" class="modal">
            <div class="modal-content">
                <h4>Erro</h4>
                <p><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Ok</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>