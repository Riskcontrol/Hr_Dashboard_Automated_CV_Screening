# PRODUCTION CV PROCESSING FIX

## 🚨 Critical Issue Fixed
CVs were remaining "pending" because GitHub Actions couldn't download files (404) and send callbacks (419 CSRF error).

## ✅ Solutions Implemented

### 1. Added Missing CV File API Endpoint
**File Created**: `app/Http/Controllers/CVFileController.php`
**Route Added**: `GET /api/cv/file/{encodedPath}`

This endpoint allows GitHub Actions to download CV files for processing.

### 2. Fixed Callback CSRF Issue  
**Route Moved**: Callback from web routes (CSRF protected) to API routes (no CSRF)
**Route**: `POST /api/cv/processing/callback`

This allows GitHub Actions to send processing results back without CSRF token errors.

### 3. Updated Environment Template
**File Updated**: `.env.example` 
**Added**: GitHub configuration variables

## 🔧 IMMEDIATE DEPLOYMENT STEPS

### Step 1: Update Live Server Code
```bash
cd /path/to/your/live/laravel/project
git pull origin main
composer install --no-dev --optimize-autoloader
```

### Step 2: Clear All Caches
```bash
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear
```

### Step 3: Add GitHub Configuration to Production .env
Add these lines to your production `.env` file:

```bash
# GitHub Configuration for CV Processing (REQUIRED)
GITHUB_TOKEN=your_github_personal_access_token
GITHUB_REPO_OWNER=Riskcontrol
GITHUB_REPO_NAME=Hr_Dashboard_Automated_CV_Screening
```

**To get GitHub token:**
1. Go to GitHub.com → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token
3. Select scopes: `repo` and `actions:write`
4. Copy token and add to `.env`

### Step 4: Restart Queue Worker
```bash
# Check if queue worker is running
ps aux | grep queue:work

# If using supervisord
supervisorctl restart laravel-queue

# Or start manually
nohup php artisan queue:work --daemon &
```

### Step 5: Test Fix
Run diagnostic script:
```bash
chmod +x diagnose-cv-processing.sh
./diagnose-cv-processing.sh
```

## 🧪 Verification Tests

### Test 1: Check New Routes
```bash
php artisan route:list | grep cv
```
Should show:
- `GET /api/cv/file/{encodedPath}`  
- `POST /api/cv/processing/callback`

### Test 2: Test CV File Endpoint
Create test file and check endpoint:
```bash
# Create test file
mkdir -p storage/app/private/cvs
echo "test content" > storage/app/private/cvs/test.pdf

# Get base64 encoded path
php -r "echo base64_encode('cvs/test.pdf');"

# Test endpoint (replace {encoded} with output above)
curl "https://your-domain.com/api/cv/file/{encoded}"
```

### Test 3: Process Existing Pending CV
```bash
php artisan tinker
# In tinker:
$pending = \App\Models\Application::where('processing_status', 'pending')->first();
if($pending) {
    $keywordSet = \App\Models\KeywordSet::find($pending->keyword_set_id);
    \App\Jobs\ProcessCVJob::dispatch($pending, $keywordSet);
    echo "Job dispatched for application ID: " . $pending->id;
}
exit
```

Then check queue:
```bash
php artisan queue:work --once
```

## 📊 Expected Results

After these fixes:
1. ✅ CVs will download successfully (no more 404 errors)
2. ✅ GitHub Actions can send callbacks (no more 419 CSRF errors) 
3. ✅ Applications will update from "pending" to "qualified"/"not_qualified"
4. ✅ Queue worker processes jobs automatically

## 🔍 Files Changed

- `app/Http/Controllers/CVFileController.php` (NEW)
- `routes/api.php` (UPDATED - added CV endpoints)
- `app/Services/GitHubActionsProcessorService.php` (UPDATED - callback URL)
- `.env.example` (UPDATED - GitHub config)

## 🚨 If Still Not Working

1. **Check Queue Worker**: `ps aux | grep queue:work`
2. **Check GitHub Token**: Test in tinker: `config('services.github.token')`
3. **Check Failed Jobs**: `php artisan queue:failed`
4. **Check Logs**: `tail -f storage/logs/laravel.log`
5. **Run Diagnostic**: `./diagnose-cv-processing.sh`

## 💡 Prevention
To prevent this in future:
1. Keep queue worker running with supervisord
2. Monitor GitHub Action logs for failures
3. Set up log monitoring for Laravel errors
4. Test CV processing after deployments
