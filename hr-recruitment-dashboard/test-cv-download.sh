#!/bin/bash

# Test CV Download Functionality
echo "=== CV Download Test Script ==="
echo "Time: $(date)"
echo

# Check if we're in Laravel directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    exit 1
fi

echo "🔍 Testing CV download functionality..."
echo

# Create test CV file
TEST_CV_DIR="storage/app/private/cvs"
TEST_CV_FILE="$TEST_CV_DIR/test_download.pdf"

echo "📁 Creating test CV file..."
mkdir -p "$TEST_CV_DIR"
echo "Test CV Content - $(date)" > "$TEST_CV_FILE"

if [ -f "$TEST_CV_FILE" ]; then
    echo "✅ Test CV file created: $TEST_CV_FILE"
else
    echo "❌ Failed to create test CV file"
    exit 1
fi

echo
echo "🧪 Testing Laravel Storage access..."

# Test if Laravel can access the file
php artisan tinker --execute="
echo 'File exists via Storage: ' . (\Storage::disk('local')->exists('cvs/test_download.pdf') ? 'YES' : 'NO') . PHP_EOL;
echo 'Storage root path: ' . \Storage::disk('local')->path('') . PHP_EOL;
echo 'Full file path: ' . \Storage::disk('local')->path('cvs/test_download.pdf') . PHP_EOL;
echo 'File content: ' . \Storage::disk('local')->get('cvs/test_download.pdf') . PHP_EOL;
"

echo
echo "🧹 Cleaning up test file..."
rm -f "$TEST_CV_FILE"

echo "✅ CV download test completed"
echo
echo "📋 Verification steps for production:"
echo "1. Ensure storage/app/private/cvs directory exists and is writable"
echo "2. Check that uploaded CV files are in storage/app/private/cvs/"
echo "3. Verify admin dashboard download links use correct route"
echo "4. Test download functionality with real application"
