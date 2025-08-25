# cPanel Git Deployment Guide

## 🚀 Setting up Git Integration with cPanel Hosting

This guide will help you configure your cPanel hosting to pull changes directly from your GitHub repository, enabling seamless deployments and updates.

## 📋 Prerequisites

- cPanel hosting account with SSH access
- Git installed on your hosting server (most modern hosts have this)
- SSH key access or personal access tokens for GitHub
- Basic familiarity with command line

## 🔍 Step 1: Check cPanel Git Support

### Method A: Check through cPanel Interface
1. Login to your cPanel
2. Look for "Git Version Control" or "Git™ Version Control" in the Files section
3. If available, you can use the GUI interface for Git operations

### Method B: Check via SSH
```bash
# SSH into your hosting account
ssh username@yourdomain.com

# Check if Git is installed
git --version

# Check current directory
pwd
```

## 🔑 Step 2: Setup SSH Keys for GitHub

### Generate SSH Key on cPanel
```bash
# SSH into your hosting account
ssh username@yourdomain.com

# Generate SSH key (use your email)
ssh-keygen -t rsa -b 4096 -C "your-email@domain.com"

# Press Enter to save to default location
# Set a passphrase (optional but recommended)

# Display your public key
cat ~/.ssh/id_rsa.pub
```

### Add SSH Key to GitHub
1. Copy the entire SSH public key output
2. Go to GitHub → Settings → SSH and GPG keys
3. Click "New SSH key"
4. Paste your key and save

### Test SSH Connection
```bash
# Test GitHub SSH connection
ssh -T git@github.com

# You should see: "Hi username! You've successfully authenticated..."
```

## 📂 Step 3: Clone Repository to cPanel

### Initial Repository Setup

```bash
# SSH into your hosting account
ssh username@yourdomain.com

# Navigate to your home directory (not public_html yet)
cd ~

# Clone your repository
git clone git@github.com:Riskcontrol/Hr_Dashboard_Automated_CV_Screening.git hr-app

# Navigate into the cloned repository
cd hr-app

# Verify the clone
git status
git remote -v
```

## 📁 Step 4: Setup Proper Directory Structure

### Recommended cPanel Structure
```bash
# Your directory structure should look like:
/home/username/
├── public_html/
│   ├── index.php (Laravel's public/index.php)
│   ├── .htaccess (Laravel's public/.htaccess)  
│   ├── build/ (Laravel's public/build/)
│   └── favicon.ico (Laravel's public/favicon.ico)
├── hr-app/ (Git repository)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── ... (other Laravel files)
└── backups/ (for deployment backups)
```

### Setup Symlinks or Copy Files
```bash
# Method A: Create symlinks (if supported)
ln -sf ~/hr-app/public/* ~/public_html/
ln -sf ~/hr-app/public/.htaccess ~/public_html/

# Method B: Copy files (safer for shared hosting)
cp ~/hr-app/public/* ~/public_html/
cp ~/hr-app/public/.htaccess ~/public_html/

# Update index.php path
# Edit ~/public_html/index.php and update require paths:
# require __DIR__.'/../hr-app/vendor/autoload.php';
# $app = require_once __DIR__.'/../hr-app/bootstrap/app.php';
```

## 🔄 Step 5: Create Deployment Script

Create an automated deployment script to handle updates:

```bash
# Create deployment script
nano ~/deploy.sh
```

