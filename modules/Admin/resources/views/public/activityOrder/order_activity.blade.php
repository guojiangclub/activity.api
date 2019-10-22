<table class="table table-hover table-striped">
    <tbody>
        <tr>
            <th>活动名称</th>
            <th>支付名称</th>
            <th>金额</th>
            <th>积分</th>
        </tr>
        <tr>
            <td width="350px"><img width="187.797px" height="117px" src="{{ $member->activity->img }}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="{{route('activity.admin.edit', $member->activity->id)}}">{{$member->activity->title}}</a>
            </td>
            <td style="line-height: 117px;">{{!$member->payment ? '' : $member->payment->title}}</td>
            <td style="line-height: 117px; ">{{!$member->payment ? 0 : $member->payment->price}}</td>
            <td style="line-height: 117px;">{{!$member->payment ? 0 : $member->payment->point}}</td>
        </tr>
    </tbody>
</table>
