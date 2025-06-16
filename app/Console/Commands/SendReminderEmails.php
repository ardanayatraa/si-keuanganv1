<?php

namespace App\Console\Commands;

use App\Mail\UtangReminderMail;
use App\Mail\PiutangReminderMail;
use App\Models\Utang;
use App\Models\Piutang;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SendReminderEmails extends Command
{
    protected $signature = 'reminder:send-artisan';
    protected $description = '📬 Kirim email reminder 3 hari sebelum jatuh tempo utang & piutang (cache-based)';

    public function handle()
    {
        $this->newLine();
        $this->info('==========================================');
        $this->info('    📆  REMINDER EMAIL SENDER v2.0');
        $this->info('==========================================');
        $this->newLine();

        $targetDate = Carbon::today()->addDays(3)->toDateString();
        $this->line("🔍 Mencari utang & piutang jatuh tempo pada: {$targetDate}");
        $this->newLine();

        $rows = [];

        // UTANG
        Utang::with('pengguna')
            ->whereDate('tanggal_jatuh_tempo', $targetDate)
            ->get()
            ->each(function($u) use (&$rows) {
                $cacheKey = "reminder:utang:{$u->id_utang}";
                // kalau belum dikirim
                if (! Cache::has($cacheKey)) {
                    Mail::to($u->pengguna->email)
                        ->queue(new UtangReminderMail($u));
                    $rows[] = [
                        'Tipe'     => 'Utang',
                        'User'     => $u->pengguna->username,
                        'Email'    => $u->pengguna->email,
                        'Nominal'  => 'Rp '.number_format($u->jumlah,0,',','.'),
                        'Due Date' => $u->tanggal_jatuh_tempo->format('d-m-Y'),
                    ];
                    // tandai sudah dikirim, simpan hingga besok lewat targetDate
                    // TTL kita set sampai tanggal jatuh tempo +1 hari
                    $expiresAt = $u->tanggal_jatuh_tempo->addDay()->endOfDay();
                    Cache::put($cacheKey, true, $expiresAt);
                }
            });

        // PIUTANG
        Piutang::with('pengguna')
            ->whereDate('tanggal_jatuh_tempo', $targetDate)
            ->get()
            ->each(function($p) use (&$rows) {
                $cacheKey = "reminder:piutang:{$p->id_piutang}";
                if (! Cache::has($cacheKey)) {
                    Mail::to($p->pengguna->email)
                        ->queue(new PiutangReminderMail($p));
                    $rows[] = [
                        'Tipe'     => 'Piutang',
                        'User'     => $p->pengguna->username,
                        'Email'    => $p->pengguna->email,
                        'Nominal'  => 'Rp '.number_format($p->jumlah,0,',','.'),
                        'Due Date' => $p->tanggal_jatuh_tempo->format('d-m-Y'),
                    ];
                    $expiresAt = $p->tanggal_jatuh_tempo->addDay()->endOfDay();
                    Cache::put($cacheKey, true, $expiresAt);
                }
            });

        if (count($rows)) {
            $this->info("\n📊 Ringkasan Reminder:");
            $this->table(
                ['Tipe', 'User', 'Email', 'Nominal', 'Due Date'],
                $rows
            );
            $this->info("✅ Total queued: " . count($rows) . "\n");
        } else {
            $this->warn("⚠️  Tidak ada utang atau piutang baru untuk di-remind.\n");
        }

        $this->info('==========================================');
        $this->info('    🚀 Selesai enqueue reminder.');
        $this->info('==========================================');

        return Command::SUCCESS;
    }
}
