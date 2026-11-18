<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OperationalReadiness
{
    public function inspect(bool $smtp = false): array
    {
        $missing = [];
        $database = ['connected' => false];
        $legacyProofs = null;
        try {
            $db = DB::connection();
            $db->getPdo();
            $database = ['connected' => true, 'driver' => $db->getDriverName(), 'name' => $db->getDatabaseName()];
            $schema = $db->getSchemaBuilder();
            foreach ([
                'users' => ['deleted_at'],
                'pemesanan' => ['expires_at', 'closed_reason', 'draft_token'],
                'pembayaran' => ['proof_disk', 'rejection_reason', 'verified_at'],
                'booking_events' => ['actor_id', 'event', 'created_at'],
                'site_settings' => ['key', 'value'],
                'jobs' => ['queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'],
                'failed_jobs' => ['uuid', 'connection', 'queue', 'payload', 'exception'],
            ] as $table => $columns) {
                if (! $schema->hasTable($table)) {
                    $missing[] = $table;
                    continue;
                }
                $present = $schema->getColumnListing($table);
                foreach (array_diff($columns, $present) as $column) {
                    $missing[] = $table.'.'.$column;
                }
            }
            if ($schema->hasTable('pembayaran')) {
                $proofs = $db->table('pembayaran')->whereNotNull('bukti_bayar');
                if ($schema->hasColumn('pembayaran', 'proof_disk')) {
                    $proofs->where('proof_disk', 'public');
                }
                $legacyProofs = $proofs->count();
            }
        } catch (Throwable) {
            // Do not expose DSNs, usernames, exception messages or credentials.
            $missing[] = 'database_unavailable';
        }
        $private = config('filesystems.disks.private.root');
        $storageReady = is_dir($private) && is_writable($private);
        $queueReady = config('queue.transactional_connection') === 'database';
        $result = [
            'ready' => $database['connected'] && $missing === [] && $storageReady && $queueReady,
            'database' => $database, 'missing_schema' => $missing,
            'private_storage_writable' => $storageReady,
            'transactional_queue_database' => $queueReady,
            'legacy_public_proofs' => $legacyProofs,
        ];
        if ($smtp) {
            $result['smtp'] = $this->checkSmtp();
        }

        return $result;
    }

    private function checkSmtp(): array
    {
        if (config('mail.default') !== 'smtp') {
            return ['authenticated' => false, 'status' => 'smtp_not_selected', 'message_sent' => false];
        }
        if (! config('mail.mailers.smtp.username') || ! config('mail.mailers.smtp.password')) {
            return ['authenticated' => false, 'status' => 'credentials_missing', 'message_sent' => false];
        }
        $transport = null;
        try {
            $transport = Mail::mailer('smtp')->getSymfonyTransport();
            $transport->start();

            return ['authenticated' => true, 'status' => 'connected_and_authenticated', 'message_sent' => false];
        } catch (Throwable) {
            return ['authenticated' => false, 'status' => 'connection_or_authentication_failed', 'message_sent' => false];
        } finally {
            try {
                $transport?->stop();
            } catch (Throwable) {
                // Connection check only; never send an email or log provider errors.
            }
        }
    }
}
