
@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="https://cdn.bootcss.com/Swiper/3.3.0/css/swiper.css">
    <link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
        <!--安装app-->
        <div class="app_install none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!-- 品牌的轮播图 -->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                
                </div>
            <div class="swiper-pagination swiper-pagination-fraction"></div>
        </div>
        <!-- 品牌小标题 -->
        <section class="brand-t">
            <div class="brand-contain no-borderTop" id="brand_fav">
                <img src="" alt="" id="brand_t_img">
                <div class="brand-right relative">
                    <h2><span class="color333" id='brand_name'></span><span class="color666"></span></h2>
                    <p>
                        <em class="brand-sort" id="brand_sort">行业分类</em> <strong class="brand-st" id="brand_st">投资额度</strong>
                    </p>
                    <span id = brand-keys></span>
                    <div class="can-join none"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <!-- 品牌信息 -->
        <section class="brand-info" >
            <div class="brand-head">
                <em></em><span>品牌信息</span>
            </div>
            <div class="brand-con">
                <div class="context">
                    <span>店铺数量:</span><div id="brand_shops"></div>
                </div>
                <div class="context">
                    <span>公司摘要:</span><div id="brand_sum"></div>
                </div>
                
                <div class="clearfix"></div>
            </div>
        </section>
        <!-- 项目信息 -->
        <section class="brand-info" style="margin-bottom: 0">
            <div class="brand-head">
                <em></em><span>项目信息</span>
            </div>
            <div class="brand-con">
                <div class="context">
                    <span>项目介绍:</span><div id="brand_intro"></div>
                </div>
                <div class="context">
                    <span>项目优势:</span><div id="brand_advg"></div>
                </div>
                <div class="context">
                    <span>补充说明:</span><div id="brand_more"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <ul class="brand-info-img">
            <ul id="brand_ul">
                
            </ul>
            <li id="brand-loadImg">点击加载更多图片</li>
        </ul>
        <section class="brand-info">
            <div class="brand-head">
                <span>加盟政策</span>
            </div>
            <div class="brand-con">
                <div class="context">
                   <p id="brand_league"></p>
                </div>
            </div>
        </section>
        <section class="brand-info" id="brand_ques">
            <div class="brand-head">
                <span>项目提问</span>
            </div>
            <div class="brand-con">
                <div class="context">
                   <p>对品牌或项目还存着金额、加盟等问题，请提交相关问题，我们会为你提供相应协助</p>
                   <a href="javascript:;" class="btn brand-ques" id="brand-ques">我要提问</a>
                </div>
                <!-- 问答部分，如果没有隐藏 -->
                <div class="brand-qa">
                   
                </div>
            </div>
        </section>
        <section class="brand-info brand-zixun">
            <div class="brand-head">
                <span>品牌资讯</span>
            </div>
            <div id="brand_zx"> </div>
            <div class="seen_more f14" id="brand_morezx" style="border-top:1px solid #ddd;font-size: 1.2rem;">更多相关品牌资讯<span class="sj_icon"></span></div>
        </section>

        <div class="brand-hr">
            <hr><span>你可能感兴趣</span><hr>
        </div>

        <section class="brand-infos brand-pinpai">
            
        </section>
        <div class="seen_more f14 none" id="brand_morepp" data-more='' style="border-top:1px solid #ddd;padding-left: 1rem;font-size: 1.2rem;">发现更多品牌列表<span class="sj_icon"></span></div>

        <div class="brand-btns">
            <div id="brand_bt1">
                <a href="tel:4000110061" id="tel">电话</a>
                <a href="javascript:;" class="leaveMessage">留言</a>
            </div>
            <div id="brand_bt2" class="none">
                <a href="javascript:;" class="leaveMessage ">留言</a>
                <a href="javascript:;" id="brand_loadAPP">下载 无界商圈 应用</a>
            </div> 
            <div id="brand_bt3" class="none">
                <a href="tel:4000110061" id="tel" style="width: 25%">电话 <span id="bt3-l">|</span></a>
                <a href="javascript:;" class="leaveMessage bt3" style="width: 25%">留言</a>
                <a href="javascript:;" class="brand_join">立即加盟</a>
            </div>     
        </div>


        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" maxlength="150" style="resize: none;"
                          placeholder="请输入5-150字的项目问题，请尽量描述"></textarea>
                <button class="fr subcomment f16" id="brand_ask">提交</button>
            </div>
        </div>

	    <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <button class="signup" id="loadapp">下载APP</button>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop

