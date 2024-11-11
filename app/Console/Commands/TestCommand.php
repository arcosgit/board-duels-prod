<?php

namespace App\Console\Commands;

use App\Models\User;
use Cache;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    //    $res = User::all()->each(function($user){
    //        Cache::put('user:' . $user->name, $user);
    //    });
    // User::with('statistic', 'setting')->get()->each(function($user){
    //     Cache::put('user:' . $user->id, $user);
    // });
    $res = Cache::get('user:9');
    dd($res->name);
       
    }
}
