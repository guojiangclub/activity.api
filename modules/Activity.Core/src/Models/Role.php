<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Models;

use GuoJiangClub\Activity\Core\Models\Traits\EntrustRoleTrait;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Contracts\EntrustRoleInterface;

class Role extends Model implements EntrustRoleInterface
{
    use EntrustRoleTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'roles');

        parent::__construct($attributes);
    }

    protected $guarded = ['id'];

    protected $fillable = ['name', 'display_name', 'description'];
}
