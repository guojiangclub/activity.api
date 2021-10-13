## 果酱互动

果酱互动是一款在线活动报名系统，可线上报名活动、支付，线下当场扫码验证身份是否有效。

## 效果截图

![果酱互动](https://cdn.guojiang.club/activity1012.jpg)

## 功能列表

- 发布活动
- 活动报名管理
- 自定义报名表单
- 在线报名
- 在线支付
- 扫码核验
- 取消报名/退款
- 分享海报

## 安装

```
# php artisan vendor:publish
# php artisan migrate
# php artisan admin:install
# 生成菜单
php artisan db:seed --class 'GuoJiangClub\Activity\Backend\Seeds\ActivityAdminSeeder'
```

## 小程序

小程序源码地址：[果酱互动小程序源码](https://github.com/guojiangclub/activity.miniprogram)

## 交流

扫码添加[玖玖|彼得助理]，可获得“陈彼得”绝密编程资源以及25个副业赚钱思维。

![玖玖|彼得助理 微信二维码](https://cdn.guojiang.club/xiaojunjunqyewx2.jpg)