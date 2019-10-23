<?php

namespace GuoJiangClub\Activity\Admin\Http\Controllers;

use App\Http\Controllers\Controller;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use GuoJiangClub\Activity\Core\Models\Role;
use GuoJiangClub\Activity\Core\Models\User;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Role::where("name", "coach")->first();

        if ($coaches AND $coaches->users->count()) {
            foreach ($coaches->users as $coach) {
                $coach->coach_name = $coach->getUserAttr('coach_name');
                $coach->title = $coach->getUserAttr('title');
                $coach->describe = $coach->getUserAttr('describe');
            }
        }

        return Admin::content(function (Content $content) use ($coaches) {
            $content->description('教练管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '教练管理', 'no-pjax' => 1, 'left-menu-active' => '教练管理']
            );

            $view = view("activity::coach.index", compact('coaches'))->render();
            $content->row($view);
        });
    }

    public function show($id)
    {
        $coach = User::find($id);
        $coach->coach_name = $coach->getUserAttr('coach_name');
        $coach->title = $coach->getUserAttr('title');
        $coach->describe = $coach->getUserAttr('describe');

        return Admin::content(function (Content $content) use ($coach) {
            $content->description('教练管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '教练管理', 'no-pjax' => 1, 'left-menu-active' => '教练管理']
            );

            $view = view("activity::coach.edit", compact('coach'))->render();
            $content->row($view);
        });
    }

    public function store($id)
    {
        $coach = User::find($id);
        $title = request('title') ?: '';
        $describe = request('describe') ?: '';
        $coach_name = request('coach_name') ?: '';
        $coach->setUserAttr('coach_name', $coach_name);
        $coach->setUserAttr('title', $title);
        $coach->setUserAttr('describe', $describe);

        return redirect(route('activity.admin.coach'));
    }
}