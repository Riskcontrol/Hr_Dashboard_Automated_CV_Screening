<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Document Available</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 25px 20px; text-align: center; border-radius: 6px 6px 0 0; }
        .content { background: #f8f9fa; padding: 25px; border: 1px solid #dee2e6; }
        .doc-box { background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; margin: 15px 0; }
        .doc-type { display: inline-block; background: #007bff; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px; margin-bottom: 8px; }
        .btn { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { text-align: center; padding: 15px; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">New Document Available</h2>
            <p style="margin:5px 0 0;">Risk Control Services Nigeria — HR Dashboard</p>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>HR has uploaded a new document that applies to all staff. Please review it at your earliest convenience.</p>
            <div class="doc-box">
                <span class="doc-type">{{ $rule->type_label }}</span>
                <h3 style="margin: 5px 0;">{{ $rule->title }}</h3>
                <p style="margin: 5px 0; color: #666; font-size: 13px;">
                    Uploaded by {{ $rule->uploaded_by }} on {{ $rule->created_at->format('F j, Y') }}
                </p>
            </div>
            <p>You can view and download this document by logging into your staff workspace:</p>
            <a href="{{ $viewUrl }}" class="btn">View Document</a>
            <p style="margin-top: 20px; font-size: 13px; color: #666;">
                If you have any questions, please contact HR directly.
            </p>
        </div>
        <div class="footer">
            <p>This is an automated notification from the HR Dashboard.</p>
        </div>
    </div>
</body>
</html>