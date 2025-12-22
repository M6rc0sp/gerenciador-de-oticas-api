#!/bin/bash

# Script de Deploy para Gerenciador de Óticas API
# Uso: ./deploy.sh [ambiente]

set -e

ENVIRONMENT=${1:-production}
TARGET_DIR="/home/documents/mvl/gerenciador-de-oticas-api"
PM2_APP_NAME=${PM2_APP_NAME:-gerenciador-oticas-api}
PORT=${PORT:-10002} # default non-standard port (override via env)

echo "🚀 Iniciando deploy para $ENVIRONMENT..."

# Verificar se o diretório do app existe (não vamos mover ele)
if [ ! -d "$TARGET_DIR" ]; then
    echo "❌ Diretório $TARGET_DIR não encontrado. Coloque o projeto nesse caminho no droplet e rode novamente." >&2
    exit 1
fi

cd "$TARGET_DIR"

echo "🔄 Atualizando código (pull)..."
git pull origin main || true

echo "📦 Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader

echo "📦 Instalando dependências Node.js e build..."
npm ci
npm run build

echo "⚙️  Verificando .env..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "❗ Copiado .env.example → .env. Edite .env com as credenciais (DB, APP_KEY, etc)."
fi

# Gerar chave da aplicação se não existir
if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ]; then
    php artisan key:generate
fi

echo "➡️  Rodando migrações (se necessário)..."
php artisan migrate --force || true

echo "🧹 Limpando caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Criar pasta de logs local para PM2
mkdir -p "$TARGET_DIR/logs"

echo "🔄 Reiniciando/ligando a app via PM2 (nome: $PM2_APP_NAME, port: $PORT)..."
# Exportar variáveis que o ecosystem.config.js lê
export PORT="$PORT"
export APP_CWD="$TARGET_DIR"
export PM2_APP_NAME="$PM2_APP_NAME"

# Reiniciar com PM2 usando o nome configurado
pm2 stop "$PM2_APP_NAME" || true
pm2 delete "$PM2_APP_NAME" || true
pm2 start ecosystem.config.js --env "$ENVIRONMENT"
pm2 save

echo "✅ Deploy concluído!"
echo "🌐 Aplicação pronta para proxy reverso em: http://127.0.0.1:$PORT"
echo "📊 Verificar status: pm2 status"
echo "📝 Ver logs: pm2 logs $PM2_APP_NAME"

echo "📌 Lembrete: atualize a VirtualHost/Proxy do Apache para apontar o host correto para http://127.0.0.1:$PORT"