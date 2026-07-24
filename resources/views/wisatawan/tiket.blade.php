@extends('layouts.app')

@section('title', 'Tiket Masuk — Bangkiang Jaran')

@section('content')

{{-- ══════════════════════════════════════════ --}}
{{--  HERO                                      --}}
{{-- ══════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1503785640985-62d183d5cfe7?auto=format&fit=crop&w=1920&q=80"
             class="w-full h-full object-cover" alt="Bangkiang Jaran">
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(0,0,0,0.72) 0%,rgba(0,0,0,0.38) 60%,rgba(0,0,0,0.18) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.45) 0%,transparent 55%);"></div>
    </div>
    <div class="relative z-10 w-full max-w-6xl mx-auto px-gutter py-32 lg:py-44">
        <div class="max-w-xl" data-aos="fade-up" data-aos-duration="900">
            <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-white/50 mb-8">Pemesanan Online</p>
            <h1 class="font-serif text-[clamp(3rem,7vw,5.5rem)] leading-[1.0] text-white mb-7">
                {{ __('messages.plan_your_journey') }}
            </h1>
            <p class="font-sans text-[15px] text-white/60 mb-10 leading-relaxed max-w-sm">
                {{ __('messages.plan_your_journey_desc') }}
            </p>
            <div class="flex flex-wrap items-center gap-3">
                @auth
                <a href="{{ route('wisatawan.pemesanan.create') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-sans text-sm font-medium px-6 py-3 rounded-full hover:bg-gray-100 transition-colors">
                    Pesan Tiket <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-sans text-sm font-medium px-6 py-3 rounded-full hover:bg-gray-100 transition-colors">
                    Masuk untuk Memesan <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @endauth
                <a href="#tiket-list"
                   class="inline-flex items-center gap-2 border border-white/25 text-white/80 font-sans text-sm px-6 py-3 rounded-full hover:border-white/50 hover:text-white transition-colors">
                    Lihat Harga
                </a>
            </div>
            <div class="flex flex-wrap gap-6 mt-14 pt-10 border-t border-white/10">
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">Jam Buka</p>
                    <p class="font-sans text-sm text-white/80">07:00 – 18:00 WITA</p>
                </div>
                <div class="w-px bg-white/10"></div>
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">Lokasi</p>
                    <p class="font-sans text-sm text-white/80">Desa Bakbakan, Gianyar</p>
                </div>
                <div class="w-px bg-white/10"></div>
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">Pengunjung</p>
                    <p class="font-sans text-sm text-white/80">5.000+ per bulan</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  TICKET LIST                               --}}
{{-- ══════════════════════════════════════════ --}}
<section id="tiket-list" class="py-24 md:py-32 px-gutter bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">Harga Tiket</p>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h2 class="font-serif text-5xl md:text-6xl text-gray-900 leading-[1.05]">Pilih Tiket Anda</h2>
                <p class="font-sans text-sm text-gray-400 max-w-xs leading-relaxed md:text-right">
                    Semua harga sudah termasuk akses penuh ke area wisata.
                </p>
            </div>
            <div class="mt-8 h-px bg-gray-100"></div>
        </div>

        @if(isset($tikets) && $tikets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($tikets as $t)
            <div class="group flex flex-col bg-white border border-gray-150 rounded-2xl p-7 hover:border-gray-300 hover:shadow-sm transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="flex items-center justify-between mb-8">
                    <span class="font-sans text-xs text-gray-300 tabular-nums">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="inline-flex items-center gap-1.5 font-sans text-xs text-gray-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Tersedia
                    </span>
                </div>
                <h3 class="font-serif text-2xl text-gray-900 mb-1 leading-snug">{{ $t->nama_tiket }}</h3>
                <p class="font-sans text-sm text-gray-400 mb-8">Per orang · 1 hari kunjungan</p>
                <div class="mt-auto">
                    <div class="flex items-baseline gap-1.5 mb-7">
                        <span class="font-sans text-sm text-gray-400">Rp</span>
                        <span class="font-serif text-4xl text-gray-900 tracking-tight">{{ number_format($t->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-px bg-gray-100 mb-7"></div>
                    <ul class="space-y-2.5 mb-8">
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            Akses masuk area wisata
                        </li>
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            Berlaku 1 hari kunjungan
                        </li>
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            Konfirmasi via email
                        </li>
                    </ul>
                    <div class="flex gap-2.5">
                        <a href="{{ route('tiket.detail', $t->id_tiket) }}"
                           class="flex-1 text-center font-sans text-sm py-3 rounded-xl border border-gray-200 text-gray-500 hover:border-gray-400 hover:text-gray-900 transition-all duration-200">
                            Detail
                        </a>
                        @auth
                        <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $t->id_tiket]) }}"
                           class="flex-1 text-center font-sans text-sm font-medium py-3 rounded-xl bg-gray-900 text-white hover:bg-gray-700 transition-all duration-200">
                            Pesan
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center font-sans text-sm font-medium py-3 rounded-xl bg-gray-900 text-white hover:bg-gray-700 transition-all duration-200">
                            Pesan
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="text-center py-24" data-aos="fade-up">
            <span class="material-symbols-outlined text-5xl text-gray-200 block mb-4">confirmation_number</span>
            <p class="font-serif text-xl text-gray-400 mb-2">Belum ada tiket tersedia</p>
            <p class="font-sans text-sm text-gray-400">Silakan pilih tanggal lain atau hubungi pengelola</p>
        </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  MENGAPA PESAN ONLINE                      --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white border-t border-gray-100">
    <div class="max-w-6xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">Keuntungan</p>
            <h2 class="font-serif text-4xl md:text-5xl text-gray-900">Mengapa Pesan Online?</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="py-10 md:py-0 md:pr-12" data-aos="fade-up" data-aos-delay="0">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">01</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">Proses Cepat & Mudah</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">Pesan tiket hanya dalam beberapa langkah. Tidak perlu antri di loket, tidak perlu datang lebih awal.</p>
            </div>
            <div class="py-10 md:py-0 md:px-12" data-aos="fade-up" data-aos-delay="80">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">02</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">Aman & Terpercaya</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">Konfirmasi langsung ke email. Data dan transaksi Anda terlindungi sepenuhnya.</p>
            </div>
            <div class="py-10 md:py-0 md:pl-12" data-aos="fade-up" data-aos-delay="160">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">03</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">Dukungan Kapan Saja</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">Tim kami siap membantu melalui email. Respons cepat, solusi jelas.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  CARA PESAN                                --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter border-t border-gray-100" style="background:#F8F7F5;">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div data-aos="fade-right">
                <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-5">Cara Pesan</p>
                <h2 class="font-serif text-4xl md:text-5xl text-gray-900 leading-[1.1] mb-14">3 Langkah<br>Mudah Pesan</h2>
                <div class="divide-y divide-gray-200">
                    <div class="flex gap-7 py-9">
                        <span class="font-serif text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">1</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">Pilih Tiket & Tanggal</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">Pilih jenis tiket, masukkan jumlah pengunjung dan tanggal kunjungan yang diinginkan.</p>
                        </div>
                    </div>
                    <div class="flex gap-7 py-9">
                        <span class="font-serif text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">2</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">Lakukan Pembayaran</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">Transfer ke rekening yang tersedia, kemudian upload bukti bayar. Verifikasi berlangsung cepat.</p>
                        </div>
                    </div>
                    <div class="flex gap-7 py-9">
                        <span class="font-serif text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">3</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">Terima E-Tiket</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">E-tiket dikirim ke email setelah pembayaran dikonfirmasi. Tunjukkan ke petugas saat tiba.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:sticky lg:top-28" data-aos="fade-left" data-aos-delay="100">
                <div class="relative rounded-2xl overflow-hidden aspect-[4/5]">
                    <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80"
                         class="w-full h-full object-cover" alt="Bangkiang Jaran" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <p class="font-serif text-white text-xl leading-snug">Nikmati pengalaman<br>yang tak terlupakan.</p>
                        <p class="font-sans text-sm text-white/60 mt-2">Bangkiang Jaran, Gianyar Bali</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  FAQ                                       --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter border-t border-gray-100" style="background:#F8F7F5;" x-data="{open: null}">
    <div class="max-w-3xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">FAQ</p>
            <h2 class="font-serif text-4xl md:text-5xl text-gray-900">Pertanyaan Umum</h2>
        </div>
        @php $faqs = [
            ['q'=>'Apakah tiket dapat dibatalkan?','a'=>'Tiket yang sudah dipesan tidak dapat dibatalkan. Namun tiket dapat dialihkan ke tanggal lain maksimal H-1 kunjungan dengan menghubungi tim kami.'],
            ['q'=>'Berapa lama konfirmasi tiket?','a'=>'Setelah melakukan pembayaran dan upload bukti transfer, konfirmasi tiket akan dikirim ke email Anda dalam waktu 1×24 jam di hari kerja.'],
            ['q'=>'Apakah tiket berlaku untuk anak di bawah 3 tahun?','a'=>'Anak di bawah 3 tahun masuk GRATIS tanpa perlu membeli tiket. Cukup beli tiket untuk pengunjung dewasa yang mendampingi.'],
            ['q'=>'Metode pembayaran apa yang diterima?','a'=>'Kami menerima transfer bank (BCA, BRI, BNI, Mandiri), serta berbagai dompet digital. Detail akan ditampilkan saat proses pemesanan.'],
            ['q'=>'Bagaimana cara menunjukkan tiket di lokasi?','a'=>'Tunjukkan e-tiket di email atau screenshot kepada petugas di pintu masuk. Pastikan kode QR atau nomor tiket terlihat jelas.'],
        ]; @endphp
        <div class="divide-y divide-gray-200" data-aos="fade-up">
            @foreach($faqs as $i => $faq)
            <div>
                <button class="w-full flex items-center justify-between py-6 text-left group"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                    <span class="font-serif text-lg text-gray-900 pr-8 group-hover:text-gray-600 transition-colors">{{ $faq['q'] }}</span>
                    <span class="flex-shrink-0 text-gray-400 transition-transform duration-300" :class="{'rotate-45': open === {{ $i }}}">
                        <span class="material-symbols-outlined text-xl">add</span>
                    </span>
                </button>
                <div x-show="open === {{ $i }}" x-collapse>
                    <p class="font-sans text-sm text-gray-500 leading-relaxed pb-6">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
