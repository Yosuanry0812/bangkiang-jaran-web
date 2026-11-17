<section class="bj-operations bj-destination-info" aria-labelledby="destination-info-title">
    <h2 id="destination-info-title">{{ __('operations.visit_information') }}</h2>
    <dl><dt>{{ __('operations.operating_hours') }}</dt><dd>{{ $destination->localized('operating_hours') }}</dd>
    <dt>{{ __('operations.address') }}</dt><dd>{{ $destination->localized('address') }}</dd></dl>
    @if($destination->localized('rules'))<h3>{{ __('operations.visit_rules') }}</h3><p class="bj-preserve-lines">{{ $destination->localized('rules') }}</p>@endif
    @php
        $contact = $destination->values();
    @endphp
    <div class="bj-operations-actions">
        @if($contact['phone'])<a href="tel:{{ preg_replace('/[^+0-9]/', '', $contact['phone']) }}">{{ $contact['phone'] }}</a>@endif
        @if($contact['whatsapp'])<a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>@endif
        @if($contact['email'])<a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>@endif
    </div>
</section>
