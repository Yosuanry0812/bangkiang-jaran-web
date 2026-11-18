<header class="admin-page-head"><div><h1>{{ __('panel.akun_saya_474b') }}</h1><p>{{ __('panel.kelola_identitas_dan_informa_d835') }}</p></div><a href="{{ route('pengelola.dashboard') }}" class="admin-btn admin-btn--quiet"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>Dashboard</a></header>
@if(session('status') === 'profile-updated')<div class="admin-alert" role="status">{{ __('messages.profile_updated') }}</div>@endif
@if(session('status') === 'password-set')<div class="admin-alert" role="status">{{ __('panel.kata_sandi_berhasil_diatur_c97f') }}</div>@endif
<div class="admin-profile-grid">
    <aside class="admin-panel admin-profile-card">
        <span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        <h2>{{ $user->name }}</h2><p>{{ $user->email }}</p><span class="admin-status mt-4">{{ __('panel.pengelola_58eb') }}</span>
        <dl><div><dt>{{ __('panel.bergabung_sejak_80a5') }}</dt><dd>{{ $user->created_at?->locale(app()->getLocale())->translatedFormat('d F Y') ?? '-' }}</dd></div><div><dt>Username</dt><dd>{{ '@' . $user->username }}</dd></div><div><dt>{{ __('panel.metode_masuk_3bc9') }}</dt><dd>{{ $user->google_id ? __('ui.google_account') : __('messages.email_password') }}</dd></div></dl>
    </aside>
    <div class="space-y-6">
        <section class="admin-panel">
            <div class="admin-panel-head"><div><h2>{{ __('panel.informasi_pribadi_6a31') }}</h2><p>{{ __('panel.gunakan_kontak_aktif_untuk_1c8d') }}</p></div><span class="material-symbols-outlined" aria-hidden="true">manage_accounts</span></div>
            <form method="POST" action="{{ route('profile.update') }}" class="admin-profile-form">
                @csrf @method('PATCH')
                <div class="admin-profile-fields">
                    @foreach([
                        'name' => ['label' => __('ui.full_name_lc'), 'type' => 'text', 'autocomplete' => 'name'],
                        'username' => ['label' => 'Username', 'type' => 'text', 'autocomplete' => 'username'],
                        'email' => ['label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'],
                        'phone' => ['label' => __('panel.nomor_telepon_e50f'), 'type' => 'tel', 'autocomplete' => 'tel'],
                    ] as $field => $meta)
                    <div><label class="admin-field" for="account-{{ $field }}">{{ $meta['label'] }}<input id="account-{{ $field }}" type="{{ $meta['type'] }}" name="{{ $field }}" value="{{ old($field, $user->{$field}) }}" autocomplete="{{ $meta['autocomplete'] }}" @if($field !== 'phone') required @endif @if(in_array($field, ['email', 'username'])) spellcheck="false" @endif @error($field) aria-invalid="true" aria-describedby="account-{{ $field }}-error" @enderror></label>
                    @error($field)<p id="account-{{ $field }}-error" class="admin-error mt-2">{{ $message }}</p>@enderror</div>
                    @endforeach
                </div>
                <div class="admin-profile-actions"><button type="submit" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">check</span>{{ __('panel.simpan_perubahan_fd33') }}</button><a href="{{ route('profile.edit') }}" class="admin-btn admin-btn--quiet">{{ __('panel.batalkan_perubahan_fe7a') }}</a></div>
            </form>
        </section>
        @if($user->google_id)
        <section class="admin-panel">
            <div class="admin-panel-head"><div><h2>{{ __('panel.atur_kata_sandi_d4fb') }}</h2><p>{{ __('panel.tambahkan_kata_sandi_untuk_8f1b') }}</p></div></div>
            <form method="POST" action="{{ route('profile.set-password') }}" class="admin-profile-form">
                @csrf
                @if(Auth::user()->password)<label class="block text-sm">{{ __('operations.current_password') }}<input type="password" name="current_password" autocomplete="current-password" required class="block w-full rounded-xl border-gray-300 mt-2"></label>@error('current_password')<p role="alert">{{ $message }}</p>@enderror
                @endif
                <div class="admin-profile-fields"><div><label for="account-password" class="admin-field">{{ __('panel.kata_sandi_baru_2987') }}<input id="account-password" type="password" name="password" autocomplete="new-password" minlength="8" required></label>@error('password')<p class="admin-error mt-2">{{ $message }}</p>@enderror</div><label for="account-confirmation" class="admin-field">{{ __('panel.konfirmasi_kata_sandi_dfa0') }}<input id="account-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></label></div>
                <div class="admin-profile-actions"><button type="submit" class="admin-btn">{{ __('panel.simpan_kata_sandi_4e7c') }}</button></div>
            </form>
        </section>
        @endif
    </div>
</div>
