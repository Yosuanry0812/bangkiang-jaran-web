<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GaleriUploadTest extends TestCase
{
    use RefreshDatabase;

    private function pengelola(): User
    {
        return User::factory()->create(['role' => 'pengelola']);
    }

    public function test_banyak_foto_bisa_diunggah_sekaligus(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->actingAs($this->pengelola())->post(route('pengelola.galeri.store'), [
            'tipe' => 'restoran',
            'file' => [UploadedFile::fake()->image('a.jpg', 40, 40), UploadedFile::fake()->image('b.png', 40, 40)],
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengelola.galeri.index'));

        $this->assertSame(2, Galeri::where('tipe', 'restoran')->count());
    }

    public function test_satu_foto_tetap_bisa_diunggah(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->actingAs($this->pengelola())->post(route('pengelola.galeri.store'), [
            'tipe' => 'wisata',
            'file' => [UploadedFile::fake()->image('a.jpg', 40, 40)],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, Galeri::wisata()->count());
    }

    public function test_unggah_lewat_ajax_mengembalikan_json(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->actingAs($this->pengelola())->postJson(route('pengelola.galeri.store'), [
            'tipe' => 'wisata',
            'file' => [UploadedFile::fake()->image('a.jpg', 40, 40)],
        ])->assertOk()->assertJson(['ok' => true, 'count' => 1]);
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        $this->actingAs($this->pengelola())->post(route('pengelola.galeri.store'), [
            'tipe' => 'wisata',
            'file' => [UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors('file.0');

        $this->assertSame(0, Galeri::wisata()->count());
    }
}
