<?php
require_once 'helpers.php';

// Inicia a sessão
session_start();

// Armazena os dados enviados
$_SESSION = $_POST;

// Converte as datas em tempo
$_POST['dataInicio'] = strtotime($_POST['dataInicio']);
$_POST['dataFim'] = strtotime($_POST['dataFim']);

// Calcula as datas das aulas sincronas e assincronas
$datasSincrona = datasDeAula($_POST['dataInicio'], $_POST['dataFim'], $_POST['diasSincrona']);
$datasAssincrona = datasDeAula($_POST['dataInicio'], $_POST['dataFim'], $_POST['diasAssincrona']);
$_SESSION['diasComAula'] = array_unique(array_merge($_POST['diasSincrona'], $_POST['diasAssincrona']));
$_SESSION['datasAulas'] = datasDeAula($_POST['dataInicio'], $_POST['dataFim'], $_SESSION['diasComAula']);

try {
    // Lê os dados do(s) arquivo(s) de log
    $dados = lerDadosDosArquivosCSV('arquivo');
    
    // Calcula as presenças
    $presencasSincrona = marcaPresenca($dados, $datasSincrona, $_POST['horaInicio'], $_POST['horaFim']);
    $presencasAssincrona = marcaPresencaAssincrona($presencasSincrona, $datasAssincrona);

    // Junta as presenças sincronas e assíncronas
    $_SESSION['presenca'] = array_merge_recursive($presencasSincrona, $presencasAssincrona);
} catch (Error $e) {
    $_SESSION['erro'] = "Envie ao menos um arquivo para o processamento.";
}

// Retorna ao index
header('Location: index.php');