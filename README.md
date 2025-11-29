Biblioteca de Componentes Reutilizáveis para Laravel com Livewire
Uma biblioteca modular de componentes reutilizáveis para aplicações Laravel utilizando Livewire, com suporte a múltiplos sistemas de design (Bootstrap e GOV.BR).

📋 Índice
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Sistemas de Design Suportados](#sistemas-de-design-suportados)
- [Componentes](#componentes)
- [Automação com Scaffold](#automação-com-scaffold)
- [Exemplos de Uso](#exemplos-de-uso)
- [Personalização](#personalização)
- [Suporte](#suporte)
- [Licença](#licença)

🚀 Instalação
1. Instalar dependências de frontend
```bash
npm install bootstrap @popperjs/core @govbr-ds/core
```
2. Instalar a biblioteca via Composer
```bash
composer require forever-prata/govbr-components-livewire
```
3. Publicar os arquivos da biblioteca
```bash
php artisan vendor:publish --provider="GovbrComponentsLivewire\GovbrComponentsLivewireServiceProvider" --force
```
⚙️ Configuração
1. Configurar pluralização em português
No arquivo AppServiceProvider.php:

```php
use Illuminate\Support\Pluralizer;

public function boot(): void
{
    Pluralizer::useLanguage('portuguese');
}
```
2. Configurar Vite
No arquivo vite.config.js:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/themes/govbr.css',
                'resources/css/themes/bootstrap.css',
                'resources/js/themes/govbr.js',
                'resources/js/themes/bootstrap.js',
            ],
            refresh: true,
        }),
    ],
});
```
3. Configurar o sistema de design
No arquivo .env:

```ini
DESIGN_SYSTEM=govbr
```
Valores possíveis: govbr ou bootstrap

🎨 Sistemas de Design Suportados
GOV.BR Design System - Padrão visual do governo federal brasileiro

Bootstrap - Framework CSS amplamente utilizado

📦 Componentes
1. Componente Botão
Renderiza botões interativos ou links com diferentes estilos e comportamentos.

```php
<livewire:botao 
    type="primary" 
    size="large" 
    label="Enviar" 
/>

<livewire:botao 
    href="https://gov.br" 
    type="secondary" 
    size="large" 
    label="Ir para o Gov.BR" 
/>

<livewire:botao 
    action="salvarUsuario" 
    type="danger" 
    label="Salvar" 
/>
```
Parâmetros:

label (string) - Texto exibido no botão

type (string) - Tipo de estilo (primary, secondary, danger, etc.)

size (string) - Tamanho (small, large)

action (string) - Método Livewire a ser executado

href (string) - URL para navegação (transforma em link)

extraClass (string) - Classes CSS adicionais

buttonType (string) - Tipo HTML (submit, reset, button)

2. Componente Checkbox
Caixa de seleção para formulários.

```php
<livewire:checkbox 
    name="termos"
    label="Aceito os termos"
    id="termos_aceite"
    checked="true"
/>
```
Parâmetros:

name (string) - Nome do campo no formulário

label (string) - Rótulo descritivo

id (string) - ID único (opcional, usa name como padrão)

checked (bool) - Estado inicial (true/false)

3. Componente Input
Campo de entrada de texto para formulários.

```php
<livewire:input 
    type="email"
    name="email"
    label="E-mail"
    placeholder="seu@email.com"
    wireModel="email"
/>
```
Parâmetros:

type (string) - Tipo de input (text, email, password, number)

name (string) - Nome do campo

label (string) - Rótulo descritivo

id (string) - ID único (opcional)

placeholder (string) - Texto de placeholder

wireModel (string) - Binding Livewire

4. Componente Radio
Botão de rádio para seleção única em grupos.

```php
<livewire:radio 
    name="opcao"
    label="Opção 1"
    value="1"
    checked="true"
/>

<livewire:radio 
    name="opcao"
    label="Opção 2"
    value="2"
/>
```
Parâmetros:

name (string) - Nome do grupo

label (string) - Rótulo da opção

value (string) - Valor submetido

id (string) - ID único (opcional)

checked (bool) - Seleção inicial

5. Componente Select
Lista suspensa para seleção de opções.

```php
<livewire:select 
    name="categoria"
    label="Categoria"
    :options="['1' => 'Opção 1', '2' => 'Opção 2']"
    placeholder="Selecione uma opção"
    id="select_simple"
/>
```
Parâmetros:

name (string) - Nome do campo

label (string) - Rótulo descritivo

options (array) - Array associativo de opções

placeholder (string) - Texto padrão

id (string) - Identificador do campo

6. Componente Table
Tabela dinâmica com recursos avançados.

```php
<livewire:table 
    :collection="$users"
    title="Lista de Usuários"
    :search="true"
    :selectable="true"
    :columns="['id', 'name', 'email']"
    extraClass="striped"
/>
```
Parâmetros:

collection (Collection) - Coleção de dados

title (string) - Título da tabela

search (bool) - Ativar busca

selectable (bool) - Seleção de linhas

columns (array) - Colunas específicas

actionsTitle (string) - Título da coluna de ações

extraClass (string) - Classes CSS adicionais

7. Componente Textarea
Área de texto para conteúdo extenso.

```php
<livewire:textarea 
    name="descricao"
    label="Descrição"
    placeholder="Digite a descrição..."
    rows="4"
/>
```
Parâmetros:

name (string) - Nome do campo

label (string) - Rótulo descritivo

placeholder (string) - Texto de placeholder

rows (int) - Número de linhas visíveis

8. Componente Upload
Upload de arquivos com suporte a múltiplos arquivos.

```php
<livewire:upload />

<livewire:upload 
    :multiple="true" 
    label="Anexar múltiplos arquivos" 
/>
```
Parâmetros:

label (string) - Rótulo descritivo

multiple (bool) - Upload múltiplo

9. Componente Card
Cards para exibição de dados estruturados.

```php
{{-- Card com array --}}
<livewire:card
    :data="$configData"
    title="Configurações"
/>

{{-- Card com objeto e avatar --}}
<livewire:card
    :data="$userData"
    title="Perfil de Usuário"
    :withAvatar="true"
    :actionButtons="$userActions"
/>

{{-- Card com modelo Eloquent --}}
<livewire:card
    :data="$produtoData"
    title="Detalhes do Produto"
    :withImage="true"
    :routeBase="'produtos'"
    imageStyle="max-height: 200px; object-fit: cover;"
/>
```
Parâmetros:

data (array|object) - Dados a serem exibidos

title (string) - Título do card

withAvatar (bool) - Exibir avatar (para objetos com propriedade avatar)

withImage (bool) - Exibir imagem (para objetos com propriedade imagem)

actionButtons (array) - Botões de ação

routeBase (string) - Base para rotas CRUD

imageStyle (string) - Estilos CSS para imagem

🔧 Automação com Scaffold
Comando de Scaffold Automático
```bash
php artisan make:scaffold create_users_table
```
Parâmetros de Relacionamento
```bash
php artisan make:scaffold create_posts_table --belongs-to=user --has-many=comments
```
Parâmetros disponíveis:

--belongs-to - Relacionamento belongsTo

--has-many - Relacionamento hasMany

--has-one - Relacionamento hasOne

--belongs-to-many - Relacionamento belongsToMany

O comando gera automaticamente:
Model com relacionamentos e fillable attributes

Controller com métodos CRUD completos

Views utilizando componentes da biblioteca

Rotas RESTful no arquivo web.php

💡 Exemplos de Uso
Exemplo Completo de Página
```php
@php
    $configData = [
        'ID da Aplicação' => 'APP_12345',
        'Modo de Manutenção' => false,
        'Nível de Log' => 'debug',
        'URL do Banco' => 'mysql://user:pass@host:3306/db',
        'Última Verificação' => new DateTime('2025-09-29 10:00:00'),
    ];

    $userData = new stdClass();
    $userData->id = 101;
    $userData->name = 'Maria da Silva';
    $userData->position = 'Desenvolvedora Chefe';
    $userData->email = 'maria.silva@example.com';
    $userData->avatar = 'https://i.pravatar.cc/150?img=25';
    $userActions = [
        'Ver Perfil' => [
            'route' => 'produtos.show',
            'params' => 1
        ],
        'Enviar Mensagem' => 'https://google.com'
    ];
@endphp

<div class="container my-5">
    <h1 class="mb-4">Exemplos de Componentes</h1>

    {{-- Cards --}}
    <div class="row">
        <div class="col-md-4">
            <livewire:card
                :data="$configData"
                title="Configurações"
            />
        </div>
        <div class="col-md-4">
            <livewire:card
                :data="$userData"
                title="Perfil de Usuário"
                :withAvatar="true"
                :actionButtons="$userActions"
            />
        </div>
    </div>

    {{-- Formulários --}}
    <div style="max-width: 500px;">
        <livewire:botao type="primary" size="large" label="Enviar" />
        <livewire:botao href="https://gov.br" type="secondary" size="large" label="Ir para o Gov.BR" />
        
        <livewire:input name="nome" label="Nome Completo" placeholder="Digite seu nome" />
        <livewire:input type="email" name="email" label="Email" placeholder="seu@email.com" wireModel="email" />
        <livewire:textarea name="mensagem" label="Mensagem" placeholder="Deixe sua mensagem..." />
        
        <livewire:checkbox name="termos" label="Aceito os termos" checked="true" />
        
        <livewire:radio name="opcao" label="Opção 1" value="opcao1" id="opcao1" />
        <livewire:radio name="opcao" label="Opção 2" value="opcao2" id="opcao2" checked="true" />
        
        <livewire:upload :multiple="true" label="Anexar múltiplos arquivos" />
    </div>

    {{-- Tabela --}}
    <livewire:table
        :collection="App\Models\Produto::all()"
        title="Produtos"
        :search="true"
        :selectable="true"
        extraClass="striped"
    />
</div>
```
Exemplo com Dados Customizados
```php
@php
    $dados = [
        ['nome' => 'Produto A', 'preco' => 10],
        ['nome' => 'Produto B', 'preco' => 20],
    ];
    $collection = collect($dados);
@endphp

<livewire:table
    :collection="$collection"
    title="Produtos"
    :columns="[
        'nome' => 'Nome do Produto',
        'preco' => 'Preço (R$)',
    ]"
    actionsTitle="Ações"
/>
```
🎯 Personalização
Adicionar Novo Sistema de Design
Crie os arquivos de estilo em resources/css/themes/novo-tema.css

Crie os arquivos JavaScript em resources/js/themes/novo-tema.js

Atualize o vite.config.js

Crie as views dos componentes em resources/views/livewire/novo-tema/

Modificar Componentes Existentes
Os componentes podem ser personalizados editando os arquivos em:

Lógica: app/Livewire/

Views: resources/views/livewire/[tema]/

📄 Licença
Este projeto está licenciado sob a MIT License.
