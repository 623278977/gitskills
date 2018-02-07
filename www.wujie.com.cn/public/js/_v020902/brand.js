
$('body').addClass('bgcolor');
new FastClick(document.body);
// FastClick.attach(document.body)
 var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
    var idArr = [];

Zepto(function() {
    var brandDetail = {
         // 获取红包信息
        awardDetail:function(id,uid){
            var param = {};
                param['brand_id'] = id;
                param['uid'] = uid;
            var url = labUser.api_path + '/brand/get-brand-redpacket/_v020900';
            ajaxRequest(param,url,function(data){
                if(data.status ){
                    $('#packet_total').text(data.message.total)
                        if(data.message.redpacket && data.message.redpacket.length >0){
                            var pacHtml = '';
                            $.each(data.message.redpacket,function(i,j){
                                if(j.status == 0){
                                    idArr.push(j.id);
                                }
                                pacHtml += '<li class="envelopes"><div class="color999"><div class="f13 l  fund_num">';
                                if(j.type == 1){
                                    pacHtml += '<p class="mb0">￥<span class="f24">'+parseInt(j.amount)+'</span></p><p class="mb0">全场无条件红包</p></div>';
                                    pacHtml += '<div class="l fund_type"><p class="mb05 f18 ">不限品牌</p><p class="mb0 f13 colorccc">加盟抵扣券</p></div>';
                                }else{
                                    pacHtml += '<p class="mb0">￥<span class="f24">'+parseInt(j.amount)+'</span></p><p class="mb0">品牌专享红包</p></div>';
                                    pacHtml += '<div class="l fund_type"><p class="mb05 f18 ">'+cutString(j.brand_name,10)+'</p><p class="mb0 f13 colorccc">加盟抵扣券</p></div>';
                                }           
                                pacHtml += '</div></li>';
                            })
                            $('#packet_list').html(pacHtml);
                        };  
                        fadeBrand('.brand-packet', 'a-bouncein');
                    }else if( data.message == 'no_redpacket'){
                       tips('你来晚了,红包已领完')
                    }else if(data.message == 'all_received'){
                        tips('你已经领取过了')
                    }
            })
        },
        //领取红包
        award: function(ids, uid) {
            var param = {};
            param['redpacket_ids'] = ids;
            param['uid'] = uid;
            param['soure'] = '3';
            var url = labUser.agent_path + '/user/custom-receive-redpacket/_v010300';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    $('.packet-body').addClass('none');
                    $('.packet_v29').css('marginLeft','-17.9rem')
                    fadeBrand('.packet_style', 'a-fadeinT');
                    $('#brand_award').attr('data-statu', true);
                }else{
                    tips('领取失败');
                }
            })
        },
        //品牌的详情信息获取
        detail: function(id, uid) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            param['is_send_cloud_info'] = '1';//表示是否给经纪人发消息
            var code = $('#brand_name').attr('data-code');
            var url = labUser.api_path + '/brand/detail/_v020902';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    shareDetail(data.message);
                    getDetail(data.message);
                    var imgs = data.message.brand.detail_images;
                    // var arr = [{id:'1',src:'a'},{id:300,src:'c'},{id:'20',src:'b'}];
                    // var tt= 20
                    // console.log(arr.findIndex(function(value, index, arr){
                       
                    //     return value.id == tt;
                    // }))
                    $('#product_imgs li').click(function(){
                        var index = $(this).index();              
                        viewBigpic('product',imgs,index);
                    });
                    $('#store_imgs li').click(function(){
                        var index = $(this).index();             
                        viewBigpic('store',data.message.store_img,index)
                    });
                    $('#products_class li').click(function(){
                        var id= $(this).attr('data-id');
                        var index=imgs.findIndex(function(value, index, arr){       
                            return value.id == id;
                        });         
                        viewBigpic('product',imgs,index);
                    });
                    favourite(data.message.relation); 
                   
                }else{
                    $('#bottom_public').remove();
                    $('#brand_detail').html('<div class="tc color999 mt5 f16">'+data.message+'</div>').removeClass('none');
                }
            })
        },
        //获取品牌的浏览记录
        history:function (id,uid,brand) {
            var param = {};
            param['relation_id'] = id;
            param['uid'] = uid;
            param['relation']=brand;
            var url = labUser.api_path + '/user/add-browse/_v020400';
            ajaxRequest(param,url,function (data) {
                if (data.status) {
                    console.log('yes');
                }
            })
        },
        //品牌的留言意向
        message: function(id, uid, mobile, realname, consult, type, content_type) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            param['mobile'] = mobile;
            param['realname'] = realname;
            param['consult'] = consult;
            param['type'] = type;
            // param['share_mark'] = share_mark;
            param['intent_type'] = content_type;
            var url = labUser.api_path + '/brand/message/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    var obj={
                            'type':'brand',
                            'id':id
                        }
                     tips('提交成功');
                        $('.fixed-bg').addClass('none');
                        $("input[name='realnames']").val('');
                        $("input[name='phones']").val('');
                        $("textarea[name='consult']").val('');
                        $("textarea[name='consults']").val('');
                        $('.brand-message').css('z-index', '-1');
                        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
                    onEvent('brand_detail_intent_submit','',obj);
                } else {
                    tips(data.message);
                    return false;
                }
            })
        }
    };
    brandDetail.detail(id, uid);
    // 公用部分
    var mySwiper2 = null,mySwiper3 = null;
    // 领取红包
    $(document).on('click', '#brand_award', function() {
        if(shareFlag){
            window.location.href = labUser.path + '/webapp/getpacket/detail/_v020902?id='+id;
        }else{
            var statu = $(this).attr('data-statu');
            if (statu) {
                tips('您已经领取过了');
                return false
            } else {
                brandDetail.awardDetail(id, uid);  
            }
        }
        
    });
    //点击立即领取
    $(document).on('click','#receive',function(){
        var ids = idArr.join(',')
        brandDetail.award(ids, uid); //领取红包的功能
    })
     //红包的关闭按钮
    $(document).on('click tap', '#packet_close', function() {
            $('.brand-packet').removeClass('a-bouncein').addClass('a-bounceout');
            $('.fixed-bg').addClass('none');
            setTimeout(function() {
                $('.brand-packet').removeClass('a-bounceout').addClass('none');
                $('.packet_style').removeClass('a-fadeinT');
            }, 800);
        })

