<div class="form-group" id="refund-detail" style="display: {{isset($model) && $model->refund_status==0 && $model->fee_type=='PASS'? "none" : "block"}}">
    <label class="col-md-2 control-label">退款设置：</label>

    <div class="col-md-8 padding-top-7">
        <div class="">
            <input name="refund_status" id="activity-support-refund" type="radio" value="1" @if(isset($model) && $model->refund_status==1) checked @elseif(!isset($model)) checked @else @endif/> 支持退款
            <input name="refund_status" type="radio" value="0" {{isset($model) && $model->refund_status==0 ? "checked" : " " }} /> 不支持退款
        </div>
        <div class="vim-panel col vim-margin-top" id="activity-refund" {{isset($model) && $model->refund_status==0 ? "hidden" : " "}} >
            @if(isset($model) && $model->refund_status==0)
                {{--不支持退款--}}
            @else
                <div class="row">
                    <label class="col-md-2 control-label">退款期限</label>
                    <div class="col col-md-5 form-group">
                        <div class="input-group">
                            <input type="text" name="refund_term" class="form-control" value="{{isset($model) && !empty($model->refund_term)? $model->refund_term : 0}}" placeholder="距离活动开始的分钟数">
                            <div class="input-group-addon">分钟</div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div>退款期限为0，表示活动开始前都可以退款。</div>
                        <div>退款期限为正数，表示活动开始前X分钟截止退款。</div>
                        <div>退款期限为负数，表示活动开始后X分钟内仍可退款。</div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 control-label">退款说明</label>
                    <div class="col-md-5 form-group">
                        <textarea class="form-control" name="refund_text" rows="6" style="resize: none;" placeholder="退款说明">{{isset($model) && !empty($model->refund_text)? $model->refund_text : ''}}</textarea>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script type="text/html" id="refund-template">
    <div class="row">
        <label class="col-md-2 control-label">退款期限</label>
        <div class="col col-md-5 form-group">
            <div class="input-group">
                <input type="number" class="form-control " name="refund_term" placeholder="距离活动开始的分钟数" value="0">
                <div class="input-group-addon">分钟</div>
            </div>
        </div>
        <div class="col-md-5">
            <div>退款期限为0，表示活动开始前都可以退款。</div>
            <div>退款期限为正数，表示活动开始前X分钟截止退款。</div>
            <div>退款期限为负数，表示活动开始后X分钟内仍可退款。</div>
        </div>
    </div>
    <div class="row">
        <label class="col-md-2 control-label">退款说明</label>
        <div class="col-md-5 form-group">
            <textarea class="form-control" name="refund_text" rows="6" style="resize: none;" placeholder="退款说明"></textarea>
        </div>
    </div>
</script>
