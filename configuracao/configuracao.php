<?php
require_once __DIR__ . '/../utils/json_helper.php';

if (isset($_POST['reiniciar_torneio'])) {
    $dadosLimpos = [
        'meta' => [
            'format' => null,
            'current_round' => 1,
            'updated_at' => date('c')
        ],
        'duplas_fixas' => [],
        'rounds' => []
    ];
    $caminhos = caminhos_json();
    gravar_json($caminhos['rodadas'], $dadosLimpos);
    
    header('Location: configuracao.php?sucesso_reset=1');
    exit;
}

$participantes = ler_participantes();
$dadosParticipantes = $participantes['participantes'] ?? [];

$dadosRodadas = ler_rodadas();
$formatoAtual = $dadosRodadas['meta']['format'] ?? '';

$mensagem = '';
$classeMensagem = '';

if (isset($_GET['erro'])) {
    if ($_GET['erro'] === 'participantes') {
        $mensagem = 'Cadastre exatamente 8 participantes antes de configurar o torneio.';
        $classeMensagem = 'erro';
    } elseif ($_GET['erro'] === 'formato') {
        $mensagem = 'Escolha uma das opções para continuar.';
        $classeMensagem = 'erro';
    } elseif ($_GET['erro'] === 'salvar') {
        $mensagem = 'Não foi possível salvar a configuração. Tente novamente.';
        $classeMensagem = 'erro';
    } elseif ($_GET['erro'] === 'duplas') {
        $mensagem = 'Selecione todos os jogadores sem repetir nenhum nas duplas!';
        $classeMensagem = 'erro';
    }
} elseif (isset($_GET['sucesso'])) {
    $mensagem = 'Formato do torneio salvo com sucesso!';
    $classeMensagem = 'sucesso';
} elseif (isset($_GET['sucesso_reset'])) {
    $mensagem = 'Torneio resetado com sucesso! Você pode iniciar uma nova configuração.';
    $classeMensagem = 'sucesso';
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
    <title>Configuração do Torneio</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Torneio Super 8 Beach Tennis</h1>
            <nav>
                <a href="../index.php">Início</a> | 
                <a href="../participantes/cadastro.php">Jogadores</a> | 
                <a href="configuracao.php" class="active">Configuração</a> | 
                <a href="../rodadas/rodadas.php">Rodadas</a> | 
                <a href="../classificacao/classificacao.php">Classificação</a>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 20px;">
        <h2>Configuração do Torneio</h2>
        <p style="margin-bottom: 20px; color: #4b5563;">Escolha como as duplas serão organizadas para a geração dos jogos.</p>

        <?php if (!empty($dadosRodadas['rounds'])): ?>
            <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 16px; border-radius: 8px; margin-bottom: 20px; max-width: 550px;">
                <p style="color: #856404; margin-top: 0; margin-bottom: 12px; font-size: 14px;">
                    ⚠️ <strong>Torneio em Andamento:</strong> Já existem rodadas geradas e placares ativos no sistema.
                </p>
                <form method="POST" onsubmit="return confirm('Tem certeza que deseja apagar o torneio atual? Isso apagará permanentemente todos os placares e o ranking da classificação!');">
                    <button type="submit" name="reiniciar_torneio" style="background: #dc3545; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;">
                        🗑️ Apagar Torneio Atual e Criar Novo
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($mensagem !== ''): ?>
            <div class="alerta-<?php echo $classeMensagem; ?>" style="padding: 12px; margin-bottom: 20px; border: 1px solid <?php echo $classeMensagem === 'erro' ? '#fcc' : '#a7f3d0'; ?>; background: <?php echo $classeMensagem === 'erro' ? '#fee' : '#ecfdf5'; ?>; color: <?php echo $classeMensagem === 'erro' ? 'red' : '#065f46'; ?>; border-radius: 6px; max-width: 550px; font-size: 14px;">
                <?php echo valor_escapado($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (count($dadosParticipantes) !== 8): ?>
            <div class="alerta-erro" style="padding: 15px; border-radius: 6px; background: #fee; border: 1px solid #fcc; color: red; max-width: 550px;">
                Atenção: Você possui atualmente <strong><?= count($dadosParticipantes) ?></strong> jogadores cadastrados. O sistema exige exatamente 8 jogadores para gerar as chaves.
            </div>
        <?php else: ?>

            <div class="config-container" style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; max-width: 550px;">
                <form action="gerar_rodadas.php" method="POST">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937;">Escolha o formato</h3>
                    
                    <label style="display: block; margin-bottom: 12px; cursor: pointer; font-size: 16px; color: #374151;">
                        <input type="radio" name="formato" value="fixas" id="radio-fixas" <?php echo $formatoAtual === 'fixas' ? 'checked' : ''; ?> required> Duplas Fixas (Montadas à Mão)
                    </label>
                    
                    <label style="display: block; margin-bottom: 20px; cursor: pointer; font-size: 16px; color: #374151;">
                        <input type="radio" name="formato" value="rotativas" id="radio-rotativas" <?php echo $formatoAtual === 'rotativas' ? 'checked' : ''; ?> required> Duplas Rotativas (Sorteio Geral)
                    </label>

                    <div id="painel-duplas-manuais" style="display: none; border-top: 2px dashed #e5e7eb; padding-top: 15px; margin-bottom: 20px;">
                        <h4 style="margin-bottom: 15px; color: #1e3a8a; margin-top: 5px;">Defina as 4 Duplas do Torneio:</h4>
                        
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 12px; border: 1px solid #e5e7eb;">
                                <strong style="display: block; margin-bottom: 8px; color: #374151; font-size: 14px;">Dupla <?= $i ?>:</strong>
                                
                                <div style="display: flex; gap: 12px; width: 100%;">
                                    <select name="duplas[<?= $i ?>][0]" style="flex: 1; width: 50%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background-color: #ffffff; font-size: 14px; color: #1f2937; outline: none;">
                                        <option value="">Selecionar Jogador 1...</option>
                                        <?php foreach ($dadosParticipantes as $index => $j): ?>
                                            <option value="<?= $index ?>"><?= valor_escapado($j['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <select name="duplas[<?= $i ?>][1]" style="flex: 1; width: 50%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background-color: #ffffff; font-size: 14px; color: #1f2937; outline: none;">
                                        <option value="">Selecionar Jogador 2...</option>
                                        <?php foreach ($dadosParticipantes as $index => $j): ?>
                                            <option value="<?= $index ?>"><?= valor_escapado($j['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 15px;">
                        Salvar e Gerar Rodadas
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </main>

<script>
    const radioFixas = document.getElementById('radio-fixas');
    const radioRotativas = document.getElementById('radio-rotativas');
    const painelDuplas = document.getElementById('painel-duplas-manuais');
    const selects = painelDuplas.querySelectorAll('select');

    function gerenciarPainel() {
        if (radioFixas.checked) {
            painelDuplas.style.display = 'block';
            selects.forEach(s => s.required = true);
            validarSelecoesDuplicadas();
        } else {
            painelDuplas.style.display = 'none';
            selects.forEach(s => {
                s.required = false;
                s.disabled = false;
            });
        }
    }

    function validarSelecoesDuplicadas() {
        const valoresSelecionados = Array.from(selects)
            .map(s => s.value)
            .filter(val => val !== "");

        selects.forEach(selectAtual => {
            const valorAtual = selectAtual.value;

            Array.from(selectAtual.options).forEach(opcao => {
                if (opcao.value === "") return;

                if (valoresSelecionados.includes(opcao.value) && opcao.value !== valorAtual) {
                    opcao.disabled = true;
                    opcao.style.color = '#ccc';
                } else {
                    opcao.disabled = false;
                    opcao.style.color = '';
                }
            });
        });
    }

    radioFixas.addEventListener('change', gerenciarPainel);
    radioRotativas.addEventListener('change', gerenciarPainel);
    painelDuplas.addEventListener('change', validarSelecoesDuplicadas);
    window.addEventListener('DOMContentLoaded', gerenciarPainel);
</script>
</body>
</html>