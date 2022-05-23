<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Repositories\User\UserInterface;

class UserSeeder extends Seeder
{
    protected $userModel;

    public function __construct(UserInterface $user)
    {
      $this->userModel = $user;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = $this->userModel->accountStatus()->keyBy('name');

        $users = [
            [
                'fullname' => "director",
                'email' => "director@gmail.com",
                'password' => ("director"),
                'mobileno' => '0123456789',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'status_id' => $statuses['active']['id']
            ]
        ];

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
