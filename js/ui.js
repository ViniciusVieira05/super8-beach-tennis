document.addEventListener("DOMContentLoaded", function () {
    // 1. Busca os elementos da tela de cadastro
    const formulario = document.querySelector(".form-cadastro");
    const tabelaJogadores = document.querySelector(".tabela-jogadores tbody");
    const containerMensagem = document.querySelector(".mensagem-limite");

    if (!formulario || !tabelaJogadores) return;

    const totalJogadores = tabelaJogadores.querySelectorAll("tr:not(.linha-vazia)").length;

    if (totalJogadores >= 8) {
        const campos = formulario.querySelectorAll("input, select, button");
        campos.forEach(campo => campo.disabled = true);

        formulario.style.opacity = "0.6";
        formulario.style.pointerEvents = "none";

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