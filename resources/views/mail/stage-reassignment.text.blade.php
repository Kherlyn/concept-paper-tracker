STAGE REASSIGNED TO YOU

Hello {{ $notifiable->name }},

A workflow stage has been reassigned to you from another user.

---

Concept Paper: {{ $conceptPaper->title }}

Tracking Number: {{ $conceptPaper->tracking_number }}

Stage: {{ $stage->stage_name }}

Previously Assigned To: {{ $previousUser->name }}

Deadline: {{ $stage->deadline?->format('F j, Y g:i A') ?? 'N/A' }}

---

This stage was reassigned by an administrator. Please review the concept paper and complete this stage before the
deadline.

View Concept Paper: {{ url('/concept-papers/' . $conceptPaper->id) }}

Thanks,
{{ config('app.name') }}
