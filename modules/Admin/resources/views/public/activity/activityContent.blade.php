<div class="form-group">
    <label class="col-md-2 control-label">活动简介：</label>
    <div class="col-md-8">
        <textarea name="description" class="form-control" rows="12">@if(isset($model)){!!  $model->description !!}@endif</textarea>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label"><span style="color: red">*</span>活动详情：</label>
    <div class="col-md-8">
        <div id="upload" class="btn btn-primary">插入相册图片</div>
        <script id="container" name="content" type="text/plain">
            @if(isset($model))
                {!!  $model->content !!}
            @endif
        </script>
    </div>
</div>