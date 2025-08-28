<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    {{-- Carrega o CSS do tema ativo --}}
        @vite(['resources/css/themes/' . config('design.system') . '.css', 'resources/js/app.js'])

        <!-- Fonts -->
    <!-- Fontawesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <title>Padrão Digital de Governo</title>
  </head>
  <body>
    {{-- Livewire Components --}}
    <livewire:botao tipo="primary" tamanho="large" label="Enviar" />

    <livewire:botao href="https://gov.br" tipo="secondary" tamanho="large" label="Ir para o Gov.BR" />

    <livewire:botao action="salvarUsuario" tipo="danger" label="Salvar" />

    <div style="max-width: 500px; margin-top: 20px;">
        <livewire:input name="nome" label="Nome Completo" placeholder="Digite seu nome" />

        <livewire:input type="password" name="senha" label="Senha" placeholder="Digite sua senha" />

        <livewire:input type="email" name="email" label="Email" placeholder="seu@email.com" wireModel="email" />

        <livewire:checkbox name="lembrar" label="Lembrar de mim" />
        <livewire:checkbox name="termos" label="Aceito os termos" checked="true" />

        <p>Selecione uma opção:</p>
        <livewire:radio name="opcao" label="Opção 1" value="opcao1" id="opcao1" />
        <livewire:radio name="opcao" label="Opção 2" value="opcao2" id="opcao2" checked="true" />
        <livewire:radio name="opcao" label="Opção 3" value="opcao3" id="opcao3" />

        <div class="mt-4">
            <div class="row align-items-end">
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
        </div>

    </div>

  </body>
</html>