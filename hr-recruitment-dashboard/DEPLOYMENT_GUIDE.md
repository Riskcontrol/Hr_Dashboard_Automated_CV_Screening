# HR Recruitment Dashboard - Deployment Guide

## 🚀 cPanel Deployment Instructions

This guide will help you deploy the HR Recruitment Dashboard Laravel application to your cPanel hosting environment.

### Prerequisites

- cPanel hosting account with PHP 8.2+ support
- MySQL database access
- SSH access (optional but recommended)
- Composer installed on hosting (or ability to upload vendor folder)

## 📋 Deployment Steps

### 1. Database Setup

#### In cPanel MySQL Databases:
```sql
-- Create database (use cPanel MySQL Databases interface)
Database Name: your_account_hrrecruitment (or similar)

-- Create database user
Username: hr_user
Password: [strong_password]

-- Grant all privileges to the user for the database
```

#### Alternative: Using phpMyAdmin
1. Login to phpMyAdmin
2. Create a new database: `your_account_hrrecruitment`
3. Create user with all privileges on this database

### 2. File Upload

#### Method A: Direct Upload (Recommended)
1. **Download/Export from Git:**
   - Download the repository as ZIP from GitHub
   - Extract the files locally

2. **Upload via cPanel File Manager:**
   - Navigate to your domain's public_html directory
   - Upload all Laravel files EXCEPT the `public` folder contents
   - The `public` folder contents should go directly in `public_html`
   - Other Laravel files go in a folder like `public_html/laravel-app/`

#### Suggested Directory Structure:
```
public_html/
├── index.php (from Laravel's public folder)
├── .htaccess (from Laravel's public folder)
├── favicon.ico (from Laravel's public folder)
├── robots.txt (from Laravel's public folder)
├── build/ (from Laravel's public folder - contains CSS/JS assets)
└── laravel-app/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── tests/
    ├── vendor/
    ├── composer.json
    ├── artisan
    └── .env
```

### 3. Environment Configuration

#### Create .env file in `laravel-app/` directory:
```env
APP_NAME="HR Recruitment Dashboard"
APP_ENV=production
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_account_hrrecruitment
DB_USERNAME=hr_user
DB_PASSWORD=your_strong_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Queue Configuration (Use database for shared hosting)
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# GitHub Integration (for CV processing)
GITHUB_TOKEN=your_personal_access_token
GITHUB_REPO_OWNER=your_github_username
GITHUB_REPO_NAME=your_repository_name

VITE_APP_NAME="${APP_NAME}"
```

### 4. Update index.php Path

#### Edit `public_html/index.php`:
```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Update these paths to point to your Laravel app directory
require __DIR__.'/laravel-app/vendor/autoload.php';

$app = require_once __DIR__.'/laravel-app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### 5. Set Proper Permissions

```bash
# Via SSH (if available):
chmod -R 755 /path/to/laravel-app
chmod -R 775 /path/to/laravel-app/storage
chmod -R 775 /path/to/laravel-app/bootstrap/cache

# Via cPanel File Manager:
# Set folder permissions to 755
# Set storage/ and bootstrap/cache/ to 775
```

### 6. Install Dependencies

#### Method A: If Composer is available on server
```bash
cd /path/to/laravel-app
composer install --no-dev --optimize-autoloader
```

#### Method B: Upload vendor folder
1. Run `composer install --no-dev --optimize-autoloader` locally
2. Upload the entire `vendor/` folder to your `laravel-app/` directory

### 7. Generate Application Key

```bash
# Via SSH:
cd /path/to/laravel-app
php artisan key:generate

# Or manually generate and add to .env:
# Use online Laravel key generator or run locally and copy
```

### 8. Run Database Migrations

```bash
# Via SSH:
cd /path/to/laravel-app
php artisan migrate

# Or import SQL manually via phpMyAdmin if no SSH access
```

### 9. Build Assets (if needed)

The assets are already built and included in the `public/build/` directory. Make sure these files are uploaded to `public_html/build/`.

### 10. Configure Cron Jobs (for Queue Processing)

#### In cPanel Cron Jobs, add:
```bash
# Run every minute
* * * * * cd /path/to/laravel-app && php artisan queue:work --stop-when-empty >> /dev/null 2>&1

# Alternative: Run every 5 minutes (for shared hosting with limited resources)
*/5 * * * * cd /path/to/laravel-app && php artisan queue:work --max-jobs=10 --stop-when-empty >> /dev/null 2>&1
```

## 🔧 Troubleshooting

### Common Issues:

#### 1. "500 Internal Server Error"
- Check `.htaccess` file in public_html
- Verify file permissions (755 for folders, 644 for files)
- Check error logs in cPanel

#### 2. "Database Connection Error"
- Verify database credentials in `.env`
- Ensure database exists and user has proper privileges
- Check if MySQL service is running

#### 3. "Class not found" errors
- Ensure `vendor/` folder is uploaded
- Run `composer dump-autoload` if possible
- Check file permissions

#### 4. CSS/JS not loading
- Verify `public/build/` folder is in `public_html/build/`
- Check `APP_URL` in `.env` matches your domain
- Clear browser cache

#### 5. File Upload Issues
- Check `storage/` folder permissions (775)
- Verify PHP upload limits in cPanel
- Ensure `storage/app/public` is writable

### Performance Optimization:

```bash
# Run these commands after deployment:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📱 Local Development vs Production Differences

| Feature | Local (XAMPP) | Production (cPanel) |
|---------|---------------|-------------------|
| Database | MySQL via XAMPP | cPanel MySQL |
| Queue Driver | database | database |
| Session Driver | database | database |
| Cache Driver | database | database |
| Mail Driver | log (testing) | smtp (real emails) |
| Debug Mode | true | false |
| Environment | local | production |

## 🔐 Security Considerations

1. **Never commit .env to version control**
2. **Use strong database passwords**
3. **Set APP_DEBUG=false in production**
4. **Keep APP_KEY secure and unique**
5. **Use HTTPS (SSL certificate)**
6. **Regularly update dependencies**

## 📞 Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] Database connection works
- [ ] User registration/login functions
- [ ] File upload works (CV upload)
- [ ] Queue jobs process (background CV processing)
- [ ] Email notifications work
- [ ] GitHub integration configured (if using)
- [ ] SSL certificate active
- [ ] Backup strategy in place

## 🆘 Support

If you encounter issues during deployment:

1. Check cPanel error logs
2. Review Laravel logs in `storage/logs/`
3. Test database connection separately
4. Verify file permissions
5. Contact your hosting provider for server-specific issues

---

**Note:** This guide assumes standard cPanel hosting. Some shared hosting providers may have different configurations or limitations. Adjust the instructions according to your specific hosting environment.