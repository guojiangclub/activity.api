<input type="hidden" id="selected_spu">
<div class="form-group">
    <label class="col-md-2 control-label">活动关联商品：</label>
    <div class="col-md-8">
        <a class="btn btn-success" id="chapter-create-btn" data-toggle="modal"
           data-target="#modal" data-backdrop="static" data-keyboard="false"
           data-url="{{route('admin.activity.getSpu')}}">
            添加商品
        </a>
    </div>
</div>

<div class="form-group">
    <div class="col-md-8 col-md-offset-2">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>商品信息</th>
                <th width="100">吊牌价(元)</th>
                <th width="180">折扣</th>
                <th width="100">折后价(元)</th>
                <th width="80">是否必选</th>
                <th width="50">操作</th>
            </tr>
            </thead>
            <tbody id="select-goods-box">

            </tbody>
        </table>

    </div>
</div>