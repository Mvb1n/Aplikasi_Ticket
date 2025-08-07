use Illuminate\Console\Scheduling\Schedule; // Pastikan ini ada

protected function schedule(Schedule $schedule): void
{
    // $schedule->command('inspire')->hourly();

    // TAMBAHKAN BARIS INI
    $schedule->command('app:check-sla-breaches')->everyFiveMinutes();
}
