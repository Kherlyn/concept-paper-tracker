CONCEPT PAPER APPROVED

Hello {{ $notifiable->name }},

Great news! A concept paper has been successfully approved and completed.

---

Concept Paper: {{ $conceptPaper->title }}

Tracking Number: {{ $conceptPaper->tracking_number }}

Completion Date: {{ $conceptPaper->completed_at?->format('F j, Y g:i A') ?? 'N/A' }}

@if ($conceptPaper->submitted_at && $conceptPaper->completed_at)
    Total Processing Time: {{ $conceptPaper->submitted_at->diffInDays($conceptPaper->completed_at) }} days
@endif

Status: APPROVED

---

All workflow stages have been completed successfully. The concept paper has reached "Budget Release" and the approval
process is now complete.

View Concept Paper: {{ url('/concept-papers/' . $conceptPaper->id) }}

Thanks,
{{ config('app.name') }}
