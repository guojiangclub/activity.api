<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div>
            <h3 class="box-title">教练列表</h3>
        </div><!-- /.box-header -->
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            @if($coaches && is_array($coaches))
                <table class="table table-hover table-striped">
                    <tbody>
                    <!--tr-th start-->
                    <tr>
                        <th style="width: 10%">教练ID</th>
                        <th style="width: 10%">昵称</th>
                        <th style="width: 10%">头衔</th>
                        <th>描述</th>
                        <th style="width: 10%">操作</th>
                    </tr>
                    <!--tr-th end-->
                    @foreach ($coaches as $coach)
                        <tr>
                            <td>{{$coach->id}}</td>
                            <td>{{$coach->coach_name}}</td>
                            <td>{{$coach->title}}</td>
                            <td>{{$coach->describe}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{route('activity.admin.coach.show', $coach->id)}}" no-pjax>
                                    <i data-toggle="tooltip" data-placement="top" class="fa fa-pencil-square-o" title="编辑"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div>
                    当前无数据
                </div>
            @endif
        </div><!-- /.box-body -->
    </div>
</div>