### Deployment Script Content:
```bash
#!/bin/bash

# HR Dashboard Deployment Script
# Usage: ./deploy.sh [branch_name]

set -e  # Exit on any error

# Configuration
REPO_DIR="$HOME/hr-app"
PUBLIC_DIR="$HOME/public_html"
BACKUP_DIR="$HOME/backups"
BRANCH=${1:-main}
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

echo "🚀 Starting deployment at $(date)"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Step 1: Backup current deployment
echo "📦 Creating backup..."
tar -czf "$BACKUP_DIR/backup_$TIMESTAMP.tar.gz" -C "$PUBLIC_DIR" . 2>/dev/null || true

# Step 2: Navigate to repository
cd "$REPO_DIR"

echo "📥 Pulling latest changes from GitHub..."

# Step 3: Stash any local changes
git stash push -m "Auto-stash before deployment $TIMESTAMP" 2>/dev/null || true

# Step 4: Pull latest changes
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"

# Step 5: Install/Update dependencies (if composer is available)
if command -v composer &> /dev/null; then
    echo "🔧 Updating dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "⚠️  Composer not found. Make sure to upload vendor folder manually."
fi

# Step 6: Run Laravel optimizations
if [ -f "artisan" ]; then
    echo "⚡ Optimizing Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Run migrations (be careful with this in production)
    # php artisan migrate --force
fi

# Step 7: Build assets (if npm is available)
if command -v npm &> /dev/null; then
    echo "🏗️  Building assets..."
    npm ci --production
    npm run build
fi

# Step 8: Update public files
echo "📂 Updating public files..."
rsync -av --delete "$REPO_DIR/public/" "$PUBLIC_DIR/"

# Step 9: Set proper permissions
echo "🔒 Setting permissions..."
find "$REPO_DIR/storage" -type f -exec chmod 664 {} \;
find "$REPO_DIR/storage" -type d -exec chmod 775 {} \;
find "$REPO_DIR/bootstrap/cache" -type f -exec chmod 664 {} \; 2>/dev/null || true
find "$REPO_DIR/bootstrap/cache" -type d -exec chmod 775 {} \; 2>/dev/null || true

# Step 10: Update index.php if needed
sed -i "s|require __DIR__.'/\.\./vendor/autoload\.php';|require __DIR__.'/../hr-app/vendor/autoload.php';|g" "$PUBLIC_DIR/index.php"
sed -i "s|require_once __DIR__.'/\.\./bootstrap/app\.php';|require_once __DIR__.'/../hr-app/bootstrap/app.php';|g" "$PUBLIC_DIR/index.php"

echo "✅ Deployment completed successfully at $(date)"
echo "📝 Backup saved as: backup_$TIMESTAMP.tar.gz"

# Step 11: Clean old backups (keep last 5)
echo "🧹 Cleaning old backups..."
cd "$BACKUP_DIR"
ls -t backup_*.tar.gz | tail -n +6 | xargs -r rm --

echo "🎉 Deployment finished! Your application is now updated."
```

### Make Script Executable
```bash
chmod +x ~/deploy.sh
```

## 🔄 Step 6: Manual Deployment Process

### Quick Update Command
```bash
# SSH into your hosting
ssh username@yourdomain.com

# Run deployment
~/deploy.sh

# Or specify a branch
~/deploy.sh development
```

### Manual Step-by-Step Process
```bash
# 1. Navigate to repository
cd ~/hr-app

# 2. Pull latest changes
git pull origin main

# 3. Update dependencies (if composer available)
composer install --no-dev --optimize-autoloader

# 4. Build assets (if npm available)
npm run build

# 5. Update public files
rsync -av public/ ~/public_html/

# 6. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Run migrations if needed
php artisan migrate --force
```

## 🎣 Step 7: GitHub Webhook Integration (Advanced)

### Setup Webhook Endpoint

Create a webhook handler script:

```bash
# Create webhook handler
nano ~/public_html/deploy-webhook.php
```

### Webhook Script:
```php
<?php
// GitHub Webhook Handler
// Place this in your public_html directory

$secret = 'your_webhook_secret_here'; // Set this in GitHub webhook settings
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Verify signature
if (!hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature)) {
    http_response_code(401);
    exit('Unauthorized');
}

$data = json_decode($payload, true);

// Only deploy on push to main branch
if ($data['ref'] === 'refs/heads/main') {
    // Log the deployment
    file_put_contents('/tmp/deploy.log', date('Y-m-d H:i:s') . " - Webhook triggered\n", FILE_APPEND);
    
    // Execute deployment script
    $output = shell_exec('cd ' . $_SERVER['HOME'] . ' && ./deploy.sh 2>&1');
    
    // Log output
    file_put_contents('/tmp/deploy.log', $output . "\n", FILE_APPEND);
    
    http_response_code(200);
    echo "Deployment triggered";
} else {
    http_response_code(200);
    echo "Not main branch, skipping deployment";
}
?>
```

