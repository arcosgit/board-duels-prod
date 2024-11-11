<?php

namespace App\Jobs\User;

use App\Models\User;
use Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateNameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $userId;
    public $newName;
    public function __construct($userId, $newName)
    {
        $this->userId = $userId;
        $this->newName = $newName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $cachedUser = Cache::get('user:' . $this->userId);

        // if ($cachedUser && $cachedUser->name === $this->newName) {
        //     // Если имя совпадает, обновляем его в базе данных
        //     $user = User::find($this->userId);
        //     if ($user) {
        //         $user->name = $this->newName;
        //         $user->save();
        //     }
        // }
    }
}
