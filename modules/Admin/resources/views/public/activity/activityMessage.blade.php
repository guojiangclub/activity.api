<div class="form-group">
    <label class="col-sm-2 control-label">报名成功短信通知：</label>
    <div class="col-sm-10">
        <label class="checkbox-inline i-checks">
            <input name="send_message" id="activity-package-settings" type="radio" value="0" {{ isset($model) && $model->send_message==0 ? 'checked' : !isset($model) ? 'checked' : '' }}> 否
        </label>
        <label class="checkbox-inline i-checks">
            <input name="send_message" type="radio" value="1" {{ isset($model) && $model->send_message==1 ? 'checked' : '' }}> 是
        </label>
    </div>
</div>

<div class="form-group package_settings" style="display: {{ isset($model) && $model->send_message == 1 ? 'block' : 'none' }};">
    <label class="col-md-2 control-label">参赛包领取地址：</label>
    <div class="col-md-5">
        {!! Form::text('package_get_address', null, ['class' => 'form-control', 'placeholder' => "参赛包领取地址"] ) !!}
    </div>
</div>

<div class="form-group package_settings" style="display: {{ isset($model) && $model->send_message == 1 ? 'block' : 'none' }};">
    <label for="" class="col-md-2 control-label">参赛包领取时间：</label>
    <div class="col-md-7">
        <div class="col-md-1 padding-clear padding-top-7" style="min-width: 220px;">
            <div class="date form_datetime">
                <span class="add-on"><i class="fa fa-calendar"></i></span>
                {!! Form::text('package_get_time', null, ['class' => '', 'placeholder' => "参赛包领取时间", "readonly", "required"]) !!}
            </div>
        </div>
    </div>
</div>