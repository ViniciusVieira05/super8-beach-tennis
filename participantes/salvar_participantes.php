<?php
require_once __DIR__ . '/../utils/json_helper.php';

$nome    = trim($_POST['nome'] ?? '');
$apelido = trim($_POST['apelido'] ?? '');

if ($nome === '' || $apelido === '') {
    header('Location: cadastro.php?erro=nome');
    exit;
}

$dados = ler_participantes();
$participantes = $dados['participantes'] ?? [];

if (count($participantes) >= 8) {
    header('Location: cadastro.php?erro=completo');
    exit;
}

$novo_jogador = [
    'id'      => uniqid(),
    'nome'    => $nome,
    'apelido' => $apelido
];

$participantes[] = $novo_jogador;
$dados['participantes'] = $participantes;
$dados['meta']['updated_at'] = date('c');

$caminhos = caminhos_json();
if (!gravar_json($caminhos['participantes'], $dados)) {
    header('Location: cadastro.php?erro=salvar');
    exit;
}

header('Location: cadastro.php?sucesso=1');
exit;