#!/bin/bash

# --- Variáveis de Configuração ---
APP_NAME="gerenciador-oticas-api"
APP_PORT="10002"
CWD_PATH="/home/documents/mvl/gerenciador-de-oticas-api"
PM2_CONFIG_FILE="ecosystem.config.cjs"

echo "--- Diagnóstico de Rede para $APP_NAME na porta $APP_PORT ---"
echo "Data: $(date)"
echo "---------------------------------------------------------"

echo "1. Verificando o estado do PM2 para $APP_NAME..."
pm2 show "$APP_NAME"
if [ $? -ne 0 ]; then
    echo "AVISO: O processo $APP_NAME não parece estar rodando sob PM2."
    echo "Tente iniciar com: cd $CWD_PATH && pm2 start $PM2_CONFIG_FILE --env production"
fi
echo ""

echo "2. Verificando qual processo está escutando na porta $APP_PORT..."
LISTEN_STATUS=$(ss -ltnp | grep -E ":$APP_PORT\b")
if [ -z "$LISTEN_STATUS" ]; then
    echo "ERRO: Nenhum processo escutando na porta $APP_PORT. O aplicativo pode não estar rodando ou não está vinculado corretamente."
    echo "Comandos para verificar o processo PM2:"
    echo "  pm2 show $APP_NAME"
    echo "  pm2 logs $APP_NAME --lines 200"
else
    echo "$LISTEN_STATUS"
    if echo "$LISTEN_STATUS" | grep -q "127.0.0.1:$APP_PORT"; then
        echo "A aplicação está escutando APENAS em 127.0.0.1 (localhost). Isso significa que ela só é acessível dentro do próprio droplet ou via proxy."
        echo "Para acesso externo direto, a aplicação precisa escutar em 0.0.0.0."
        echo "--- Sugestão de correção para o binding da aplicação (se desejado acesso direto por IP) ---"
        echo "1. Edite o arquivo $CWD_PATH/$PM2_CONFIG_FILE:"
        echo "   Mude '--host=127.0.0.1' para '--host=0.0.0.0' na seção 'args'."
        echo "   Exemplo: 'args': 'serve --host=0.0.0.0 --port=${PORT||10002}'"
        echo "2. Reinicie o PM2:"
        echo "   cd $CWD_PATH"
        echo "   pm2 restart $APP_NAME"
        echo "   pm2 save"
    elif echo "$LISTEN_STATUS" | grep -q "0.0.0.0:$APP_PORT"; then
        echo "A aplicação está escutando em 0.0.0.0 (todas as interfaces). Isso é bom para acesso externo."
    fi
fi
echo ""

echo "3. Verificando o estado do firewall (UFW)..."
if command -v ufw &> /dev/null; then
    sudo ufw status verbose
    if ! sudo ufw status | grep -q "Status: active"; then
        echo "AVISO: UFW está inativo. O tráfego não está sendo filtrado por ele."
    elif ! sudo ufw status | grep -q "ALLOW IN .* $APP_PORT/tcp"; then
        echo "AVISO: A porta $APP_PORT/tcp não está explicitamente aberta no UFW."
        echo "--- Sugestão de correção para UFW ---"
        echo "  sudo ufw allow $APP_PORT/tcp"
        echo "  sudo ufw reload"
    else
        echo "Porta $APP_PORT/tcp está aberta no UFW."
    fi
else
    echo "UFW não encontrado. Verificando iptables/nftables..."
fi
echo ""

echo "4. Verificando iptables (se UFW não estiver ativo ou para mais detalhes)..."
if command -v iptables &> /dev/null; then
    sudo iptables -L -n --line-numbers | grep -E "Chain|:$APP_PORT"
    if command -v nft &> /dev/null; then
        echo "Verificando também nftables..."
        sudo nft list ruleset | grep -E "table|dport $APP_PORT"
    fi
else
    echo "iptables não encontrado."
fi
echo ""

echo "5. Teste de conexão local (do droplet para si mesmo) para 0.0.0.0 e 127.0.0.1:"
echo "--- Teste 0.0.0.0:$APP_PORT ---"
curl -s -o /dev/null -w "%{http_code}" http://0.0.0.0:$APP_PORT/
if [ $? -eq 0 ]; then echo " (Sucesso)"; else echo " (Falha)"; fi
echo "--- Teste 127.0.0.1:$APP_PORT ---"
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:$APP_PORT/
if [ $? -eq 0 ]; then echo " (Sucesso)"; else echo " (Falha)"; fi
echo ""

echo "--- Diagnóstico Concluído ---"
echo "Se a porta ainda estiver inacessível externamente após as correções acima, considere:"
echo "  - Firewall do provedor de nuvem (ex: DigitalOcean Cloud Firewall). Verifique o painel de controle."
echo "  - Outras regras de rede ou roteamento no droplet."
echo "  - Logs do sistema (ex: /var/log/syslog, /var/log/kern.log) para mensagens de bloqueio."
