# Troubleshooting Guide

## CV Processing Issues

### Problem: CVs remain "pending" after submission, GitHub Action not triggered

This typically happens when:

1. **Queue worker is not running**
2. **GitHub configuration is missing or incorrect**
3. **Failed jobs are not being retried**

### Solution Steps:

#### 1. Check Queue Worker Status

On the live server, check if the queue worker is running:

```bash
# Check if supervisord is running
ps aux | grep supervisord

# Check if laravel-queue process is running
ps aux | grep "queue:work"

# If supervisord is not running, start it:
supervisord -c /path/to/supervisord.conf

# Check supervisord status
supervisorctl status

# Restart queue worker if needed
supervisorctl restart laravel-queue
```

#### 2. Verify GitHub Configuration

Ensure your `.env` file on the live server contains:

```bash
# GitHub Configuration for CV Processing
GITHUB_TOKEN=your_github_personal_access_token
GITHUB_REPO_OWNER=Riskcontrol
GITHUB_REPO_NAME=Hr_Dashboard_Automated_CV_Screening
```

To generate a GitHub token:
1. Go to GitHub Settings > Developer settings > Personal access tokens
2. Generate a new token with `actions:write` and `repo` permissions
3. Add it to your `.env` file

#### 3. Test Queue Processing

```bash
# Check for pending jobs
php artisan queue:work --once

# Check for failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear config cache after .env changes
php artisan config:clear
```

#### 4. Manual Job Trigger (for testing)

```bash
php artisan tinker
# In tinker:
$app = \App\Models\Application::find(5); // Replace 5 with actual application ID
$keywordSet = \App\Models\KeywordSet::find($app->keyword_set_id);
\App\Jobs\ProcessCVJob::dispatch($app, $keywordSet);
exit
```

#### 5. Check Logs

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check queue worker logs (if using supervisord)
tail -f logs/laravel-queue.log
tail -f logs/laravel-queue-error.log
```

### Common Issues:

1. **GitHub Token Invalid**: Generate a new token with correct permissions
2. **Queue Worker Stopped**: Restart using supervisorctl
3. **Database Connection Issues**: Check DB credentials in .env
4. **File Permissions**: Ensure storage/ directories are writable

### Production Deployment Checklist:

- [ ] Queue worker is running (supervisord or systemd)
- [ ] GitHub token is set in .env
- [ ] GitHub repo owner/name are correct
- [ ] Laravel logs are being written
- [ ] File upload directory has proper permissions
- [ ] Database connection is working

### Debug Commands:

```bash
# Test GitHub API connection
php artisan tinker
# In tinker:
$service = new \App\Services\GitHubActionsProcessorService();
// This will show if GitHub config is loaded properly

# Check queue connection
php artisan queue:work --once --verbose

# Check application status
php artisan tinker
# In tinker:
\App\Models\Application::where('processing_status', 'pending')->count()
```
