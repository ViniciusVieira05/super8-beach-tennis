<?php
require_once 'utils/json_helper.php';

// Carrega os dados para exibir um resumo na tela inicial
$dadosParticipantes = ler_participantes();
$participantes = $dadosParticipantes['participantes'] ?? [];
$totalJogadores = count($participantes);

$dadosRodadas = ler_rodadas();
$formato = $dadosRodadas['meta']['format'] ?? null;
$rodadaAtual = $dadosRodadas['meta']['current_round'] ?? 1;
$totalRodadas = count($dadosRodadas['rounds'] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneio Super 8 - Beach Tennis</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Torneio Super 8 Beach Tennis</h1>
            <nav>
                <a href="index.php" class="active">Início</a> | 
                <a href="participantes/cadastro.php">Jogadores</a> | 
                <a href="configuracao/configuracao.php">Configuração</a> | 
                <a href="rodadas/rodadas.php">Rodadas</a> | 
                <a href="classificacao/classificacao.php">Classificação</a>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 30px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2>Bem-vindo ao Gerenciador de Torneios Super 8</h2>
            <p style="color: #4b5563; font-size: 16px;">Organize seus campeonatos de Beach Tennis nos formatos de Duplas Fixas ou Rotativas de forma simples e rápida.</p>
        </div>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
            
            <div class="card" style="flex: 1; min-width: 280px; max-width: 350px; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; text-align: center;">
                <h3>Jogadores</h3>
                <p style="color: #4b5563;"><strong><?= $totalJogadores ?> de 8</strong> inscritos</p>
                <a href="participantes/cadastro.php" class="btn" style="display: block; background: #2563eb; color: white; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; margin-top: 15px;">
                    <?= $totalJogadores < 8 ? 'Cadastrar Jogadores' : 'Ver Lista' ?>
                </a>
            </div>

            <div class="card" style="flex: 1; min-width: 280px; max-width: 350px; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; text-align: center;">
                <h3>Configuração</h3>
                <p style="color: #4b5563;">
                    Formatos: <strong><?= $formato ? ($formato === 'fixas' ? 'Duplas Fixas' : 'Duplas Rotativas') : 'Não definido' ?></strong>
                </p>
                <a href="configuracao/configuracao.php" class="btn" style="display: block; background: #6c757d; color: white; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; margin-top: 15px;">
                    Configurar Chaves
                </a>
            </div>

            <div class="card" style="flex: 1; min-width: 280px; max-width: 350px; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; text-align: center;">
                <h3>Andamento</h3>
                <p style="color: #4b5563;">
                    <?php if (!$formato): ?>
                        Aguardando chaves...
                    <?php elseif ($rodadaAtual > $totalRodadas): ?>
                        <strong>Torneio Concluído!</strong>
                    <?php else: ?>
                        Rodada atual: <strong><?= $rodadaAtual ?> de <?= $totalRodadas ?></strong>
                    <?php endif; ?>
                </p>
                <a href="rodadas/rodadas.php" class="btn" style="display: block; background: #059669; color: white; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; margin-top: 15px;">
                    Ir para as Quadras
                </a>
            </div>

        </div>
    </main>

</body>
</html>