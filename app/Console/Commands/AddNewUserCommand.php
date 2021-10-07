<?php

namespace App\Console\Commands;

use App\Data\Models\User;
use App\Data\Repositories\UserRepository;
use Illuminate\Console\Command;

class AddNewUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {--N|name=}
                            {--E|email=}
                            {--P|password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(UserRepository $userRepository)
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $userRepository->addNewAdminUser($name, $email, $password);
    }
}
