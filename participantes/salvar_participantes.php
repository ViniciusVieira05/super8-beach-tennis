<?php
require_once __DIR__ . '/../utils/json_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir_todos') {
    $caminhos = caminhos_json();

    $estruturaParticipantes = [
        'participantes' => []
    ];

    $estruturaRodadas = [
        'meta' => [
            'format' => 'rotativas',
            'current_round' => 1,
            'updated_at' => date('c')
        ],
        'rounds' => []
    ];

    $sucessoParticipantes = gravar_json($caminhos['participantes'], $estruturaParticipantes);
    $sucessoRodadas = gravar_json($caminhos['rodadas'], $estruturaRodadas);

    if ($sucessoParticipantes && $sucessoRodadas) {
        header('Location: cadastro.php?sucesso=reset');
    } else {
        header('Location: cadastro.php?erro=reset');
    }
    exit;
}
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