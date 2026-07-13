#!/bin/bash

# Script de déploiement pour Kids Club Theme via SCP
# Utilise tar + scp pour déployer le thème

set -e

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ ! -f "$SCRIPT_DIR/.deploy-config" ]; then
    echo -e "${RED}Erreur: Le fichier .deploy-config n'existe pas${NC}"
    exit 1
fi

source "$SCRIPT_DIR/.deploy-config"

log()   { echo -e "${GREEN}[DEPLOY]${NC} $1"; }
error() { echo -e "${RED}[ERREUR]${NC} $1"; }

log "Déploiement vers $SSH_USER@$SSH_HOST:$SSH_PATH"

if ! ssh "$SSH_ALIAS" "exit" 2>/dev/null; then
    error "Impossible de se connecter à $SSH_ALIAS"
    exit 1
fi

log "Construction des assets..."
if [ -f "$SCRIPT_DIR/package.json" ]; then
    npm run build
fi

log "Création de l'archive..."
TEMP_ARCHIVE="/tmp/kidsclub-deploy-$(date +%s).tar.gz"

tar -czf "$TEMP_ARCHIVE" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='demo-blocks.html' \
    --exclude='responsive-audit.html' \
    --exclude='.git' \
    --exclude='.claude' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='.env' \
    --exclude='.deploy-config' \
    --exclude='package-lock.json' \
    --exclude='composer.lock' \
    --exclude='.gitignore' \
    --exclude='README.md' \
    --exclude='deploy-scp.sh' \
    --exclude='ZACP' \
    --exclude='.superpowers' \
    --exclude='.phpactor.json' \
    --exclude='*-backup.mp4' \
    --exclude='RAPPORT-*.md' \
    --exclude='docs/superpowers' \
    --exclude='assets/hero clips' \
    --exclude='assets/backgrounds' \
    --exclude='assets/logo' \
    --exclude='assets/symbols' \
    --exclude='PastedGraphic-*.png' \
    -C "$SCRIPT_DIR" .

log "Création du répertoire distant si nécessaire..."
ssh "$SSH_ALIAS" "mkdir -p $SSH_PATH"

log "Transfert de l'archive..."
scp "$TEMP_ARCHIVE" "$SSH_ALIAS":/tmp/

log "Extraction sur le serveur..."
ARCHIVE_NAME=$(basename "$TEMP_ARCHIVE")
ssh "$SSH_ALIAS" "cd $SSH_PATH && tar -xzf /tmp/$ARCHIVE_NAME && rm /tmp/$ARCHIVE_NAME"

rm "$TEMP_ARCHIVE"

log "Correction des permissions..."
ssh "$SSH_ALIAS" "cd $SSH_PATH && find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \;"

log "Déploiement terminé !"
log "Site en production : $REMOTE_URL"
