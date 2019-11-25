@extends('backend::layouts.default')

@section('sidebar-menu')
    <li class="{{ Active::pattern('admin/activity/activity-*') }}">
        <a href="#">
            <i class="iconfont icon-huodongneirongguanli"></i>
            <span class="nav-label">活动内容管理</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level">
            <li class="{{ Active::pattern('admin/activity/activity-city') }}">
                <a href="{{route('activity.admin.city')}}">城市列表</a></li>
            <li class="{{ Active::pattern('admin/activity/activity-list') }}">
                <a href="{{route('activity.admin.index')}}">活动列表</a></li>
            <li class="{{ Active::pattern('admin/activity/activity-create') }}">
                <a href="{{route('activity.admin.create')}}">活动发布</a></li>
            <li class="{{ Active::pattern(['admin/activity/activity-form', 'admin/activity/activity-form-curd*']) }}">
                <a href="{{route('activity.admin.form')}}">活动报名表单</a></li>
            <li class="{{ Active::pattern('admin/activity/activity-category') }}">
                <a href="{{route('activity.admin.category')}}">活动分类</a></li>
            <li class="{{ Active::pattern(['admin/activity/activity-statement', 'admin/activity/activity-statement-curd*']) }}">
                <a href="{{route('activity.admin.statement')}}">免责声明</a></li>
            <li class="{{ Active::pattern('admin/activity/activity-coach') }}">
                <a href="{{route('activity.admin.coach')}}">教练管理</a></li>
        </ul>
    </li>

    <li class="{{ Active::pattern(['admin/activity/activityOrder-*', 'admin/activity/refund*']) }}">
        <a href="#">
            <i class="iconfont icon-huodongdingdanguanli"></i>
            <span class="nav-label">活动订单管理</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Active::pattern('admin/activity/activityOrder-list') }}">
                <a href="{{route('activityOrder.admin.index')}}">订单列表</a></li>
            <li class="{{ Active::pattern('admin/activity/refund*') }}">
                <a href="{{route('admin.activity.refund')}}">退款管理</a></li>
        </ul>
    </li>

    <li class="{{ Active::pattern('admin/activity/discount*') }}">
        <a href="#">
            <i class="iconfont icon-huodongyingxiaoguanli"></i>
            <span class="nav-label">活动营销管理</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Active::pattern('admin/activity/discount') }}">
                <a href="{{route('activity.admin.discount.index')}}">优惠活动列表</a></li>

            <li class="{{ Active::pattern('admin/activity/discount/create') }}">
                <a href="{{route('activity.admin.discount.create')}}">添加优惠活动</a></li>
        </ul>
    </li>
@endsection


