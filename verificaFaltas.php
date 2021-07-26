<?php
require_once 'helpers.php';

// Define o locale e o fuso horário
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Bahia');

// Inicia a sessão
session_start();

// Armazena os dados enviados
$_SESSION = $_POST;

// Converte as datas em tempo
$_POST['dataInicio'] = strtotime($_POST['dataInicio']);
$_POST['dataFim'] = strtotime($_POST['dataFim']);

// Calcula as datas das aulas sincronas e assincronas
$datasSincrona = datasDeAula($_POST['dataInicio'], $_POST['dataFim'], $_POST['diasSincrona']);
$_SESSION['diasComAula'] = array_unique(array_merge($_POST['diasSincrona'], $_POST['diasAssincrona']));
$_SESSION['datasAulas'] = datasDeAula($_POST['dataInicio'], $_POST['dataFim'], $_SESSION['diasComAula']);

try {
    // Lê os dados do(s) arquivo(s) de log
    $dados = lerDadosDosArquivosCSV('arquivo');
    
    // Calcula as presenças
    $_SESSION['presenca'] = marcaPresenca($dados, $datasSincrona, $_POST['horaInicio'], $_POST['horaFim']);
} catch (Error $e) {
    $_SESSION['erro'] = "Envie ao menos um arquivo para o processamento.";
}

// Retorna ao index
header('Location: index.php');