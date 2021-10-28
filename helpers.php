<?php
// Define o locale e o fuso horário
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Bahia');

function lerDadosDosArquivosCSV($fileInputName)
{
    $corpo = [];

    foreach ($_FILES[$fileInputName]['tmp_name'] as $i => $name) {
        $arquivo = fopen($name, "r");

        $cabecalho = fgetcsv($arquivo, $_FILES[$fileInputName]['size'][$i], ",");

        while (($dados = fgetcsv($arquivo, $_FILES[$fileInputName]['size'][$i], ",")) !== FALSE) {
            $linha = array_combine($cabecalho, $dados);

            preg_match('/(\d+)\/(\d+)\/(\d+)\s(\d+:\d+)/', $linha['﻿Hora'], $partes);

            $corpo[$linha['Nome completo']][] = [
                'Data' => strtotime("{$partes[1]}-{$partes[2]}-{$partes[3]}"),
                'Hora' => $partes[4]
            ];
        }

        fclose($arquivo);
    }

    return $corpo;
}

function primeiroDiaDaSerie($data, $diaDaSemana)
{
    $diaDaData = date('w', $data);
    if ($diaDaData == $diaDaSemana)
        return $data;
    elseif ($diaDaData < $diaDaSemana)
        $diaDaSemana =  $diaDaSemana - 7;
        
    return strtotime("Sunday +$diaDaSemana days", $data);
}

function datasDeAula($dataInicio, $dataFim, $diasDaSemana)
{
    $datas = [];

    foreach ($diasDaSemana as $dia)
        $datas[] = primeiroDiaDaSerie($dataInicio, $dia);

    sort($datas);

    $i = 0;

    while ($datas[count($datas) - 1] <= $dataFim)
        $datas[] = strtotime('+1 week', $datas[$i++]);

    array_pop($datas);

    return $datas;
}

function percorreArrayDatas($datas)
{
    array_walk($datas, function ($data) {
        var_dump(utf8_encode(strftime('%A, %d de %B de %Y', $data)));
    });
}

function marcaPresenca($dadosLog, $datasAula, $horaInicio, $horaFim, $antecipacao = "00:20")
{
    $presenca = [];

    foreach ($dadosLog as $aluno => $datas) {
        $datasFiltradas = array_filter($datas, function ($data) use ($horaInicio, $horaFim, $antecipacao) {
            return estaEntre(subtraiTempos($horaInicio, $antecipacao), $data['Hora'], $horaFim);
        });
        $dias = array_unique(array_column($datasFiltradas, 'Data'));

        $presenca[$aluno] = array_intersect($datasAula, $dias);
    }

    ksort($presenca, SORT_LOCALE_STRING);

    return $presenca;
}

function marcaPresencaAssincrona($presencas, $datasAssincrona)
{
    foreach ($presencas as $aluno => $presenca) {
        $presencasAssincronas[$aluno] = array_filter($datasAssincrona, function ($el) use ($presenca) {
            foreach ($presenca as $dia) {
                if ($mesmaSemana = naMesmaSemana($el, $dia))
                    break;
            }
            return $mesmaSemana;
        });
    }
    
    return $presencasAssincronas;
}

function somaTempos(...$tempos)
{
    $i = 0;

    foreach ($tempos as $tempo) {
        sscanf($tempo, '%d:%d', $hora, $min);
        $i += $hora * 60 + $min;
    }

    if ($h = floor($i / 60)) {
        $i %= 60;
    }

    return sprintf('%02d:%02d', $h, $i);
}

function subtraiTempos($tempo, ...$tempos)
{
    sscanf($tempo, '%d:%d', $hora, $min);
    $i = $hora * 60 + $min;

    foreach ($tempos as $tempo) {
        sscanf($tempo, '%d:%d', $hora, $min);
        $i -= $hora * 60 + $min;
    }

    $i = max($i, 0);

    if ($h = floor($i / 60)) {
        $i %= 60;
    }

    return sprintf('%02d:%02d', $h, $i);
}

function estaEntre($inicio, $tempo, $fim)
{
    $hAnt = 0;
    $mAnt = 0;

    foreach (func_get_args() as $tempo) {
        sscanf($tempo, '%d:%d', $hora, $min);

        if ($hora < $hAnt || ($hora == $hAnt && $min < $mAnt))
            return false;

        list($hAnt, $mAnt) = [$hora, $min];
    }

    return true;
}

function naMesmaSemana($data1, $data2)
{
    return strtotime("next Sunday", $data1) == strtotime("next Sunday", $data2);
}
