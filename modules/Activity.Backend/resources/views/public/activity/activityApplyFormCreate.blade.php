<div class="form-group" id="activityForm">
    <label class="col-md-2 control-label">报名填写信息：</label>
    <div class="col-md-10">
        <div class="vim-panel col vim-margin-top activity-form">
            <div class="row padding-top-7">
                {{--<label class="col-md-2 control-label">姓名：</label>
                <div class="col col-md-4">
                    <input type="text" class="form-control" name="name" value="">
                </div>--}}
                <div class="type-select">
                    <div>添加选项</div>
                    <ul>
                        <li class="addFormElement" data-type="user_name">姓名</li>
                        <li class="addFormElement" data-type="mobile">手机</li>
                        <li class="addFormElement" data-type="province">所在省市</li>
                        <li class="addFormElement" data-type="certificate_type">证件类型</li>
                        <li class="addFormElement" data-type="id_card">身份证号</li>
                        <li class="addFormElement" data-type="other_certificate">其他证件</li>
                        <li class="addFormElement" data-type="text">文本框</li>
                        <li class="addFormElement" data-type="textarea">多行文本框</li>
                        <li class="addFormElement" data-type="select">下拉框</li>
                        <li class="addFormElement" data-type="radio">单选按钮</li>
                        <li class="addFormElement" data-type="checkbox">多选按钮</li>
                        <li class="addFormElement" data-type="file">文件上传</li>
                        <li class="addFormElement" data-type="range">滑动条</li>
                    </ul>
                </div>
            </div>
            {{--<div class="row padding-top-7">
                <label class="col-md-2 control-label">手机：</label>
                <div class="col col-md-4">
                    <input type="text" class="form-control" name="phone" value="">
                </div>
            </div>
            <div class="row"><div class="col-md-8"></div></div>--}}
            {{--<component v-bind:is="item.com" v-for="(item,index) in activityForm"></component>--}}
        </div>
    </div>
</div>