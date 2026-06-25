<?php
require_once __DIR__ . '/../utils/json_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rodadas.php');
    exit;
}

$numRodada = (int)($_POST['numero_rodada'] ?? 0);
$partidasPost = $_POST['partidas'] ?? [];

foreach ($partidasPost as $partidaEnviada) {
    $placarA = (int)($partidaEnviada['placar_a'] ?? 0);
    $placarB = (int)($partidaEnviada['placar_b'] ?? 0);

    if ($placarA === $placarB) {
        header('Location: rodadas.php?erro=empate');
        exit;
    }
}

$dadosRodadas = ler_rodadas();
$totalRodadas = count($dadosRodadas['rounds'] ?? []);

foreach ($dadosRodadas['rounds'] as &$rodada) {
    if ((int)$rodada['numero'] === $numRodada) {
        foreach ($rodada['partidas'] as $index => &$partida) {
            if (isset($partidasPost[$index])) {
                $partida['placar_a'] = (int)$partidasPost[$index]['placar_a'];
                $partida['placar_b'] = (int)$partidasPost[$index]['placar_b'];
                $partida['status'] = 'finalizada'; 
            }
        }
    }
}
unset($rodada);
unset($partida);

if ($numRodada === (int)($dadosRodadas['meta']['current_round'] ?? 1)) {
    if ($numRodada < $totalRodadas) {
        $dadosRodadas['meta']['current_round'] = $numRodada + 1;
    } else {
        $dadosRodadas['meta']['current_round'] = $totalRodadas + 1;
    }
}

$dadosRodadas['meta']['updated_at'] = date('c');

$caminhos = caminhos_json();
if (!gravar_json($caminhos['rodadas'], $dadosRodadas)) {
    header('Location: rodadas.php?erro=salvar');
    exit;
}

header('Location: rodadas.php');
exit;