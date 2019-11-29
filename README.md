# helpdesk

## Instalação
`composer install --no-dev`

## Configuração do Banco
Alterar o arquivo `/src/Adapter/db.php`

## Gerar tabelas do Banco de Dados
`php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update --force`

## Executar / Subir o servidor
`php -S localhost:8000 -t public/`