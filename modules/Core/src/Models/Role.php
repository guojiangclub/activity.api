<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-11-30
 * Time: 12:00
 */

namespace GuoJiangClub\Activity\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Contracts\EntrustRoleInterface;
use Zizaco\Entrust\Traits\EntrustRoleTrait;

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
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'roles');

        parent::__construct($attributes);
    }

    protected $guarded = ['id'];

    protected $fillable = ['name', 'display_name', 'description'];

}