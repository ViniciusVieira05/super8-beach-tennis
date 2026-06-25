<?php

function calcular_pontuacao($participantes, $rodadas, $formato = 'rotativas') {
    $ranking = [];

    if ($formato === 'fixas') {
        foreach ($rodadas as $rodada) {
            foreach ($rodada['partidas'] as $partida) {
                $nomesA = [$partida['dupla_a'][0]['nome'], $partida['dupla_a'][1]['nome']];
                sort($nomesA);
                $idDuplaA = md5(implode('-', $nomesA));

                $nomesB = [$partida['dupla_b'][0]['nome'], $partida['dupla_b'][1]['nome']];
                sort($nomesB);
                $idDuplaB = md5(implode('-', $nomesB));

                if (!isset($ranking[$idDuplaA])) {
                    $ranking[$idDuplaA] = [
                        'id'        => $idDuplaA,
                        'nome'      => $partida['dupla_a'][0]['nome'] . ' / ' . $partida['dupla_a'][1]['nome'],
                        'apelido'   => ($partida['dupla_a'][0]['apelido'] ?? '') . ' / ' . ($partida['dupla_a'][1]['apelido'] ?? ''),
                        'is_dupla'  => true,
                        'pontos'    => 0,
                        'vitorias'  => 0,
                        'derrotas'  => 0,
                        'games_ven' => 0, 
                        'games_per' => 0  
                    ];
                }

                if (!isset($ranking[$idDuplaB])) {
                    $ranking[$idDuplaB] = [
                        'id'        => $idDuplaB,
                        'nome'      => $partida['dupla_b'][0]['nome'] . ' / ' . $partida['dupla_b'][1]['nome'],
                        'apelido'   => ($partida['dupla_b'][0]['apelido'] ?? '') . ' / ' . ($partida['dupla_b'][1]['apelido'] ?? ''),
                        'is_dupla'  => true,
                        'pontos'    => 0,
                        'vitorias'  => 0,
                        'derrotas'  => 0,
                        'games_ven' => 0, 
                        'games_per' => 0  
                    ];
                }
            }
        }

        foreach ($rodadas as $rodada) {
            foreach ($rodada['partidas'] as $partida) {
                if (($partida['status'] ?? '') === 'finalizada') {
                    $placarA = (int)$partida['placar_a'];
                    $placarB = (int)$partida['placar_b'];

                    $nomesA = [$partida['dupla_a'][0]['nome'], $partida['dupla_a'][1]['nome']];
                    sort($nomesA);
                    $idDuplaA = md5(implode('-', $nomesA));

                    $nomesB = [$partida['dupla_b'][0]['nome'], $partida['dupla_b'][1]['nome']];
                    sort($nomesB);
                    $idDuplaB = md5(implode('-', $nomesB));

                    $ranking[$idDuplaA]['games_ven'] += $placarA;
                    $ranking[$idDuplaA]['games_per'] += $placarB;
                    $ranking[$idDuplaA]['pontos']    += $placarA;

                    if ($placarA > $placarB) {
                        $ranking[$idDuplaA]['vitorias'] += 1;
                        $ranking[$idDuplaA]['pontos']   += 2;
                    } else {
                        $ranking[$idDuplaA]['derrotas'] += 1;
                    }

                    $ranking[$idDuplaB]['games_ven'] += $placarB;
                    $ranking[$idDuplaB]['games_per'] += $placarA;
                    $ranking[$idDuplaB]['pontos']    += $placarB;

                    if ($placarB > $placarA) {
                        $ranking[$idDuplaB]['vitorias'] += 1;
                        $ranking[$idDuplaB]['pontos']   += 2;
                    } else {
                        $ranking[$idDuplaB]['derrotas'] += 1;
                    }
                }
            }
        }


    } else {
        foreach ($participantes as $index => $p) {
            $idJogador = $p['id'] ?? $index;

            $ranking[$idJogador] = [
                'id'        => $idJogador,
                'nome'      => $p['nome'] ?? 'Jogador Sem Nome',
                'apelido'   => $p['apelido'] ?? '',
                'is_dupla'  => false,
                'pontos'    => 0,
                'vitorias'  => 0,
                'derrotas'  => 0,
                'games_ven' => 0, 
                'games_per' => 0  
            ];
        }

        foreach ($rodadas as $rodada) {
            foreach ($rodada['partidas'] as $partida) {
                if (($partida['status'] ?? '') === 'finalizada') {
                    $placarA = (int)$partida['placar_a'];
                    $placarB = (int)$partida['placar_b'];

                    $ganhouA = $placarA > $placarB;
                    $ganhouB = $placarB > $placarA;

                    foreach ($partida['dupla_a'] as $indexJ => $jogador) {
                        $id = $jogador['id'] ?? array_search($jogador['nome'], array_column($participantes, 'nome'));
                        if ($id === false) $id = $indexJ;

                        if (isset($ranking[$id])) {
                            $ranking[$id]['games_ven'] += $placarA;
                            $ranking[$id]['games_per'] += $placarB;
                            $ranking[$id]['pontos']    += $placarA; 

                            if ($ganhouA) {
                                $ranking[$id]['vitorias'] += 1;
                                $ranking[$id]['pontos']   += 2; 
                            } else if ($ganhouB) {
                                $ranking[$id]['derrotas'] += 1;
                            }
                        }
                    }

                    foreach ($partida['dupla_b'] as $indexJ => $jogador) {
                        $id = $jogador['id'] ?? array_search($jogador['nome'], array_column($participantes, 'nome'));
                        if ($id === false) $id = $indexJ;

                        if (isset($ranking[$id])) {
                            $ranking[$id]['games_ven'] += $placarB;
                            $ranking[$id]['games_per'] += $placarA;
                            $ranking[$id]['pontos']    += $placarB; 

                            if ($ganhouB) {
                                $ranking[$id]['vitorias'] += 1;
                                $ranking[$id]['pontos']   += 2; 
                            } else if ($ganhouA) {
                                $ranking[$id]['derrotas'] += 1;
                            }
                        }
                    }
                }
            }
        }
    }

    uasort($ranking, function($a, $b) {
        if ($b['pontos'] !== $a['pontos']) {
            return $b['pontos'] <=> $a['pontos'];
        }

        if ($b['vitorias'] !== $a['vitorias']) {
            return $b['vitorias'] <=> $a['vitorias'];
        }

        $saldoA = $a['games_ven'] - $a['games_per'];
        $saldoB = $b['games_ven'] - $b['games_per'];
        if ($saldoB !== $saldoA) {
            return $saldoB <=> $saldoA;
        }

        if ($b['games_ven'] !== $a['games_ven']) {
            return $b['games_ven'] <=> $a['games_ven'];
        }

        if (($a['is_dupla'] ?? false)) {
            return strcmp($a['nome'], $b['nome']);
        }
        return $a['id'] <=> $b['id'];
    });

    return $ranking;
}