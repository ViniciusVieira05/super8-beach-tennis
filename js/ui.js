document.addEventListener("DOMContentLoaded", function () {
    // 1. Busca os elementos da tela de cadastro
    const formulario = document.querySelector(".form-cadastro");
    const tabelaJogadores = document.querySelector(".tabela-jogadores tbody");
    const containerMensagem = document.querySelector(".mensagem-limite");

    // Se não estiver na página de cadastro, interrompe o script para não gerar erros
    if (!formulario || !tabelaJogadores) return;

    // 2. Conta quantas linhas (jogadores) já existem na tabela HTML
    // (Descontando linhas de aviso como "Nenhum jogador cadastrado")
    const totalJogadores = tabelaJogadores.querySelectorAll("tr:not(.linha-vazia)").length;

    // 3. Aplica as regras caso já existam 8 jogadores
    if (totalJogadores >= 8) {
        // Desativa todos os campos de texto e seleção do formulário
        const campos = formulario.querySelectorAll("input, select, button");
        campos.forEach(campo => campo.disabled = true);

        // Altera o visual do formulário para mostrar que está desativado
        formulario.style.opacity = "0.6";
        formulario.style.pointerEvents = "none";

        // Cria e exibe uma mensagem profissional de sucesso e o botão de avançar
        if (containerMensagem) {
            containerMensagem.innerHTML = `
                <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #a7f3d0;">
                    <p style="margin: 0 0 10px 0; font-weight: bold;">🎉 Limite de 8 jogadores atingido com sucesso!</p>
                    <a href="../configuracao/configuracao.php" style="display: inline-block; padding: 10px 20px; background-color: #059669; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                        Avançar para Configuração do Torneio →
                    </a>
                </div>
            `;
        }
    }
});