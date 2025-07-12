<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Status</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .status-qualified { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; }
        .status-not-qualified { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; }
        .keyword { background: #e9ecef; padding: 3px 8px; margin: 2px; border-radius: 3px; font-size: 0.9em; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Risk Control Services Nigeria</h1>
            <p>Application Status Update</p>
        </div>

        <div class="content">
            <h2>Dear {{ $application->applicant_name }},</h2>
            
            <p>Your job application for <strong>{{ $application->keywordSet->job_title ?? 'N/A' }}</strong> has been processed.</p>

            @if($application->isQualified())
                <div class="status-qualified">
                    <h3>🎉 Congratulations! You're Qualified</h3>
                    <p>Your CV matches our requirements with <strong>{{ $application->match_percentage }}%</strong> compatibility.</p>
                    
                    @if($application->found_keywords)
                        <p><strong>Matching Skills Found:</strong></p>
                        <div>
                            @foreach($application->found_keywords as $keyword)
                                <span class="keyword">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    <p style="margin-top: 15px;">
                        <strong>Next Steps:</strong><br>
                        Our HR team will contact you within 2-3 business days to schedule an interview.
                    </p>
                </div>
            @else
                <div class="status-not-qualified">
                    <h3>Application Status: Not Qualified</h3>
                    <p>Your CV matches <strong>{{ $application->match_percentage }}%</strong> of our requirements.</p>
                    
                    @if($application->found_keywords)
                        <p><strong>Skills Found:</strong></p>
                        <div>
                            @foreach($application->found_keywords as $keyword)
                                <span class="keyword">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($application->missing_keywords)
                        <p><strong>Skills We're Looking For:</strong></p>
                        <div>
                            @foreach($application->missing_keywords as $keyword)
                                <span class="keyword">{{ $keyword }}</span>
                            @endforeach
                        </div>
                        <p style="margin-top: 15px;">
                            <em>Consider developing these skills and applying for future opportunities.</em>
                        </p>
                    @endif
                </div>
            @endif

            <hr style="margin: 20px 0;">
            
            <p><strong>Application Details:</strong></p>
            <ul>
                <li>Application ID: #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</li>
                <li>Submitted: {{ $application->created_at->format('M d, Y \a\t H:i') }}</li>
                <li>Processed: {{ $application->processed_at->format('M d, Y \a\t H:i') }}</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for your interest in Risk Control Services Nigeria.</p>
            <p>For any questions, please contact us at hr@riskcontrol.ng</p>
        </div>
    </div>
</body>
</html>