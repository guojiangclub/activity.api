<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, shrink-to-fit=no">


    <title>{{$activity?$activity->title:'活动不存在'}}</title>
    <style type="text/css">
        body {
            padding: 15px
        }
        img {
            max-width: 100% !important;
        }
    </style>
</head>
<body>
{!! $activity?$activity->content:'活动不存在' !!}
</body>
</html>