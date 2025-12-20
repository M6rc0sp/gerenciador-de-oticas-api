#!/bin/bash

# Script de Deploy para Gerenciador de Óticas API
# Uso: ./deploy.sh [ambiente]

set -e

ENVIRONMENT=${1:-production}
PROJECT_DIR="/var/www/gerenciador-oticas-api"
BACKUP_DIR="/var/backups/gerenciador-oticas-api"

echo "🚀 Iniciando deploy para $ENVIRONMENT..."

# Criar diretórios necessários
sudo mkdir -p $PROJECT_DIR
sudo mkdir -p $BACKUP_DIR
sudo mkdir -p /var/log/pm2

# Backup se existir
if [ -d "$PROJECT_DIR/.git" ]; then
    echo "📦 Fazendo backup..."
    sudo cp -r $PROJECT_DIR $BACKUP_DIR/$(date +%Y%m%d_%H%M%S)
fi

# Clonar/Atualizar código
if [ ! -d "$PROJECT_DIR/.git" ]; then
    echo "📥 Clonando repositório..."
    sudo git clone https://github.com/M6rc0sp/gerenciador-de-oticas-api.git $PROJECT_DIR
    cd $PROJECT_DIR
else
    echo "🔄 Atualizando código..."
    cd $PROJECT_DIR
    sudo git pull origin main
fi

# Instalar dependências PHP
echo "📦 Instalando dependências PHP..."
sudo composer install --no-dev --optimize-autoloader

# Instalar dependências Node.js
echo "📦 Instalando dependências Node.js..."
sudo npm ci
sudo npm run build

# Configurar ambiente
echo "⚙️  Configurando ambiente..."
if [ ! -f ".env" ]; then
    sudo cp .env.example .env
    echo "❗ Configure o arquivo .env com suas credenciais!"
fi

# Gerar chave da aplicação
sudo php artisan key:generate

# Executar migrações
sudo php artisan migrate --force

# Limpar caches
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Configurar permissões
sudo chown -R www-data:www-data $PROJECT_DIR
sudo chmod -R 755 $PROJECT_DIR/storage
sudo chmod -R 755 $PROJECT_DIR/bootstrap/cache

# Reiniciar aplicação com PM2
echo "🔄 Reiniciando aplicação..."
cd $PROJECT_DIR
sudo pm2 stop gerenciador-oticas-api || true
sudo pm2 delete gerenciador-oticas-api || true
sudo pm2 start ecosystem.config.js --env $ENVIRONMENT
sudo pm2 save

echo "✅ Deploy concluído!"
echo "🌐 Aplicação rodando em: http://seu-droplet-ip:8000"
echo "📊 Verificar status: pm2 status"
echo "📝 Ver logs: pm2 logs gerenciador-oticas-api"