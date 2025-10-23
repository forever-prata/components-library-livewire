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

    try {
        $produtoData = App\Models\Produto::factory()->make([
            'imagem' => 'https://i.pravatar.cc/400?img=12'
        ]);
    } catch (Exception $e) {
        $produtoData = new App\Models\Produto([
            'id' => 1,
            'nome' => 'Produto de Exemplo',
            'descricao' => 'Esta é uma descrição do produto.',
            'preco' => 99.90,
            'estoque' => 50,
            'imagem' => 'https://i.pravatar.cc/400?img=12',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
@endphp
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script>
        window.designSystem = '{{ config('design.system') }}';
    </script>
                @php
            $viteAssets = [
                'resources/css/themes/' . config('design.system') . '.css',
                'resources/js/themes/' . config('design.system') . '.js',
                'resources/js/app.js'
            ];
            if (config('design.system') !== 'materialize') {
                array_unshift($viteAssets, 'resources/css/app.css');
            }
        @endphp
        @vite($viteAssets)

        <!-- Fonts -->
    <!-- Fontawesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <title>Padrão Digital de Governo</title>
  </head>
  <body>
    <div class="container my-5">
        <h1 class="mb-4">Exemplos de Componentes</h1>

        <div class="row gy-4">
            {{-- Cards --}}
            <div class="col-12">
                <h2>Cards</h2>
                <div class="row">
                    <div class="col-md-4">
                        <livewire:card
                            :data="$configData"
                            titulo="Configurações"
                        />
                    </div>
                    <div class="col-md-4">
                        <livewire:card
                            :data="$userData"
                            titulo="Perfil de Usuário"
                            :comAvatar="true"
                            :actionButtons="$userActions"
                        />
                    </div>
                    <div class="col-md-4">
                         <livewire:card
                            :data="$produtoData"
                            titulo="Detalhes do Produto"
                            :comImagem="true"
                            :routeBase="'produtos'"
                            estiloImagem="max-height: 200px; object-fit: cover;"
                        />
                    </div>
                </div>
            </div>

            <hr class="my-5">

            {{-- Outros Componentes --}}
            <div class="col-12">
                 <h2>Outros Componentes</h2>
                 <div style="max-width: 500px;">
                    <livewire:botao tipo="primary" tamanho="large" label="Enviar" />
                    <livewire:botao tipo="primary" tamanho="large" label="Enviar" />
                    <livewire:botao href="https://gov.br" tipo="secondary" tamanho="large" label="Ir para o Gov.BR" />
                    <livewire:botao action="salvarUsuario" tipo="danger" label="Salvar" />

                    <div class="my-3">
                        <livewire:input name="nome" label="Nome Completo" placeholder="Digite seu nome" />
                        <livewire:input type="password" name="senha" label="Senha" placeholder="Digite sua senha" />
                        <livewire:input type="email" name="email" label="Email" placeholder="seu@email.com" wireModel="email" />
                        <livewire:textarea name="mensagem" label="Mensagem" placeholder="Deixe sua mensagem..." />
                    </div>

                    <div class="my-3">
                        <livewire:checkbox name="lembrar" label="Lembrar de mim" />
                        <livewire:checkbox name="termos" label="Aceito os termos" checked="true" />
                    </div>

                    <div class="my-3">
                        <p>Selecione uma opção:</p>
                        <livewire:radio name="opcao" label="Opção 1" value="opcao1" id="opcao1" />
                        <livewire:radio name="opcao" label="Opção 2" value="opcao2" id="opcao2" checked="true" />
                        <livewire:radio name="opcao" label="Opção 3" value="opcao3" id="opcao3" />
                    </div>

                    <div class="my-3">
                        @livewire('select', [
                            'name' => 'select_simple',
                            'label' => 'Select Simples',
                            'id' => 'select_simple',
                            'options' => [
                                '1' => 'Opção 1',
                                '2' => 'Opção 2',
                                '3' => 'Opção 3',
                            ]
                        ])
                    </div>

                    <div class="my-3">
                        <p class="font-weight-bold">Upload de Arquivo Único:</p>
                        <livewire:upload />
                    </div>

                    <div class="my-3">
                        <p class="font-weight-bold">Upload de Múltiplos Arquivos:</p>
                        <livewire:upload :multiple="true" label="Anexar múltiplos arquivos" />
                    </div>

                 </div>
            </div>

            <div class="col-12">
                <livewire:table
                    :collection="App\Models\Produto::all()"
                    titulo="Produtos"
                    :busca="true"
                    :selecionavel="true"
                    classeExtra="striped"
                />
            </div>

            @php
                $dados = [
                    ['nome' => 'Produto A', 'preco' => 10],
                    ['nome' => 'Produto B', 'preco' => 20],
                ];

                $collection = collect($dados);
            @endphp

            <livewire:table
                :collection="$collection"
                titulo="Produtos"
                :colunas="[
                    'nome' => 'Nome do Produto',
                    'preco' => 'Preço (R$)',
                ]"
                actionsTitle="Ações"
            />

        </div>
    </div>
  </body>
</html>
