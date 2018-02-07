@extends('layouts.default')
@section('css')
@stop
@section('main')
    <section class="tc none" id="container">
        <div class="contract">
        </div>
        <div class="f24 colorf4" style="margin-top: -1rem;">邀请码</div>
        <div class="mt2 contract_num ui-border-radius" id="num"></div>
        <div class="mt2 c8a f14">长按复制数字</div>
        <div class="contract_btn f18 mt2 ui-border-radius">立即下载APP</div>
        <p class="c8a mt2 f16">若按钮失效，请点击右上角菜单</p>
        <p class="c8a f16">选择使用浏览器打开</p>
        <div class="safari none"><img src="{{URL::asset('/')}}/images/safari.png" style="left: 0;"></div>
    </section>
@stop
@section('endjs')
    <script>
        Zepto(function () {
            var arg = getQueryStringArgs(),
                    code = arg['code'] || 86665789;
            document.getElementById('num').innerHTML = code;
            $('#container').removeClass('none');
            //下载、打开事件
            if (is_weixin()) {
                /**微信内置浏览器**/
                $(document).on('tap', '.contract_btn', function () {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                //点击隐藏蒙层
                $(document).on('tap', '.safari', function () {
                    $(this).addClass('none');
                });
            }
            else {
                if (isiOS) {
                    $(document).on('tap', '.contract_btn', function () {
                        window.location.href = 'https://itunes.apple.com/app/id981501194';
                    });
                }
                else if (isAndroid) {
                    $(document).on('tap', '.contract_btn', function () {
                        window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                    });
                }
            }
        });
    </script>
@stop