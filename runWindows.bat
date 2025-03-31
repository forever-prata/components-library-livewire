@echo off
setlocal enabledelayedexpansion

echo Escolha uma opcao:
echo [1] Rodar somente npm
echo [2] Rodar tudo
set /P opcao=Digite o numero da opcao:

if !opcao!==1 (
  echo Rodando somente npm...
  npm run dev
) else (
  echo Rodando tudo...
  call composer update
  copy .env.example .env
  echo -n "Altere os valores do arquivo .env e tecle <enter>"
  pause
  php artisan key:generate
  echo "Escolha uma opcao de migracao:"
  echo "1. Rodar somente migrate (php artisan migrate)"
  echo "2. Rodar somente seed (php artisan db:seed)"
  echo "3. Rodar migrate e seed (php artisan migrate + php artisan db:seed)"
  echo "4. Deletar tudo e rodar tudo (php artisan migrate:fresh --seed)"
  set /P opcao=Digite o numero da opcao:

  if !opcao!==1 (
      php artisan migrate
  ) else if !opcao!==2 (
      php artisan db:seed
  ) else if !opcao!==3 (
      php artisan migrate
      php artisan db:seed
  ) else if !opcao!==4 (
      php artisan migrate:fresh --seed
  ) else (
      echo "Opção invalida"
  )
  call npm install --save-dev vite
  npm run dev
)
