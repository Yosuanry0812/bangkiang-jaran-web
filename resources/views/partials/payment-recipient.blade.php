@if($method['is_trial'] ?? false)
    <p class="bj-payment-trial-note">{{ app()->getLocale() === 'en' ? 'Trial method. Upload a sample receipt; no transfer is needed.' : 'Metode uji coba. Unggah bukti contoh, tidak perlu melakukan transfer.' }}</p>
@else
    <div><p class="font-sans text-xs text-stone mb-1">{{ __('messages.account_number') }}</p><p class="font-sans text-xl font-semibold text-forest">{{ $method['number'] }}</p><p class="font-sans text-xs text-stone mt-1">a.n. {{ $method['account_name'] }}</p></div>
    <button type="button" class="bj-recipient-copy" data-number="{{ $method['number'] }}" onclick="salin(this.dataset.number, 'salin-{{ $method['id'] }}')"><span class="material-symbols-outlined" id="salin-{{ $method['id'] }}" aria-hidden="true">content_copy</span>{{ __('messages.copy') }}</button>
@endif