// 红包使用说明
    $(document).on('click','.toFound',function () {
        window.location.href= labUser.path + 'webapp/protocol/venture/_v020900?pagetag=025-3';
        return false;
    });

    //分享出去的标语
    $(document).on('click ', '.share-li', function() {
        // var text =$(this).text();
        $(this).addClass('share-li-sel');
        $('.share-title').addClass('none');
        showShare();
        $('.fixed-bg').addClass('none');
        $(this).removeClass('share-li-sel');
    });

    //点击相关活动跳转
    $(document).on('click','.toAct',function(){
        var actID =$(this).attr('data_id');
        window.location.href = labUser.path +'webapp/activity/detail/_v020900?id='+actID+'&pagetag=02-2&uid='+uid + shareUrl;
    })
        // 按钮点击查看公司详情
    $(document).on('click', '#company_details', function() {
            window.location.href = labUser.path + 'webapp/brand/company/_v020900?id=' + id + '&uid=' + uid + shareUrl;
        })
   
    // function bounceInDown(){
    //     $('.fixed-bg').removeClass('none')
    //      $('#consult_type').removeClass('none').addClass('bounceInDown')
    //         setTimeout(function(){
    //             $('#consult_type').removeClass('bounceInDown');
    //         }, 500);
    // }
        //APP的发送加盟意向点击弹出
    $(document).on('click', '#brand_suggest', function() {
        // var pinpai = $('#pinpai'); //支持品牌加盟
        // var qudao = $('#qudao'); //支持渠道加盟 
        var brandname= $('#brand_name').text();
        customerService(id,brandname);
    });
    //选中加盟方式
    // $(document).on('click','.schemes li',function(){
    //     $(this).addClass('choosen');
    //     $(this).siblings('li').removeClass('choosen');
    // })
    //想想再说
    // $(document).on('click','#wait_more',function(){
    //     $('#consult_type').addClass('bounceOutUp');
    //     $('#consult_type').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
    //         $('.fixed-bg').addClass('none');
    //         $('#consult_type').addClass('none').removeClass('bounceOutUp'); 
    //     })
    // })
    //现在咨询
    // $(document).on('click','#confirm_consul',function(){
    //     var brandname =$('#brand_name').text();
    //     customerService(id,brandname);
    // })
    //分享出去的加盟意向点击弹出
    $(document).on('click', '.brand-share-ask ', function() {
        var type = $(this).attr('data-type');
        $('.brand-message').css('z-index', '199').data('type',type);
        fadeBrand('.brand-message-share', 'a-fadeinT');
        $('.brand-message-share').css('top',0);
         _czc.push(﻿["_trackEvent",'','brand_detail_intent']);  
       
    });
    // 点击蒙层关闭
    $(document).on('click', '.fixed-bg', function() {
        $(this).addClass('none');
        $('.lookBigPic').addClass('none');
        // $('#consult_type').addClass('none').removeClass('bounceOutUp'); 
        if(mySwiper2){
            mySwiper2.destroy(false);
        }
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');//提交意向框 消失
        $('#commentback').addClass('none');//提问框消失
        $('.share-reset').click();
        $('#packet_close').click();
        $('.share-title').addClass('none').removeClass('a-fadeinB');
    });
   
    // }
    //查看全部评价
    $(document).on('click','.toAlljudge',function(){
        if(shareFlag){
            tips('请至App中查看');
        }else{
             window.location.href = labUser.path + 'webapp/brand/allchat/_v020900?id='+id+'&uid='+uid;
        }
    })

    //点击项目问答跳到问答详情页(2.7新增.my-ques)
    $(document).on('click', '#brand_toquestion', function() {
        // var b_id = $(this).attr('data-id');
        onEvent('brand_detail_question','',{'type':'brand','id':id});   
        window.location.href = labUser.path + 'webapp/brand/questions/_v020900?id=' + id + '&uid=' + uid +'&pagetag=025-1'+shareUrl;
    });

    //点击我要提问
    $(document).on('click','.my-ques',function(){
        if(shareFlag){
            tips('请至App完成提问');
        }else{
            fadeBrand('#commentback','a-fadeinB');
        }
    })
    //提问
    $(document).on('click','#subcomments',function(){
        var con = $('#comtextarea').val();
        var param ={
            'id':id,
            'uid':uid,
            'content':con
        };
        var url =labUser.api_path + '/brand/ask';
        if(con.length<5 || con.length >150){
            tips('请输入5-150字的项目问题');
            $('#comtextarea').focus();
        }else{
            ajaxRequest(param,url,function(data){
                if(data.status){
                    tips(data.message);
                    $('.fixed-bg').addClass('none');
                    $('#commentback').addClass('none');
                    $('#comtextarea').val('');
                }
            })
        };
    });
    var comObj= document.getElementById('comtextarea'), submitbtn = document.getElementById('subcomments');
     comObj.oninput = function () {
                var text = this.value;
                if (text.length > 4) {
                    submitbtn.style.backgroundColor = '#ff5a00';
                }
                else {
                    submitbtn.style.backgroundColor = '#999';
                }
            }
    //输入框
    $('#comtextarea').on('focus', function () {
        setTimeout(function () {
            var c = window.document.body.scrollHeight;
            window.scroll(0, c);
        }, 500);
        return false;
    });
        //意向留言
    $(document).on('click', '.send-mes', function() {
        var phone = $("input[name='phone']").val(),
            realname = $("input[name='realname']").val(),
            consult = $("textarea[name='consult']").val();
        var type = urlPath.indexOf('is_share') > 0 ? 'html5' : 'app';
        var share_mark = $('#brand_name').attr('data-mark');
        // var code = $('#brand_name').attr('data-code');
        if (phone == '' || realname == '' || consult == '') {
            tips('请填写完整');
            return false;
        }
        brandDetail.message(id, uid, phone, realname, consult, type, share_mark,'intent');
    });
    $(document).on('click', '.share-send-mes', function() {
        var phone = $("input[name='phones']").val(),
            realname = $("input[name='realnames']").val(),
            consult = $("textarea[name='consults']").val();
        type = urlPath.indexOf('is_share') > 0 ? 'html5' : 'app';
        content_type = $('.brand-message-share').attr('data-type');
        if (phone == '' || realname == '' || consult == '') {
            tips('请填写完整');
            return false;
        }
        brandDetail.message(id, 0, phone, realname, consult, type,content_type);
        _czc.push(﻿["_trackEvent",'','brand_detail_intent_submit']); 

    })
