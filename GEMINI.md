# Resumo do Projeto e Decisões de Design (Gerado por Gemini CLI)

Este documento resume as principais decisões arquiteturais e implementações realizadas para adicionar funcionalidade de temas dinâmicos a este projeto Laravel.

## Objetivo Principal
Permitir a troca de Design Systems (atualmente GOV.BR, Bootstrap e Materialize) para os componentes da aplicação, controlada por uma variável de ambiente.

## Arquitetura de Temas Dinâmicos

1.  **Seleção de Tema:**
    *   Uma variável de ambiente `DESIGN_SYSTEM` no `.env` (ex: `DESIGN_SYSTEM=govbr`, `DESIGN_SYSTEM=bootstrap`, `DESIGN_SYSTEM=materialize`) define o tema ativo.
    *   Um arquivo de configuração Laravel (`config/design.php`) lê essa variável (`config('design.system')`).

2.  **Carregamento de CSS Temático:**
    *   Arquivos CSS específicos para cada tema são criados em `resources/css/themes/` (ex: `govbr.css`, `bootstrap.css`, `materialize.css`).
    *   `govbr.css` importa o `@govbr-ds/core/dist/core.css` do `node_modules`.
    *   `bootstrap.css` importa o `bootstrap/dist/css/bootstrap.min.css` do `node_modules`.
    *   `materialize.css` importa o `materialize-css/dist/css/materialize.min.css` do `node_modules`.
    *   O `vite.config.js` é configurado para compilar esses arquivos temáticos.
    *   O layout principal (`resources/views/welcome.blade.php`) usa `@vite(['resources/css/themes/' . config('design.system') . '.css'])` para carregar dinamicamente o CSS do tema ativo.

3.  **Views de Componentes Temáticas:**
    *   As views dos componentes são organizadas em subdiretórios por tema (ex: `resources/views/livewire/govbr/botao.blade.php`, `resources/views/livewire/bootstrap/botao.blade.php`, `resources/views/livewire/materialize/botao.blade.php`).
    *   A classe PHP do componente (seja Blade Component ou Livewire Component) usa `config('design.system')` para retornar a view correta do tema ativo.

## Componente Implementado: `Botao`

O componente `Botao` foi implementado e, posteriormente, convertido para um componente Livewire, demonstrando a compatibilidade com o sistema de temas.

*   **Localização:** `app/Livewire/Botao.php` (classe) e `resources/views/livewire/{tema}/botao.blade.php` (views).
*   **Funcionalidades:** Aceita propriedades como `tipo`, `tamanho`, `href` (para links), `action` (para ações Livewire) e `label` (para o texto do botão).
*   **Adaptação de Renderização:** Renderiza uma tag `<a>` se `href` for fornecido, ou uma tag `<button>` caso contrário, adaptando-se às classes CSS do tema ativo.
*   **Correções de Layout (Livewire):** Para os temas Bootstrap and Materialize, a view do componente é envolvida por um `div` que serve dois propósitos:
    1.  Atua como o elemento raiz único exigido pelo Livewire.
    2.  É estilizado com `display: inline-block` para garantir que os componentes fiquem alinhados lado a lado, em vez de empilhados verticalmente.

## Componente Implementado: `Input`

Seguindo o mesmo padrão do `Botao`, o componente `Input` foi criado para renderizar campos de formulário adaptados para cada Design System.

*   **Localização:** `app/Livewire/Input.php` (classe) e `resources/views/livewire/{tema}/input.blade.php` (views).
*   **Funcionalidades:** Aceita propriedades como `type`, `name`, `label`, `id`, `placeholder`, `value` e `wireModel` para integração com o Livewire.
*   **Correções de Layout:**
    *   **Largura:** Na view de exemplo (`welcome.blade.php`), os componentes de input foram envolvidos por um `div` com `max-width` para controlar a largura excessiva.
    *   **Label do Materialize:** O problema de sobreposição do label no tema Materialize foi resolvido de duas formas:
        1.  O atributo `placeholder` foi removido da view `materialize/input.blade.php`, pois o label já serve a este propósito.
        2.  O JavaScript do Materialize foi importado e inicializado no arquivo `resources/js/app.js` e, em seguida, compilado com `npm run build` para garantir a ativação das animações do framework.

## Componente Implementado: `Checkbox`

O componente `Checkbox` foi implementado seguindo o padrão de temas dinâmicos.

*   **Localização:** `app/Livewire/Checkbox.php` (classe) e `resources/views/livewire/{tema}/checkbox.blade.php` (views).
*   **Funcionalidades:** Aceita propriedades como `name`, `label`, `id` e `checked`.

## Componente Implementado: `Radio`

O componente `Radio` foi implementado seguindo o padrão de temas dinâmicos.

*   **Localização:** `app/Livewire/Radio.php` (classe) e `resources/views/livewire/{tema}/radio.blade.php` (views).
*   **Funcionalidades:** Aceita propriedades como `name`, `label`, `id`, `value` e `checked`.
*   **Correção de Bug:** Foi corrigido um problema onde os botões de rádio não eram selecionáveis corretamente devido à falta de `id`s únicos para cada opção.

## Componente Implementado: `Select`

O componente `Select` foi implementado seguindo o padrão de temas dinâmicos.

*   **Localização:** `app/Livewire/Select.php` (classe) e `resources/views/livewire/{tema}/select.blade.php` (views).
*   **Funcionalidades:** Aceita propriedades como `name`, `label`, `id`, `options`, `placeholder` e `wireModel` para integração com o Livewire.
*   **Correções de Layout:**
    *   **GOV.BR:** O componente `Select` do GOV.BR requer a importação e inicialização de seus scripts (`core-init.js` e `core.min.js`) para a correta interatividade.
    *   **Materialize:** O componente `Select` do Materialize requer a inicialização de seus scripts (`M.FormSelect.init`) para a correta exibição e funcionalidade.

