<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contacts students who\'s absence has exceeded the set threshold in weeks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clients = Client::with([
            'settings' => function($query) {
                $query->where('belt_id', '<', 5);
                },
            'users' => function($query) {
                $query->with([
                    'rank' => function($q) {
                        $q->where('belt_id', '<', 5);
                    },
                    'lastcheckin',
                ])
                ->where(['active' => 1]);
            }])
            ->get();
        foreach ($clients as $client) {

            Log::info("Processing absentees for $client->name");
            $this->info("Processing absentees for $client->name");

            $settings = $client->settings;
            $absenceThresholds = $settings->mapWithKeys(function ($setting) {
                return [$setting->belt_id => $setting->weeks_absent_til_contact];
            });

            $owner = $client->firstAdmin;

            foreach ($client->users as $user) {

                if ( ! $user->rank || ! $user->lastcheckin) {
                    continue;
                }

                $lastCheckin = new \DateTime($user->lastcheckin->checked_in_at);
                $now = new \DateTime();
                $timeDiff = $lastCheckin->diff($now);
                $weeksSinceLastCheckin = floor($timeDiff->days / 7);
                $weeksThreshold = $absenceThresholds->get($user->rank->belt_id);

                if ($weeksSinceLastCheckin > $weeksThreshold) {

                    try {
                        Log::info("Emailing $user->name");
                        $this->info("Emailing $user->name");
                        $to = config('app.env') == 'development' ? config('mail.from.address') : $user;
                        Mail::to($to)
                            ->bcc(config('mail.from.address'))
                            ->send(new \App\Mail\ContactAbsent(
                                (array) $owner,
                                $user
                            ));
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        $this->error($e->getMessage());
                    }
                }
            }
        }
        return 0;
    }
}
