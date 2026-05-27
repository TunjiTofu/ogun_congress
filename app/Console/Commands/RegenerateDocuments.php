<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegenerateDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate ID cards and consent forms for all campers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ids = \App\Models\Camper::pluck('id');
        $ids->each(fn ($id) => \App\Jobs\GenerateCamperDocumentsJob::dispatch($id)->onQueue('documents'));
        $this->info("Queued {$ids->count()} campers for document regeneration.");
    }
}
