<?php

function caminhos_json() {
    return [
        'participantes' => __DIR__ . '/../data/participantes.json',
        'rodadas'       => __DIR__ . '/../data/rodadas.json'
    ];
}

function ler_participantes() {
    $caminhos = caminhos_json();
    if (!file_exists($caminhos['participantes'])) {
        return ['meta' => [], 'participantes' => []];
    }
    $conteudo = file_get_contents($caminhos['participantes']);
    return json_decode($conteudo, true) ?? ['meta' => [], 'participantes' => []];
}

function ler_rodadas() {
    $caminhos = caminhos_json();
    if (!file_exists($caminhos['rodadas'])) {
        return ['meta' => [], 'duplas_fixas' => [], 'rounds' => []];
    }
    $conteudo = file_get_contents($caminhos['rodadas']);
    return json_decode($conteudo, true) ?? ['meta' => [], 'duplas_fixas' => [], 'rounds' => []];
}

function gravar_json($caminho, $dados) {
    $pasta = dirname($caminho);
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    
    $textoJson = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $textoJson) !== false;
}