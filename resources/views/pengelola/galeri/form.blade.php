@extends('pengelola.layouts.admin')
@section('title', __('messages.add_media'))

@section('content')
@php $en = app()->getLocale() === 'en'; @endphp
<header class="admin-page-head"><div><h1>{{ __('messages.add_media') }}</h1></div></header>

<form class="gallery-upload" action="{{ route('pengelola.galeri.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
    @csrf
    <fieldset>
        <legend><label for="fileInput">{{ __('messages.file_image') }}</label></legend>
        <input class="gallery-upload__file" type="file" name="file[]" id="fileInput" accept="image/jpeg,image/png" multiple required>
        <p class="gallery-upload__hint">{{ $en ? 'You can select up to 20 photos at once. Format: JPG, JPEG, PNG. Max 50MB each.' : 'Bisa pilih sampai 20 foto sekaligus. Format: JPG, JPEG, PNG. Maks 50MB per foto.' }}</p>
        <p class="gallery-upload__count" id="fileCount" hidden></p>
        <div class="gallery-upload__thumbs" id="thumbs"></div>
        @error('file') <p class="gallery-upload__error">{{ $message }}</p> @enderror
        @foreach($errors->get('file.*') as $messages) @foreach(array_unique($messages) as $message) <p class="gallery-upload__error">{{ $message }}</p> @endforeach @endforeach
    </fieldset>

    <fieldset>
        <legend>{{ __('panel.tipe_galeri_1100') }}</legend>
        <div class="gallery-upload__types">
            <label><input type="radio" name="tipe" value="wisata" required {{ old('tipe', 'wisata') === 'wisata' ? 'checked' : '' }}><span>{{ __('panel.wisata_bangkiang_jaran_b48d') }}</span></label>
            <label><input type="radio" name="tipe" value="restoran" {{ old('tipe') === 'restoran' ? 'checked' : '' }}><span>{{ __('panel.restoran_kuliner_7b13') }}</span></label>
            <label><input type="radio" name="tipe" value="sejarah" {{ old('tipe') === 'sejarah' ? 'checked' : '' }}><span>{{ __('panel.sejarah_bangkiang_jaran') }}</span></label>
        </div>
        <p class="gallery-upload__hint">{{ __('panel.sejarah_hint') }}</p>
        @error('tipe') <p class="gallery-upload__error">{{ $message }}</p> @enderror
    </fieldset>

    <div class="gallery-upload__actions">
        <button type="submit" class="admin-btn" id="submitBtn"><span class="material-symbols-outlined" aria-hidden="true">upload</span><span data-label>{{ __('messages.save') }}</span></button>
        <a href="{{ route('pengelola.galeri.index') }}" class="admin-btn admin-btn--quiet">{{ __('messages.back') }}</a>
    </div>
    <p class="gallery-upload__hint" id="busyNote" hidden>{{ $en ? 'Uploading and optimizing photos, please keep this page open.' : 'Sedang mengunggah dan mengoptimalkan foto, jangan tutup halaman ini.' }}</p>
</form>

