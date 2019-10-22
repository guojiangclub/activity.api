<div class="ibox float-e-margins">
    <div class="ibox-content">
        <div>
            <h3 class="box-title">城市列表</h3>
        </div>
        <a href="{{ route('activity.admin.city.create')}}" class="btn btn-primary" no-pjax>新建活动城市</a>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            @if(count($cities)>0)
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr>
                        <th>图片</th>
                        <th>名称</th>
                        <th>活动数量</th>
                        <th>操作</th>
                    </tr>
                    @foreach ($cities as $city)
                        <tr class="city_item">
                            <td style="width: 20%">
                                <img src="{{$city->img}}" style="width: 200px;height: 80px;">
                            </td>
                            <td style="width: 20%">{{$city->name}}</td>
                            <td style="width: 20%">{{$city->act_count}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{route('activity.admin.city.edit', $city->id)}}" no-pjax>
                                    <i data-toggle="tooltip" data-placement="top" class="fa fa-pencil-square-o" title="编辑"></i>
                                </a>
                                <a class="btn btn-xs btn-danger">
                                    <i data-url="{{route('activity.admin.city.delete', $city->id)}}" class="fa fa-trash city-delete" title="删除"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div> {!! $cities->render() !!}</div>
            @else
                <div>
                    当前无数据
                </div>
            @endif
        </div><!-- /.box-body -->

    </div>
</div>

<script>
$(function(){
    $('.city-delete').on('click', function(){
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
                    type: "post",
                    url: url,
                    success: function(data){
                        thisPoint.closest('.city_item').remove();
                        swal({
                            title: "删除成功！",
                            showConfirmButton: true,
                            confirmButtonText: "确认"
                        });
                    }
                });
            } else {
            }
        });
    });
});
</script>