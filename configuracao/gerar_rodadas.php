<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/sorteio.php';

$dadosParticipantes = ler_participantes();
$participantes = $dadosParticipantes['participantes'] ?? [];

if (count($participantes) !== 8) {
    header('Location: configuracao.php?erro=participantes');
    exit;
}

$formato = $_POST['formato'] ?? 'rotativas';

$totalRodadas = 7;
$rodadasGeradas = [];

$dadosRodadas = ler_rodadas();

if ($formato === 'fixas') {
    $duplasFormulario = $_POST['duplas'] ?? []; 
    
    $cicloBase = [
        1 => [
            ['dupla_a' => 1, 'dupla_b' => 2],
            ['dupla_a' => 3, 'dupla_b' => 4]
        ],
        2 => [
            ['dupla_a' => 1, 'dupla_b' => 3],
            ['dupla_a' => 2, 'dupla_b' => 4]
        ],
        3 => [
            ['dupla_a' => 1, 'dupla_b' => 4],
            ['dupla_a' => 2, 'dupla_b' => 3]
        ]
    ];

    for ($numRodada = 1; $numRodada <= 7; $numRodada++) {
        $idCiclo = $numRodada % 3;
        if ($idCiclo === 0) $idCiclo = 3; 

        $partidasDaRodada = $cicloBase[$idCiclo];
        $partidasJson = [];

        foreach ($partidasDaRodada as $indexPartida => $conf) {
            $da = $conf['dupla_a'];
            $db = $conf['dupla_b'];

            $partidasJson[] = [
                'id' => $numRodada . '_' . ($indexPartida + 1),
                'quadra' => $numeroQuadra,
                'dupla_a' => [
                    $participantes[$duplasFormulario[$da][0]] ?? ['nome' => 'Sem jogador'],
                    $participantes[$duplasFormulario[$da][1]] ?? ['nome' => 'Sem jogador']
                ],
                'dupla_b' => [
                    $participantes[$duplasFormulario[$db][0]] ?? ['nome' => 'Sem jogador'],
                    $participantes[$duplasFormulario[$db][1]] ?? ['nome' => 'Sem jogador']
                ],
                'placar_a' => 0,
                'placar_b' => 0,
                'status' => 'pendente'
            ];
        }

        $rodadasGeradas[] = [
            'numero' => $numRodada,
            'status' => 'pendente',
            'partidas' => $partidasJson
        ];
    }

    $dadosRodadas['duplas_fixas'] = $duplasFormulario;

} else {
    $dadosRodadas['duplas_fixas'] = [];
    
    $rodadasGeradas = gerar_rodadas_torneio($participantes, $formato);
}

if (empty($rodadasGeradas)) {
    header('Location: configuracao.php?erro=salvar');
    exit;
}

$dadosRodadas['meta']['format'] = $formato;
$dadosRodadas['meta']['current_round'] = 1;
$dadosRodadas['meta']['updated_at'] = date('c');
$dadosRodadas['rounds'] = $rodadasGeradas;

$caminhos = caminhos_json();
if (!gravar_json($caminhos['rodadas'], $dadosRodadas)) {
    header('Location: configuracao.php?erro=salvar');
    exit;
}

header('Location: ../rodadas/rodadas.php');
exit;