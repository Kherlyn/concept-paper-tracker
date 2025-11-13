@component('mail::message')
    # Concept Paper Returned

    Hello {{ $notifiable->name }},

    A concept paper has been returned to the previous stage and requires your attention.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Returned From:** {{ $stage->stage_name }}

        **Remarks:**
        {{ $remarks }}
    @endcomponent

    Please review the remarks carefully and take appropriate action to address the concerns raised.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id), 'color' => 'error'])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
