<div class="hr-line-dashed"></div>
<div class="row">
    <div class="col-md-2 col-md-offset-3">
        <div class="vim-button-box">
            <button type="button" data-status="1" class="btn btn-danger vim-activity-store" value="1">
                保存并发布
            </button>
            <button type="button" data-status="1" class="btn btn-primary vim-activity-store" value="{{isset($model) ? $model->status : 0}}">
                保存
            </button>
            <button class="btn"><a href="{!! route('activity.admin.index')!!}">取消</a>
            </button>
        </div>
    </div>
</div>