//提交意向加盟时获取用户详情
    function getUserdetail(uid,user_id){
        var param={};
            param['uid']=uid;
            param['user_outh']=user_id;
        var url=labUser.api_path+'/user/getuserdetail';
        ajaxRequest(param,url,function(data){
            if(data.status){
                var telnum=data.message[0].username||'';
                var nickname=data.message[0].nickname||'';
                $('input[name="realname"]').val(nickname);
                $('input[name="phone"]').val(telnum);
            }
        })
    }
    getUserdetail(uid,uid);
    
    // brandDetail.awardDetail(id,uid,'onload');
    brandDetail.history(id,uid,'brand');

    function getDetail(result) {
        var brand = result.brand,comment = result.comment;
        
        // 顶部的轮播图
            $.each(result.banners, function(i, item) {
            var str = '';
                str += '<div class="swiper-slide"><img src="' + item.src + '" alt="" /></div>';
                $('.swiper-brand').append(str);    
            });
            var mySwiper = new Swiper('#swiper-container1',{//子swiper
                        loop:true, //可循环切换
                        // nested:true, //用于嵌套相同方向的swiper时，当切换到子swiper时停止父swiper的切换。
                        // resistanceRatio: 0, //抵抗率,值越小抵抗越大越难将slide拖离边缘，0时完全无法拖离。
                        // slidesPerView : 'auto',
                        // loopedSlides :1,
                        pagination : '.swiper-pagination', 
                        paginationType : 'custom',
                        paginationCustomRender: function (swiper, current, total) {
                                    return '<span class="f24">'+current+'</span>' + '<em class="f16"> / ' + total+'</em>';
                            },
                        onInit: function(swiper){
                                var swiperDom = $('#swiper-container1 .swiper-wrapper');
                                var wrapperwidth = swiperDom.css('width');
                                swiperDom.css({'transform': 'translate3d(-'+wrapperwidth+', 0px, 0px)'});
                            }
                    })   
        document.title = brand.name; //页面标题
        (brand.agency_way.area == '1'|| brand.agency_way.channel == '1') ? '' : $('#join_type_box').remove(); 
        // brand.agency_way.single == '1' ? $('#dandian').removeClass('none'):'';
        brand.agency_way.area == '1'? '' : $('#pinpai').remove();
        brand.agency_way.channel == '1'? '' : $('#qudao').remove();
        $('#brand_name').text(brand.name);
        $('#brand_award').data('fetch', brand.fetched_fund);
        if(brand.red_packet == '0'){
            $('#brand_award').addClass('none');
        }
        $('.zhuan').text(brand.share_num > 9999 ? '9999+' : brand.share_num);
        $('.fav').text(brand.favorite_count > 9999 ? '9999+' : brand.favorite_count);
        $('.view').text(brand.click_num > 9999 ? '9999+' : brand.click_num);
        
        if(brand.company){
            $('#company_name').html('<em class="integrity">诚信认证</em><em class="brand_company">'+brand.company+'</em>').attr('data-type','1');
        }else{
            $('#company_name').text(brand.name).attr('data-type','0');
        }
        $('#solgan').text(brand.slogan);//标语
        $('#industry_class').text(brand.category_name);//行业分类
        if(brand.join_area){
            $('#join_area span').eq(1).text(brand.join_area);//加盟区域
            $('#join_area').removeClass('none');//加盟区域
        }
        if(brand.store_area){
            $('#shop_area span').eq(1).text(brand.store_area);//店铺面积
            $('#shop_area').removeClass('none');//店铺面积
        }
        if(brand.contract_deadline){
            $('#contract_period span').eq(1).text(brand.contract_deadline);//合同期限
            $('#contract_period').removeClass('none');//合同期限
        }
        $('#brand_investment').html('<em class="color-red">'+brand.investment_min + ' ~ ' + brand.investment_max + '</em> 万元');
        if (brand.slogan!=='') {
            $('#brand_sort2').removeClass('none');
        }
        var str = '';
        for (var i = 0; i < brand.products.length; i++) {
            str += ' '+brand.products[i];
        }
        $('#brand_products').html(cutString(str,22));
        $('#shop_num').text(brand.shops_num+'家');
        //盈利预估
        if(brand.initial_investment){
            $('#initial_investment').text(brand.initial_investment);//初始投资总额
            $('#single_customer_price').text(brand.single_customer_price);//客单价
            $('#day_flow').text(brand.day_flow);//日客流量
            $('#month_sales_mount').text(brand.month_sales_mount);//月销售额
            $('#margin_rate').text(brand.margin_rate);//毛利率
            $('#return_period').text(brand.return_period);//回报周期
            $('#profit').removeClass('none');
        }
       
       
    //主打产品
        if(brand.detail_images && brand.detail_images.length >0){
            var pro_img=''; 
            var pro_name = '',pro_class = ''
            $.each(brand.detail_images,function(i,j){
                if(i<2){
                    pro_img +='<li><img src="'+j.src+'" data-index = "'+i+'" data-intro ="'+j.introduce+'""></li>';
                };        
            })
            $('#product_imgs').append(pro_img);
            $('#product').removeClass('none');

            //产品分类
            if(brand.classify_detail_images && brand.classify_detail_images.length > 0){
                var proClass = '';
                $.each(brand.classify_detail_images,function(i,j){
                    proClass += ' <div class="fline lh45 b" style="padding-left: 1.33rem;">'+i+'</div>';
                    if(j.length > 0){
                        proClass += '<ul class="color666 products lh45">';
                        $.each(j,function(index,item){
                            proClass +='<li class="bline rline" data-id="'+item.id+'">'+item.goods_name+'</li>'
                        })
                        proClass +='</ul><div class="clearfix"></div>';
                    }       
                })
                $('#products_class').html(proClass);
            }else{
                $('#products_class').remove();
            }
            
        }
        
    //门店实景
        if(result.store_img && result.store_img.length >0){
             var sto_img=''; 
            $.each(result.store_img,function(i,j){
                if(i<6){
                    sto_img +='<li><img src="'+j.url+'" data-index = "'+i+'" data-intro ="'+j.introduce+'""></li>';
                }         
            })
            $('#store_imgs').append(sto_img);
            $('#stores').removeClass('none');
        }
    //品牌视频
        if(result.videos && result.videos.length >0){
             var vod_img=''; 
            $.each(result.videos,function(i,j){
                vod_img +='<li data-id="'+j.id+'"><img src="'+j.image+'" data-index = "'+i+'"></li>';
            })
            $('#video_imgs').append(vod_img);
            $('#brand_video').removeClass('none');
        }

    //评价
       if(comment){
            var judgeHtml ='';
            judgeHtml += '<div class=" fline lh45"><span class="tleft f16w ">评价</span><span class="color666 f12 ml05"> ('+brand.comments+')</span>';
            judgeHtml += '</div><div><p class="mt1-33"><img src="'+comment.avatar+'" alt="" class="judger_head mr1-33"><span class="f16">'+comment.nickname+'</span></p>';
            judgeHtml +='<p class="f12 color8a">'+comment.content+'</p></div>';
            judgeHtml += '<div class="tf fline tc lh45 "><button class="toAlljudge  border-ffa">查看全部评价</button></div>';
            $('#brand_judge').html(judgeHtml);
       }else{
            $('#brand_judge').parent('div').remove();
       };

        //项目问答
        if (result.questions.length == 0) {
            $('.ques-asks').addClass('none');
            $('.tf').removeClass('fline')
            $('#brand_toquestion').removeClass('none');
            if (shareFlag) {
                $('#brand_question').addClass('none');
            }
        }else{
            $('#brand_ques').text(result.questions[0].quiz);
            $('#brand_ans').text(result.questions[0].answer);
        }
        
        $('#brand_name').attr('src', brand.logo);

        //相关活动
        if(result.activity.length>0){
            var actHtml = '';
            $.each(result.activity,function(index,item){
                actHtml+=' <div class="fline toAct" data_id="'+item.id+'"><div class="l act_img ">';
                if(item.activity_status == 1){
                    actHtml += '<button class="f14 act_ing "><span></span>报名中';
                }else{
                    actHtml +=' <button class="f14 act_over ">活动已结束';
                };
                actHtml +='</button><img src="'+item.list_img+'" alt="" style="height:100%"></div>';
                actHtml +=' <div class="act_intro"> <p class="f16 mb0 b">'+cutString(item.subject,22)+'</p>';
                actHtml += ' <p class="f12 color8a mb0">开始时间：<span>'+unix_to_datetime(item.begin_time)+'</span></p>';
                var sHtml = '';
                if(item.zone_name && item.zone_name.length > 0){
                    $.each(item.zone_name,function(i,j){
                        if(i <=2){
                            sHtml += j+' ';
                        }else if(i == 3){
                            sHtml +='...';
                        }
                    })
                };
                actHtml += '<p class="f12 color8a">活动场地：<span>'+sHtml+'</span></p></div>';
                actHtml +='<img src="/images/more_icon.png" class="to_act"><div class="clearfix"></div></div>';
            });
            $('.brand_act').html(actHtml);
            $('.rel_activity').removeClass('none');
        }

        $('#brand_detail').removeClass('none');
        if (!shareFlag) {
                $('#brand_btns_app').removeClass('none');
            }
       
    };

   function viewBigpic(type,imgArr,index){

    var imgHtml = '',introHtml='';
        if(type == 'product'){
            $.each(imgArr,function(i,j){
                imgHtml += '<div class="swiper-slide"><img src="'+j.src+'" style="max-height: 100%;max-width:100%;"></div>';
                introHtml +='<div class="swiper-slide swiper-no-swiping"><p class="f16">'+j.goods_name+'</p><p class="f12 color666">'+(j.introduce || '暂无描述')+'</p></div>';
            });
            $('#product_swiper').html(imgHtml).css('display','flex');
            $('#swiper3 .swiper-wrapper').html(introHtml);
        }else{
             $.each(imgArr,function(i,j){
                imgHtml+='<div class="swiper-slide"><img src="'+j.url+'" style="max-height: 100%;max-width:100%;"></div>';
                introHtml +='<div class="swiper-slide swiper-no-swiping">'+(j.introduce || '暂无描述')+'</div>';
            });
            $('#product_swiper').html(imgHtml).css('display','flex');
            $('#swiper3 .swiper-wrapper').html(introHtml);
        }
         mySwiper2 = new Swiper('#swiper3',{
            initialSlide :index,
            autoHeight:true,
            effect : 'fade',
            fade: {
              crossFade: true,
            },
            noSwiping : true,
            observer:true,
            observeParents:true,
            threshold : 50,//设置最短拖动距离 
        }); 
        mySwiper1 = new Swiper('#swiper2',{
            initialSlide :index,
            // autoResize:true,
            // effect:'cube',
            prevButton:'.swiper-prev-btn',
            nextButton:'.swiper-next-btn',
            observer:true,
            observeParents:true,
            threshold : 50,//设置最短拖动距离
            pagination : '#bigPic_pag',
            paginationType : 'custom',
            paginationCustomRender: function (swiper, current, total) {
                        return '<span class="f24">'+current+'</span>' + '<em class="f16"> / ' + total+'</em>';
                },
            onSlideChangeEnd: function(swiper){
              var i = swiper.activeIndex;
              mySwiper2.slideTo(i,100,false);
            }
        });
        
        
        // mySwiper2.params.control = mySwiper1;
        $('.fixed-bg').removeClass('none');
        $('.lookBigPic').removeClass('none');
        fadeBrand('.lookBigPic','a-fadeinT');
        
   }

    //品牌分享出去的标语
    function shareTitle(type) {
        var param = {};
        param['search_type'] = type;
        var url = labUser.api_path + '/search/hotwords/_v020500';
        ajaxRequest(param, url, function(data) {
            if (data.status) {
                console.log(data.message);
                $.each(data.message,function (i,item) {
                    var str='';
                    str+='<li class="share-li tl lh45 white-bg border-8a-b"><em></em>'+item+'</li>'
                    $('#ul_share_t').append(str);
                })
            }
        })
    }
    shareTitle('share');

    //功能
    // 传递创业基金值
    function funds(e) {
        $('.b-fund').text(e);
    }
    //提示框
    function tips(e) {
        $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none ');
        }, 1500);

    }
    //点击更多详情
    $(document).on('click','#toMoreDetail',function(){
        window.location.href = labUser.path +'webapp/brandpro/detail/_v020900?id='+id+'&uid='+uid+shareUrl;
    })
    //了解详细规则
    $(document).on('click','#knowdetail',function(){
        window.location.href =labUser.path + 'webapp/protocol/moreshare/_v020700';
    })
   //查看公司详情
   $(document).on('click','#company_name',function(){
        var type = $(this).attr('data-type');
        if(type == '1'){
             window.location.href =labUser.path + 'webapp/brand/company/_v020900?id='+id;
        }
       
   })
  
    //点击相关视频跳转
    $(document).on('click','#video_imgs li',function(){
        var id=$(this).attr('data-id');
        if(shareFlag){
            window.location.href = labUser.path +'webapp/vod/detail/_v020900?id='+id+'&uid='+uid+'&is_share=1';
        }else{
            window.location.href = labUser.path +'webapp/vod/detail/_v020900?id='+id+'&uid='+uid+'&pagetag=05-4';
        }      
    })

     // js去掉所有html标记的函数：

    function delHtmlTag(str)
    {
          return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    }
    //品牌名称和标语切割-返回名称
    function brandSplit(str) {
        return str.split('|')[0];
    }
    function brandSplits(str) {
        return str.split('|')[1];
    }
    //判断品牌标题字数过长缩小字号
    function brandName(str) {
        if (str.length>=20) {
            $('#brand_name').addClass('f14');
        }
    }
    //移动端title栏显示是否收藏
    function favourite(relation) {
        var fav = relation.is_favorite ;
        if (fav == 0) {
            setFavourite(0);
        } else {
            setFavourite(1);
        }
    }
   
    //是否分享
    function shareDetail(result) {
        var selfObj = result.brand;
        if (shareFlag) {
            $('.install-app').removeClass('none');
            $('#brand_num').text('已申请加盟');
            $('#brand_asks').removeClass('none');
            $('.brand-s').addClass('none');
            $('#brand_btns_share').removeClass('none');
            // $('#brand_more_share').removeClass('none');
           
             //转发后每产生一次阅读，获得奖励
           
            //浏览器判断
            if (is_weixin()) {
                /**微信内置浏览器**/
                $(document).on('tap', '#brand_loadAPP,#openapp', function() {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                // 点击隐藏蒙层
                $(document).on('tap', '.safari', function() {
                    $(this).addClass('none');
                });
                var sharetitle = $('.share-li-sel').text()||selfObj.name;
                var wxurl = labUser.api_path + '/weixin/js-config';
                var desptStr = removeHTMLTag(selfObj.name);
                var nowhitespace = desptStr.replace(/&nbsp;/g, '');
                var despt = cutString(desptStr, 60);
                // var nowhitespaceStr = cutString(nowhitespace, 60);
                var nowhitespaceStr = "项目："+selfObj.name+'\r\n'+"行业："+selfObj.category_name;
                ajaxRequest({ url: location.href }, wxurl, function(data) {
                    if (data.status) {
                        wx.config({
                            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                            appId: data.message.appId, // 必填，公众号的唯一标识
                            timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                            nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                            signature: data.message.signature, // 必填，签名，见附录1
                            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                        });
                        wx.ready(function() {
                            wx.onMenuShareTimeline({
                                title:sharetitle, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: selfObj.logo, // 分享图标
                                success: function() {
                                    // 用户确认分享后执行的回调函数
                                    // getReward(data.message.share_mark,'relay',0,data.message.code)
                                    // secShare(0,'brand',id,'weixin',data.message.share_mark);
                                    sencondShare('relay');
                                },
                                cancel: function() {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                            wx.onMenuShareAppMessage({
                                title: selfObj.name,
                                desc: nowhitespaceStr,
                                link: location.href,
                                imgUrl: selfObj.logo,
                                trigger: function(res) {
                                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                    console.log('用户点击发送给朋友');
                                },
                                success: function(res) {
                                    // getReward(data.message.share_mark,'relay',0,data.message.code);
                                    // secShare(0,'brand',id,'weixin',data.message.share_mark);
                                    sencondShare('relay');
                                },
                                cancel: function(res) {
                                    console.log('已取消');
                                },
                                fail: function(res) {
                                    console.log(JSON.stringify(res));
                                }
                            });
                        });
                    }
                });
            } else {
                if (isiOS) {
                    //打开本地a
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname.substring(1);
                        var strParam = window.location.search;
                        var appurl = strPath + strParam;
                        var share = '&is_share';
                        var appurl2 = appurl.replace(/is_share=1/g, '');
                        window.location.href = 'openwjsq://' + appurl2;
                    });
                    /**下载app**/
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'https://itunes.apple.com/app/id981501194';
                    });
                } else if (isAndroid) {
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                    });
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname;
                        var strParam = window.location.search.replace(/is_share=1/g, '');
                        var appurl = strPath + strParam;
                        window.location.href = 'openwjsq://welcome' + appurl;
                    });
                }
            };
        } else {

            //打电话
            $(document).on('click', '#tel', function() {
                var num = '4000110061';
                callNum(num);
            })
        }

    }
})

