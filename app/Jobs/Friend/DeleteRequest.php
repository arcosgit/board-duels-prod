<?php

namespace App\Jobs\Friend;

use App\Models\WaitFriend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteRequest implements ShouldQueue
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
        WaitFriend::where('id', $this->id)->delete();
    }
}
