DEADLINE REACHED

Hello {{ $notifiable->name }},

The deadline for a concept paper has been reached and requires immediate attention.

---

Concept Paper: {{ $conceptPaper->title }}

Tracking Number: {{ $conceptPaper->tracking_number }}

Current Stage: {{ $conceptPaper->currentStage?->stage_name ?? 'N/A' }}

Deadline: {{ $conceptPaper->deadline_date?->format('F j, Y g:i A') ?? 'N/A' }}

Status: DEADLINE REACHED

---

This concept paper has reached its overall deadline without completion. Please prioritize this paper to ensure timely
processing and avoid further delays.

View Concept Paper: {{ url('/concept-papers/' . $conceptPaper->id) }}

Thanks,
{{ config('app.name') }}