function onEvent(action,str,obj) {
    if (isAndroid) {
        javascript:jsUmsAgent.onEvent(action,str,obj);
    } else if (isiOS) {
        var message = {
                method : 'onEvent',
                params : {
                  'eventId':action,
                  'id':obj.id,
                  'type':obj.type
                }
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
    
}
//shareBrand
function shareBrand() {
    fadeBrand('.share-title','a-fadeinB');
}
//弹出淡入效果
function fadeBrand(ele, type) {
    $('.fixed-bg').removeClass('none');
    $(ele).removeClass('none').addClass(type);
    $(ele).css('opacity',1);
}


// 收藏状态
    function setFavourite(state){
      if (isAndroid) {
            javascript:myObject.setFavourite(state);
        } else if (isiOS) {
            var message = {
                method : 'setFavourite',
                params : {
                  "state":state
                }
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
    }
//咨询客服
function customerService(id,brandName){
    if (isAndroid) {
        javascript:myObject.customerService(id,brandName);
    } 
    else if (isiOS) {
       var message = {
                method : 'customerService',
                params : {
                  "id":id,
                  'brandName':brandName
                }
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
}
function showShare() {
    // shareOut('title', window.location.href, '', 'header', 'content');
    var args = getQueryStringArgs(),
        id = args['id'] || '0';
    var title = $('.share-li-sel').text();
    var pageUrl =window.location.href;
    var img = $('#brand_name').attr('src');
    var header = '';
    var content = "项目："+ cutString($('#brand_name').text(),8)+'\r\n'+"行业："+$('#industry_class').text();
        shareOut(title, pageUrl, img, header, content,'','','','','','','share','brand',id);//分享

};
function shareOut(title, url, img, header, content, begintime, citys, id, type, share_mark, relation_id, share_type, share_content, contentid) {
    var data = {};
    data['title'] = title;
    data['url'] = (url + "&is_share=1").replace(/uid=\d*/g,'uid=0').replace(/agent_id=\d*/g,'agent_id=0');
    data['img'] = img;
    data['header'] = header;
    data['content'] = content;
    data['begintime'] = begintime;
    data['citys'] = citys;
    data['id'] = id;
    data['type'] = type;
    data['share_mark'] = share_mark;//1
    data['relation_id'] = relation_id;//接口1、2都用到,详情页的code
    data['share_type'] = share_type;//接口1类型,share:分享，relay：转发， watch：观看，enroll：报名，sign:签到, view：点击， intent:品牌意向
    data['share_content'] = share_content;//接口2、分享的内容activity,live,video,news,brand等
    data['share_contentid'] = contentid;//接口2、分享的内容对应的id
    if (isAndroid) {
        javascript:myObject.share(JSON.stringify(data));
    } else if (isiOS) {
         var message = {
                method : 'share',
                params : data
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
}
  

   

