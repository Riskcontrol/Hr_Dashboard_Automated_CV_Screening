# Queue Management Quick Reference

## 🚀 For Shared Hosting (No Root Access)

### Method 1: Cron Job (Recommended)
Set up in cPanel cron jobs:
```bash
* * * * * cd /home/yourusername/public_html/your-project && php artisan queue:work --stop-when-empty --timeout=50 > /dev/null 2>&1
```

### Method 2: Manual Scripts
```bash
# Run once
./run-queue.sh

# Start background worker
./queue-manager.sh start

# Check worker status  
./queue-manager.sh status

# Stop worker
./queue-manager.sh stop
```

### Method 3: Laravel Scheduler
Add to cron (once):
```bash
* * * * * cd /home/yourusername/public_html/your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🔧 Troubleshooting Commands

### Check Queue Status
```bash
php artisan queue:monitor default
```

### Process Jobs Manually
```bash
php artisan queue:work --once
```

### Check Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
php artisan queue:retry all
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

## 📊 Monitoring

### Check Running Processes
```bash
ps aux | grep "queue:work"
```

### Kill Stuck Processes
```bash
pkill -f "queue:work"
```

### View Queue Logs
```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/queue-worker.log  # if using queue-manager.sh
```

## 🎯 Best Practices for Shared Hosting

1. **Use cron jobs instead of long-running processes**
2. **Set reasonable timeouts (50-60 seconds)**
3. **Monitor failed jobs regularly**
4. **Use `--stop-when-empty` to prevent memory leaks**
5. **Restart workers after code deployments**

## ⚡ Quick Commands

```bash
# Emergency: Process all pending jobs now
php artisan queue:work --stop-when-empty

# Check what's in the queue
php artisan tinker
DB::table('jobs')->count()

# Manual job dispatch (testing)
php artisan tinker
$app = \App\Models\Application::where('processing_status', 'pending')->first();
$keywordSet = \App\Models\KeywordSet::find($app->keyword_set_id);
\App\Jobs\ProcessCVJob::dispatch($app, $keywordSet);
```
