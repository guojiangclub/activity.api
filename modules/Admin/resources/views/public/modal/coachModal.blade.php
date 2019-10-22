<!-- Modal -->
<div class="modal fade bs-example-modal-lg" id="coach-list" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">选择教练</h4>
            </div>
            <div class="modal-body">
                <div class="vim-button-box" style="justify-content: center;">
                    <div><label for="" class="control-label">教练会员名称</label></div>
                    <div class="col-md-5"><input type="text" class="form-control" id="vim-search-coach-by-name">
                    </div>
                    <div>
                        <button class="btn btn-primary">搜索</button>
                    </div>
                </div>
                <hr/>
                <p>请选择教练</p>

                <div class="row">
                    @if(count($coachArray)>0)
                        @foreach($coachArray as $coach)
                            <div class="col col-md-4 vim-modal-coach-list" value="{{$coach->name}}">
                                <div>
                                    <input type="checkbox"
                                           class="icheckbox icheckbox_square-green vim-modal-coach-list-checkbox"/>
                                    <input type="hidden" name="user_id" value="{{$coach->id}}">
                                    <input type="hidden" name="mobile" value="{{$coach->mobile}}">
                                    <input type="hidden" name="name" value="{{$coach->name}}">
                                    <img src={{empty($coach->avatar) ? "/assets/backend/activity/backgroundImage/peopleAvatar.png" : $coach->avatar}} alt=""
                                         style="width: 50px; height: 50px"
                                         class="img vim-margin-top">
                                    {{--<i class="fa fa-fa-user-circle" style="width: 50px; height: 50px" hidden></i>--}}
                                    <span>{{$coach->name}}</span></div>
                            </div>
                        @endforeach
                    @else
                        <span>还未添加教练角色（coach）</span>
                    @endif
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="vim-store-selected-coach">保存</button>
            </div>
        </div>
    </div>
</div>
