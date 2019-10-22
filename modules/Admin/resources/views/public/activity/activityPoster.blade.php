<div class="form-group">
    <label class="col-md-2 control-label"><span style="color: red">*</span>详情图片：</label>
    <div class="col-md-7">
        <div class="pull-left" id="activity-poster">
            <img src={{isset($model) && !empty($model->img) ? $model->img : "/assets/backend/activity/backgroundImage/pictureBackground.png"}} alt="" class="img" width="226px"
                 height="91px"
                 style=" margin-right: 23px;">
            {!! Form::hidden('img', null, ['class' => 'form-control'] ) !!}
        </div>
        <div class="clearfix" style="padding-top: 22px;">
            <div id="filePicker">添加图片</div>
            <p style="color: #b6b3b3">温馨提示：图片尺寸建议为：750*300, 图片小于4M</p>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label"><span style="color: red">*</span>列表图片：</label>
    <div class="col-md-7">
        <div class="pull-left" id="activity-poster-list">
            <img src={{isset($model) && !empty($model->img_list) ? $model->img_list : "/assets/backend/activity/backgroundImage/pictureBackground2.png"}} alt="" class="img" width="91px"
                 height="91px"
                 style=" margin-right: 23px;">
            {!! Form::hidden('img_list', null, ['class' => 'form-control'] ) !!}
        </div>
        <div class="clearfix" style="padding-top: 22px;">
            <div id="filePickerImgList">添加图片</div>
            <p style="color: #b6b3b3">温馨提示：图片尺寸至少为：168*168, 图片小于4M</p>
        </div>
    </div>
</div>