@push('scripts')
<script>
(() => {
    const input = document.getElementById('fileInput'), thumbs = document.getElementById('thumbs'),
          count = document.getElementById('fileCount'), form = document.getElementById('uploadForm'),
          btn = document.getElementById('submitBtn'), note = document.getElementById('busyNote');
    const en = @json($en), max = 20, MAX_EDGE = 2560, SKIP_BELOW = 2.5 * 1024 * 1024, PARALLEL = 3;
    const t = (id, en_) => en ? en_ : id;
    let cells = [];

    input.addEventListener('change', () => {
        thumbs.replaceChildren(); cells = [];
        const files = [...input.files];
        count.hidden = files.length === 0;
        if (files.length > max) {
            count.textContent = t(`Terlalu banyak foto (${files.length}). Maksimal ${max}.`, `Too many photos (${files.length}). Maximum is ${max}.`);
            return;
        }
        count.textContent = t(`${files.length} foto dipilih`, `${files.length} photo(s) selected`);
        files.forEach(file => {
            const img = document.createElement('img');
            img.alt = file.name; img.title = file.name;
            thumbs.appendChild(img); cells.push(img);
            createImageBitmap(file, { resizeWidth: 240, resizeQuality: 'low' }).then(bmp => {
                const c = document.createElement('canvas'); c.width = bmp.width; c.height = bmp.height;
                c.getContext('2d').drawImage(bmp, 0, 0); bmp.close();
                img.src = c.toDataURL('image/jpeg', 0.7);
            }).catch(() => { img.src = URL.createObjectURL(file); });
        });
    });

    // Kecilkan di browser: sisi terpanjang maks 2560px. Cukup tajam untuk layar 2K, ukuran file turun belasan kali lipat.
    async function shrink(file) {
        if (file.size <= SKIP_BELOW) return file;
        try {
            // Probe kecil dulu (decode JPEG diperkecil, cepat) untuk tahu rasio, lalu decode langsung ke ukuran akhir.
            const probe = await createImageBitmap(file, { resizeWidth: 64, resizeQuality: 'low' });
            const ratio = probe.width / probe.height; probe.close();
            const w = ratio >= 1 ? MAX_EDGE : Math.round(MAX_EDGE * ratio);
            const h = ratio >= 1 ? Math.round(MAX_EDGE / ratio) : MAX_EDGE;
            const bmp = await createImageBitmap(file, { resizeWidth: w, resizeHeight: h, resizeQuality: 'medium' });
            const c = new OffscreenCanvas(bmp.width, bmp.height);
            c.getContext('2d').drawImage(bmp, 0, 0); bmp.close();
            const blob = await c.convertToBlob({ type: 'image/jpeg', quality: 0.9 });
            if (!blob || blob.size >= file.size) return file;
            return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' });
        } catch (e) { return file; }
    }

    async function send(file, index) {
        cells[index]?.classList.add('is-busy');
        const body = new FormData();
        body.append('_token', form.querySelector('[name=_token]').value);
        body.append('tipe', form.querySelector('[name=tipe]:checked').value);
        body.append('file[]', await shrink(file));
        const res = await fetch(form.action, { method: 'POST', body, headers: { Accept: 'application/json' } });
        if (!res.ok) {
            let msg = t('Gagal mengunggah.', 'Upload failed.');
            try { const j = await res.json(); msg = Object.values(j.errors || {}).flat()[0] || j.message || msg; } catch (e) {}
            throw new Error(msg);
        }
        cells[index]?.classList.replace('is-busy', 'is-done') || cells[index]?.classList.add('is-done');
    }

    form.addEventListener('submit', async event => {
        const files = [...input.files];
        if (!files.length || files.length > max || !window.createImageBitmap || !window.fetch) {
            if (files.length > max) event.preventDefault();
            return; // tanpa fitur modern: kirim biasa
        }
        event.preventDefault();
        btn.disabled = true; note.hidden = false;
        const label = btn.querySelector('[data-label]');
        let done = 0, failed = [], next = 0;
        const progress = () => { label.textContent = t(`Mengunggah ${done}/${files.length}…`, `Uploading ${done}/${files.length}…`); };
        progress();
        const worker = async () => {
            while (next < files.length) {
                const i = next++;
                try { await send(files[i], i); done++; }
                catch (e) { failed.push(`${files[i].name}: ${e.message}`); cells[i]?.classList.replace('is-busy', 'is-failed'); }
                progress();
            }
        };
        await Promise.all(Array.from({ length: Math.min(PARALLEL, files.length) }, worker));
        if (failed.length) {
            btn.disabled = false; label.textContent = @json(__('messages.save'));
            note.textContent = t(`${done} berhasil, ${failed.length} gagal — ${failed[0]}`, `${done} uploaded, ${failed.length} failed — ${failed[0]}`);
            return;
        }
        note.textContent = t(`${done} foto berhasil ditambahkan.`, `${done} photo(s) added.`);
        window.location = @json(route('pengelola.galeri.index'));
    });
})();
</script>
@endpush
@endsection
