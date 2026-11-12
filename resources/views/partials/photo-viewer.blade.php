@php($viewerEnglish = app()->getLocale() === 'en')
<dialog id="bj-photo-viewer" class="bj-photo-viewer" aria-label="{{ $viewerEnglish ? 'Full photo' : 'Foto lengkap' }}" data-loading-label="{{ $viewerEnglish ? 'Loading photo…' : 'Memuat foto…' }}" data-error-label="{{ $viewerEnglish ? 'The photo could not be loaded.' : 'Foto belum dapat dimuat.' }}">
    <button type="button" class="bj-photo-viewer__close" data-photo-close autofocus aria-label="{{ $viewerEnglish ? 'Close photo' : 'Tutup foto' }}"><span aria-hidden="true">×</span></button>
    <div class="bj-photo-viewer__stage" data-photo-stage>
        <img alt="" decoding="async" hidden>
        <p role="status" data-photo-status></p>
    </div>
</dialog>
