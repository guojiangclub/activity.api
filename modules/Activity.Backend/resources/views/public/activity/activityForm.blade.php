<div class="form-group">
    <label class="col-md-2 control-label">报名表单：</label>
    <div class="col-md-4">
        <select class="form-control" name="form_id">
            <option value="0">请选择</option>
            @if(count($forms))
                @foreach($forms as $form)
                    <option {{isset($model) && isset($model->form_id) && $model->form_id && $model->form_id==$form->id ? 'selected' : ''}} value="{{$form->id}}">{{$form->name}}</option>
                @endforeach
            @endif
        </select>
        @if(isset($model) && isset($model->form_id) && $model->form_id)
            <p style="color: #b6b3b3; margin: 0;margin-top: 5px;">温馨提示：修改报名表单可能导致用户报名数据丢失，请谨慎操作！</p>
        @endif
    </div>
</div>