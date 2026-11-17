<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemesanan';

    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'id_tiket',
        'tgl_kunjungan',
        'jumlah',
        'total_harga',
        'detail',
        'status',
        'kode_booking',
        'expires_at', 'closed_reason', 'draft_token',
    ];

    protected $casts = [
        'detail' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan');
    }

    public function isApproved(): bool
    {
        return $this->status === 'selesai' && $this->pembayaran?->status === 'valid';
    }

    public function statusLabel(): string
    {
        if ($this->closed_reason === 'expired') {
            return __('operations.expired');
        }
        if ($this->status === 'dibatalkan') {
            return __('messages.cancelled');
        }
        if ($this->isApproved()) {
            return __('messages.completed');
        }
        if ($this->pembayaran?->status === 'ditolak') {
            return __('operations.event_payment_rejected');
        }

        return $this->pembayaran?->status === 'pending' ? __('messages.waiting_verification') : __('messages.pending');
    }

    public function statusToken(): string
    {
        return $this->status.'|'.($this->pembayaran?->status ?? 'none');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'selesai')->whereHas('pembayaran', fn ($q) => $q->where('status', 'valid'));
    }

    public function scopeHoldingCapacity($query)
    {
        return $query->where(function ($query) {
            $query->where(fn ($q) => $q->approved())->orWhere(function ($q) {
                $q->where('status', 'pending')->where(function ($hold) {
                    $hold->where('expires_at', '>', now())->orWhereNull('expires_at')
                        ->orWhereHas('pembayaran', fn ($payment) => $payment->where('status', 'pending'));
                });
            });
        });
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && ($this->tgl_kunjungan < now()->toDateString()
            || ($this->expires_at && $this->expires_at->lte(now())));
    }

    public function canCancel(): bool
    {
        return $this->status === 'pending' && ! in_array($this->pembayaran?->status, ['pending', 'valid'], true);
    }

    public function events()
    {
        return $this->hasMany(BookingEvent::class, 'id_pemesanan')->orderBy('created_at')->orderBy('id');
    }

    public function recordEvent(string $event, ?string $description = null): void
    {
        $this->events()->create(['actor_id' => auth()->id(), 'event' => $event,
            'description' => $description, 'created_at' => now()]);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['q'] ?? null, function ($q, $term) {
            $q->where(function ($search) use ($term) {
                $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);
                $search->where('kode_booking', 'like', '%'.$term.'%')
                    ->orWhereHas('detailPemesanan', fn ($d) => $d->where('nama_pengunjung', 'like', '%'.$term.'%'));
            });
        })->when($filters['status'] ?? null, function ($q, $status) {
            if ($status === 'expired') {
                $q->where('closed_reason', 'expired');
            } else {
                $q->where('status', $status);
            }
        })->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('tgl_kunjungan', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('tgl_kunjungan', '<=', $date))
            ->when($filters['channel'] ?? null, fn ($q, $channel) => $q->where('kode_booking', $channel === 'offline' ? 'like' : 'not like', 'BJ-OFF-%'));
    }

    public function detailPemesanan()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }
}
