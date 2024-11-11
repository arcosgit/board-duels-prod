<?php

namespace App\Jobs\Game;

use App\Models\GameAccept;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteAcceptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        GameAccept::find($this->id)->delete();
    }
}
