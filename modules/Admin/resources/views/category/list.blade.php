    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <a href="{{ route('activity.admin.category.curd')}}" class="btn btn-primary" no-pjax>添加活动分类</a>
            <div class="hr-line-dashed"></div>
            <div class="table-responsive">
                @if(count($categories)>0)
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>分类名称</th>
                            <th>操作</th>
                        </tr>
                        @foreach ($categories as $category)
                            <tr class="city_item">
                                <td style="width: 15%">{{$category->name}}</td>
                                <td style="width: 10%">
                                    <a class="btn btn-xs btn-primary" href="{{route('activity.admin.category.curd', $category->id)}}" no-pjax>
                                        <i data-toggle="tooltip" data-placement="top" class="fa fa-pencil-square-o" title="编辑"></i>
                                    </a>
                                    <a class="btn btn-xs btn-danger">
                                        <i data-url="{{route('activity.admin.category.delete', $category->id)}}" class="fa fa-trash category-delete" title="删除"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div> {!! $categories->render() !!}</div>
                @else
                    <div>
                        当前无数据
                    </div>
                @endif
            </div><!-- /.box-body -->

        </div>
    </div>

    <script>
    $(function () {
	    $('.category-delete').on('click', function () {
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
						    type: "get",
						    url: url,
						    success: function (res) {
						    	if(res.status){
								    thisPoint.closest('.city_item').remove();
								    swal({
									    title: "删除成功！",
									    showConfirmButton: true,
									    confirmButtonText: "确认"
								    });
                                } else {
								    swal("删除失败!", res.message, "error");
                                }
						    }
					    });
				    }
			    });
	    });
    });
</script>