## Atualizações de Configuração e Dependências

*   **`package.json`:**
    *   Adicionadas dependências: `@govbr-ds/core`, `bootstrap`, `materialize-css`.
    *   Adicionada `devDependency`: `sass`.
*   **`.env` e `.env.example`:**
    *   `SESSION_DRIVER` alterado para `file`.
    *   `QUEUE_CONNECTION` alterado para `sync`.
    *   `CACHE_STORE` alterado para `file`.
    *   Adicionada variável `DESIGN_SYSTEM=govbr`.
*   **`database/database.sqlite`:**
    *   Criado para evitar erro de driver.
*   **Versão do Laravel:**
    *   O projeto foi atualizado para o Laravel 12.

## Próximos Passos Sugeridos

*   Converter outros componentes Blade para Livewire.
*   Criar novos componentes Livewire já com a estrutura de temas.
*   Implementar uma interface de usuário para a troca de temas (sem precisar editar o `.env`).
*   Estruturar o layout da aplicação de forma mais robusta.
*   Revisar e aprimorar o componente `Input` do tema GOV.BR.

## Atualização: Componente `Table` (GOV.BR)

Foi identificado um problema com o componente `Table` do GOV.BR, onde a tabela não estava funcionando corretamente. A investigação inicial focou em possíveis problemas de inicialização JavaScript ou CSS.

No entanto, o usuário identificou que o problema estava no "formato da blade" (`resources/views/livewire/govbr/table.blade.php`). O usuário corrigiu o problema atualizando a estrutura HTML do componente para incluir:

*   **Atributos `data-` dinâmicos:** Adição de `data-search`, `data-selection`, e `data-collapse` condicionalmente, ativando funcionalidades específicas do GOV.BR.
*   **IDs únicos:** Utilização de `uniqid()` para gerar IDs únicos para elementos, crucial para a correta interação JavaScript, especialmente em múltiplas instâncias do componente.
*   **Estrutura de busca detalhada:** Implementação completa da barra de busca conforme o padrão GOV.BR, incluindo `search-trigger`, `search-bar`, e botões de controle.
*   **Funcionalidade de seleção de linhas:** Adição de checkboxes para seleção de linhas na tabela.

Esta correção demonstrou a importância da adesão estrita ao markup exigido pelo Design System GOV.BR para a ativação de suas funcionalidades complexas.

---

*Última atualização: 1 de setembro de 2025*

## Atualização: Comando `make:scaffold` e Componente `Table`

Esta seção detalha as melhorias e correções implementadas no comando `make:scaffold` e no componente `Table` para garantir a geração de CRUDs funcionais e alinhados com os Design Systems do projeto.

### Comando `make:scaffold`

*   **Funcionalidade:** Criado para automatizar a geração de Model, Controller, Rotas e Views de um CRUD a partir de um arquivo de migration existente.
*   **Uso:** `php artisan make:scaffold <nome_do_arquivo_migration>` (ex: `create_produtos_table`).
*   **Melhorias na Geração:**
    *   **Model:** Gera o Model com a `trait HasFactory` e a propriedade `$fillable` preenchida automaticamente com base nas colunas da migration.
    *   **Controller:** Gera um Controller resource básico.
    *   **Rotas:** Adiciona a rota `Route::resource` ao `routes/web.php`.
    *   **Views:** Gera views (`index`, `create`, `edit`, `show`) que utilizam os componentes Livewire (`livewire:input`, `livewire:botao`) e estendem um layout dedicado (`layouts/scaffold.blade.php`).
*   **Correções de Bugs:**
    *   Corrigido erro de sintaxe no `@foreach` das views geradas.
    *   Corrigido o processo de geração do Model para incluir `HasFactory` e `$fillable` de forma robusta.

### Componente `Table` (`app/Livewire/Table.php` e Views Temáticas)

O componente `Table` foi aprimorado para renderizar ações de forma mais flexível e robusta, adaptando-se aos diferentes Design Systems.

*   **Nova Estrutura de Dados:** O controller agora passa um array `$rows` onde a última coluna de cada linha pode ser um array associativo contendo URLs para ações (`show`, `edit`, `delete`).
*   **Renderização Condicional de Ações:** As views temáticas da tabela (`govbr`, `bootstrap`, `materialize`) foram modificadas para:
    *   Detectar se a última célula de uma linha é um array de ações.
    *   Se for, renderizar dinamicamente os botões `<livewire:botao>` (para Show/Edit) e um formulário com `<livewire:botao type="submit">` (para Delete), usando as URLs fornecidas.
    *   Para as demais células, o conteúdo é renderizado normalmente.
*   **Resolução de Problemas Específicos:
    *   **GOV.BR:** Corrigido o desalinhamento de colunas quando a seleção de linhas (`selecionavel`) está ativa, garantindo que a `<td>` do checkbox seja condicional no `<tbody>` assim como no `<thead>`.
    *   **Materialize:** Após várias tentativas de forçar o alinhamento, a view do Materialize foi ajustada para renderizar os botões de ação e o conteúdo das células de forma que se adapte melhor ao CSS rígido do tema, garantindo que os botões de delete funcionem e o texto não empilhe.

### Outras Correções e Lições Aprendidas

*   **Configuração de Banco de Dados:** Reforçada a importância de limpar o cache de configuração (`php artisan config:clear` ou `optimize:clear`) após alterar as credenciais do banco de dados no `.env`, para evitar erros de `could not find driver`.
*   **`layouts/scaffold.blade.php`:** Criado um layout dedicado para as views geradas, evitando dependência direta do `welcome.blade.php`.

---

*Última atualização: quarta-feira, 4 de setembro de 2025*