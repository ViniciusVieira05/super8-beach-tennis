# Torneio Super 8 - Beach Tennis

Sistema completo em PHP para gerenciamento, sorteio de jogos e classificação automatizada de torneios de Beach Tennis com 8 participantes. Desenvolvido para suportar tanto competições de duplas fixas quanto o dinâmico formato de duplas rotativas.

---

##  Funcionalidades Principais

* **Dois Formatos de Competição:**
    * **Duplas Rotativas:** O algoritmo matemático (Round Robin) sorteia os jogos de forma que cada atleta jogue com parceiros diferentes ao longo de 7 rodadas.
    * **Duplas Fixas:** Permite a montagem prévia de 4 duplas estáticas que se enfrentam em um ciclo completo de confrontos.
* **Painel de Rodadas Dinâmico:** Gerenciamento centralizado de quadras e status de partidas (*Pendente* / *Finalizada*).
* **Trava de Segurança Contra Empates:** Validação integrada em JavaScript (front-end) e PHP (back-end) que impede a inserção de placares idênticos, respeitando a regra oficial do esporte onde não existem empates.
* **Classificação Inteligente:** Atualização instantânea da tabela baseada em critérios rigorosos de desempate, chaveada automaticamente dependendo do formato do torneio (Visão por Jogador Individual ou por Dupla Armada).
* **Relatório Pronto para Impressão:** Botão nativo configurado via CSS `@media print` para emitir tabelas limpas de classificação, omitindo menus visuais para compartilhamento direto em formato PDF ou papel.

---

## Regras de Classificação e Desempate

A tabela de pontuação aplica a ordenação baseada estritamente nos seguintes critérios sucessivos:

1.  **Pontos Gerais:** Soma de 2 pontos por vitória conquistada + 1 ponto para cada game vencido (marcado).
2.  **Número de Vitórias:** Quantidade líquida de partidas ganhas.
3.  **Saldo de Games:** Diferença matemática de ($Games Vencidos - Games Perdidos$).
4.  **Games Pró (Ataque):** Volume bruto de games marcados.
5.  **Critério Supremo:** Ordem de inscrição (IDs locais) para evitar duplicidade visual de posições.

---

##  Estrutura de Arquivos do Projeto

```text
super8/
├── index.php                      → Página inicial / menu do sistema
│
├── participantes/
│   ├── cadastro.php               → Exibe o formulário de cadastro dos 8 participantes
│   └── salvar_participantes.php   → Recebe o POST, valida e grava participantes.json
│
├── configuracao/
│   ├── configuracao.php           → Exibe opções de formato (fixas ou rotativas)
│   └── gerar_rodadas.php          → Lógica PHP de sorteio/escalonamento e geração
│                                    das 7 rodadas → grava rodadas.json
│
├── rodadas/
│   ├── rodadas.php                → Lê rodadas.json via PHP e exibe a rodada atual
│   └── salvar_placar.php          → Recebe o POST com o placar, atualiza rodadas.json
│                                    e recalcula a pontuação acumulada
│
├── classificacao/
│   └── classificacao.php          → Lê rodadas.json, calcula e ordena o ranking,
│                                    exibe a tabela de classificação completa
│
├── utils/
│   ├── json_helper.php            → Funções reutilizáveis: ler_json(), gravar_json()
│   ├── pontuacao.php              → Funções de cálculo de pontos e saldo de games
│   └── sorteio.php                → Algoritmo de geração de confrontos (Round 
│                                    para rotativas; todos-contra-todos para fixas)
│
├── css/
│   └── style.css                  → Estilos da interface
│
├── js/
│   └── ui.js                      → Apenas interações visuais: feedback de formulário,
│                                    envio assíncrono via fetch(), exibição de alertas
│
└── data/
    ├── participantes.json          → Jogadores cadastrados
    └── rodadas.json                → Rodadas, confrontos e placares lançados
