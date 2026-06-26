<?php
require_once __DIR__ . '/../utils/json_helper.php';

$dadosRodadas = ler_rodadas();
$rodadas = $dadosRodadas['rounds'] ?? [];
$rodadaAtualNum = (int)($dadosRodadas['meta']['current_round'] ?? 1);
$totalRodadas = count($rodadas); 

if (isset($_GET['rodada_ver'])) {
    $rodadaExibirNum = (int)$_GET['rodada_ver']; 
} else {
    $rodadaExibirNum = $rodadaAtualNum <= $totalRodadas ? $rodadaAtualNum : $totalRodadas;
}

$rodadaExibir = null;
foreach ($rodadas as $r) {
    if ((int)$r['numero'] === $rodadaExibirNum) {
        $rodadaExibir = $r;
        break;
    }
}

function valor_escapado($texto) {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rodadas do Torneio</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Torneio Super 8 Beach Tennis</h1>
            <nav>
                <a href="../index.php">Início</a> | 
                <a href="../participantes/cadastro.php">Jogadores</a> | 
                <a href="../configuracao/configuracao.php">Configuração</a> | 
                <a href="rodadas.php" class="active">Rodadas</a> | 
                <a href="../classificacao/classificacao.php">Classificação</a>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 20px;">
        <header style="margin-bottom: 20px;">
            <h2>Painel de Rodadas</h2>
            <p>Gerenciamento e lançamento de placares do Super 8.</p>

            <?php if ($totalRodadas > 0): ?>
                <div style="margin-top: 15px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    <span style="font-weight: bold; color: #4b5563; font-size: 14px;">Visualizar / Editar:</span>
                    <?php for ($i = 1; $i <= $totalRodadas; $i++): ?>
                        <a href="rodadas.php?rodada_ver=<?= $i ?>" 
                           style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; text-decoration: none; font-weight: bold; font-size: 14px; 
                                  background: <?= $rodadaExibirNum === $i ? '#2563eb; color: white; border-color: #2563eb;' : 'white; color: #334155;' ?>;">
                            R<?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'empate'): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 6px; margin-bottom: 25px; font-weight: bold;">
                ❌ Erro ao Salvar: Uma partida de Beach Tennis não pode terminar empatada! Ajuste os placares para que haja um vencedor.
            </div>
        <?php endif; ?>

        <?php if (!$rodadaExibir || $totalRodadas === 0): ?>
            <section class="card" style="background: #ecfdf5; padding: 20px; border-radius: 8px; border: 1px solid #a7f3d0; text-align: center;">
                <h2 style="color: #065f46; margin-top: 0;">Torneio Encerrado!</h2>
                <p style="color: #047857; margin-bottom: 20px;">Todas as rodadas foram finalizadas com sucesso e os resultados estão consolidados.</p>
                <p style="color: #475569; font-size: 14px;">Utilize os botões acima (R1, R2...) se precisar corrigir algum placar.</p>
            </section>
        <?php else: ?>
            <section class="card" style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h2 style="margin-top: 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                    Rodada <?= $rodadaExibir['numero'] ?> de <?= $totalRodadas ?>
                    <?php if ($rodadaExibir['numero'] < $rodadaAtualNum): ?>
                        <span style="font-size: 14px; color: #ef4444; font-weight: normal; margin-left: 10px;">(Modo Edição)</span>
                    <?php endif; ?>
                </h2>
                
                <form action="salvar_placar.php" method="post" onsubmit="return validarFormularioEmpate(this);">
                    <input type="hidden" name="numero_rodada" value="<?= $rodadaExibir['numero'] ?>">
                    
                    <?php foreach ($rodadaExibir['partidas'] as $index => $partida): ?>
                        <div class="partida-row" style="border-bottom: 1px solid #eee; padding: 15px 0; margin-bottom: 15px;">
                            <h4 style="margin: 0 0 10px 0; color: #4b5563;">
                                Quadra <?= $partida['quadra'] ?> 
                                <span style="font-size: 12px; font-weight: normal; padding: 2px 6px; border-radius: 4px; background: <?= $partida['status'] === 'pendente' ? '#fef3c7; color: #92400e;' : '#d1fae5; color: #065f46;'; ?>">
                                    <?= ucfirst($partida['status']) ?>
                                </span>
                            </h4>
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px; line-height: 1.4;">
                                    <strong><?= valor_escapado($partida['dupla_a'][0]['nome']) ?></strong> 
                                    <span style="color: #6b7280;">(<?= valor_escapado($partida['dupla_a'][0]['apelido'] ?? '') ?>)</span><br>
                                    <strong><?= valor_escapado($partida['dupla_a'][1]['nome']) ?></strong> 
                                    <span style="color: #6b7280;">(<?= valor_escapado($partida['dupla_a'][1]['apelido'] ?? '') ?>)</span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="number" name="partidas[<?= $index ?>][placar_a]" class="placar-a"
                                           value="<?= valor_escapado($partida['placar_a']) ?>" 
                                           min="0" max="99" style="width: 60px; padding: 8px; text-align: center; border-radius: 6px; border: 1px solid #ccc; font-weight: bold; font-size: 16px;" required>
                                    <span style="font-weight: bold; color: #9ca3af;">X</span>
                                    <input type="number" name="partidas[<?= $index ?>][placar_b]" class="placar-b"
                                           value="<?= valor_escapado($partida['placar_b']) ?>" 
                                           min="0" max="99" style="width: 60px; padding: 8px; text-align: center; border-radius: 6px; border: 1px solid #ccc; font-weight: bold; font-size: 16px;" required>
                                </div>

                                <div style="flex: 1; min-width: 200px; text-align: right; line-height: 1.4;">
                                    <strong><?= valor_escapado($partida['dupla_b'][0]['nome']) ?></strong> 
                                    <span style="color: #6b7280;">(<?= valor_escapado($partida['dupla_b'][0]['apelido'] ?? '') ?>)</span><br>
                                    <strong><?= valor_escapado($partida['dupla_b'][1]['nome']) ?></strong> 
                                    <span style="color: #6b7280;">(<?= valor_escapado($partida['dupla_b'][1]['apelido'] ?? '') ?>)</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn" style="background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; width: 100%;">
                        <?= $rodadaExibir['numero'] < $rodadaAtualNum ? 'Atualizar Placares Desta Rodada' : 'Salvar Placares e Avançar' ?>
                    </button>
                </form>
            </section>
        <?php endif; ?>
    </main>

    <script>
    function validarFormularioEmpate(form) {
        const linhasPartida = form.querySelectorAll('.partida-row');
        for (let i = 0; i < linhasPartida.length; i++) {
            const placarA = parseInt(linhasPartida[i].querySelector('.placar-a').value) || 0;
            const placarB = parseInt(linhasPartida[i].querySelector('.placar-b').value) || 0;
            if (placarA === placarB) {
                alert("⚠️ Atenção: No Beach Tennis não existe empate! Uma das partidas da rodada está com o placar igual. Ajuste os números para prosseguir.");
                return false;
            }
        }
        return true;
    }
    </script>
    <script src="../js/ui.js"></script>
</body>
</html>