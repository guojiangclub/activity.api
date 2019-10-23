<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Member\Backend\Console;

use GuoJiangClub\Activity\Core\Models\Role;
use Illuminate\Console\Command;

class RolesCommand extends Command
{
    protected $signature = 'roles:factory';

    protected $description = 'roles factory.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!Role::where('name', 'coach')->first()) {
            Role::create(['name' => 'coach', 'display_name' => '教练员角色', 'description' => '教练员角色']);
        }
    }
}
