@component('mail::message')
    # ⚠️ Overdue Stage Alert

    Hello {{ $notifiable->name }},

    A workflow stage assigned to you is now **overdue** and requires immediate attention.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Stage:** {{ $stage->stage_name }}

        **Deadline:** {{ $stage->deadline->format('F j, Y g:i A') }}

        **Status:** <span style="color: #dc2626; font-weight: bold;">OVERDUE</span>
    @endcomponent

    Please complete this stage as soon as possible to avoid further delays in the approval process.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id), 'color' => 'error'])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
