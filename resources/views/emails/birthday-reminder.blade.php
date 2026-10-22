<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upcoming Staff Birthday</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .highlight { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; text-align: center; }
        .highlight .count { font-size: 1.6em; font-weight: bold; color: #856404; }
        .details { margin-top: 20px; }
        .details td { padding: 6px 0; }
        .details td:first-child { color: #666; width: 140px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Upcoming Staff Birthday</h2>
        </div>
        <div class="content">
            <div class="highlight">
                <p class="count">{{ $staffProfile->full_name }}'s birthday is in {{ $daysUntil }} {{ $daysUntil === 1 ? 'day' : 'days' }}</p>
            </div>

            <table class="details">
                <tr><td>Employee ID</td><td>{{ $staffProfile->employee_id }}</td></tr>
                <tr><td>Department</td><td>{{ $staffProfile->department ?: '—' }}</td></tr>
                <tr><td>Job Title</td><td>{{ $staffProfile->job_title ?: '—' }}</td></tr>
                <tr><td>Date of Birth</td><td>{{ $staffProfile->date_of_birth?->format('F j') }}</td></tr>
            </table>

            <p style="margin-top: 20px;">This is reminder {{ 4 - $daysUntil }} of 3 leading up to the birthday.</p>
        </div>
        <div class="footer">
            <p>Automated reminder from the HR Dashboard.</p>
        </div>
    </div>
</body>
</html>