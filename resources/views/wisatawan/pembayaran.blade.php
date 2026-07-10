@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen">
    {{-- Content --}}
    @if(isset($pemesanan))
    <section class="flex items-center justify-center py-xl px-gutter min-h-[calc(100vh-200px)]">
        <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 gap-lg bg-surface-container-lowest rounded-2xl card-shadow p-lg">
            {{-- Payment Info Side --}}
            <div class="flex flex-col gap-md">
                <div>
                    <h1 class="font-display text-headline-md text-primary mb-sm">Complete Your Payment</h1>
                    <p class="font-body text-body-md text-on-surface-variant">Please transfer the exact amount to secure your Bangkiang Jaran experience.</p>
                </div>
                <div class="bg-surface-container-low rounded-xl p-md border border-outline-variant/30 flex justify-between items-center">
                    <div>
                        <p class="font-body text-caption text-outline mb-xs">Total Amount</p>
                        <p class="font-display text-headline-sm text-on-background">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-primary/10 px-sm py-xs rounded-full">
                        <span class="font-body text-label-md text-primary">{{ $pemesanan->kode_booking }}</span>
                    </div>
                </div>
                <div class="border-t border-outline-variant/30 pt-md">
                    <h2 class="font-display text-headline-sm text-primary mb-md">Manual Transfer</h2>
                    <div class="space-y-md">
                        {{-- Bank Detail --}}
                        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl border border-outline-variant/50 p-sm">
                            <div class="flex items-center gap-sm">
                                <div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-outline">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-body text-label-md text-on-background">Bank BCA</p>
                                    <p class="font-body text-body-md text-on-surface-variant">1234 5678 9012</p>
                                    <p class="font-body text-caption text-outline">a.n. Pengelola Bangkiang Jaran</p>
                                </div>
                            </div>
                            <button class="p-xs text-primary hover:bg-primary/10 rounded-lg transition-colors flex items-center gap-xs"
                                    onclick="navigator.clipboard.writeText('123456789012').then(() => { this.querySelector('span').textContent = 'check'; setTimeout(() => { this.querySelector('span').textContent = 'content_copy'; }, 2000); })">
                                <span class="material-symbols-outlined text-[20px]">content_copy</span>
                                <span class="font-body text-label-md hidden md:inline">Salin</span>
                            </button>
                        </div>
                        {{-- QRIS Detail --}}
                        <div class="flex flex-col items-center bg-surface-container-lowest rounded-xl border border-outline-variant/50 p-md text-center">
                            <p class="font-body text-label-md text-on-background mb-sm">Scan QRIS (All Payments)</p>
                            <div class="w-48 h-48 bg-white rounded-lg flex items-center justify-center border border-outline-variant/20 mb-sm">
                                <span class="material-symbols-outlined text-6xl text-outline">qr_code</span>
                            </div>
                            <p class="font-body text-caption text-outline">Scan using any supported banking or e-wallet app.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Side --}}
            <div class="flex flex-col gap-md">
                <h2 class="font-display text-headline-sm text-primary mb-sm">Upload Proof</h2>

                <form method="POST" action="{{ route('wisatawan.pembayaran.store', $pemesanan->id_pemesanan) }}"
                      enctype="multipart/form-data" class="flex flex-col gap-md flex-grow">
                    @csrf

                    {{-- Metode --}}
                    <div>
                        <label for="metode" class="font-body text-label-md text-on-surface-variant mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">payments</span>
                            Metode Pembayaran
                        </label>
                        <select name="metode" id="metode" required
                                class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl px-4 py-3 font-body text-body-md focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all">
                            <option value="">Pilih metode</option>
                            <option value="BCA">BCA</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Upload Bukti --}}
                    <div class="flex-grow flex flex-col justify-center border-2 border-dashed border-outline-variant rounded-2xl bg-surface-container-lowest hover:bg-surface-container-low transition-colors duration-300 relative group cursor-pointer overflow-hidden"
                         id="drop-zone">
                        <input type="file" name="bukti_bayar" id="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="p-lg flex flex-col items-center justify-center text-center pointer-events-none" id="upload-prompt">
                            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-md text-primary">
                                <span class="material-symbols-outlined text-[32px]">cloud_upload</span>
                            </div>
                            <p class="font-body text-label-md text-on-background mb-xs">Drag and drop your receipt here</p>
                            <p class="font-body text-body-md text-on-surface-variant mb-md">or click to browse from your device</p>
                            <p class="font-body text-caption text-outline">Supports JPG, PNG (Max 2MB)</p>
                        </div>
                        {{-- Hidden State: Uploaded Image Preview --}}
                        <div class="hidden absolute inset-0 w-full h-full bg-surface-container-lowest z-20 flex flex-col items-center justify-center p-md" id="upload-preview-container">
                            <img alt="Preview" class="max-h-[70%] object-contain rounded-lg mb-md" id="upload-preview" src="">
                            <p class="font-body text-caption text-on-surface-variant truncate w-full text-center mb-sm" id="file-name"></p>
                            <button class="text-error hover:bg-error/10 px-sm py-xs rounded font-body text-label-md flex items-center gap-xs z-30" id="remove-file" type="button">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Remove
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full bg-primary-container text-white font-body text-label-md py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-sm"
                            id="submit-btn">
                        Submit Payment Proof
                    </button>
                </form>

                {{-- Status Indicator --}}
                <div class="hidden mt-md p-md rounded-xl bg-secondary/10 border border-secondary/30 flex items-start gap-sm" id="status-indicator">
                    <span class="material-symbols-outlined text-secondary mt-xs">hourglass_empty</span>
                    <div>
                        <p class="font-body text-label-md text-secondary">Menunggu Verifikasi</p>
                        <p class="font-body text-caption text-on-surface-variant mt-xs">Your payment proof has been submitted. Our team will verify it shortly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

@push('scripts')
<script>
    // File Upload Logic
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('bukti_bayar');
    const uploadPrompt = document.getElementById('upload-prompt');
    const previewContainer = document.getElementById('upload-preview-container');
    const previewImage = document.getElementById('upload-preview');
    const fileNameDisplay = document.getElementById('file-name');
    const removeButton = document.getElementById('remove-file');

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/') || file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    fileNameDisplay.textContent = file.name;
                    uploadPrompt.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                    fileInput.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                alert("Please upload an image file.");
            }
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', (e) => { handleFiles(e.target.files); });
    }

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        if (dropZone) dropZone.addEventListener(eventName, (e) => { e.preventDefault(); e.stopPropagation(); }, false);
    });
    ['dragenter', 'dragover'].forEach(eventName => {
        if (dropZone) dropZone.addEventListener(eventName, () => { dropZone.classList.add('border-primary', 'bg-surface-container-low'); }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        if (dropZone) dropZone.addEventListener(eventName, () => { dropZone.classList.remove('border-primary', 'bg-surface-container-low'); }, false);
    });

    if (dropZone) {
        dropZone.addEventListener('drop', (e) => { handleFiles(e.dataTransfer.files); }, false);
    }

    if (removeButton) {
        removeButton.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            previewImage.src = '';
            fileNameDisplay.textContent = '';
            previewContainer.classList.add('hidden');
            uploadPrompt.classList.remove('hidden');
            fileInput.classList.remove('hidden');
        });
    }
</script>
@endpush
@endsection
