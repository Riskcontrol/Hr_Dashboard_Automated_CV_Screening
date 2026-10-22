# 🚀 HR Dashboard - Quick Reference Guide

## 📋 cPanel Git Deployment - Quick Start

### 🔧 One-Time Setup
```bash
# 1. SSH into your cPanel hosting
ssh username@yourdomain.com

# 2. Upload and run setup script
wget https://raw.githubusercontent.com/Riskcontrol/Hr_Dashboard_Automated_CV_Screening/main/setup-cpanel-git.sh
chmod +x setup-cpanel-git.sh
./setup-cpanel-git.sh
```

### 🚀 Daily Deployment Commands
```bash
# Deploy latest changes
~/deploy.sh

# Deploy specific branch
~/deploy.sh development

# View deployment logs
tail -f /tmp/hr-dashboard-deploy.log

# Manual backup before risky changes
tar -czf ~/backups/manual_$(date +%Y%m%d_%H%M%S).tar.gz -C ~/public_html .
```

### 🎯 GitHub Webhook Setup
1. **Repository Settings** → **Webhooks** → **Add webhook**
2. **URL**: `https://yourdomain.com/deploy-webhook.php`
3. **Content-Type**: `application/json`
4. **Secret**: Use the secret from setup output
5. **Events**: Select "Just the push event"

### 🗄️ Database Configuration
```env
# Add to ~/hr-app/.env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=youraccount_hrdashboard
DB_USERNAME=youraccount_hruser
DB_PASSWORD=your_secure_password
```

### 🔄 Emergency Rollback
```bash
# List available backups
ls -la ~/backups/

# Restore from backup
tar -xzf ~/backups/backup_YYYYMMDD_HHMMSS.tar.gz -C ~/public_html/

# Or rollback using Git
cd ~/hr-app
git log --oneline -10
git checkout COMMIT_HASH
~/deploy.sh
```

### 🛠️ Troubleshooting
```bash
# Check Laravel logs
tail -f ~/hr-app/storage/logs/laravel.log

# Check PHP errors  
tail -f ~/public_html/error_log

# Test database connection
cd ~/hr-app && php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"

# Clear Laravel caches
cd ~/hr-app
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 📱 Local vs Production

| Environment | Local (XAMPP) | Production (cPanel) |
|-------------|---------------|---------------------|
| **Deploy** | Git pull | `~/deploy.sh` |
| **Database** | phpMyAdmin | cPanel MySQL |
| **Logs** | Terminal | `tail -f ~/hr-app/storage/logs/laravel.log` |
| **Assets** | `npm run dev` | `npm run build` (automated) |
| **Migrate** | `php artisan migrate` | `cd ~/hr-app && php artisan migrate --force` |

### 🔗 Important File Locations
```
~/hr-app/                    # Git repository
~/public_html/              # Web files
~/backups/                  # Deployment backups
~/deploy.sh                 # Deployment script
~/public_html/deploy-webhook.php  # Auto-deployment endpoint
```

### 📞 Emergency Contacts
- **Hosting Support**: Contact your cPanel provider
- **GitHub Issues**: Create issue in repository
- **Database Issues**: Check cPanel MySQL section

---

**💡 Pro Tip**: Bookmark this page for quick access to commands!