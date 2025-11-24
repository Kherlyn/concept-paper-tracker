@component('mail::message')
    # ⏰ Deadline Reached

    Hello {{ $notifiable->name }},

    The deadline for a concept paper has been reached and requires immediate attention.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Current Stage:** {{ $conceptPaper->currentStage?->stage_name ?? 'N/A' }}

        **Deadline:** {{ $conceptPaper->deadline_date?->format('F j, Y g:i A') ?? 'N/A' }}

        **Status:** <span style="color: #dc2626; font-weight: bold;">DEADLINE REACHED</span>
    @endcomponent

    This concept paper has reached its overall deadline without completion. Please prioritize this paper to ensure timely
    processing and avoid further delays.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id), 'color' => 'error'])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
