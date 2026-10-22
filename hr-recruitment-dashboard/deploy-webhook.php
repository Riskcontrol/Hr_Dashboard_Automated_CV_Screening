<?php
/**
 * GitHub Webhook Handler for HR Dashboard
 * 
 * Place this file in your public_html directory
 * Configure webhook URL: https://yourdomain.com/deploy-webhook.php
 * 
 * Security: Set a secret in GitHub webhook settings and update $secret below
 */

// Configuration
$secret = 'change_this_to_your_webhook_secret'; // CHANGE THIS!
$logFile = '/tmp/hr-dashboard-deploy.log';
$deployScript = $_SERVER['HOME'] . '/deploy.sh';

// Function to log messages
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Function to send response and exit
function sendResponse($code, $message) {
    http_response_code($code);
    echo $message;
    logMessage("Response: $code - $message");
    exit;
}

// Start logging
logMessage("Webhook received from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, 'Method not allowed');
}

// Get payload
$payload = file_get_contents('php://input');
if (empty($payload)) {
    sendResponse(400, 'Empty payload');
}

// Verify GitHub signature
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (empty($signature)) {
    sendResponse(401, 'Missing signature');
}

$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expectedSignature, $signature)) {
    logMessage("Signature mismatch. Expected: $expectedSignature, Got: $signature");
    sendResponse(401, 'Invalid signature');
}

// Parse payload
$data = json_decode($payload, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse(400, 'Invalid JSON payload');
}

logMessage("Webhook validated successfully");

// Check if it's a push event to main branch
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/main') {
    $ref = $data['ref'] ?? 'unknown';
    logMessage("Ignoring push to branch: $ref");
    sendResponse(200, "Ignoring push to $ref - only main branch triggers deployment");
}

// Check if deployment script exists
if (!file_exists($deployScript)) {
    logMessage("Deployment script not found: $deployScript");
    sendResponse(500, 'Deployment script not found');
}

// Log the deployment trigger
$commitSha = $data['after'] ?? 'unknown';
$commitMessage = $data['head_commit']['message'] ?? 'No message';
$pusher = $data['pusher']['name'] ?? 'unknown';

logMessage("Deployment triggered by $pusher");
logMessage("Commit: $commitSha - $commitMessage");

// Execute deployment script in background
$command = "nohup bash $deployScript > /tmp/deploy-output.log 2>&1 &";
$result = exec($command);

logMessage("Deployment script executed: $command");

// Send success response
sendResponse(200, json_encode([
    'status' => 'success',
    'message' => 'Deployment triggered successfully',
    'commit' => $commitSha,
    'timestamp' => date('c')
]));
?>