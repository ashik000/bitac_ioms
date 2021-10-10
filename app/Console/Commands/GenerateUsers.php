<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'users:generate
                            {--N|name=}
                            {--E|email=}
                            {--P|password=}';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Generate users';

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
     * @return mixed
     */
    public function handle()
    {
        $name     = $this->option('name');
        $email    = $this->option('email');
        $password = $this->option('password');

        $user = app('App\Repositories\UserRepository')->createUser($name, $email, $password);
    }
}
