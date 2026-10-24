<header class="admin-page-head"><div><h1>Akun saya</h1><p>Kelola identitas dan informasi kontak akun pengelola.</p></div><a href="{{ route('pengelola.dashboard') }}" class="admin-btn admin-btn--quiet"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>Dashboard</a></header>
@if(session('status') === 'profile-updated')<div class="admin-alert" role="status">{{ __('messages.profile_updated') }}</div>@endif
@if(session('status') === 'password-set')<div class="admin-alert" role="status">Kata sandi berhasil diatur.</div>@endif
<div class="admin-profile-grid">
    <aside class="admin-panel admin-profile-card">
        <span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        <h2>{{ $user->name }}</h2><p>{{ $user->email }}</p><span class="admin-status mt-4">Pengelola</span>
        <dl><div><dt>Bergabung sejak</dt><dd>{{ $user->created_at?->locale(app()->getLocale())->translatedFormat('d F Y') ?? '-' }}</dd></div><div><dt>Username</dt><dd>{{ '@' . $user->username }}</dd></div><div><dt>Metode masuk</dt><dd>{{ $user->google_id ? 'Akun Google' : 'Email & kata sandi' }}</dd></div></dl>
    </aside>
    <div class="space-y-6">
        <section class="admin-panel">
            <div class="admin-panel-head"><div><h2>Informasi pribadi</h2><p>Gunakan kontak aktif untuk keperluan operasional.</p></div><span class="material-symbols-outlined" aria-hidden="true">manage_accounts</span></div>
            <form method="POST" action="{{ route('profile.update') }}" class="admin-profile-form">
                @csrf @method('PATCH')
                <div class="admin-profile-fields">
                    @foreach([
                        'name' => ['label' => 'Nama lengkap', 'type' => 'text', 'autocomplete' => 'name'],
                        'username' => ['label' => 'Username', 'type' => 'text', 'autocomplete' => 'username'],
                        'email' => ['label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'],
                        'phone' => ['label' => 'Nomor telepon', 'type' => 'tel', 'autocomplete' => 'tel'],
                    ] as $field => $meta)
                    <div><label class="admin-field" for="account-{{ $field }}">{{ $meta['label'] }}<input id="account-{{ $field }}" type="{{ $meta['type'] }}" name="{{ $field }}" value="{{ old($field, $user->{$field}) }}" autocomplete="{{ $meta['autocomplete'] }}" @if($field !== 'phone') required @endif @if(in_array($field, ['email', 'username'])) spellcheck="false" @endif @error($field) aria-invalid="true" aria-describedby="account-{{ $field }}-error" @enderror></label>
                    @error($field)<p id="account-{{ $field }}-error" class="admin-error mt-2">{{ $message }}</p>@enderror</div>
                    @endforeach
                </div>
                <div class="admin-profile-actions"><button type="submit" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">check</span>Simpan perubahan</button><a href="{{ route('profile.edit') }}" class="admin-btn admin-btn--quiet">Batalkan perubahan</a></div>
            </form>
        </section>
        @if($user->google_id)
        <section class="admin-panel">
            <div class="admin-panel-head"><div><h2>Atur kata sandi</h2><p>Tambahkan kata sandi untuk masuk menggunakan email.</p></div></div>
            <form method="POST" action="{{ route('profile.set-password') }}" class="admin-profile-form">
                @csrf
                <div class="admin-profile-fields"><div><label for="account-password" class="admin-field">Kata sandi baru<input id="account-password" type="password" name="password" autocomplete="new-password" minlength="8" required></label>@error('password')<p class="admin-error mt-2">{{ $message }}</p>@enderror</div><label for="account-confirmation" class="admin-field">Konfirmasi kata sandi<input id="account-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></label></div>
                <div class="admin-profile-actions"><button type="submit" class="admin-btn">Simpan kata sandi</button></div>
            </form>
        </section>
        @endif
    </div>
</div>
