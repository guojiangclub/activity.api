    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <div>
                <h3 class="box-title">活动：{{$activity->title}} - 奖励积分审核</h3>
            </div><!-- /.box-header -->
            <div class="hr-line-dashed"></div>
            <div class="table-responsive">
                @if(count($members)>0)
                    {!! Form::open( [ 'url' => [route('activity.admin.rewards.store', $activity->id)], 'method' => 'POST', 'id' => 'store-activity-rewards'] ) !!}
                        <table class="table table-hover table-striped">
                            <tbody>
                            <!--tr-th start-->
                            <tr>
                                <th>用户ID</th>
                                <th>昵称</th>
                                <th>奖励数值</th>
                                <th>审核状态</th>
                            </tr>
                            <!--tr-th end-->
                            @foreach ($members as $member)
                                <tr>
                                    <td>{{$member->user_id}}</td>
                                    <td>{{$member->name}}</td>
                                    <td>{{$member->point_value}}</td>
                                    <td>
                                        <input type="checkbox" class="checkbox" value="{{$member->id}}" name="ids[]" {{$member->point_status == 1 ? 'checked' : ''}}>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="footable-visible">
                                        <input type="submit" class="btn btn-primary" value="保存"/>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    {!! Form::close() !!}
                @else
                    <div>
                        当前无数据
                    </div>
                @endif
            </div><!-- /.box-body -->
        </div>
    </div>
    <script>
        $('#store-activity-rewards').ajaxForm({
            success: function (result) {
                if (!result.status) {
                    swal("保存失败!", result.error, "error")
                } else {
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        window.location = '{!!route('activity.admin.index')!!}';
                    });
                }
            },
            error: function () {
                swal("保存失败!", '服务器内部错误', "error")
            }
        });
    </script>