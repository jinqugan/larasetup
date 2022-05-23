<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            /**
             * Account status
             */
            [
                'name' => 'pending',
                'lang_code' => 'status.account_pending',
                'type' => 'account',
                'access' => true,
                'description' => 'account valid and unverified. Account unverified able to login'
            ],
            [
                'name' => 'active',
                'lang_code' => 'status.account_active',
                'type' => 'account',
                'access' => true,
                'description' => 'account valid and verified. Account verified able to login'
            ],
            [
                'name' => 'inactive',
                'lang_code' => 'status.account_inactive',
                'type' => 'account',
                'access' => true,
                'description' => 'account valid but inactive for a period of time. Account inactive able to login'
            ],
            [
                'name' => 'disabled',
                'lang_code' => 'status.account_disabled',
                'type' => 'account',
                'access' => false,
                'description' => 'account invalid and disabled by admin. Account disabled unable to login'
            ],
            [
                'name' => 'suspended',
                'lang_code' => 'status.account_suspended',
                'type' => 'account',
                'access' => false,
                'description' => 'account invalid and suspended by admin/system. Account suspended unable to login'
            ],
            [
                'name' => 'deleted',
                'lang_code' => 'status.account_deleted',
                'type' => 'account',
                'access' => false,
                'description' => 'account invalid and not exist in our system. Account deleted unable to login'
            ]
        ];

        Status::insert($statuses);
    }
}
