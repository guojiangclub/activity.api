<div class="form-group">
    <label class="col-md-2 control-label"><span style="color: red">*</span>活动名称：</label>
    <div class="col-md-5">
        {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => "活动名称"] ) !!}
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">副标题：</label>
    <div class="col-md-5">
        {!! Form::text('subtitle', null, ['class' => 'form-control', 'placeholder' => "副标题"] ) !!}
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">分享副标题：</label>
    <div class="col-md-5">
        {!! Form::text('share_title', null, ['class' => 'form-control', 'placeholder' => "分享副标题"] ) !!}
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">活动分类：</label>
    <div class="col-md-4">
        <select class="form-control" name="category_id">
            <option value="0">请选择</option>
            @if(count($categories))
                @foreach($categories as $category)
                    <option {{isset($model) && isset($model->category_id) && $model->category_id && $model->category_id==$category->id ? 'selected' : ''}} value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

