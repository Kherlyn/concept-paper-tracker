@component('mail::message')
    # New Stage Assignment

    Hello {{ $notifiable->name }},

    A new workflow stage has been assigned to you.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Stage:** {{ $stage->stage_name }}

        **Deadline:** {{ $stage->deadline->format('F j, Y g:i A') }}
    @endcomponent

    Please complete this stage before the deadline to ensure timely processing.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id)])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
