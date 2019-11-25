<div class="form-group">
    <label class="col-md-2 control-label">添加教练：</label>
    <div class="col-md-8">
        <div class="row row_indent" id="vim-activity-selected-coach">
            @if(isset($selectedCoach))
                @foreach($selectedCoach as $item)
                    <div class="col col-md-3 vim-coach-list-item" style="min-width: 280px;margin-right: 20px;">
                        <div class="">
                            <div class="thumbnail vim-thumbnail row">
                                <div class="col col-md-9">
                                    <div class="pull-left">
                                        <img src={{empty($item->user()->first()->avatar) ? "/assets/backend/activity/backgroundImage/peopleAvatar.png" : $item->user()->first()->avatar }} alt="" class="img img-around"
                                             style="width: 75px; height: 75px; margin-right: 23px; ">
                                    </div>
                                    <div class="vim-coach-info vim-flex-center-vertically"
                                         style="height: 75px">
                                        <div>
                                            <h3 class="vim-coach-name">{{$item->user()->first()->name}}</h3>
                                            <h3 class="vim-coach-phone">{{$item->user()->first()->mobile}}</h3>
                                            <input type="hidden" name="user_id[]" value={{$item->user_id}}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-md-3 vim-flex-center-vertically pull-right"
                                     style="height: 75px">
                                    <i class="fa fa-trash fa-3x delete-coach-i"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
            @endif
        </div>
        <div class="row row_indent">
            <button class="btn btn-primary" type="button" data-target="#coach-list" data-toggle="modal">添加教练</button>
        </div>
    </div>
</div>

<script type="text/html" id="select-coach-template">
    <div class="col col-md-3 vim-coach-list-item" style="min-width: 280px;margin-right: 20px;">
        <div class="">
            <div class="thumbnail vim-thumbnail row">
                <div class="col col-md-9">
                    <div class="pull-left">
                        <img src={#avatar#} alt="" class="img img-around" style="width: 75px; height: 75px; margin-right: 23px; ">
                    </div>
                    <div class="vim-coach-info vim-flex-center-vertically" style="height: 75px">
                        <div>
                            <h3 class="vim-coach-name">{#name#}</h3>
                            <h3 class="vim-coach-phone">{#phone#}</h3>
                            <input type="hidden" name="user_id[]" value={#id#}>
                        </div>
                    </div>
                </div>
                <div class="col col-md-3 vim-flex-center-vertically pull-right" style="height: 75px">
                    <i class="fa fa-trash fa-3x delete-coach-i"></i>
                </div>
            </div>
        </div>
    </div>
</script>
