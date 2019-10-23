<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Member\Backend\Http\Controllers;

use Carbon\Carbon;

use GuoJiangClub\Activity\Core\Models\Role;
use GuoJiangClub\Member\Backend\Models\User;
use GuoJiangClub\Member\Backend\Repository\UserRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Excel;
use iBrand\Backend\Http\Controllers\Controller;
use iBrand\Component\Point\Models\Point;
use iBrand\Component\Point\Repository\PointRepository;
use Illuminate\Http\Request;
use Response;
use Validator;

class UserController extends Controller
{
    protected $userRepository;

    protected $roleRepository;

    protected $permissions;

    protected $addressRepository;

    protected $integralRepository;

    protected $couponHistoryRepository;

    protected $userGroupRepository;

    protected $orderLogRepository;

    protected $pointRepository;

    protected $cache;

    public function __construct(UserRepository $userRepository,
                                PointRepository $pointRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->pointRepository = $pointRepository;
        $this->cache = cache();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index()
    {
        $users = $this->UsersSearch(['status' => 1]);

        return LaravelAdmin::content(function (Content $content) use ($users) {
            $content->header('会员管理');

            $content->breadcrumb(
                ['text' => '会员管理', 'url' => 'member/users', 'no-pjax' => 1, 'left-menu-active' => '会员管理']
            );

            $content->body(view('member-backend::auth.index', compact('users')));
        });
    }

    /**
     * @param CreateUserRequest $request
     *
     * @return mixed
     */
    public function create()
    {
        return LaravelAdmin::content(function (Content $content) {
            $content->header('创建会员');

            $content->breadcrumb(
                ['text' => '会员管理', 'url' => 'member/users', 'no-pjax' => 1],
                ['text' => '创建会员', 'url' => 'member/users/create', 'no-pjax' => 1, 'left-menu-active' => '会员管理']
            );

            $content->body(view('member-backend::auth.create'));
        });
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        //验证
        $rules = [
            'mobile' => 'unique:' . config('ibrand.app.database.prefix', 'ibrand_') . 'user' . ',mobile'
        ];
        $message = [
            'required' => ':attribute 不能为空',
            'unique' => ':attribute 已经存在',
            'email' => ':attribute 格式不正确'
        ];

        $attributes = [
            'name' => '会员名',
            'email' => 'Email',
            'mobile' => '手机号码',
            'group_id' => '用户等级分组',
        ];

        $validator = Validator::make(
            $request->all(),
            $rules,
            $message,
            $attributes
        );
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $warning = $warnings->first();

            flash($warning, 'danger');

            return redirect()->back()->withInput();
        }

        $input = $request->except('assignees_roles', 'permission_user', '_token');

        if (isset($input['email']) && !empty($input['email'])) {
            $data['email'] = $input['email'];
        }

        if (isset($input['nick_name']) && !empty($input['nick_name'])) {
            $data['nick_name'] = $input['nick_name'];
        }

        if (isset($input['password']) && !empty($input['password'])) {
            $data['password'] = $input['password'];
        }

        $data['mobile'] = $input['mobile'];
        $data['status'] = isset($input['status']) ? 1 : 2;
        User::create($data);

        flash('用户创建成功', 'success');

        return redirect()->route('admin.users.index');
    }

    /**
     * @param $id
     * @param EditUserRequest $request
     *
     * @return mixed
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        if (isset($user->bind)) {
            $user->open_id = $user->bind->open_id;
        }

        $points = $user->points()->paginate(20);

        $roles = Role::get();
        $redirect_url = request('redirect_url');

        return LaravelAdmin::content(function (Content $content) use ($roles, $user, $points, $redirect_url) {
            $content->header('编辑会员');

            $content->breadcrumb(
                ['text' => '会员管理', 'url' => 'member/users', 'no-pjax' => 1],
                ['text' => '编辑会员', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '会员管理']
            );

            $content->body(view('member-backend::auth.edit', compact('roles', 'balance', 'groups', 'user', 'points', 'redirect_url'))
            );
        });
    }

    public function getUserPointData($uid)
    {
        $where['user_id'] = $uid;

        $pointData = $this->pointRepository->getPointsByConditions($where, 15);

        return $this->ajaxJson(true, $pointData);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return mixed
     */
    public function update($id, Request $request)
    {
        //验证
        $rules = [
            'email' => "unique:".config('ibrand.app.database.prefix', 'ibrand_') . 'user'.",email,$id|email",
            'mobile' => "unique:".config('ibrand.app.database.prefix', 'ibrand_') . 'user'.",mobile,$id",
        ];
        $message = [
            'required' => ':attribute 不能为空',
            'unique' => ':attribute 已经存在',
            'email' => ':attribute 格式不正确',
        ];

        $attributes = [
            'email' => 'Email',
            'mobile' => '手机号码',
        ];

        $validator = Validator::make(
            $request->all(),
            $rules,
            $message,
            $attributes
        );
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $warning = $warnings->first();

            return $this->ajaxJson(false, [], 404, $warning);
        }

        $user = User::find($id);
        $input = $request->except('assignees_roles', 'permissions');
        $input['email'] = trim($input['email']);

        $input = array_filter($input);

        if (!empty($input['email']) and $user->email !== $input['email']) {
            if (User::where('email', '=', $input['email'])->first()) {
                return $this->ajaxJson(false, [], 404, '系统已经存在此邮箱');
            }
        }

        $input['email'] = empty($input['email']) ? null : $input['email'];

        $this->userRepository->update($input, $id);

        if (request()->has('permissions')) {
            $selectRoles = request()->permissions;
        } else {
            $selectRoles = [];
        }
        $roles = Role::pluck('id')->toArray();
        if (!empty($roles)) {
            $user->detachRoles($roles);
        }
        if (!empty($selectRoles)) {
            $user->attachRoles($selectRoles);
        }

        return $this->ajaxJson(true, [], 200, '更新成功');
    }


    /**
     * @param $id
     * @param $status
     * @param MarkUserRequest $request
     *
     * @return mixed
     */
    public function mark($id, $status)
    {
        $this->userRepository->mark($id, $status);
        flash('用户更新成功', 'success');

        return redirect()->back();
    }


    /**
     * @return mixed
     */
    public function banned()
    {
        $users = $this->UsersSearch(['status' => 2]);

        return LaravelAdmin::content(function (Content $content) use ($users) {
            $content->header('已禁用会员');

            $content->breadcrumb(
                ['text' => '会员管理', 'url' => 'member/users', 'no-pjax' => 1],
                ['text' => '已禁用会员', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '会员管理']
            );

            $content->body(view('member-backend::auth.banned', compact('users')));
        });
    }


    /**用户搜索
     * @param array $where
     * @param bool $delete
     * @return mixed
     */

    public function UsersSearch($where = [], $delete = false)
    {
        if (!empty(request('name'))) {
            $where['name'] = ['like', '%' . request('name') . '%'];
        }

        if (!empty(request('email'))) {
            $where['email'] = ['like', '%' . request('email') . '%'];
        }

        if (!empty(request('mobile'))) {
            $where['mobile'] = ['like', '%' . request('mobile') . '%'];
        }

        if (true == $delete) {
            return $this->userRepository->getDeletedUsersPaginated($where);
        }

        return $this->userRepository->searchUserPaginated($where);
    }

    public function addPoint()
    {
        $id = request('user_id');
        $data = [
            'user_id' => $id,
            'action' => 'admin_action',
            'note' => request('note'),
            'value' => request('value'),
            'status' => 1];
        if (request('value') < 0) {
            $data['valid_time'] = 0;
        }
        Point::create($data);
        return $this->ajaxJson();
    }

}
