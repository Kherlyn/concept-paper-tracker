@component('mail::message')
    # ✅ Concept Paper Approved

    Hello {{ $notifiable->name }},

    Great news! A concept paper has been successfully approved and completed.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Completion Date:** {{ $conceptPaper->completed_at?->format('F j, Y g:i A') ?? 'N/A' }}

        @if ($conceptPaper->submitted_at && $conceptPaper->completed_at)
            **Total Processing Time:** {{ $conceptPaper->submitted_at->diffInDays($conceptPaper->completed_at) }} days
        @endif

        **Status:** <span style="color: #16a34a; font-weight: bold;">APPROVED</span>
    @endcomponent

    All workflow stages have been completed successfully. The concept paper has reached "Budget Release" and the approval
    process is now complete.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id), 'color' => 'success'])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
