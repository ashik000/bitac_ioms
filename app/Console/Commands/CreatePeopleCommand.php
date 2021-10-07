<?php

namespace App\Console\Commands;

use App\Data\Models\Operator;
use Illuminate\Console\Command;

class CreatePeopleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:create-people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new employees';

    protected $codes = [
        "15024",
        "15530",
        "17326",
        "17739",
        "21821",
        "34562",
        "40687",
        "41145",
        "41273",
        "42419",
        "42439",
        "42720",
        "42721",
        "42745",
        "43063",
        "43080",
        "43132",
        "43142",
        "43244",
        "D-16368",
        "D-17168",
        "D-17938",
        "D-18929",
        "D-29534",
        "D-29541",
        "D-29660",
        "D-29771",
        "D-29776",
        "D-29802",
        "D-29818",
        "D-29882",
        "D-29956",
        "D-29959",
        "D-29971",
        "D-29976",
        "D-29985",
        "D-29992",
        "D-29994",
        "D-30104",
        "D-31627",
        "D-888"
    ];

    protected $names = [
        "Md. Sabuj Miah",
        "Md. Ariful Islam",
        "Md. Ab. Alim",
        "Sanour Hossen",
        "Md Mostafa Kamal",
        "Birball Chandra Roy",
        "Md. Biddut Hosen",
        "Md. Sabbir Hossen",
        "Md. Shariot Ullah",
        "Abdus Shobhan Mithu",
        "Khirul Islam" ,
        "Md. Rakib Hasan" ,
        "Md. Rubel Hossain",
        "Md. Hridoy",
        "Md. Ripon Sarker",
        "Mohammad Aminur Hossen" ,
        "Abdul Salam",
        "Md. Sumon Miah",
        "Md. Aminur Islam",
        "Abdur Rahim",
        "Sujan Mia",
        "Md. Nazrul Islam",
        "Md. Saiful Islam",
        "Md. Sonaullah",
        "Habibur Rahman",
        "Salauddin Bablu",
        "Md. Rakib Hasan" ,
        "Sagor",
        "Harun Khan",
        "Foysal Ahammed",
        "Aminur" ,
        "Shaikhul Hasan",
        "Mahmudul Hasan",
        "Md. Jamirul Islam",
        "Md. Shahin. Miah",
        "Ashik" ,
        'Md. Jahid Hossain',
        "Md. Nasir Hossain",
        "Ashikur Rahman" ,
        "Md. Jahanger Hossain",
        "Md. Fozlul Haque"
    ];

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

        for($i = 0; $i < count($this->codes); $i++) {
            $operator = new Operator();
            $operator->code = $this->codes[$i];
            $operator->first_name = $this->names[$i];
            $operator->last_name = '';
            $operator->created_at = now();
            $operator->updated_at = now();
            $operator->save();
        }
        return 0;
    }
}
