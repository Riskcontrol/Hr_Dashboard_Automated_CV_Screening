# #!/bin/bash

# # CV Processing Diagnostic Script
# # Run this on your live server to diagnose CV processing issues

# echo "=== CV Processing Diagnostic Script ==="
# echo "Timestamp: $(date)"
# echo

# # Check if we're in a Laravel project
# if [ ! -f "artisan" ]; then
#     echo "❌ Error: This doesn't appear to be a Laravel project directory"
#     echo "Please run this script from your Laravel project root"
#     exit 1
# fi

# echo "✅ Laravel project detected"
# echo

# # Check PHP version
# echo "📋 PHP Version:"
# php -v | head -n 1
# echo

# # Check if .env file exists
# if [ -f ".env" ]; then
#     echo "✅ .env file exists"
# else
#     echo "❌ .env file missing"
#     echo "Please copy .env.example to .env"
#     exit 1
# fi

# # Check GitHub configuration
# echo "📋 GitHub Configuration:"
# GITHUB_TOKEN=$(grep "GITHUB_TOKEN=" .env | cut -d'=' -f2)
# GITHUB_OWNER=$(grep "GITHUB_REPO_OWNER=" .env | cut -d'=' -f2)
# GITHUB_REPO=$(grep "GITHUB_REPO_NAME=" .env | cut -d'=' -f2)

# if [ -z "$GITHUB_TOKEN" ] || [ "$GITHUB_TOKEN" = "your_github_personal_access_token" ]; then
#     echo "❌ GITHUB_TOKEN not configured or using default value"
# else
#     echo "✅ GITHUB_TOKEN configured"
# fi

# if [ -z "$GITHUB_OWNER" ] || [ "$GITHUB_OWNER" = "your_github_username" ]; then
#     echo "❌ GITHUB_REPO_OWNER not configured or using default value"
# else
#     echo "✅ GITHUB_REPO_OWNER: $GITHUB_OWNER"
# fi

# if [ -z "$GITHUB_REPO" ] || [ "$GITHUB_REPO" = "your_repository_name" ]; then
#     echo "❌ GITHUB_REPO_NAME not configured or using default value"
# else
#     echo "✅ GITHUB_REPO_NAME: $GITHUB_REPO"
# fi

# echo

# # Check queue configuration
# echo "📋 Queue Configuration:"
# QUEUE_CONNECTION=$(grep "QUEUE_CONNECTION=" .env | cut -d'=' -f2)
# echo "Queue Driver: $QUEUE_CONNECTION"
# echo

# # Check for pending jobs
# echo "📋 Queue Status:"
# PENDING_JOBS=$(php artisan tinker --execute="echo \DB::table('jobs')->count();" 2>/dev/null || echo "Error")
# echo "Pending jobs in queue: $PENDING_JOBS"

# FAILED_JOBS=$(php artisan queue:failed --format=json 2>/dev/null | wc -l || echo "Error")
# echo "Failed jobs: $FAILED_JOBS"
# echo

# # Check if queue worker processes are running
# echo "📋 Queue Worker Status:"
# QUEUE_PROCESSES=$(ps aux | grep "queue:work" | grep -v grep | wc -l)
# if [ "$QUEUE_PROCESSES" -gt 0 ]; then
#     echo "✅ Queue worker processes running: $QUEUE_PROCESSES"
#     ps aux | grep "queue:work" | grep -v grep
# else
#     echo "❌ No queue worker processes found"
#     echo "You need to start the queue worker with: php artisan queue:work"
# fi
# echo

# # Check supervisord status if available
# echo "📋 Supervisord Status:"
# if command -v supervisorctl &> /dev/null; then
#     echo "Supervisord processes:"
#     supervisorctl status 2>/dev/null || echo "Supervisord not running or not accessible"
# else
#     echo "Supervisorctl not available"
# fi
# echo

# # Check recent applications
# echo "📋 Recent Applications:"
# php artisan tinker --execute="
# \$recent = \App\Models\Application::latest()->limit(5)->get(['id', 'applicant_email', 'processing_status', 'created_at']);
# foreach(\$recent as \$app) {
#     echo 'ID: ' . \$app->id . ', Email: ' . \$app->applicant_email . ', Status: ' . \$app->processing_status . ', Created: ' . \$app->created_at . PHP_EOL;
# }
# " 2>/dev/null
# echo

# # Check logs
# echo "📋 Recent Log Entries:"
# if [ -f "storage/logs/laravel.log" ]; then
#     echo "Last 5 log entries:"
#     tail -n 5 storage/logs/laravel.log
# else
#     echo "❌ Laravel log file not found"
# fi
# echo

# echo "=== Diagnostic Complete ==="
# echo
# echo "📋 Quick Fixes:"
# echo "1. If GitHub config is missing, add to .env:"
# echo "   GITHUB_TOKEN=your_token_here"
# echo "   GITHUB_REPO_OWNER=Riskcontrol" 
# echo "   GITHUB_REPO_NAME=Hr_Dashboard_Automated_CV_Screening"
# echo
# echo "2. If queue worker not running:"
# echo "   php artisan queue:work --daemon"
# echo "   Or use supervisord: supervisorctl start laravel-queue"
# echo
# echo "3. Clear config cache after .env changes:"
# echo "   php artisan config:clear"
# echo
# echo "4. Test processing manually:"
# echo "   php artisan queue:work --once"