### Configure GitHub Webhook
1. Go to your GitHub repository → Settings → Webhooks
2. Add webhook URL: `https://yourdomain.com/deploy-webhook.php`
3. Content type: `application/json`
4. Secret: Set the same secret as in your PHP script
5. Events: Select "Just the push event"

## 🛡️ Step 8: Security Considerations

### Protect Sensitive Files
```bash
# Add to .htaccess in public_html
<Files "deploy-webhook.php">
    # Only allow GitHub webhook IPs (optional)
    # Require ip 140.82.112.0/20
    # Require ip 185.199.108.0/22
    # Require ip 192.30.252.0/22
</Files>

# Protect the hr-app directory
<Directory "/home/username/hr-app">
    Deny from all
</Directory>
```

### Environment Security
```bash
# Make sure .env is not accessible
echo "Files ~ \"^\.env\"" > ~/hr-app/.htaccess
echo "    Require all denied" >> ~/hr-app/.htaccess
echo "</Files>" >> ~/hr-app/.htaccess
```

## 🧪 Step 9: Testing Your Setup

### Test Manual Deployment
```bash
# 1. Make a small change to your repository
# 2. Push to GitHub
# 3. SSH to your hosting
# 4. Run deployment script
~/deploy.sh

# 5. Check if changes are live
curl -I https://yourdomain.com
```

### Test Webhook (if configured)
1. Push a commit to the main branch
2. Check webhook delivery in GitHub
3. Verify changes are live on your site
4. Check deployment logs: `tail -f /tmp/deploy.log`

## 🚨 Troubleshooting

### Common Issues:

#### Git Authentication Failed
```bash
# Use personal access token instead of SSH
git remote set-url origin https://username:token@github.com/Riskcontrol/Hr_Dashboard_Automated_CV_Screening.git
```

#### Permission Denied
```bash
# Fix file permissions
chmod 755 ~/deploy.sh
chmod -R 775 ~/hr-app/storage
chmod -R 775 ~/hr-app/bootstrap/cache
```

#### Composer/NPM Not Available
- Upload `vendor/` folder manually after running `composer install` locally
- Upload `public/build/` folder after running `npm run build` locally

#### Database Migration Issues
```bash
# Backup database before migrations
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Run migrations carefully
php artisan migrate --force
```

## 📊 Monitoring and Maintenance

### Regular Tasks:
1. **Monitor disk space**: Git history can grow large
2. **Clean old backups**: Keep only recent backups
3. **Update dependencies**: Regularly update composer/npm packages
4. **Security updates**: Keep hosting environment updated
5. **Database backups**: Backup before major deployments

### Rollback Process:
```bash
# If deployment fails, rollback:
cd ~/backups
tar -xzf backup_YYYYMMDD_HHMMSS.tar.gz -C ~/public_html/

# Or use Git rollback:
cd ~/hr-app
git log --oneline -10  # Find commit to rollback to
git checkout commit_hash
~/deploy.sh
```

## 🎯 Benefits of This Setup

- ✅ **One-command deployments**: `~/deploy.sh`
- ✅ **Automatic backups**: Before each deployment
- ✅ **Zero-downtime updates**: Quick file synchronization
- ✅ **Version control**: Full Git history on production
- ✅ **Rollback capability**: Easy to revert changes
- ✅ **Automated workflows**: With GitHub webhooks
- ✅ **Production optimization**: Automatic caching and optimization

This setup gives you professional-grade deployment capabilities on your cPanel hosting!