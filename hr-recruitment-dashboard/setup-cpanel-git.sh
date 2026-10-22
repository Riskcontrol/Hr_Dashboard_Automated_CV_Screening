#!/bin/bash

# HR Dashboard - cPanel Git Setup Script
# Run this script after uploading to your cPanel hosting

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 HR Dashboard - cPanel Git Setup${NC}"
echo "======================================="

# Get user input
echo -e "${YELLOW}Please provide the following information:${NC}"
echo

read -p "Your GitHub username: " GITHUB_USER
read -p "Your email address: " EMAIL
read -p "Your domain name (e.g., yourdomain.com): " DOMAIN

echo
echo -e "${BLUE}📋 Setup Summary:${NC}"
echo "GitHub User: $GITHUB_USER"
echo "Email: $EMAIL" 
echo "Domain: $DOMAIN"
echo

read -p "Continue with setup? [Y/n]: " CONFIRM
if [[ $CONFIRM =~ ^[Nn]$ ]]; then
    echo "Setup cancelled."
    exit 0
fi

echo
echo -e "${BLUE}🔧 Starting setup process...${NC}"

# Step 1: Create necessary directories
echo -e "${YELLOW}📁 Creating directories...${NC}"
mkdir -p ~/backups
mkdir -p ~/.ssh
echo -e "${GREEN}✅ Directories created${NC}"

# Step 2: Generate SSH key if it doesn't exist
if [ ! -f ~/.ssh/id_rsa ]; then
    echo -e "${YELLOW}🔑 Generating SSH key...${NC}"
    ssh-keygen -t rsa -b 4096 -C "$EMAIL" -f ~/.ssh/id_rsa -N ""
    echo -e "${GREEN}✅ SSH key generated${NC}"
else
    echo -e "${YELLOW}🔑 SSH key already exists${NC}"
fi

# Step 3: Display public key
echo -e "${BLUE}📋 Your SSH Public Key:${NC}"
echo "======================================="
cat ~/.ssh/id_rsa.pub
echo "======================================="
echo
echo -e "${YELLOW}📌 IMPORTANT: Copy the above SSH key and add it to your GitHub account${NC}"
echo "   1. Go to GitHub.com → Settings → SSH and GPG keys"
echo "   2. Click 'New SSH key'"
echo "   3. Paste the key above"
echo "   4. Save the key"
echo

read -p "Press Enter after adding the SSH key to GitHub..."

# Step 4: Test GitHub connection
echo -e "${BLUE}🔗 Testing GitHub connection...${NC}"
if ssh -T git@github.com -o StrictHostKeyChecking=no 2>&1 | grep -q "successfully authenticated"; then
    echo -e "${GREEN}✅ GitHub SSH connection successful${NC}"
else
    echo -e "${RED}❌ GitHub SSH connection failed${NC}"
    echo "Please make sure you've added the SSH key to your GitHub account"
    exit 1
fi

# Step 5: Clone repository
echo -e "${BLUE}📥 Cloning repository...${NC}"
if [ -d ~/hr-app ]; then
    echo -e "${YELLOW}⚠️  Repository directory already exists. Skipping clone.${NC}"
else
    git clone git@github.com:Riskcontrol/Hr_Dashboard_Automated_CV_Screening.git ~/hr-app
    echo -e "${GREEN}✅ Repository cloned${NC}"
fi

# Step 6: Setup deployment script
echo -e "${BLUE}🔧 Setting up deployment script...${NC}"
cp ~/hr-app/deploy.sh ~/deploy.sh
chmod +x ~/deploy.sh
echo -e "${GREEN}✅ Deployment script ready${NC}"

# Step 7: Setup webhook handler
echo -e "${BLUE}🎣 Setting up webhook handler...${NC}"
cp ~/hr-app/deploy-webhook.php ~/public_html/deploy-webhook.php

# Generate random webhook secret
WEBHOOK_SECRET=$(openssl rand -hex 32)
sed -i "s/change_this_to_your_webhook_secret/$WEBHOOK_SECRET/g" ~/public_html/deploy-webhook.php
echo -e "${GREEN}✅ Webhook handler configured${NC}"

# Step 8: Initial deployment
echo -e "${BLUE}🚀 Running initial deployment...${NC}"
cd ~/hr-app

# Create .env from example if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${YELLOW}⚠️  .env file created from example. Please configure it with your database settings.${NC}"
fi

# Run deployment
~/deploy.sh

echo
echo -e "${GREEN}🎉 Setup completed successfully!${NC}"
echo
echo -e "${BLUE}📋 Next Steps:${NC}"
echo "1. Configure your .env file with database settings:"
echo "   nano ~/hr-app/.env"
echo
echo "2. Set up GitHub webhook (optional for auto-deployment):"
echo "   URL: https://$DOMAIN/deploy-webhook.php"
echo "   Secret: $WEBHOOK_SECRET"
echo "   Content-Type: application/json"
echo "   Event: Push events"
echo
echo "3. Configure your database in cPanel MySQL"
echo
echo "4. Run migrations:"
echo "   cd ~/hr-app && php artisan migrate"
echo
echo -e "${BLUE}🔧 Useful Commands:${NC}"
echo "Deploy updates: ~/deploy.sh"
echo "View logs: tail -f /tmp/hr-dashboard-deploy.log"
echo "Backup current: tar -czf ~/backups/manual_backup_\$(date +%Y%m%d_%H%M%S).tar.gz -C ~/public_html ."
echo
echo -e "${GREEN}Your HR Dashboard is now ready for Git-based deployments!${NC}"