#!/bin/bash
# Descrição: Verificação de segurança das VM's do PROXMOX
# Data: 27/09/2023
# Rodrigo Curvêllo - rodrigo.curvello@gmail.com - http://curvello.com

echo "Escolha uma opção:"
echo "[1] Rodar tudo"
echo "[2] Rodar somente npm"
read -p "Digite o número da opção: " opcao
if [ "$opcao" == "1" ]
then
 echo "Rodando tudo..."
 composer install
 cp .env.example .env
 read -p "Altere os valores do arquivo .env e tecle <enter>"
 php artisan key:generate
 echo "Escolha uma opção de migração:"
 echo "1. Rodar somente migrate (php artisan migrate) "
 echo "2. Rodar somente seed (php artisan db:seed) "
 echo "3. Rodar migrate e seed (php artisan migrate + php artisan db:seed) "
 echo "4. Deletar tudo e rodar tudo (php artisan migrate:fresh --seed) "
 read -p "Digite o número da opção: " opcao
 if [ "$opcao" == "1" ]
 then
   php artisan migrate
 elif [ "$opcao" == "2" ]
 then
   php artisan db:seed
 elif [ "$opcao" == "3" ]
 then
   php artisan migrate
   php artisan db:seed
 elif [ "$opcao" == "4" ]
 then
   php artisan migrate:fresh --seed
 else
   echo "Opção inválida"
 fi
 npm install --save-dev vite
 npm run dev
elif [ "$opcao" == "2" ]
then
 echo "Rodando somente npm..."
 npm run dev
else
 echo "Opção inválida"
fi
