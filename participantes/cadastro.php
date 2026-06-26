<?php
require_once '../utils/json_helper.php';

$dadosParticipantes = ler_participantes();
$participantes = $dadosParticipantes['participantes'] ?? [];
$totalJogadores = count($participantes);
$limiteAtingido = ($totalJogadores >= 8);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Jogadores - Torneio Super 8</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Torneio Super 8 Beach Tennis</h1>
            <nav>
                <a href="../index.php">Início</a> | 
                <a href="cadastro.php" class="active">Jogadores</a> | 
                <a href="../configuracao/configuracao.php">Configuração</a> | 
                <a href="../rodadas/rodadas.php">Rodadas</a> | 
                <a href="../classificacao/classificacao.php">Classificação</a>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 20px;">
        
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'reset'): ?>
            <div style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                ✅ O torneio foi resetado com sucesso! Todos os dados foram limpos.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'reset'): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                ❌ Ocorreu um erro ao tentar resetar os arquivos do sistema. Verifique as permissões de escrita.
            </div>
        <?php endif; ?>

        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0;">Status das Inscrições</h3>
                <p style="margin: 5px 0 0 0; color: #4b5563;">Vagas preenchidas: <strong><?php echo $totalJogadores; ?> de 8</strong></p>
            </div>
            
            <?php if ($limiteAtingido): ?>
                <a href="../configuracao/configuracao.php" style="background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Configurar Chaves do Torneio →
                </a>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            <?php if (!$limiteAtingido): ?>
                <div style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <form action="salvar_participantes.php" method="POST" class="form-cadastro">
                        <h3 style="margin-top: 0;">Novo Cadastro</h3>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; margin-bottom: 5px;">Nome Completo:</label>
                            <input type="text" name="nome" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="display:block; margin-bottom: 5px;">Apelido:</label>
                            <input type="text" name="apelido" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <button type="submit" style="width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                            Cadastrar Jogador
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="flex: 1; min-width: 300px; background: #ecfdf5; padding: 20px; border-radius: 8px; border: 1px solid #a7f3d0; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                    <h3 style="color: #065f46; margin: 10px 0 5px 0;">Inscrições Encerradas</h3>
                    <p style="color: #047857; margin: 0;">Todas as 8 vagas do torneio já foram preenchidas. Use o botão acima para gerar os confrontos.</p>
                </div>
            <?php endif; ?>

            <div style="flex: 1.5; min-width: 350px;">
                <h3 style="margin-top: 0;">Jogadores Confirmados</h3>
                <table class="tabela-jogadores" border="1" style="border-collapse: collapse; width: 100%; text-align: left;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Nome</th>
                            <th style="padding: 10px;">Apelido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($participantes)): ?>
                            <?php foreach ($participantes as $index => $jogador): ?>
                                <tr>
                                    <td style="padding: 10px;"><?php echo $index + 1; ?></td>
                                    <td style="padding: 10px;"><?php echo htmlspecialchars($jogador['nome'] ?? ''); ?></td>
                                    <td style="padding: 10px;"><?php echo htmlspecialchars($jogador['apelido'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="linha-vazia">
                                <td colspan="3" style="padding: 20px; text-align: center; color: #6b7280;">Nenhum jogador cadastrado ainda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

<?php if (!empty($participantes)): ?>
    <div style="margin-top: 25px; text-align: right;">
        <form action="salvar_participantes.php" method="POST" onsubmit="return confirmarExclusaoGeral();" style="display: inline-block;">
            
            <input type="hidden" name="acao" value="excluir_todos">
            
            <button type="submit" style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
                🗑️ Excluir Todos e Resetar Torneio
            </button>
        </form>
    </div>
<?php endif; ?>

    <script src="../js/ui.js"></script>
</body>
</html>