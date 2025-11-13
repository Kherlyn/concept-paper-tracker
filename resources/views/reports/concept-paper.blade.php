<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept Paper Report - {{ $paper->tracking_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background-color: #cfe2ff;
            color: #084298;
        }

        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-returned {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #842029;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .audit-entry {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 3px solid #2c3e50;
        }

        .audit-header {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .audit-meta {
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .audit-remarks {
            font-style: italic;
            color: #555;
        }

        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #bdc3c7;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Concept Paper Report</h1>
        <p>{{ $paper->tracking_number }}</p>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <div class="section-title">Paper Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tracking Number:</div>
                <div class="info-value">{{ $paper->tracking_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Title:</div>
                <div class="info-value">{{ $paper->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Requisitioner:</div>
                <div class="info-value">{{ $paper->requisitioner->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value">{{ $paper->department }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nature of Request:</div>
                <div class="info-value">{{ ucfirst($paper->nature_of_request) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                    @if ($paper->isOverdue())
                        <span class="status-badge status-overdue">Overdue</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Submitted At:</div>
                <div class="info-value">{{ $paper->submitted_at->format('F d, Y H:i:s') }}</div>
            </div>
            @if ($paper->completed_at)
                <div class="info-row">
                    <div class="info-label">Completed At:</div>
                    <div class="info-value">{{ $paper->completed_at->format('F d, Y H:i:s') }}</div>
                </div>
            @endif
            @if ($processingDays !== null)
                <div class="info-row">
                    <div class="info-label">Processing Time:</div>
                    <div class="info-value">{{ $processingDays }} day(s)</div>
                </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Workflow Progress</div>
        <table>
            <thead>
                <tr>
                    <th>Stage</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Started</th>
                    <th>Completed</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paper->stages as $stage)
                    <tr>
                        <td>{{ $stage->stage_name }}</td>
                        <td>{{ $stage->assignedUser->name ?? $stage->assigned_role }}</td>
                        <td>
                            <span class="status-badge status-{{ $stage->status }}">
                                {{ ucfirst($stage->status) }}
                            </span>
                            @if ($stage->isOverdue())
                                <span class="status-badge status-overdue">Overdue</span>
                            @endif
                        </td>
                        <td>{{ $stage->started_at ? $stage->started_at->format('M d, Y H:i') : '-' }}</td>
                        <td>{{ $stage->completed_at ? $stage->completed_at->format('M d, Y H:i') : '-' }}</td>
                        <td>{{ $stage->deadline->format('M d, Y H:i') }}</td>
                    </tr>
                    @if ($stage->remarks)
                        <tr>
                            <td colspan="6" style="background-color: #fff3cd; padding: 8px;">
                                <strong>Remarks:</strong> {{ $stage->remarks }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($paper->attachments->count() > 0)
        <div class="section">
            <div class="section-title">Attachments</div>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Uploaded By</th>
                        <th>Upload Date</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paper->attachments as $attachment)
                        <tr>
                            <td>{{ $attachment->file_name }}</td>
                            <td>{{ $attachment->uploader->name ?? 'N/A' }}</td>
                            <td>{{ $attachment->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($paper->auditLogs->count() > 0)
        <div class="section">
            <div class="section-title">Audit Trail</div>
            @foreach ($paper->auditLogs as $log)
                <div class="audit-entry">
                    <div class="audit-header">{{ $log->action }}</div>
                    <div class="audit-meta">
                        By: {{ $log->user->name ?? 'System' }} |
                        Date: {{ $log->created_at->format('F d, Y H:i:s') }}
                        @if ($log->stage_name)
                            | Stage: {{ $log->stage_name }}
                        @endif
                    </div>
                    @if ($log->remarks)
                        <div class="audit-remarks">{{ $log->remarks }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>This is a system-generated report from the Concept Paper Tracker</p>
        <p>Report generated on {{ $generatedAt }}</p>
    </div>
</body>

</html>
