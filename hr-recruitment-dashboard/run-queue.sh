#!/bin/bash

# Simple Queue Runner for Shared Hosting
# Run this manually or via cron job

echo "=== Laravel Queue Runner ==="
echo "Time: $(date)"
echo

# Change to your project directory
cd "$(dirname "$0")"

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    exit 1
fi

echo "📂 Working directory: $(pwd)"

# Process pending jobs
echo "🔄 Processing queue jobs..."
php artisan queue:work --stop-when-empty --timeout=60 --memory=512 --tries=3

echo
echo "✅ Queue processing completed"
echo "📊 Queue status:"
php artisan queue:monitor default

# Show recent failed jobs if any
FAILED_COUNT=$(php artisan queue:failed --format=json 2>/dev/null | wc -l)
if [ "$FAILED_COUNT" -gt 0 ]; then
    echo
    echo "⚠️  Failed jobs found: $FAILED_COUNT"
    echo "Run 'php artisan queue:failed' to see details"
    echo "Run 'php artisan queue:retry all' to retry failed jobs"
fi

echo
echo "=== Queue Runner Finished ==="
