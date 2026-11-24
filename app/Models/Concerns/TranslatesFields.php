<?php

namespace App\Models\Concerns;

use App\Services\AutoTranslator;

/**
 * Model mendefinisikan `protected array $autoTranslate = ['kolom_id' => 'kolom_en']`.
 * Saat disimpan, sisi yang kosong diisi otomatis dari sisi yang terisi (ID <-> EN).
 * Isian manual tidak pernah ditimpa.
 */
trait TranslatesFields
{
    public function autoTranslateMap(): array
    {
        return $this->autoTranslate ?? [];
    }

    public static function bootTranslatesFields(): void
    {
        static::saving(function ($model) {
            $translator = app(AutoTranslator::class);
            if (! $translator->enabled()) {
                return;
            }
            foreach ($model->autoTranslate ?? [] as $id => $en) {
                $idValue = trim((string) ($model->attributes[$id] ?? ''));
                $enValue = trim((string) ($model->attributes[$en] ?? ''));
                if ($idValue !== '' && $enValue === '') {
                    if ($t = $translator->translate($idValue, 'id', 'en')) {
                        $model->attributes[$en] = $t;
                    }
                } elseif ($enValue !== '' && $idValue === '') {
                    if ($t = $translator->translate($enValue, 'en', 'id')) {
                        $model->attributes[$id] = $t;
                    }
                }
            }
        });
    }
}
