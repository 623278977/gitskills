@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none" style="margin-bottom: 5rem;position: relative;padding-bottom: 5rem;">
        <section class="brand-t" style="margin-top: 0">
            <div class="brand-contain no-borderTop">
                <img src="" alt="" id="brand_t_img">
                <div class="brand-right">
                    <h2><span class="color333" id='brand_name'></span><span class="color666"></span></h2>
                    <p>
                        <em class="brand-sort" id="brand_sort">行业分类</em> <strong class="brand-st" id="brand_st">投资额度</strong>
                    </p>
                    <span id = brand-keys></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <div class="">
            <section class="brand-j">
            <!-- <div class="brand-join">
                <div class="join-state">
                    <div class="pl">
                        <div class="t color666 f14">加盟说明</div>
                        <div class="join-state-con color999 f12">
                            16516165
                        </div>
                    </div>
                    
                </div>
                <div class="join-order">
                    <div class="pl">
                        <div class="order-l fl">
                            <p class="color999 f12">如有意向，可以直接下订单提交订金</p>
                            <p class="color999 f12">加盟拓展，先行先得！</p>
                        </div>
                        <div class="order-r fr">
                            <em>￥</em><span>1000</span>
                            <a href="javascript:;" class="btn">交付订金</a>
                        </div>
                         <div class="clearfix"></div>
                    </div>   
                </div>
               
            </div> -->
        </section>  
        <p class="join-b-p">
        请看清交易须知，如有疑问拨打客服热线400-0110-061
        </p>
        </div>
        
    </section>
    
   
@stop

@section('endjs')
    <script>
    Zepto(function () {
        new FastClick(document.body);
       
        var urlPath = window.location.href,
            id = {{$id}},
            uid = labUser.uid; 
            var address = [];
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false; 
        // if (shareFlag) {
        //     $('#brand_getcode').removeClass('none');
        //     $('#brand_realname').val('');
        //     $('#brand_tel').val('');
        //     uid = 0;
        //     $('#p_address').addClass('none');
        // }

        var joinDetail = {
            // 获取品牌详情
            brandDetail:function (id,uid) {
                var param= {};
                param['id'] = id;
                param['uid'] = uid;
                var url = labUser.api_path + '/brand/detail';
                // var url = '/api/brand/detail';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        getBrandDetail(data.message);
                        $('#act_container').removeClass('none');
                    }
                })
            },     
            //获取加盟列表
            listDetail:function (id) {
                var param= {};
                param['brand_id'] = id;
                param['uid'] = uid;
                var url = labUser.api_path + '/brand/today-goods';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        getListDetail(data.message);
                        $('#act_container').removeClass('none');
                    }
                })
            }     
        };
        joinDetail.brandDetail(id,uid); 
        joinDetail.listDetail(id);
        $(document).on('click','.pay-goods',function () {
            var goodsid = $(this).data('pay');
            var type = 'brand';
            buygoods(goodsid, type);
        })
        function getBrandDetail(result) {
            if (!shareFlag) {
                setPageTitle('立即加盟-'+result.brand.name);
            }  
            document.title = '立即加盟-'+result.brand.name;
            var brand = result.brand;
            $('#brand_t_img').attr("src",brand.logo);
            $('#brand_name').text(brand.name);
            $('.brand-right h2 *:nth-child(2)').text('【'+brand.zone_name+'】');
            $('#brand_sort').text(brand.category_name);
            $('#brand_st').text(brand.investment_min+'万元-'+brand.investment_max+'万元');
            for (var i = 0; i < brand.keywords.length; i++) {
                var str='';
                str+='<span class="brand-key">'+brand.keywords[i]+'</span>';
                $('#brand-keys').append(str);
            };
            if (brand.keywords.length==0) {
                $('.brand-key').addClass('none')
            }
        }
        function getListDetail(result) {
            $.each(result,function (i,item) {
                var str='';
                str+='<div class="brand-join">';
                if (item.league!='') {
                    str+='<div class="join-state">';
                    str+='<div class="pl">';
                        str+='<div class="t color666 f14" style="padding-bottom:0.5rem">加盟说明</div>';
                        str+='<div class="join-state-con color999 f12">'+item.league+'</div>';
                    str+='</div>';
                    
                    str+='</div>';
                }
  
                str+='<div class="join-order">';
                    str+='<div class="pl" style="padding-bottom:0.5rem;">';
                        str+='<div class="order-l fl" >';
                           str+=' <p class="color999 f12" style="margin-bottom:0.5rem;">如有意向，可以直接下订单提交定金</p>';
                            str+='<p class="color999 f12">加盟拓展，先行先得！</p>';
                        str+='</div>';
                        str+='<div class="order-r fr">';
                            str+='<em>￥</em><span>'+f(item.price)+'</span>';
                            if (item.num==0) {
                                str+='<a  class="btn pay-out" data-pay="'+item.id+'" style="disabled:true;">已售完</a>';
                            }else{
                                 str+='<a href="javascript:;" class="btn pay-goods" data-pay="'+item.id+'">交付定金</a>';
                            }
                           
                        str+='</div>';
                         str+='<div class="clearfix"></div>';
                    str+='</div>';   
               str+=' </div>';
            str+='</div>';
             $('.brand-j').append(str);
            });
        };
        // 千分位转换
        function f(num) {
            // if (typeof num != 'number')//判断是否是数字
            //     return;
            num += '';
            if (num.indexOf('.') != -1) { //判断是否存在小数 
                return fn(num.split('.')[0]) + '.' + num.split('.')[1];
            } else {
                return fn(num);
            }

            function fn(newNum) {
                var str = '';
                var l = newNum.length;
                while (l > 3) {
                    str = ',' + newNum.substring(l - 3, l) + str;
                    l = l - 3;
                }
                str = newNum.substring(0, l) + str;
                return str;
            }
        }
       
       
    });
   </script>
   <script>	

   		//分享
        // function showShare() {
        //     var title = $('#resultName').text();
        //     var url = window.location.href;
        //     var img = $('.zb_banner>img').attr('src');
        //     var header = '专版';
        //     var content = cutString($('.zb_p span').text(), 18);
        //     shareOut(title, url, img, header, content);
        // };
        // title
        function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        }

   </script>
@stop