@extends('layouts.news')
@section('css')
    <style>
        .ui-scroller {width:auto;height:300px;margin:20px;padding:10px;overflow:hidden;border:1px solid #ccc;}
        .ui-scroller li {margin-bottom:10px;}
    </style>
@stop
@section('main')
    <section class="none" id="cons">
    <header class="ui-header ui-header-positive ui-border-b">
        <i class="ui-icon-return" onclick="history.back()"></i>
        <h1>选项卡 tab</h1>
        <button class="ui-btn">回首页</button>
    </header>
    <footer class="ui-footer ui-footer-btn f16">
        <ul class="ui-tiled ui-border-t">
            <li data-href="index.html" class="ui-border-r">
                <div>基础样式</div>
            </li>
            <li data-href="ui.html" class="ui-border-r">
                <div>UI组件</div>
            </li>
            <li data-href="js.html" class="ui-border-r">
                <div>JS插件</div>
            </li>
            <li data-href="help.html" id="btn1">
                <div>帮助</div>
            </li>
        </ul>
    </footer>
    <section class="ui-container">
        <!--
        <section id="tab">
            <div class="demo-item">
                <p class="demo-desc">标签栏</p>
                <div class="demo-block">
                    <div class="ui-tab">
                        <ul class="ui-tab-nav ui-border-b f14">
                            <li class="current">热门推荐</li>
                            <li>全部表情</li>
                            <li>表情</li>
                        </ul>
                        <ul class="ui-tab-content" style="width:300%">
                            <li>
                                <div class="ui-arrowlink">箭头链接</div>
                                <div class="ui-arrowlink ui-border">
                                    <p>第一回</p>
                                    <p>第二回</p>
                                    <p>第仨回</p>
                                    <p>第四回</p>
                                </div>
                                <div class="ui-arrowlink">箭头链接</div>
                                <div class="ui-arrowlink">箭头链接</div>
                            </li>
                            <li>
                                <div class="ui-border-b">下边框</div>
                                <div class="ui-border-b">下边框</div>
                                <div class="ui-border-b">下边框</div>
                                <div class="ui-border-b">下边框</div>
                            </li>
                            <li>
                                <div class="ui-border-radius">圆角</div>
                                <div class="ui-border-radius">圆角</div>
                                <div class="ui-border-radius">圆角</div>
                            </li>
                        </ul>
                    </div>
                </div>
                <script class="demo-script">
                </script>
            </div>
        </section>
        -->
        <div class="ui-slider">
            <ul class="ui-slider-content" style="width: 300%">
                <li class="current"><span style="background-image:url('/images/live.png')"></span></li>
                <li><span style="background-image:url('/images/live.png')"></span></li>
                <li><span style="background-image:url('/images/live.png')"></span></li>
            </ul>
        </div>
        <div class="ui-scroller">
            <ul>
                <li>1、活动时间：2014.09.25 - 2014.10.31</li>
                <li>2、活动面向“预付费（Q点Q币、QQ卡、财付通/银行卡）开通超级QQ”的用户。以下支付方式的用户不 在本次活动范围内，“同时开通预付费超级QQ和短信版超级QQ”、“同时开通预付费超级QQ与短信版 会员”、“同时开通预付费超级QQ与iOS会员”、“开通短信版超级QQ”及“宽带/固定电话/超级/”（相关活动可留意超级QQ官网消息）。</li>
                <li>3、活动中，成长值的转移规则：① QQ会员成长值 = 超级QQ成长值 -（超级QQ成长值/超级QQ成长速度）*（超级QQ成长速度 - 同条件下会员成长速度）② 若您在转移前同时开通了超级QQ和QQ会员，转移后会员成长值在上述成长值（超Q转换而来）与原会员成长值间取较高者。较低部分，转换成等值的QQ会员加倍成长卡赠送给您。</li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
                <li>4、活动时间：2014.09.25 - 2014.10.311、活动间2014.09.25 - 2014.10.31、活动时间：  2014.09.25 -  活动时间活动时间活动时间  </li>
            </ul>
        </div>
    </section>
    </section>
@stop
@section('endjs')
    <script>
        Zepto(function(){
            var el=$.loading({
                content:'加载中...',
            });
            setTimeout(function(){
                el.loading("hide");
            },5000);
//        el.on("loading:hide",function(){
//            console.log("loading hide");
//        });
            var param = {};
            param["id"] = '331';
            param["uid"] = '183768';
            param["position_id"] = '0';
            param["maker_id"] = '18';
            var url = labUser.api_path + '/activity/detail';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    setTimeout(function(){
                        $('#cons').removeClass('none');
                        el.loading('hide');
                    },3000);
                }
            });
            $("#btn1").tap(function(){
                var dia=$.dialog({
                    title:'温馨提示',
                    content:'温馨提示内容',
//                    button:["确认","取消"]
                    button:["确认"]
                });

                dia.on("dialog:action",function(e){
                    console.log(e.index)
                });
                dia.on("dialog:hide",function(e){
                    console.log("dialog hide")
                });

            });

        });
//        (function() {
//            var scroll = new fz.Scroll('.ui-scroller', {
//                scrollY: true
//            });
//            scroll.scrollTo(0, 0);
//            // 若 offsetX 和 offsetY 都是 true，则滚动到元素位于屏幕中央的位置；
//            //scroll.scrollToElement("li:nth-child(3)", 1000, true, true);
//        })();
        (function(){
            var slider = new fz.Scroll('.ui-slider', {
                role: 'slider',
                indicator: true,
                autoplay: true,
                interval: 3000
            });
            slider.on('beforeScrollStart', function(from, to) {
                //console.log(from, to);
            });
            slider.on('scrollEnd', function(cruPage) {
                //console.log(curPage);
            });
        })();
//        (function() {
//            var tab = new fz.Scroll('.ui-tab', {
//                role: 'tab',
//                autoplay: true,
//                interval: 3000
//            });
//            /* 滑动开始前 */
//            tab.on('beforeScrollStart', function(fromIndex, toIndex) {
//                // console.log(fromIndex, toIndex); // from 为当前页，to 为下一页
//            })
//        })();
    </script>
@stop