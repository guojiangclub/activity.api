<div class="form-group">
    <label for="" class="col-md-2 control-label">
        <span style="color: red">*</span>活动城市：
    </label>
    <div class="col-md-8 padding-clear">
        <div class="col-md-4">
            <select name="city_id" id="" class="form-control" required>
                <option>选择城市</option>
                @foreach($city as $item)
                    <option value={{$item->id}} @if(isset($model) && $model->city_id == $item->id) selected @endif>{{$item->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-2 control-label">
        <span style="color: red">*</span>活动地点：
    </label>
    <div class="col-md-8 padding-clear">
        <div class="col-md-5">
            <input class="form-control" type="text" name="address" placeholder="具体地点" data-toggle="modal" data-target="#getPointModal" id="address_location" value="{{isset($model) ? $model->address : ''}}">
        </div>
        <div class="col-md-3">
            <input class="form-control" type="text" name="address_name" placeholder="地点名称" id="address_name" value="{{isset($model) ? $model->address_name : null}}">
            <input type="hidden" name="address_point" id="address_point" value="{{isset($model) ? $model->address_point : null}}">
        </div>
    </div>
</div>

<!--获取坐标和地址模态窗 begin-->
<div class="modal fade" id="getPointModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">选择地址</h4>
            </div>
            <div class="modal-body">
                <!--获取坐标和地址 begin-->
                <div class="point-wrap">
                    <div class="point-head">
                        <input type="text" id="search_t" />
                        <button id="btn_search" type="button" class="btn">搜索</button>
                        当前地址：<input type="text" id="addr_cur" placeholder="请使用左侧搜索选择地址，不要手动填写">
                        <!--当前坐标--><input type="hidden" id="poi_cur" />
                        <!--当前地名--><input type="hidden" id="name_cur" />
                    </div>
                    <div class="point-content">
                        <div id="tooles">
                            <div id="cur_city">
                                <strong>北京市</strong><span class="change_city">[<span style="text-decoration:underline;">更换城市</span>]<span id="level">当前缩放等级：10</span></span>
                                <div id="city" class="dn">
                                    <h3 class="city_class">热门城市<span class="close">X</span></h3>
                                    <div class="city_container">
                                        <span class="city_name">北京</span>
                                        <span class="city_name">深圳</span>
                                        <span class="city_name">上海</span>
                                        <span class="city_name">香港</span>
                                        <span class="city_name">澳门</span>
                                        <span class="city_name">广州</span>
                                        <span class="city_name">天津</span>
                                        <span class="city_name">重庆</span>
                                        <span class="city_name">杭州</span>
                                        <span class="city_name">成都</span>
                                        <span class="city_name">武汉</span>
                                        <span class="city_name">青岛</span>
                                    </div>
                                    <h3 class="city_class">全国城市</h3>
                                    <div class="city_container">
                                        <div class="city_container_left">直辖市</div>
                                        <div class="city_container_right">
                                            <span class="city_name">北京</span>
                                            <span class="city_name">上海</span>
                                            <span class="city_name">天津</span>
                                            <span class="city_name">重庆</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">内蒙古</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">呼和浩特</span>
                                            <span class="city_name">包头</span>
                                            <span class="city_name">乌海</span>
                                            <span class="city_name">赤峰</span>
                                            <span class="city_name">通辽</span>
                                            <span class="city_name">鄂尔多斯</span>
                                            <span class="city_name">呼伦贝尔</span>
                                            <span class="city_name">巴彦淖尔</span>
                                            <span class="city_name">乌兰察布</span>
                                            <span class="city_name">兴安盟</span>
                                            <span class="city_name">锡林郭勒盟</span>
                                            <span class="city_name">阿拉善盟</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">山西</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">太原</span>
                                            <span class="city_name">大同</span>
                                            <span class="city_name">阳泉</span>
                                            <span class="city_name">长治</span>
                                            <span class="city_name">晋城</span>
                                            <span class="city_name">朔州</span>
                                            <span class="city_name">晋中</span>
                                            <span class="city_name">运城</span>
                                            <span class="city_name">忻州</span>
                                            <span class="city_name">临汾</span>
                                            <span class="city_name">吕梁</span>

                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">陕西</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">西安</span>
                                            <span class="city_name">铜川</span>
                                            <span class="city_name">宝鸡</span>
                                            <span class="city_name">咸阳</span>
                                            <span class="city_name">渭南</span>
                                            <span class="city_name">延安</span>
                                            <span class="city_name">汉中</span>
                                            <span class="city_name">榆林</span>
                                            <span class="city_name">安康</span>
                                            <span class="city_name">商洛</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">河北</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">石家庄</span>
                                            <span class="city_name">唐山</span>
                                            <span class="city_name">秦皇岛</span>
                                            <span class="city_name">邯郸</span>
                                            <span class="city_name">邢台</span>
                                            <span class="city_name">保定</span>
                                            <span class="city_name">张家口</span>
                                            <span class="city_name">承德</span>
                                            <span class="city_name">沧州</span>
                                            <span class="city_name">廊坊</span>
                                            <span class="city_name">衡水</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">辽宁</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">沈阳</span>
                                            <span class="city_name">大连</span>
                                            <span class="city_name">鞍山</span>
                                            <span class="city_name">抚顺</span>
                                            <span class="city_name">本溪</span>
                                            <span class="city_name">丹东</span>
                                            <span class="city_name">锦州</span>
                                            <span class="city_name">营口</span>
                                            <span class="city_name">阜新</span>
                                            <span class="city_name">辽阳</span>
                                            <span class="city_name">盘锦</span>
                                            <span class="city_name">铁岭</span>
                                            <span class="city_name">朝阳</span>
                                            <span class="city_name">葫芦岛</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">吉林</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">长春</span>
                                            <span class="city_name">吉林</span>
                                            <span class="city_name">四平</span>
                                            <span class="city_name">辽源</span>
                                            <span class="city_name">通化</span>
                                            <span class="city_name">白山</span>
                                            <span class="city_name">松原</span>
                                            <span class="city_name">白城</span>
                                            <span class="city_name">延边</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">黑龙江</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">哈尔滨</span>
                                            <span class="city_name">齐齐哈尔</span>
                                            <span class="city_name">鸡西</span>
                                            <span class="city_name">鹤岗</span>
                                            <span class="city_name">双鸭山</span>
                                            <span class="city_name">大庆</span>
                                            <span class="city_name">伊春</span>
                                            <span class="city_name">牡丹江</span>
                                            <span class="city_name">佳木斯</span>
                                            <span class="city_name">七台河</span>
                                            <span class="city_name">黑河</span>
                                            <span class="city_name">绥化</span>
                                            <span class="city_name">大兴安岭</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">江苏</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">南京</span>
                                            <span class="city_name">无锡</span>
                                            <span class="city_name">徐州</span>
                                            <span class="city_name">常州</span>
                                            <span class="city_name">苏州</span>
                                            <span class="city_name">南通</span>
                                            <span class="city_name">连云港</span>
                                            <span class="city_name">淮安</span>
                                            <span class="city_name">盐城</span>
                                            <span class="city_name">扬州</span>
                                            <span class="city_name">镇江</span>
                                            <span class="city_name">泰州</span>
                                            <span class="city_name">宿迁</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">安徽</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">合肥</span>
                                            <span class="city_name">蚌埠</span>
                                            <span class="city_name">芜湖</span>
                                            <span class="city_name">淮南</span>
                                            <span class="city_name">马鞍山</span>
                                            <span class="city_name">淮北</span>
                                            <span class="city_name">铜陵</span>
                                            <span class="city_name">安庆</span>
                                            <span class="city_name">黄山</span>
                                            <span class="city_name">阜阳</span>
                                            <span class="city_name">宿州</span>
                                            <span class="city_name">滁州</span>
                                            <span class="city_name">六安</span>
                                            <span class="city_name">宣城</span>
                                            <span class="city_name">池州</span>
                                            <span class="city_name">亳州</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">山东</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">济南</span>
                                            <span class="city_name">青岛</span>
                                            <span class="city_name">淄博</span>
                                            <span class="city_name">枣庄</span>
                                            <span class="city_name">东营</span>
                                            <span class="city_name">潍坊</span>
                                            <span class="city_name">烟台</span>
                                            <span class="city_name">威海</span>
                                            <span class="city_name">济宁</span>
                                            <span class="city_name">泰安</span>
                                            <span class="city_name">日照</span>
                                            <span class="city_name">莱芜</span>
                                            <span class="city_name">临沂</span>
                                            <span class="city_name">德州</span>
                                            <span class="city_name">聊城</span>
                                            <span class="city_name">滨州</span>
                                            <span class="city_name">菏泽</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">浙江</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">杭州</span>
                                            <span class="city_name">宁波</span>
                                            <span class="city_name">温州</span>
                                            <span class="city_name">嘉兴</span>
                                            <span class="city_name">绍兴</span>
                                            <span class="city_name">金华</span>
                                            <span class="city_name">衢州</span>
                                            <span class="city_name">舟山</span>
                                            <span class="city_name">台州</span>
                                            <span class="city_name">丽水</span>
                                            <span class="city_name">湖州</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">江西</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">南昌</span>
                                            <span class="city_name">景德镇</span>
                                            <span class="city_name">萍乡</span>
                                            <span class="city_name">九江</span>
                                            <span class="city_name">新余</span>
                                            <span class="city_name">鹰潭</span>
                                            <span class="city_name">赣州</span>
                                            <span class="city_name">吉安</span>
                                            <span class="city_name">宜春</span>
                                            <span class="city_name">抚州</span>
                                            <span class="city_name">上饶</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">福建</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">福州</span>
                                            <span class="city_name">厦门</span>
                                            <span class="city_name">莆田</span>
                                            <span class="city_name">三明</span>
                                            <span class="city_name">泉州</span>
                                            <span class="city_name">漳州</span>
                                            <span class="city_name">南平</span>
                                            <span class="city_name">龙岩</span>
                                            <span class="city_name">宁德</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">湖南</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">长沙</span>
                                            <span class="city_name">株洲</span>
                                            <span class="city_name">湘潭</span>
                                            <span class="city_name">衡阳</span>
                                            <span class="city_name">邵阳</span>
                                            <span class="city_name">岳阳</span>
                                            <span class="city_name">常德</span>
                                            <span class="city_name">张家界</span>
                                            <span class="city_name">益阳</span>
                                            <span class="city_name">郴州</span>
                                            <span class="city_name">永州</span>
                                            <span class="city_name">怀化</span>
                                            <span class="city_name">娄底</span>
                                            <span class="city_name">湘西</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">湖北</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">武汉</span>
                                            <span class="city_name">黄石</span>
                                            <span class="city_name">襄樊</span>
                                            <span class="city_name">十堰</span>
                                            <span class="city_name">宜昌</span>
                                            <span class="city_name">荆门</span>
                                            <span class="city_name">鄂州</span>
                                            <span class="city_name">孝感</span>
                                            <span class="city_name">荆州</span>
                                            <span class="city_name">黄冈</span>
                                            <span class="city_name">咸宁</span>
                                            <span class="city_name">随州</span>
                                            <span class="city_name">恩施</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">河南</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">郑州</span>
                                            <span class="city_name">开封</span>
                                            <span class="city_name">洛阳</span>
                                            <span class="city_name">平顶山</span>
                                            <span class="city_name">焦作</span>
                                            <span class="city_name">鹤壁</span>
                                            <span class="city_name">新乡</span>
                                            <span class="city_name">安阳</span>
                                            <span class="city_name">濮阳</span>
                                            <span class="city_name">许昌</span>
                                            <span class="city_name">漯河</span>
                                            <span class="city_name">三门峡</span>
                                            <span class="city_name">南阳</span>
                                            <span class="city_name">商丘</span>
                                            <span class="city_name">信阳</span>
                                            <span class="city_name">周口</span>
                                            <span class="city_name">驻马店</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">海南</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">海口</span>
                                            <span class="city_name">三亚</span>
                                            <span class="city_name">三沙</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">广东</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">广州</span>
                                            <span class="city_name">深圳</span>
                                            <span class="city_name">珠海</span>
                                            <span class="city_name">汕头</span>
                                            <span class="city_name">韶关</span>
                                            <span class="city_name">佛山</span>
                                            <span class="city_name">江门</span>
                                            <span class="city_name">湛江</span>
                                            <span class="city_name">茂名</span>
                                            <span class="city_name">东沙群岛</span>
                                            <span class="city_name">肇庆</span>
                                            <span class="city_name">惠州</span>
                                            <span class="city_name">梅州</span>
                                            <span class="city_name">汕尾</span>
                                            <span class="city_name">河源</span>
                                            <span class="city_name">阳江</span>
                                            <span class="city_name">清远</span>
                                            <span class="city_name">东莞</span>
                                            <span class="city_name">中山</span>
                                            <span class="city_name">潮州</span>
                                            <span class="city_name">揭阳</span>
                                            <span class="city_name">云浮</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">广西</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">南宁</span>
                                            <span class="city_name">柳州</span>
                                            <span class="city_name">桂林</span>
                                            <span class="city_name">梧州</span>
                                            <span class="city_name">北海</span>
                                            <span class="city_name">防城港</span>
                                            <span class="city_name">钦州</span>
                                            <span class="city_name">贵港</span>
                                            <span class="city_name">玉林</span>
                                            <span class="city_name">百色</span>
                                            <span class="city_name">贺州</span>
                                            <span class="city_name">河池</span>
                                            <span class="city_name">来宾</span>
                                            <span class="city_name">崇左</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">贵州</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">贵阳</span>
                                            <span class="city_name">遵义</span>
                                            <span class="city_name">安顺</span>
                                            <span class="city_name">铜仁</span>
                                            <span class="city_name">毕节</span>
                                            <span class="city_name">六盘水</span>
                                            <span class="city_name">黔西南</span>
                                            <span class="city_name">黔东南</span>
                                            <span class="city_name">黔南</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">四川</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">成都</span>
                                            <span class="city_name">自贡</span>
                                            <span class="city_name">攀枝花</span>
                                            <span class="city_name">泸州</span>
                                            <span class="city_name">德阳</span>
                                            <span class="city_name">绵阳</span>
                                            <span class="city_name">广元</span>
                                            <span class="city_name">遂宁</span>
                                            <span class="city_name">内江</span>
                                            <span class="city_name">乐山</span>
                                            <span class="city_name">南充</span>
                                            <span class="city_name">宜宾</span>
                                            <span class="city_name">广安</span>
                                            <span class="city_name">达州</span>
                                            <span class="city_name">眉山</span>
                                            <span class="city_name">雅安</span>
                                            <span class="city_name">巴中</span>
                                            <span class="city_name">资阳</span>
                                            <span class="city_name">阿坝</span>
                                            <span class="city_name">甘孜</span>
                                            <span class="city_name">凉山</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">云南</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">昆明</span>
                                            <span class="city_name">保山</span>
                                            <span class="city_name">昭通</span>
                                            <span class="city_name">丽江</span>
                                            <span class="city_name">普洱</span>
                                            <span class="city_name">临沧</span>
                                            <span class="city_name">曲靖</span>
                                            <span class="city_name">玉溪</span>
                                            <span class="city_name">文山</span>
                                            <span class="city_name">西双版纳</span>
                                            <span class="city_name">楚雄</span>
                                            <span class="city_name">红河</span>
                                            <span class="city_name">德宏</span>
                                            <span class="city_name">大理</span>
                                            <span class="city_name">怒江</span>
                                            <span class="city_name">迪庆</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">甘肃</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">兰州</span>
                                            <span class="city_name">嘉峪关</span>
                                            <span class="city_name">金昌</span>
                                            <span class="city_name">白银</span>
                                            <span class="city_name">天水</span>
                                            <span class="city_name">酒泉</span>
                                            <span class="city_name">张掖</span>
                                            <span class="city_name">武威</span>
                                            <span class="city_name">定西</span>
                                            <span class="city_name">陇南</span>
                                            <span class="city_name">平凉</span>
                                            <span class="city_name">庆阳</span>
                                            <span class="city_name">临夏</span>
                                            <span class="city_name">甘南</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">宁夏</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">银川</span>
                                            <span class="city_name">石嘴山</span>
                                            <span class="city_name">吴忠</span>
                                            <span class="city_name">固原</span>
                                            <span class="city_name">中卫</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">青海</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">西宁</span>
                                            <span class="city_name">玉树</span>
                                            <span class="city_name">果洛</span>
                                            <span class="city_name">海东</span>
                                            <span class="city_name">海西</span>
                                            <span class="city_name">黄南</span>
                                            <span class="city_name">海北</span>
                                            <span class="city_name">海南</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">西藏</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">拉萨</span>
                                            <span class="city_name">那曲</span>
                                            <span class="city_name">昌都</span>
                                            <span class="city_name">山南</span>
                                            <span class="city_name">日喀则</span>
                                            <span class="city_name">阿里</span>
                                            <span class="city_name">林芝</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="city_container">
                                        <div class="city_container_left"><span class="style_color">新疆</span></div>
                                        <div class="city_container_right">
                                            <span class="city_name">乌鲁木齐</span>
                                            <span class="city_name">克拉玛依</span>
                                            <span class="city_name">吐鲁番</span>
                                            <span class="city_name">哈密</span>
                                            <span class="city_name">博尔塔拉</span>
                                            <span class="city_name">巴音郭楞</span>
                                            <span class="city_name">克孜勒苏</span>
                                            <span class="city_name">和田</span>
                                            <span class="city_name">阿克苏</span>
                                            <span class="city_name">喀什</span>
                                            <span class="city_name">塔城</span>
                                            <span class="city_name">伊犁</span>
                                            <span class="city_name">昌吉</span>
                                            <span class="city_name">阿勒泰</span>
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                </div>
                            </div>
                        </div>
                        <div id="bside_left">
                            <div id="txt_pannel">
                                <h3>使用说明：</h3>

                                <p>1、点击上方[更换城市]按钮选择城市</p>

                                <p>2、在搜索框输入地点关键字进行搜索</p>

                                <p>3、在左侧列表中点击对应地点</p>

                                <p style="color: red;">注意：请不要手动填入[当前地址]，否则无法存入坐标值！</p>
                            </div>

                        </div>
                        <div id="bside_rgiht">
                            <div id="map-container"></div>
                        </div>
                    </div>
                </div>
                <!--获取坐标和地址 end-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="getLocation()">提交更改</button>
            </div>
        </div>
    </div>
</div>
<!--获取坐标和地址模态窗 end-->