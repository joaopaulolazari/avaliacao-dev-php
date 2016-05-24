#!/bin/sh

echo ''
echo 'hooks/install.sh:'
echo '--- Copiando arquivos pre-commit e pre-push'

cp hooks/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit

cp hooks/pre-push .git/hooks/pre-push
chmod +x .git/hooks/pre-push