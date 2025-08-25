#!/bin/bash

# HR Dashboard Deployment Script for cPanel
# Usage: ./deploy.sh [branch_name]
# Make executable with: chmod +x deploy.sh

set -e  # Exit on any error

# Configuration
REPO_DIR="$HOME/hr-app"
PUBLIC_DIR="$HOME/public_html"
BACKUP_DIR="$HOME/backups"
BRANCH=${1:-main}
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Starting HR Dashboard deployment at $(date)${NC}"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Step 1: Backup current deployment
echo -e "${YELLOW}📦 Creating backup...${NC}"
if [ -d "$PUBLIC_DIR" ]; then
    tar -czf "$BACKUP_DIR/backup_$TIMESTAMP.tar.gz" -C "$PUBLIC_DIR" . 2>/dev/null || true
    echo -e "${GREEN}✅ Backup created: backup_$TIMESTAMP.tar.gz${NC}"
fi

# Step 2: Check if repository exists
if [ ! -d "$REPO_DIR" ]; then
    echo -e "${RED}❌ Repository directory not found: $REPO_DIR${NC}"
    echo -e "${YELLOW}Please clone the repository first:${NC}"
    echo "git clone git@github.com:Riskcontrol/Hr_Dashboard_Automated_CV_Screening.git hr-app"
    exit 1
fi

# Step 3: Navigate to repository
cd "$REPO_DIR"

echo -e "${BLUE}📥 Pulling latest changes from GitHub...${NC}"

# Step 4: Stash any local changes
git stash push -m "Auto-stash before deployment $TIMESTAMP" 2>/dev/null || true

# Step 5: Pull latest changes
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"

echo -e "${GREEN}✅ Code updated from GitHub${NC}"

# Step 6: Install/Update dependencies (if composer is available)
if command -v composer &> /dev/null; then
    echo -e "${BLUE}🔧 Updating PHP dependencies...${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction
    echo -e "${GREEN}✅ Composer dependencies updated${NC}"
else
    echo -e "${YELLOW}⚠️  Composer not found. Make sure vendor folder is uploaded manually.${NC}"
fi

# Step 7: Build assets (if npm is available)
if command -v npm &> /dev/null; then
    echo -e "${BLUE}🏗️  Building frontend assets...${NC}"
    npm ci --production
    npm run build
    echo -e "${GREEN}✅ Assets built successfully${NC}"
else
    echo -e "${YELLOW}⚠️  NPM not found. Make sure public/build folder is uploaded manually.${NC}"
fi

# Step 8: Run Laravel optimizations
if [ -f "artisan" ]; then
    echo -e "${BLUE}⚡ Optimizing Laravel...${NC}"
    
    # Clear existing caches
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    
    # Cache for production
    php artisan config:cache
    php artisan route:cache  
    php artisan view:cache
    
    echo -e "${GREEN}✅ Laravel optimization completed${NC}"
    
    # Ask about running migrations
    echo -e "${YELLOW}🔄 Do you want to run database migrations? [y/N]${NC}"
    read -r -n 1 RUN_MIGRATIONS
    echo
    if [[ $RUN_MIGRATIONS =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}🗄️  Running database migrations...${NC}"
        php artisan migrate --force
        echo -e "${GREEN}✅ Migrations completed${NC}"
    else
        echo -e "${YELLOW}⏭️  Skipping migrations${NC}"
    fi
fi

# Step 9: Update public files
echo -e "${BLUE}📂 Updating public files...${NC}"

# Ensure public directory exists
mkdir -p "$PUBLIC_DIR"

# Sync public directory files
rsync -av --delete "$REPO_DIR/public/" "$PUBLIC_DIR/"

# Step 10: Update index.php paths
echo -e "${BLUE}🔧 Updating file paths...${NC}"

# Update index.php to point to correct Laravel app directory
if [ -f "$PUBLIC_DIR/index.php" ]; then
    # Backup original index.php
    cp "$PUBLIC_DIR/index.php" "$PUBLIC_DIR/index.php.backup"
    
    # Update paths in index.php
    sed -i "s|require __DIR__.'/\.\./vendor/autoload\.php';|require __DIR__.'/../hr-app/vendor/autoload.php';|g" "$PUBLIC_DIR/index.php"
    sed -i "s|require_once __DIR__.'/\.\./bootstrap/app\.php';|require_once __DIR__.'/../hr-app/bootstrap/app.php';|g" "$PUBLIC_DIR/index.php"
    
    echo -e "${GREEN}✅ Index.php paths updated${NC}"
fi

# Step 11: Set proper permissions
echo -e "${BLUE}🔒 Setting proper permissions...${NC}"

# Laravel storage permissions
find "$REPO_DIR/storage" -type f -exec chmod 664 {} \; 2>/dev/null || true
find "$REPO_DIR/storage" -type d -exec chmod 775 {} \; 2>/dev/null || true

# Bootstrap cache permissions  
find "$REPO_DIR/bootstrap/cache" -type f -exec chmod 664 {} \; 2>/dev/null || true
find "$REPO_DIR/bootstrap/cache" -type d -exec chmod 775 {} \; 2>/dev/null || true

# Public directory permissions
find "$PUBLIC_DIR" -type f -exec chmod 644 {} \; 2>/dev/null || true
find "$PUBLIC_DIR" -type d -exec chmod 755 {} \; 2>/dev/null || true

echo -e "${GREEN}✅ Permissions set correctly${NC}"

# Step 12: Clean old backups (keep last 5)
echo -e "${BLUE}🧹 Cleaning old backups...${NC}"
cd "$BACKUP_DIR"
ls -t backup_*.tar.gz 2>/dev/null | tail -n +6 | xargs -r rm -- 2>/dev/null || true

echo -e "${GREEN}✅ Old backups cleaned${NC}"

# Step 13: Deployment summary
echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
echo -e "${BLUE}📊 Deployment Summary:${NC}"
echo -e "   📅 Time: $(date)"
echo -e "   🌿 Branch: $BRANCH"
echo -e "   💾 Backup: backup_$TIMESTAMP.tar.gz"
echo -e "   📁 App Directory: $REPO_DIR"
echo -e "   🌐 Public Directory: $PUBLIC_DIR"

# Step 14: Show current commit
cd "$REPO_DIR"
CURRENT_COMMIT=$(git rev-parse --short HEAD)
COMMIT_MESSAGE=$(git log -1 --pretty=%B)
echo -e "   📝 Current Commit: $CURRENT_COMMIT"
echo -e "   💬 Commit Message: $COMMIT_MESSAGE"

echo -e "\n${GREEN}🌐 Your HR Dashboard application is now updated and live!${NC}"

# Optional: Show application URL
if [ -n "$1" ] && [ "$1" = "--show-url" ]; then
    echo -e "\n${BLUE}🔗 Access your application at: https://$(hostname -f)${NC}"
fi

echo -e "\n${YELLOW}💡 Tips:${NC}"
echo -e "   - Check your website to ensure everything is working"
echo -e "   - Monitor error logs if you encounter issues"
echo -e "   - To rollback: tar -xzf backups/backup_$TIMESTAMP.tar.gz -C public_html/"

exit 0