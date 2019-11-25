<table class="table table-hover table-striped">
    <tbody>
    <tr>
        <th>报名人</th>
        <th>电话</th>
        <th>性别</th>
        <th>生日</th>
        <th>地址</th>
        <th>邮箱</th>
    </tr>
    <tr>
        {{! $user = $member->user}}
        <td> <i class="fa fa-user"></i>&nbsp;@if($user->name) {{$user->name}} @elseif($user->nick_name) {{$user->nick_name}} @else @endif</td>
        <td><i class="fa fa-mobile"></i>&nbsp;{{$user->mobile}}</td>
        <td><i class="fa fa-mars"></i>&nbsp;{{$user->sex}}</td>
        <td><i class="fa fa-birthday-cake"></i>&nbsp;{{$user->birthday}}</td>
        <td><i class="fa fa-home">&nbsp;</i>{{$user->address}}</td>
        <td><i class="fa fa-envelope">&nbsp;</i>{{$user->email}}</td>

    </tr>
    </tbody>
</table>
