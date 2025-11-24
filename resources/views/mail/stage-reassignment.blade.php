@component('mail::message')
    # 🔄 Stage Reassigned to You

    Hello {{ $notifiable->name }},

    A workflow stage has been reassigned to you from another user.

    @component('mail::panel')
        **Concept Paper:** {{ $conceptPaper->title }}

        **Tracking Number:** {{ $conceptPaper->tracking_number }}

        **Stage:** {{ $stage->stage_name }}

        **Previously Assigned To:** {{ $previousUser->name }}

        **Deadline:** {{ $stage->deadline?->format('F j, Y g:i A') ?? 'N/A' }}
    @endcomponent

    This stage was reassigned by an administrator. Please review the concept paper and complete this stage before the
    deadline.

    @component('mail::button', ['url' => url('/concept-papers/' . $conceptPaper->id)])
        View Concept Paper
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
