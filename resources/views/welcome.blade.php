<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    {{-- Carrega o CSS do tema ativo --}}
        @vite(['resources/css/themes/' . config('design.system') . '.css'])

        <!-- Fonts -->
    <!-- Fontawesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <title>Padrão Digital de Governo</title>
  </head>
  <body>

    {{-- Blade Components (Comentados para referência) --}}
    {{--
    <x-botao tipo="primary" tamanho="large">
        Enviar
    </x-botao>

    <x-botao href="https://gov.br" tipo="secondary" tamanho="large">
        Ir para o Gov.BR
    </x-botao>

    <x-botao action="salvarUsuario" tipo="danger">
        Salvar
    </x-botao>
    --}}

    {{-- Livewire Components --}}
    <livewire:botao tipo="primary" tamanho="large" label="Enviar" />

    <livewire:botao href="https://gov.br" tipo="secondary" tamanho="large" label="Ir para o Gov.BR" />

    <livewire:botao action="salvarUsuario" tipo="danger" label="Salvar" />

  </body>
</html>
