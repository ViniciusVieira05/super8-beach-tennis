<?php

function gerar_rodadas_torneio($participantes, $formato) {
    if (count($participantes) !== 8) return [];

    $rounds = [];

    if ($formato === 'fixas') {

        $esquemaFixas = [
            0 => [ 'q1' => [[0, 1], [2, 3]], 'q2' => [[4, 5], [6, 7]] ],
            1 => [ 'q1' => [[0, 1], [4, 5]], 'q2' => [[2, 3], [6, 7]] ], 
            2 => [ 'q1' => [[0, 1], [6, 7]], 'q2' => [[2, 3], [4, 5]] ] 
        ];

        for ($r = 1; $r <= 3; $r++) {
            $configRodada = $esquemaFixas[$r - 1];
            $rounds[] = [
                "numero" => $r,
                "partidas" => criar_estrutura_partidas($participantes, $configRodada)
            ];
        }

    } else {
        $esquemaRotativas = [
            0 => [ 'q1' => [[0, 1], [2, 3]], 'q2' => [[4, 5], [6, 7]] ],
            1 => [ 'q1' => [[0, 2], [4, 6]], 'q2' => [[1, 3], [5, 7]] ],
            2 => [ 'q1' => [[0, 3], [5, 6]], 'q2' => [[1, 2], [4, 7]] ],
            3 => [ 'q1' => [[0, 4], [1, 5]], 'q2' => [[2, 6], [3, 7]] ],
            4 => [ 'q1' => [[0, 5], [2, 7]], 'q2' => [[1, 4], [3, 6]] ],
            5 => [ 'q1' => [[0, 6], [3, 5]], 'q2' => [[1, 7], [2, 4]] ],
            6 => [ 'q1' => [[0, 7], [1, 6]], 'q2' => [[2, 5], [3, 4]] ]
        ];

        for ($r = 1; $r <= 7; $r++) {
            $configRodada = $esquemaRotativas[$r - 1];
            $rounds[] = [
                "numero" => $r,
                "partidas" => criar_estrutura_partidas($participantes, $configRodada)
            ];
        }
    }

    return $rounds;
}

function criar_estrutura_partidas($participantes, $configRodada) {
    return [
        [
            "quadra" => 1,
            "dupla_a" => [ $participantes[$configRodada['q1'][0][0]], $participantes[$configRodada['q1'][0][1]] ],
            "dupla_b" => [ $participantes[$configRodada['q1'][1][0]], $participantes[$configRodada['q1'][1][1]] ],
            "placar_a" => "",
            "placar_b" => "",
            "status" => "pendente"
        ],
        [
            "quadra" => 2,
            "dupla_a" => [ $participantes[$configRodada['q2'][0][0]], $participantes[$configRodada['q2'][0][1]] ],
            "dupla_b" => [ $participantes[$configRodada['q2'][1][0]], $participantes[$configRodada['q2'][1][1]] ],
            "placar_a" => "",
            "placar_b" => "",
            "status" => "pendente"
        ]
    ];
}