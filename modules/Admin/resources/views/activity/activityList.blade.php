{!! Html::style('assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<style>
    .list-inline li {
        padding: 8px;
        cursor: pointer;
    }

    .list-inline li:first-child {
        padding: 10px;
        font-size: 15px;
        font-weight: bold;
    }

    .vim-thumbnail {
        margin: 2px 5px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .img {
        width: 188px;
        height: 170px;
        margin-right: 23px;
    }

    .vim-activity-address-time {
        color: #7d8084;
        font-size: 15px;
    }

    .vim-activity-status-operation {
        position: relative;
        height: 117px;
    }

    .vim-activity-status-operation .vim-activity-status {
        float: right;
    }

    .vim-activity-operation {
        position: absolute;
        bottom: 0px;
        right: 0px;
    }

    .vim-activity-operation {
        cursor: pointer;
    }
</style>

<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="row">
            <div class="col col-md-9 col-xs-12 col-ms-12">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <h3 class="box-title">活动列表</h3>
                        </div>
                    </div>
                </div>
                <hr/>
                <div>
                    {!! Form::open( [ 'url' => [route('activity.admin.index')], 'method' => 'GET'] ) !!}
                    <div>
                        <ul class="vim-activity-status list-inline">
                            <li>报名状态:</li>
                            <li @if(request('status') == -1 ) class="btn btn-primary" @endif value="-1">全部</li>
                            <li value="1" @if(request('status') == 1 ) class="btn btn-primary" @endif>报名中</li>
                            <li value="2" @if(request('status') == 2 ) class="btn btn-primary" @endif>进行中</li>
                            <li value="3" @if(request('status') == 3 ) class="btn btn-primary" @endif>已结束</li>
                            <li value="4" @if(request('status') == 4 ) class="btn btn-primary" @endif>截止报名</li>
                        </ul>
                        <ul class="vim-activity-publish-time list-inline">
                            <li>活动时间:</li>
                            <li @if(request('time') == -1 ) class="btn btn-primary" @endif value="-1">全部</li>
                            <li @if(request('time') == 7 ) class="btn btn-primary" @endif value=7>一周内</li>
                            <li @if(request('time') == 30 ) class="btn btn-primary" @endif value=30>一个月内</li>
                            <li @if(request('time') == 90 ) class="btn btn-primary" @endif value=90>三个月内</li>
                            <li>
                                <div class="date form_datetime">
                                    <span class="add-on"><i class="fa fa-calendar"></i></span>
                                    <input name="starts_at" value="{{request('starts_at')}}" type="text" placeholder="手动选择开始时间" readonly/>
                                </div>
                            </li>
                            <li>
                                <div class="date form_datetime">
                                    <span class="add-on"><i class="fa fa-calendar"></i></span>
                                    <input name="ends_at" value="{{request('ends_at')}}" type="text" placeholder="手动选择结束时间" readonly/>
                                </div>
                            </li>

                            <li><input class="form-control" type="text" name="id" value="" placeholder="请输入活动ID">
                            </li>

                            <li>
                                <input type="hidden" id="activity-status" name="status" value="-1"/>
                                <input type="hidden" id="activity-time" name="time" value="-1">
                                <input type="submit" class="btn btn-primary" value="搜索"/>
                                <input type="button" class="btn btn-primary" value="导出" id="excel"/>
                            </li>
                        </ul>

                    </div>
                    {!! Form::close() !!}
                    <hr/>
                    <div class="box-body">
                        @foreach($activities as $activity)
                            <div class="thumbnail vim-thumbnail row vim-anchor-for-delete">
                                <div class="col col-md-8">
                                    <div class="vim-activity-poster pull-left">
                                        <img src={{empty($activity->img_list) ? "/assets/backend/activity/backgroundImage/pictureBackground.png" : $activity->img_list }} alt=""
                                             class="img">
                                    </div>
                                    <div class="vim-activity-content">
                                        <h3 class="vim-activity-title">{{$activity->title}}</h3>
                                        <button class="btn btn-outline btn-primary btn-xs">通用类</button>
                                        &nbsp;&nbsp;活动ID:{{$activity->id}}
                                        <div class="vim-activity-address-time">
                                            <span>{{$activity->created_at}} 发布</span>
                                            &nbsp;&nbsp;&nbsp;&nbsp;<span>报名人数: <i
                                                        style="color: #008cee">{{empty( $activity->member_count) ? 0 : $activity->member_count}}</i> 人</span>
                                            <p>活动时间：{{$activity->starts_at}} 至 <br>{{$activity->ends_at}}</p>
                                            <p>{{$activity->city()->first()->name}} {{$activity->address}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-md-4">
                                    <div class="vim-activity-status-operation">
                                        @if($activity->status == 0)
                                            <div class="vim-activity-status text-warning">
                                                审核中
                                            </div>
                                        @elseif($activity->status == 1)
                                            <div class="vim-activity-status text-success">
                                                报名中
                                            </div>
                                        @elseif($activity->status == 2)
                                            <div class="vim-activity-status text-info">
                                                进行中
                                            </div>
                                        @elseif($activity->status == 3)
                                            <div class="vim-activity-status text-danger">
                                                已结束
                                            </div>
                                        @elseif($activity->status == 4)
                                            <div class="vim-activity-status text-danger">
                                                截止报名
                                            </div>
                                        @endif
                                        <div class="vim-activity-operation">
                                            @if($activity->status == 0)
                                                <i class="fa fa-bullhorn activity-update-status"
                                                   data-url="{{route("activity.admin.status.update", $activity->id)}}">
                                                    发布</i> |
                                            @endif
                                            <a href="{{route('activity.admin.rewards', $activity->id)}}"
                                               style="color: #676a6c"><i class="fa fa-trophy"> 奖励审核</i></a> |
                                            <a href="{{route('activity.admin.edit', $activity->id)}}"
                                               style="color: #676a6c" no-pjax><i class="fa fa-pencil-square-o"> 修改</i></a> |
                                            <i class="fa fa-trash activity-delete"
                                               data-url="{{route("activity.admin.delete", $activity->id)}}"> 删除</i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                        {!! $activities->appends(request()->except('page'))->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
<script>
    $(function () {

        $('.vim-activity-status >li:gt(0)').on('click', function () {
            var thisPoint = $(this);
            $("#activity-status").val(thisPoint.val());
            thisPoint.siblings().removeClass();
            thisPoint.addClass("btn btn-primary");
        });

        $('.vim-activity-publish-time >li:gt(0):lt(6)').on('click', function () {
            var thisPoint = $(this);
            thisPoint.siblings().removeClass();
            $("#activity-time").val(-1);
            if ($('.vim-activity-publish-time >li:gt(0):lt(6)').index(thisPoint) < 4) {
                thisPoint.addClass("btn btn-primary");
                $('.vim-activity-publish-time .form_datetime input').val('');
                $("#activity-time").val(thisPoint.val());
            }
        });

        $('.activity-delete').on('click', function () {
            var thisPoint = $(this);
            var url = thisPoint.data('url');
            swal({
                        title: "确认删除此项？",
                        imageUrl: "/assets/backend/activity/backgroundImage/delete-xxl.png",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确认",
                        cancelButtonText: "取消",
                        closeOnConfirm: false,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "DELETE",
                                url: url,
                                success: function (data) {
                                    thisPoint.closest('.vim-anchor-for-delete').remove();
                                    swal({
                                        title: "删除成功！",
                                        timer: 600,
                                        showConfirmButton: true
                                    });
                                }
                            });
                        } else {
                        }
                    });
        });

        $('.activity-update-status').on('click', function () {
            var thisPoint = $(this);
            var url = thisPoint.data('url');
            swal({
                        title: "确认发布此活动？",
                        text: "发布后活动进入报名状态",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确认",
                        cancelButtonText: "取消",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "PATCH",
                                url: url,
                                success: function (data) {
                                    swal({
                                        title: "发布成功",
                                        timer: 1000,
                                        imageUrl: "/assets/backend/activity/backgroundImage/thumbs-up.jpg",
                                        showConfirmButton: true
                                    });
                                    window.location = "{{ route('activity.admin.index') }}";
                                }
                            });
                        }
                    });
        });

        $('#excel').on('click', function () {
            var url = '{{url()->current()}}';
            if (window.location.search == '') {
                url = url + '?' + 'excel=1';
            } else {
                url = url + window.location.search + '&' + 'excel=1';
            }
            window.location.href = url;
        });
    });

    $('.form_datetime').datetimepicker({
	    minView: 0,
	    format: "yyyy-mm-dd hh:ii:ss",
	    autoclose: 1,
	    language: 'zh-CN',
	    weekStart: 1,
	    todayBtn: 1,
	    todayHighlight: 1,
	    startView: 2,
	    forceParse: 0,
	    showMeridian: true,
	    minuteStep: 1,
	    maxView: 4
    });
</script>