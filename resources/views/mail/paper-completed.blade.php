@component('mail::message')
    # ✅ Concept Paper Completed

    Hello {{ $notifiable->name }},

    Great news! A concept paper has successfully completed all workflow stages.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Department:** {{ $conceptPaper->department }}

        **Completed At:** {{ $conceptPaper->completed_at->format('F j, Y g:i A') }}
    @endcomponent

    The budget has been released and the approval process is complete.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id), 'color' => 'success'])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