@section('endjs')
    <!-- <script src="{{URL::asset('/')}}/js/zepto/swiper.min.js"></script> -->
    <script src="https://cdn.bootcss.com/Swiper/3.3.0/js/swiper.min.js"></script>
    <script>
   
     var urlPath = window.location.href,
            id = {{$id}},
            uid ={{$uid}};
            // uid = labUser.uid;   
    Zepto(function () {
        new FastClick(document.body);
        window.document.title =$('#brand_name').text(); 
        viewAdd(uid,'brand',id);
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        $('#brand_morepp').data('brandmore',id);  
        $('#brand_morezx').data('zxbrandmore',id);
        $('#tapdiv').on('click',function () {
            $('#commentback').addClass('none')
        });
        $('#comtextarea').on('focus', function () {
            setTimeout(function () {
                var c = window.document.body.scrollHeight;
                window.scroll(0, c);
            }, 500);
            return false;
        });
       
        var inputtext = document.getElementById('comtextarea');
        var submitbtn = document.getElementById('brand_ask');
        inputtext.oninput = function () {
            var text = this.value;
            if(text.length>4){
                submitbtn.style.backgroundColor = '#1e8cd4';
            }
            else{
                submitbtn.style.backgroundColor = '#999';
            }
        }
        /**评论按钮绑定input选中**/
        $("#brand-ques").bind("click", function () {
            if (uid==0) {
                showLogin();
                return false
            }
            $('#commentback').removeClass('none');
            $('.textareacon textarea').focus();
            $('#tapdiv').one('click', function () {
                $('#comtextarea').val('');
                $('#commentback').addClass('none');
                $('#brand_ask').css('backgroundColor','#999');
            });
        });
        // 点击提问提交
        $('#brand_ask').on('click',function () {
            var content = $('#comtextarea').val();
            if (content.length<5||content.length>150) {
                alert('请输入5-150字的项目问题');
                // return false;
                $('#comtextarea').focus();
            }else{
                brandDetail.ask(id,uid,content);
            }
        })
        //留言按钮
        $('.leaveMessage').on('click',function () {
            if (!shareFlag) {
                if (uid==0) {
                    showLogin();
                    return false
                }
            }
            if (shareFlag) {
                 window.location.href=labUser.path+'webapp/brand/note?&pagetag=07-1-1&is_share&id='+id+'&uid='+uid;
            }else{
                 window.location.href=labUser.path+'webapp/brand/note?&pagetag=07-1-1&id='+id+'&uid='+uid;
            }
        });
        // 立即加盟
        $(document).on('click','.brand_join',function () {
            if (uid==0) {
                showLogin();
                return false
            }else{
                window.location.href=labUser.path+'webapp/brand/join/_v020400?&id='+id+'&uid='+uid;
            }
        })
        function shareDetail(result) {
            var selfObj = result.brand;

            // 是否在分享页
            if (shareFlag) {
                    // $('#loadAppBtn').removeClass('none');
                    $('#installapp').removeClass('none');
                    $('#brand_ques').addClass('none');
                    $('#brand_bt1').addClass('none');
                    $('#brand_bt2').removeClass('none');
                    //浏览器判断
                    if (is_weixin()) {
                        /**微信内置浏览器**/
                        $(document).on('tap', '#brand_loadAPP,#openapp', function () {
                            var _height = $(document).height();
                            $('.safari').css('height', _height);
                            $('.safari').removeClass('none');
                        });
                        //点击隐藏蒙层
                        $(document).on('tap', '.safari', function () {
                            $(this).addClass('none');
                        });
                         var wxurl = labUser.api_path + '/weixin/js-config';
                        var desptStr = removeHTMLTag(selfObj.name);
                        var nowhitespace = desptStr.replace(/&nbsp;/g,'');
                        var despt = cutString(desptStr, 60);
                        var nowhitespaceStr =cutString(nowhitespace, 60);
                        ajaxRequest({url: location.href}, wxurl, function (data) {
                            if (data.status) {
                                wx.config({
                                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                    appId: data.message.appId, // 必填，公众号的唯一标识
                                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                    signature: data.message.signature, // 必填，签名，见附录1
                                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                });
                                wx.ready(function () {
                                    wx.onMenuShareTimeline({
                                        title: selfObj.name, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: selfObj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: selfObj.name,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: selfObj.logo,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                        },
                                        cancel: function (res) {
                                            console.log('已取消');
                                        },
                                        fail: function (res) {
                                            console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
                    }
                    else {
                        if (isiOS) {
                            //打开本地a
                            $(document).on('tap', '#openapp', function () {
                                var strPath = window.location.pathname.substring(1);
                                var strParam = window.location.search;
                                var appurl = strPath + strParam;
                                var share = '&is_share';
                                var appurl2 = appurl.replace(/is_share=1/g,'');
                                window.location.href = 'openwjsq://' + appurl2;
                            });
                            /**下载app**/
                            $(document).on('tap', '#brand_loadAPP', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#brand_loadAPP', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp', function () {
                                var strPath = window.location.pathname;
                                var strParam = window.location.search.replace(/is_share=1/g,'');
                                var appurl = strPath + strParam;
                                window.location.href = 'openwjsq://welcome'+ appurl;
                            });
                        }
                    };
                    $(document).on('click', '#brand_morezx,#brand_morepp', function () {
                       alert('请打开app查看');
                    });
                    // $(document).on('click','.brand-pinpai .brand-contain',function () {
                    //     alert('请打开app查看')
                    // })
                      //查看品牌详细信息
                    $(document).on('click','.brand-pinpai .brand-contain',function () {
                        var brandid = $(this).data('id');
                        window.location.href= labUser.path+'webapp/brand/detail/_v020400?id='+brandid+'&is_share=1&pagetag=02-1-2';
                    });
                    //查看品牌资讯详情
                    $(document).on('click','#brand_zx .brand-news',function () {
                        var zxid = $(this).data('id');
                       window.location.href= labUser.path+'webapp/headline/detail/_v020400?id='+zxid+'&is_share=1&pagetag=02-4';
                    })
            }else{
                //查看更多相关品牌
                $(document).on('click', '#brand_morepp', function () {
                    var pp = $(this).data('brandmore');
                    ppbrandMore(pp);
                });
                //查看更多品牌资讯
                 $(document).on('click', '#brand_morezx', function () {
                    var zx = $(this).data('zxbrandmore');
                    var name = $('#brand_name').text();
                    zxbrandMore(zx,name);
                });
                //查看品牌详细信息
                $(document).on('click','.brand-pinpai .brand-contain',function () {
                    var brandid = $(this).data('id');
                    window.location.href= labUser.path+'webapp/brand/detail/_v020400?pagetag=02-1-2&id='+brandid+'&uid='+uid+'';
                });
                //查看品牌资讯详情
                $(document).on('click','#brand_zx .brand-news',function () {
                    var zxid = $(this).data('id');
                   window.location.href= labUser.path+'webapp/headline/detail/_v020400?pagetag=02-4&id='+zxid+'';
                })
                //打电话
                $(document).on('click','#tel',function () {
                    var num = '4000110061';
                    callNum(num);
                })
            }
        }
        //ajax
        var brandDetail = {
            detail:function (id,uid) {
                var param= {};
                param['id'] = id;
                param['uid'] = uid;
                var url = labUser.api_path + '/brand/detail/_v020400';
                // var url = '/api/brand/detail';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        getBrandDetail(data.message);
                        shareDetail(data.message);
                        var swiper = new Swiper('.swiper-container', {
                            pagination : '.swiper-pagination',
                            paginationType : 'custom',
                            autoplay:'5000',
                            paginationCustomRender: function (swiper, current, total) {
                                return '<span class="f16">'+current+'</span>' + ' / ' + total;
                            }
                        });

                        $('#act_container').removeClass('none');
                        //设置title
                        window.document.title =$('#brand_name').text();      
                    }
                })
            },
            ask:function (id,uid,content) {
                var param= {};
                param['id'] = id;
                param['uid'] = uid;
                param['content'] = content;
                var url = labUser.api_path + '/brand/ask';
                // var url = '/api/brand/ask';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        alert('你的提问已发送至无界商圈后台，稍后会有客服跟您联系');
                        $('#comtextarea').val('');
                        $('#commentback').addClass('none');
                        $('#subcomments').css('backgroundColor','#999');
                        return false;

                        
                    }
                })
            },
           
        };
        brandDetail.detail(id,uid);
        function getBrandDetail(result) {
            if (!shareFlag) {
                setPageTitle(result.brand.name);
                //判断底部按钮是否有立即加盟
                if (result.goods) {
                    $('#brand_bt3').removeClass('none');
                    $('#brand_bt1').addClass('none')
                }
            } 
            // 2.4版本品牌是否可加盟图标
            if (result.goods) {
                $('.can-join').removeClass('none')
            }   

            // 顶部的轮播图
            $.each(result.banners,function (i,item) {
                var str='';
                str+='<div class="swiper-slide"><img src="'+item.src+'" alt="" /></div>';
                $('.swiper-wrapper').append(str);
            });
            // 提问回答QA
            $.each(result.questions,function (i,item) {
                var str=['<div class="qa-block">',
                        '<div class="context qa-q ">',
                            '<span >Q：</span><div>'+item.quiz+'</div>',
                        '</div>',
                        '<div class="context  qa-a">',
                            '<span>A：</span><div>'+item.answer+'</div>',
                        '</div>',
                        '<div class="clearfix"></div>',
                    '</div>'].join('');
                $('.brand-qa').append(str);
            });
            //?????品牌信息图片列表
            if (result.brand.detail_images.length<=2) {
                    $('#brand-loadImg').addClass('none');
                }
            $.each(result.brand.detail_images,function (i,item) {
                if(i >=2){
                    return false
                }
                var str='';
                str+='<li><img src="'+item.src+'" alt=""></li>';
                $('#brand_ul').append(str);
            });
            $('#brand-loadImg').on('click',function () {
                $.each(result.brand.detail_images,function (i,item) {
                    if (i>=2) {
                         var str='';
                        str+='<li><img src="'+item.src+'" alt=""></li>';
                        $('#brand_ul').append(str);
                    }
                });
                $(this).addClass('none');
            })
            //品牌资讯列表
            if (result.news.length == 0) {
                $('.brand-zixun').addClass('none');
            }
            $.each(result.news,function (i,item) {
                if(result.news.length<=3){
                    $('#brand_morezx').addClass('none')
                }else if(i>=3){
                    return false
                }
                    var str='';
                    str+=[
                        '<div class="brand-contain brand-news" data-id="'+item.id+'">',
                            '<img src="'+item.logo+'" alt="" >',
                            '<div class="brand-right">',
                                '<h2>'+item.title+' </h2>',
                                '<div class="text-p-wrap" id="p-text">'+item.detail+'</div>',
                            '</div>',
                            '<div class="clearfix"></div>',
                        '</div>'
                    ].join('');
                    $('#brand_zx').append(str);                   
            });
            //相关品牌列表
            $.each(result.brands,function (i,item) {
                if (result.brands.length==0) {
                    var str1='<p class="f14" style="padding:1rem 0">暂无</p>'
                    $('.brand-pinpai').append(str1);
                    return false;
                }else if(result.brands.length<=5){
                    $('#brand_morepp').addClass('none')
                }else if(i>=5){
                    return false;
                };
                var str='';
                    str+='<div class="brand-contain" data-id="'+item.id+'">';
                    str+='<img src="'+item.logo+'" alt="" >';
                    str+='<div class="brand-right relative">';
                    str+='<h2>'+item.name+' <span class="color666">'+'【'+item.zone_name+'】'+'</span></h2>';
                    str+='<p>';
                    str+='<em class="brand-sort">'+item.category_name+'</em> <strong class="brand-st">'+item.investment_min+'万元-'+item.investment_max+'万元'+'</strong>';
                    str+='</p>';
                    str+='<span id ="brand-keys">';
                    if (item.canJoin==1) {
                    	str+='<div class="can-join "></div>';
                    }
                    for (var i = 0; i < item.keywords.length; i++) {
                        str+='<span class="brand-key">'+cutString(item.keywords[i],5)+'</span>';
                    };
                    str+='</span>';
                    str+='</div>';
                    str+='<div class="clearfix"></div>';
                    str+='</div>';
                   $('.brand-infos').append(str); 
            })
            var brand = result.brand;
            $('#brand_fav').data('favourite',result.relation.is_favorite);
            var fav = $('#brand_fav').attr('data-favourite');
            // console.log(fav)
            var flag = result.relation.is_favorite;
            if (!shareFlag) {
                if (flag==1) {
                    setFavourite(1);
                }else{
                    setFavourite(0);
                }
            }
            $('#brand_t_img').attr("src",brand.logo);
            $('#brand_name').text(brand.name);
            $('.brand-right h2 *:nth-child(2)').text('【'+brand.zone_name+'】');
            $('#brand_sort').text(brand.category_name);
            $('#brand_st').text(brand.investment_min+'万元-'+brand.investment_max+'万元');
            for (var i = 0; i < brand.keywords.length; i++) {
                var str='';
                str+='<span class="brand-key">'+cutString(brand.keywords[i],5)+'</span>';
                $('#brand-keys').append(str);
            };
            $('#brand_shops').html(brand.shops_num);
            $('#brand_sum').html(brand.summary);
            $('#brand_intro').html(brand.introduce);
            $('#brand_advg').html(brand.superiority);
            brand.supply = brand.supply == ''?'暂无':brand.supply;
            $('#brand_more').html(brand.supply);
            $('#brand_league').html(brand.league);
        }; 
    });
   </script>
   <script>	
        function collect(id,uid,type) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            param['type'] = type;
            var url = labUser.api_path + '/brand/collect';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    console.log('fav');
                }else{
                    // alert(data.message);
                    return false
                }
            });
        }
   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var title = $('#brand_name').text();
            var url = window.location.href;
            var img = $('#brand_t_img').attr('src');
            var header = '品牌';
            var content = cutString($('#brand_sum').text(), 18);
            shareOut(title, url, img, header, content);
        };
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
         //收藏
        function favourite() {
            var fav = $('#brand_fav').attr('data-favourite');
            if (fav == 0) {
                collect(id,uid,'do');
                setFavourite(1);
                $('#brand_fav').data('favourite',1);
            }
            else {
                collect(id,uid,'undo');
                setFavourite(0);
                $('#brand_fav').data('favourite',0);
            }
        }
        //查看更多品牌
        function ppbrandMore(id) {
            if (isAndroid) {
                javascript:myObject.ppbrandMore(id);
            } else if (isiOS) {
                var data = {
                    "moreId": id,
                }
                window.webkit.messageHandlers.ppbrandMore.postMessage(data);
            }
        }
        //查看更多资讯
        function zxbrandMore(id,name) {
            if (isAndroid) {
                javascript:myObject.zxbrandMore(id,name);
            } else if (isiOS) {
                var data = {
                    "moreId": id,
                    "moreName":name
                }
                window.webkit.messageHandlers.zxbrandMore.postMessage(data);
            }
        }
        //打电话
        function callNum(num){
            if (isAndroid) {
                javascript:myObject.callNum(num);
            } else if (isiOS) {
                var data = {
                    "num": num
                }
                window.webkit.messageHandlers.callNum.postMessage(data);
            }
        }
        //swiper幻灯片
        function swipers() {
            var swiper = new Swiper('.swiper-container', {
                pagination : '.swiper-pagination',
                paginationType : 'custom',
                // autoplay:'2000',
                paginationCustomRender: function (swiper, current, total) {
                    return '<span class="f16">'+current+'</span>' + ' / ' + total;
                }
            });
        }
       
      
       
   </script>
@stop