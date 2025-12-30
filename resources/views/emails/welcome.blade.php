@component('mail::message')
{{ __('top') }}

@component('mail::button', ['url' => route('register')])
{{ __('Clique me') }}
@endcomponent

{{ __('Content :content', ['content' => 'to email']) }}

{{ __('footer') }}
@endcomponent
