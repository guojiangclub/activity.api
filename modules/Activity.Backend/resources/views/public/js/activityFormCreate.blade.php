<script>
            @if(!empty($nameArr))
	var nameArr = {!! $nameArr !!};
            @else
	var nameArr = [];
    @endif

	$('.addFormElement').on('click', function () {
		var type = $(this).attr('data-type');
		var component = '';
		var date = new Date();
		var time = date.getTime();
		var rand = Math.ceil(Math.random() * 100000);
		var inputIndex = time.toString() + rand.toString();

		switch (type) {
			case 'text':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">文本框</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off"><i class="fa switch fa-toggle-off" title="是否显示"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="文本框" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'textarea':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">多行文本框</span>：</label><div class="col col-md-3"><textarea class="form-control"></textarea></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="textarea"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="多行文本框" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'select':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">下拉选择框</span>：</label><div class="col col-md-3"><select class="form-control selectOptions_' + inputIndex + '"><option>请选择</option><option class="select_options_' + inputIndex + '_0 select_options_delete_' + inputIndex + '_0">选项</option><option class="select_options_' + inputIndex + '_1 select_options_delete_' + inputIndex + '_1">选项</option></select><input type="hidden" class="select_options_' + inputIndex + '_0" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="0"/><input type="hidden" class="select_options_' + inputIndex + '_1" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="1"/></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="select"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="下拉选择框" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div><div class="form-group select_options_delete_' + inputIndex + '_0"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'select_options_' + inputIndex + '_0\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger select_options_delete" data-num="' + inputIndex + '_0" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group select_options_delete_' + inputIndex + '_1"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'select_options_' + inputIndex + '_1\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger select_options_delete" data-num="' + inputIndex + '_1" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group"><div class="col-md-3"></div><div class="col-md-6"><a class="btn btn-success select_add_option" data-type="selectOptions_' + inputIndex + '" data-index="' + inputIndex + '">添加选项</a></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'radio':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">单选按钮</span>：</label><div class="col col-md-3"><div class="form-group"><div class="radio radioOptions_' + inputIndex + '"><label class="radio_options_delete_' + inputIndex + '_0"><input type="radio" disabled><span class="radio_options_' + inputIndex + '_0">选项</span><input type="hidden" class="radio_options_' + inputIndex + '_0" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="0"/></label><label class="radio_options_delete_' + inputIndex + '_1"><input type="radio" disabled><span class="radio_options_' + inputIndex + '_1">选项</span><input type="hidden" class="radio_options_' + inputIndex + '_1" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="1"/></label></div></div></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="radio"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="单选按钮" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div><div class="form-group radio_options_delete_' + inputIndex + '_0"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'radio_options_' + inputIndex + '_0\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger radio_options_delete" data-num="' + inputIndex + '_0" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group radio_options_delete_' + inputIndex + '_1"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'radio_options_' + inputIndex + '_1\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger radio_options_delete" data-num="' + inputIndex + '_1" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group"><div class="col-md-3"></div><div class="col-md-6"><a class="btn btn-success radio_add_option" data-type="radioOptions_' + inputIndex + '" data-index="' + inputIndex + '">添加选项</a></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'checkbox':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">多选按钮</span>：</label><div class="col col-md-3"><div class="form-group"><div class="checkbox checkboxOptions_' + inputIndex + '"><label class="checkbox_options_delete_' + inputIndex + '_0"><input type="checkbox" disabled><span class="checkbox_options_' + inputIndex + '_0">选项</span><input type="hidden" class="checkbox_options_' + inputIndex + '_0" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="0"/></label><label class="checkbox_options_delete_' + inputIndex + '_1"><input type="checkbox" disabled><span class="checkbox_options_' + inputIndex + '_1">选项</span><input type="hidden" class="checkbox_options_' + inputIndex + '_1" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="1"/></label><label class="checkbox_options_delete_' + inputIndex + '_2"><input type="checkbox" disabled><span class="checkbox_options_' + inputIndex + '_2">选项</span><input type="hidden" class="checkbox_options_' + inputIndex + '_2" name="activityForm[input_' + inputIndex + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="2"/></label></div></div></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="checkbox"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="多选按钮" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div><div class="form-group checkbox_options_delete_' + inputIndex + '_0"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'checkbox_options_' + inputIndex + '_0\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger checkbox_options_delete" data-num="' + inputIndex + '_0" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group checkbox_options_delete_' + inputIndex + '_1"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'checkbox_options_' + inputIndex + '_1\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger checkbox_options_delete" data-num="' + inputIndex + '_1" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group checkbox_options_delete_' + inputIndex + '_2"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'checkbox_options_' + inputIndex + '_2\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger checkbox_options_delete" data-num="' + inputIndex + '_2" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div><div class="form-group"><div class="col-md-3"></div><div class="col-md-6"><a class="btn btn-success checkbox_add_option" data-type="checkboxOptions_' + inputIndex + '" data-index="' + inputIndex + '">添加选项</a></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'file':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">文件上传</span>：</label><div class="col col-md-3"><input type="file" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="file"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="inupt_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="文件上传" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'range':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_' + inputIndex + '_modal"><span class="input_' + inputIndex + '_title" title="点击修改">滑动条</span>：</label><div class="col col-md-3"><input type="range" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="range"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="input_' + inputIndex + '"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value=""><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/><div class="modal fade" id="input_' + inputIndex + '_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">修改属性</h4></div><div class="modal-body"><div class="form-group"><label class="col-md-3 control-label">字段名称：</label><div class="col-md-6"><input type="text" class="form-control input_' + inputIndex + '_title" value="" placeholder="滑动条" oninput="updateTitle(\'input_' + inputIndex + '_title\', this)"></div></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div></div>';
				break;
			case 'province':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">所在省市</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="province" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="province"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="所在省市"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
			case 'id_card':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">身份证号</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="id_card" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="id_card"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="身份证号"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
			case 'user_name':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">姓名</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="user_name" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="user_name"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="姓名"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
			case 'mobile':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">手机</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="mobile" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="mobile"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="手机"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
			case 'other_certificate':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">其他证件</span>：</label><div class="col col-md-3"><input type="text" class="form-control"></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="mobile" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="text"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="other_certificate"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="其他证件"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
			case 'certificate_type':
				component = '<div class="row padding-top-7 items_control"><label class="col-md-2 control-label input-title"><span class="input_' + inputIndex + '_title" title="点击修改">证件类型</span>：</label><div class="col col-md-3"><div class="form-group"><div class="radio radioOptions_' + inputIndex + '"><label class="radio_options_delete_' + inputIndex + '_0"><input type="radio" disabled><span class="radio_options_' + inputIndex + '_0">身份证</span><input type="hidden" class="radio_options_' + inputIndex + '_0" name="activityForm[input_' + inputIndex + '][options][name][]" value="身份证"/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="0"/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][value][]" value="id_card"/></label><label class="radio_options_delete_' + inputIndex + '_1"><input type="radio" disabled><span class="radio_options_' + inputIndex + '_1">其他证件</span><input type="hidden" class="radio_options_' + inputIndex + '_1" name="activityForm[input_' + inputIndex + '][options][name][]" value="其他证件"/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][index][]" value="1"/><input type="hidden" name="activityForm[input_' + inputIndex + '][options][value][]" value="other_certificate"/></label></div></div></div><div class="col col-md-4 padding-top-7"><div class="col-md-3"><a class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;<a class="btn btn-xs btn-danger remove-items" data-name="input_' + inputIndex + '" style="cursor:pointer;" title="删除"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="off" title="是否显示"><i class="fa switch fa-toggle-off"></i></a><input type="hidden" name="activityForm[input_' + inputIndex + '][status]" value="0">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_' + inputIndex + '][is_necessary]" value="0"></div><input type="hidden" name="activityForm[input_' + inputIndex + '][type]" value="radio"/><input type="hidden" name="activityForm[input_' + inputIndex + '][index]" value="' + inputIndex + '"/><input type="hidden" class="input_' + inputIndex + '_name" name="activityForm[input_' + inputIndex + '][name]" value="certificate_type"><input class="input_' + inputIndex + '_title" type="hidden" name="activityForm[input_' + inputIndex + '][title]" value="证件类型"><input type="hidden" name="activityForm[input_' + inputIndex + '][value]" value=""/></div>';
				break;
		}

		if ($.inArray(type, ['province', 'id_card', 'user_name', 'mobile', 'certificate_type', 'other_certificate']) >= 0) {
			if ($.inArray(type, nameArr) >= 0) {
				swal({
					title: '',
					text: "请勿重复添加",
					type: "warning",
					timer: 1000,
					showConfirmButton: true
				});

				return;
			} else {
				nameArr.push(type);
			}
		}

		$('.activity-form').append(component);
		initicheckboxStyle();
	});

	$('body').on('click', '.remove-items', function (index) {
		var data_name = $(this).attr('data-name');
		$(this).parent('div').parent('div').remove();
		if ($.inArray(data_name, nameArr) >= 0) {
			var index = $.inArray(data_name, nameArr);
			nameArr.splice(index, 1);
		}

		$('.move-items').eq(0).children('i').removeClass('fa-angle-up').addClass('fa-angle-down');
	});

	$('body').on('click', '.move-items', function () {
		var action = $(this).attr('data-action');
		var copy_dom = $(this).parent('div').parent('div').parent('div');
		if (action == 'up') {
			var prev_div = copy_dom.prev('div .items_control');
			console.log(prev_div.length);
			if (prev_div.length <= 0) {
				return false;
			}

			prev_div.before(copy_dom);
		} else {
			var next_div = copy_dom.next('div .items_control');
			console.log(next_div.length);
			if (next_div.length <= 0) {
				return false;
			}

			next_div.after(copy_dom);
		}
	});

	$('body').on('click', '.switch-status', function () {
		var status = $(this).attr('data-status');
		var current_style = 'fa-toggle-' + status;
		var status_val = 0;
		if (status == 'off') {
			status = 'on';
			status_val = 1;
		} else {
			status = 'off';
			$(this).next('input').next('input:checkbox').attr('checked', false).val(0);
			$(this).next('input').next('div').children('input:checkbox').iCheck('uncheck');
			$(this).next('input').next('div').children('input:checkbox').val(0);
		}

		$(this).children('i').removeClass(current_style).addClass('fa-toggle-' + status);
		$(this).attr('data-status', status);
		$(this).next('input:hidden').val(status_val);
	});

	$('body').on('change', '.is_necessary', function () {
		if ($(this).is(':checked')) {
			$(this).val(1);
		} else {
			$(this).val(0);
		}
	});

	$('body').on('ifChecked', '.is_necessary', function () {
		$(this).val(1);
	});

	$('body').on('ifUnchecked', '.is_necessary', function () {
		$(this).val(0);
	});

	$('body').on('click', '.radio_add_option', function () {
		var data_index = $(this).attr('data-index');
		var data_type = $(this).attr('data-type');
		var date = new Date();
		var time = date.getTime();
		var rand = Math.ceil(Math.random() * 100000);
		var radioOptionsIndex = time.toString() + rand.toString();
		var radioTemplate = '<div class="form-group radio_options_delete_' + data_index + '_' + radioOptionsIndex + '"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'radio_options_' + data_index + '_' + radioOptionsIndex + '\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger radio_options_delete" data-num="' + data_index + '_' + radioOptionsIndex + '" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div>';
		var radioOptionsTemplate = '<label class="radio_options_delete_' + data_index + '_' + radioOptionsIndex + '"><input type="radio" disabled><span class="radio_options_' + data_index + '_' + radioOptionsIndex + '">选项</span><input type="hidden" class="radio_options_' + data_index + '_' + radioOptionsIndex + '" name="activityForm[input_' + data_index + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + data_index + '][options][index][]" value="' + radioOptionsIndex + '"/></label>';
		$(this).parent('div').parent('div').before(radioTemplate);
		$('.' + data_type).append(radioOptionsTemplate);
		initicheckboxStyle();
	});

	$('body').on('click', '.checkbox_add_option', function () {
		var data_index = $(this).attr('data-index');
		var data_type = $(this).attr('data-type');
		var date = new Date();
		var time = date.getTime();
		var rand = Math.ceil(Math.random() * 100000);
		var checkboxOptionsIndex = time.toString() + rand.toString();
		var checkboxTemplate = '<div class="form-group checkbox_options_delete_' + data_index + '_' + checkboxOptionsIndex + '"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'checkbox_options_' + data_index + '_' + checkboxOptionsIndex + '\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger checkbox_options_delete" data-num="' + data_index + '_' + checkboxOptionsIndex + '" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div>';
		var checkboxOptionsTemplate = '<label class="checkbox_options_delete_' + data_index + '_' + checkboxOptionsIndex + '"><input type="checkbox" disabled><span class="checkbox_options_' + data_index + '_' + checkboxOptionsIndex + '">选项</span><input type="hidden" class="checkbox_options_' + data_index + '_' + checkboxOptionsIndex + '" name="activityForm[input_' + data_index + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + data_index + '][options][index][]" value="' + checkboxOptionsIndex + '"/></label>';
		$(this).parent('div').parent('div').before(checkboxTemplate);
		$('.' + data_type).append(checkboxOptionsTemplate);
		initicheckboxStyle();
	});

	$('body').on('click', '.select_add_option', function () {
		var data_index = $(this).attr('data-index');
		var data_type = $(this).attr('data-type');
		var date = new Date();
		var time = date.getTime();
		var rand = Math.ceil(Math.random() * 100000);
		var selectOptionsIndex = time.toString() + rand.toString();
		var selectTemplate = '<div class="form-group select_options_delete_' + data_index + '_' + selectOptionsIndex + '"><div class="col-md-3"></div><div class="col-md-6"><input type="text" class="form-control" value="" oninput="updateTitle(\'select_options_' + data_index + '_' + selectOptionsIndex + '\', this)" placeholder="选项"></div><div class="col-md-1"><a class="btn btn-xs btn-danger select_options_delete" data-num="' + data_index + '_' + selectOptionsIndex + '" style="cursor:pointer; margin-top:13px;"><i class="fa fa-trash"></i></a></div></div>';
		var selectOptionsTemplate = '<option class="select_options_' + data_index + '_' + selectOptionsIndex + ' select_options_delete_' + data_index + '_' + selectOptionsIndex + '"></option>';
		var inputHiddenTemplate = '<input type="hidden" class="select_options_' + data_index + '_' + selectOptionsIndex + '" name="activityForm[input_' + data_index + '][options][name][]" value=""/><input type="hidden" name="activityForm[input_' + data_index + '][options][index][]" value="' + selectOptionsIndex + '"/>';
		$(this).parent('div').parent('div').before(selectTemplate);
		$('.' + data_type).append(selectOptionsTemplate);
		$('.' + data_type).parent('div').append(inputHiddenTemplate);
		initicheckboxStyle();
	});

	$('body').on('click', '.radio_options_delete', function () {
		var num = $(this).attr('data-num');
		$('.radio_options_delete_' + num).remove();
	});

	$('body').on('click', '.checkbox_options_delete', function () {
		var num = $(this).attr('data-num');
		$('.checkbox_options_delete_' + num).remove();
	});

	$('body').on('click', '.select_options_delete', function () {
		var num = $(this).attr('data-num');
		$('.select_options_delete_' + num).remove();
	});

	function updateTitle(c, o) {
		var currentObj = $(o);
		var title = currentObj.val();
		$('.' + c).each(function (index) {
			if (index == 0) {
				$(this).text(title);
			} else {
				$(this).val(title);
			}
		});
	}

	function initicheckboxStyle() {
		$('#activityForm').find('input').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
			increaseArea: '20%'
		});
	}
</script>