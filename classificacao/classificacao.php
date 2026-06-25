<?php
require_once '../utils/json_helper.php';
require_once '../utils/pontuacao.php';

$dados_participantes = ler_participantes();
$dados_rodadas = ler_rodadas();

$participantes = $dados_participantes['participantes'] ?? [];
$rodadas       = $dados_rodadas['rounds'] ?? []; 

$formato = $dados_rodadas['meta']['format'] ?? 'rotativas';

$ranking = calcular_pontuacao($participantes, $rodadas, $formato);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Classificação - Torneio Super 8</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        @media print {
            .header, nav { 
                display: none !important; 
            }
            .btn-imprimir { 
                display: none !important; 
            }
            main { 
                margin-top: 0 !important; 
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Torneio Super 8 Beach Tennis</h1>
            <nav>
                <a href="../index.php">Início</a> | 
                <a href="../participantes/cadastro.php">Jogadores</a> | 
                <a href="../configuracao/configuracao.php">Configuração</a> | 
                <a href="../rodadas/rodadas.php">Rodadas</a> | 
                <a href="classificacao.php" class="active">Classificação</a>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 20px;">
        <h2>Classificação Geral</h2>
        <p style="color: #4b5563;">
            Regra aplicada: Vitória vale 2 pontos + 1 ponto por cada game vencido. 
            (Exibindo modo: <strong><?php echo $formato === 'fixas' ? 'Duplas Fixas' : 'Duplas Rotativas'; ?></strong>)
        </p>

        <div style="margin-top: 15px; text-align: right;">
            <button onclick="window.print();" class="btn-imprimir" style="background: #059669; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                🖨️ Imprimir Classificação
            </button>
        </div>

        <table class="tabela-jogadores" border="1" style="border-collapse: collapse; width: 100%; text-align: left; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th style="padding: 10px; width: 60px;">Pos.</th>
                    <th style="padding: 10px;"><?php echo $formato === 'fixas' ? 'Dupla' : 'Jogador'; ?></th>
                    <th style="padding: 10px; text-align: center;">P (Pontos)</th>
                    <th style="padding: 10px; text-align: center;">V (Vitórias)</th>
                    <th style="padding: 10px; text-align: center;">D (Derrotas)</th>
                    <th style="padding: 10px; text-align: center;">GV (Vencidos)</th>
                    <th style="padding: 10px; text-align: center;">GP (Perdidos)</th>
                    <th style="padding: 10px; text-align: center;">SG (Saldo)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($ranking)):
                    $posicao = 1; 

                    foreach ($ranking as $jogador): 
                        $sg = (int)($jogador['games_ven'] ?? 0) - (int)($jogador['games_per'] ?? 0);
                ?>
                        <tr style="<?php echo $posicao <= 3 ? 'font-weight: bold; background: #f9fafb;' : ''; ?>">
                            <td style="padding: 10px;">
                                <?php 
                                if ($posicao === 1) echo '🥇 ';
                                elseif ($posicao === 2) echo '🥈 ';
                                elseif ($posicao === 3) echo '🥉 ';
                                echo $posicao; ?>º
                            </td>
                            <td style="padding: 10px;">
                                <?php echo htmlspecialchars($jogador['nome'] ?? ''); ?>
                                <?php if (!empty($jogador['apelido']) && trim($jogador['apelido']) !== '/'): ?>
                                    <span style="color: #6b7280; font-weight: normal;">(<?php echo htmlspecialchars($jogador['apelido']); ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px; text-align: center; background: #eff6ff; color: #1e40af; font-weight: bold;"><?php echo $jogador['pontos'] ?? 0; ?></td>
                            <td style="padding: 10px; text-align: center; color: #065f46;"><?php echo $jogador['vitorias'] ?? 0; ?></td>
                            <td style="padding: 10px; text-align: center; color: #991b1b;"><?php echo $jogador['derrotas'] ?? 0; ?></td>
                            <td style="padding: 10px; text-align: center;"><?php echo $jogador['games_ven'] ?? 0; ?></td>
                            <td style="padding: 10px; text-align: center;"><?php echo $jogador['games_per'] ?? 0; ?></td>
                            <td style="padding: 10px; text-align: center; font-weight: bold; color: <?php echo $sg >= 0 ? '#047857' : '#b91c1c'; ?>;">
                                <?php echo ($sg > 0 ? '+' : '') . $sg; ?>
                            </td>
                        </tr>
                <?php 
                        $posicao++; 
                    endforeach; 
                else:
                ?>
                    <tr class="linha-vazia">
                        <td colspan="8" style="text-align: center; padding: 30px; color: #6b7280;">
                            Nenhum resultado computado até o momento. Lance placares na página de <a href="../rodadas/rodadas.php">Rodadas</a>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <script src="../js/ui.js"></script>
</body>
</html>