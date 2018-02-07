// 
// Created by Wangcx
// 
$('body').addClass('bgcolor');
new FastClick(document.body);
 var args = getQueryStringArgs();
    var uid = args['agent_id'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var origin_mark = args['share_mark'] ;//分销参数，分享页用
    var  origin_code = args['code'] || 0;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
    var is_fromlearn = urlPath.indexOf('fromlearn') > 0 ? true : false;
    var mySwiper;

Zepto(function() {
    $(document).on('click','.toFound',function () {
        window.location.href= labUser.path + 'webapp/protocol/venture/_v020500?pagetag=025-3';
        return false;
    });
    //点击相关活动跳转
    $(document).on('click','.toAct',function(){
        var actID =$(this).attr('data_id');
        window.location.href = labUser.path +'webapp/agent/activity/detail?id='+actID+'&pagetag=02-2&agent_id='+uid + shareUrl;
    })
        // 按钮点击查看公司详情
    $(document).on('click', '#company_details', function() {
            window.location.href = labUser.path + 'webapp/brand/company/_v020800?id=' + id + '&uid=' + uid + shareUrl;
        })
    
    // 点击蒙层关闭
    $(document).on('click', '.fixed-bg', function() {
        $(this).addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.share-reset').click();
        $('#packet_close').click();
        $('.share-title').addClass('none').removeClass('a-fadeinB');
        $('.businessTip').addClass('none');
        $('.certification').addClass('none');
    });
    function removeall() {
        $('.fixed-bg').addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
        $('.share-reset').click(); 
    }
    
   
    var brandDetail = {
        // 创业基金
       
        //品牌的详情信息获取
        detail: function(id, uid) {
            var param = {};
            param['id'] = id;
            param['agent_id'] = uid;
            // var code = $('#brand_name').attr('data-code');
            var url = labUser.agent_path + '/brand/detail/_v010004';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    shareDetail(data.message);
                    getDetail(data.message);
                    getMore(data.message);
                    getJoin(data.message);
                    is_agent(data.message);
                    //没有相关视频时，隐藏视频标签页
                    // is_showvideo(data.message);
                    if(is_fromlearn){
                            mySwiper.slideTo(2,200,true);
                            if(!shareFlag){
                                changeBrandTitle(2);
                            };
                        }else{
                            if(!shareFlag){
                                changeBrandTitle(0);
                            };
                        };
                }else{
                    $('#bottom_public').remove();
                    $('#brand_detail').html('<div class="tc color999 mt5 f16">'+data.message+'</div>').removeClass('none');
                }
            })
        }
    };
    brandDetail.detail(id, uid);
    // brandDetail.history(id,uid,'brand');

    function getDetail(result) {
        var brand = result.brand;
        var is_public = result.identity_card ? true : false;
        $('#brand_btns_app').prop('is_public',is_public);
        // 顶部的轮播图
            $.each(brand.banners, function(i, item) {
            var str = '';
                str += '<div class="swiper-slide"><img src="' + item.src + '" alt="" /></div>';
                $('.swiper-brand').append(str);    
            });
            var mySwiper2 = new Swiper('#swiper-container2',{//子swiper
                        loop:true, //可循环切换
                        // nested:true, //用于嵌套相同方向的swiper时，当切换到子swiper时停止父swiper的切换。
                        // resistanceRatio: 0, //抵抗率,值越小抵抗越大越难将slide拖离边缘，0时完全无法拖离。
                        // slidesPerView : 'auto',
                        // loopedSlides :1,
                        pagination : '.swiper-pagination', 
                        paginationType : 'custom',
                        paginationCustomRender: function (swiper, current, total) {
                                    return '<span class="f16">'+current+'</span>' + ' / ' + total;
                            },
                        onInit: function(swiper){
                                var swiperDom = $('#swiper-container2 .swiper-wrapper');
                                var wrapperwidth = swiperDom.css('width');
                                swiperDom.css({'transform': 'translate3d(-'+wrapperwidth+', 0px, 0px)'});
                            }
                    }) ;
        (brand.agency_way.area == '1'|| brand.agency_way.channel == '1') ? '' : $('#join_type_box').remove(); 
        brand.agency_way.area == '1'? '' : $('#pinpai').remove();
        brand.agency_way.channel == '1'? '' : $('#qudao').remove();
        $('.commission').text(Math.round(brand.max_percent*10000)/100+'%')
        $('.zhuan').text(brand.share_num > 9999 ? '9999+' : brand.share_num);
        $('.fav').text(brand.favours > 9999 ? '9999+' : brand.favours);
        $('.view').text(brand.views > 9999 ? '9999+' : brand.views);
        $('.brand_name').text(brand.title);
        $('.brand_title').text(brand.title);
        $('#category_name2').text(brand.slogan);
        $('#category_name').text(brand.category_name);
        $('#brand_investment').text(brand.investment_min + ' ~ ' + brand.investment_max + ' 万元');
        if (brand.slogan!=='') {
            $('#brand_sort2').removeClass('none');
        }
        var str = '';
        for (var i = 0; i < brand.products.length; i++) {
            str +=' '+ brand.products[i] ;
        }
        $('#brand_products').html(cutString(str,22));
        $('#brand_shops').text(brand.shops_num);
        $('#brand_click').html('<strong class="seen"></strong> ' + brand.views);
        var keyHtml = '';
        console.log(brand.keywords);
        for(var j=0; j<brand.keywords.length;j++){
            keyHtml += '<span>'+brand.keywords[j]+'</span>';
        };
        $('#keywords').html(keyHtml);

        //申请进度
        var steps = $('.steps').children('div');
        var statu = result.process_status;
            for(var i=0;i<statu;i++){
                steps.eq(2*i).addClass('stepblue').children('p').eq(0).removeClass('color999');
                steps.eq(2*i+1).addClass('blueline');
            };
        if(result.process_status == 4){
            $('.flow').remove();
            $('#brandAgent').removeClass('none');
            $('.deputy').text(result.contactor.name);
            $('.call_tel').attr('href','tel:'+result.contactor.tel);
            $('.send_mes').attr('href','sms:'+result.contactor.tel);
            getAgentInfo(result);
        }else{
            $('.flow').removeClass('none');
            $('#brandAgent').remove();
        }
        // 品牌标签 
        // 公司名片
        $('#brand_company').text(brand.company);
        $('#brand_company_add').text(brand.address);
        if (brand.is_auth === "no") {
            $('#brand_auth').addClass('none');
        }
        $('#brand_logo').attr('src', brand.logo).data('sharecontent',result.brand.share_content);


        //相关活动   
        if(result.activity.length>0){
            var actHtml = '';
            $.each(result.activity,function(index,item){
                if(index == result.activity.length-1){
                    actHtml+=' <div class="toAct" data_id="'+item.id+'"><div class="l act_img ">';
                }else{
                    actHtml+=' <div class="fline toAct" data_id="'+item.id+'"><div class="l act_img ">';
                }
                if(item.can_apply == 1){
                    actHtml += '<button class="f14 act_ing "><span></span>报名中';
                }else{
                    actHtml +=' <button class="f14 act_over ">活动已结束';
                };
                actHtml +='</button><img src="'+item.list_img+'" alt="" style="height:100%"></div>';
                actHtml +=' <div class="act_intro"> <p class="f14 mb0 b">'+cutString(item.title,22)+'</p>';
                actHtml += ' <p class="f11 color999 mb0 mt05">开始时间：<span>'+unix_to_datetime(item.begin_time)+'</span></p>';
                var sHtml = '';
                if(item.cities && item.cities.length > 0){
                    $.each(item.cities,function(i,j){
                        if(i <=2){
                            sHtml += j+' ';
                        }else if(i == 3){
        
                            sHtml +='...';
                        }
                    })
                };
                actHtml += '<p class="f11 color999">活动场地：<span>'+sHtml+'</span></p></div>';
                actHtml +='<div class="clearfix"></div></div>';
            });
            $('.brand_act').html(actHtml).css('padding-bottom','8rem');
            $('.rel_activity').removeClass('none');
        }else{
            $('.brand-company-h').css('padding-bottom','8rem');
        };
        // $('#brand_detail').removeClass('none');
    };

    function getAgentInfo(obj){
        var event = obj.events;
        var eventHtml = '';
            eventHtml += '<div class="fline"><p class="mt1-5"><span class="ml0-4 sucIcon">成</span><span class="b ml1-2 f14">该品牌成单量</span>';
            eventHtml += '</p><p class="deals color666 f12"><span class="pl3-2 ">我的成单量 <em>'+obj.my_own_orders+'</em></span>';
            eventHtml += '<span >我的下线经纪人成单量<em>'+obj.my_subordinate_orders+'</em></span></p></div>';
            eventHtml += '<div class="fline"><p class=" pt1-5 mycustomer f14 b">我的客户 × <span class="brand_title" style="vertical-align: bottom;">'+cutString(obj.brand.title,15)+'</span></p>';
            eventHtml += '<p class="docCustomer color666 f12"><span class="pl3-2 ">累计跟单客户<em>'+obj.total_customers+'</em></span>';
            eventHtml += '<span>当前跟单客户<em style="margin-right: 7rem;">'+obj.now_customers+'</em></span></p></div>';
            eventHtml += '<div><p class="pt1-5 eventPoint f14 b">事件点</p><ul class="events pl3-2 pr0-4 mb1-5">';
            $.each(event,function(i,j){
                var summary = j.summary;
                if(j.type == 2){
                    if(j.zone_name){
                        summary = j.summary +' : '+j.name + ' ('+j.zone_name+')';
                    }else{
                        summary = j.summary +' : '+j.name;
                    }
                    
                }
                if(i == 0){
                    eventHtml += '<li><span class="color666"><em class="point"></em>'+summary+'</span><span class="color999">'+unix_to_yeardate2(j.time)+'</span></li>';
                }else{
                    eventHtml += '<li><span class="color666"><em class="point"><i class="verLine"></i></em>'+summary+'</span><span class="color999">'+unix_to_yeardate2(j.time)+'</span></li>';
                }
                
            });
            eventHtml += '</ul></div>';  
            $('#brandAgent').append(eventHtml);
    };
    //视频
    function getVideos(result){
            var videos = result.videos || '';
            $.each(videos, function(i, item){
                var str = '';
                    str += '<li class="fline" data-id="'+item.id+'"><div class="l video_img"><p class="playlogo mb0"><img src="/images/play.png" alt=""></p>';
                    str += '<img src="'+item.image+'" alt="" style="height:100%"></div>';
                    str +='<div class="video_intro "><p class="f14 mb0 h45 b">'+cutString(item.title,22)+'</p>';
                    str +='<p class="f11 mb0 color999">录制时间：<span>'+unix_to_mdhm(item.created_at)+'</span></p>';
                    str +='<div class="f11 video_dis color999">视频描述：<span>'+removeHTMLTag(item.summary)+'</span></div></div>';
                    str +='<div class="clearfix"></div></li>'
                $('#relativevideo').append(str);
                $('.no-data').removeClass('none');
            });
            if (videos==''||videos==undefined||videos.length==0) {
                $('#sec_video').removeClass('bgwhite');
                $('.videoss').removeClass('none');
            }
        };

    //获取章节数据
        function getDataDetail(brand_id,agent_id,status){
                var param = {};
                    param['brand_id']=brand_id;
                    param['agent_id']=agent_id;
                var url = labUser.agent_path + '/brand/chapter-list/_v010004';
                ajaxRequest(param,url,function(data){
                    if(data.status){ 
                        var sectionHtml = '';
                        var persent = Math.round(data.message.completeness*100);
                        $('.progress_num').text(persent+'%');
                        if(data.message){
                            if(status !=4){
                                 if(data.message.is_complete == 1){
                                        $('#inter_learn').remove();
                                        $('#learn_all').removeClass('none');
                                    }else{
                                        $('#learn_all').remove();
                                        if(!is_fromlearn){
                                            $('#inter_learn').removeClass('none');
                                        }
                                        
                                    };     
                                //头部
                                sectionHtml+='<div class="head fline bgwhite">';
                                sectionHtml+='<p class="f15 color333 width70">'+data.message.brand_name+'-'+data.message.brand_slogan+'的代理学习内容</p>';
                                sectionHtml+='<div>';
                                sectionHtml+='<span class="f12 color999 pb05" style="display: inline-block;">已学完</span>';
                                sectionHtml+='<span class="f12 color999 progress_num">'+persent+'%</span>';
                                sectionHtml+='<div class="progress">';
                                sectionHtml+='<div class="progressBar" >';
                                sectionHtml+='</div>';
                                sectionHtml+='</div>';
                                sectionHtml+='</div>';
                                sectionHtml+='</div>';
                                //提醒
                                sectionHtml+='<div class="warn bgwhite">';
                                sectionHtml+='<p class="f12 cfd4d4d mb1-2">提醒：</p>';
                                //未完成全部章节
                                if(data.message.is_complete==0){
                                    sectionHtml+='<div class="unfinished">';
                                    sectionHtml+='<p class="f12 color666 mb1-2">1、完成每章节中的资讯、视频学习和浏览任务。</p>';
                                    sectionHtml+='<p class="f12 color666 mb1-2">2、看完资讯或视频，点击“在线测试”，完成简单题目考试。</p>';
                                    sectionHtml+='<p class="f12 color666 mb1-2">3、 通过在线或电话回访完成考核，获得品牌代理资质。</p>';
                                    sectionHtml+='<p class="f12 color666 ">4、如有疑问，联系无界商圈客服人员进行咨询、确认。</p>';
                                    sectionHtml+='</div>';
                                };
                                //完成全部章节
                                if(data.message.is_complete==1){
                                    sectionHtml+='<div class="finished ">';
                                    sectionHtml+='<p class="f12 color666">已完成章节学习内容，请等待我们的客服专员与您取得电话联系</p>';
                                    sectionHtml+='</div>';
                                };
                                sectionHtml+='</div>';
                            }
                            
                            //章节
                            if(data.message.chapter.length>0){
                                $.each(data.message.chapter, function(j,k) {
                                    sectionHtml+='<div class="section mt1-2 bgwhite">';
                                    sectionHtml+='<div class="catalog fline mb1-5">';
                                    sectionHtml+='<p>';
                                    sectionHtml+='<span class="f15 color333 b">'+k.chapter_num+'</span>&nbsp;&nbsp;';
                                    sectionHtml+='<span class="f15 color333 b">'+k.name+'</span>';
                                    sectionHtml+='</p>';
                                    sectionHtml+='<img src="/images/agent/pull_down.png" class="pull_down"/>';
                                    sectionHtml+='</div>';
                                    if(k.content.length>0){
                                        $.each(k.content, function(m,n) {
                                            sectionHtml+='<div class="study_section" type="'+n.type+'" id="'+n.id+'" sec_id="'+n.cotent_num+'">';
                                            sectionHtml+='<div class="catalog_cont" section_catalogId="'+n.id+'">';
                                            sectionHtml+='<p class="catalog_num">';
                                            if(n.type=='article'){
                                                sectionHtml+='<img src="/images/agent/sectiontext.png" class="section_text mr05"/>';
                                            }else{
                                                sectionHtml+='<img src="/images/agent/video.png" class="section_video"/>';
                                            }
                                            sectionHtml+='<span class="f13 color333">'+n.cotent_num+n.title+'</span>';
                                            sectionHtml+='</p>';
                                            //小章节是否完成
                                            sectionHtml+='<p class="">';
                                            if(n.is_complete==0){
                                                sectionHtml+='<span class="begin_xueyi" >开始学习</span>';
                                            }else{
                                                sectionHtml+='<span class="again_xuexi">已学习</span>';
                                            }
                                            sectionHtml+='</p>';
                                            sectionHtml+='</div>';
                                            sectionHtml+='</div>';
                                        });
                                    };
                                    sectionHtml+='</div>';
                                });
                            }else{
                                sectionHtml+='<div class="mt8 tc"><img src="/images/agent/no_article.png" style="width:50%;"><div>'
                            }
                            
                        };
                        $('.brand_chapter').html(sectionHtml);
                        //进度条
                        var num = data.message.completeness;
                        console.log(num)
                        $('.progressBar').css('width',$('.progress').width()*num+'px');
                    }
                });
                
            
            };
    //章节相关js与跳转
        //点击学习跳转
            $(document).on('click','.study_section',function(){
                var type = $(this).attr('type');
                var type_id = $(this).attr('id');
                var sec_id =$(this).attr('sec_id');
                if(type=='article'){
                    //跳转资讯学习页面
                    headlineStudy(type_id,id,uid,sec_id);
                }else {
                    //跳转视频学习页面
                    videoStudy(type_id,id,uid);
                };
                
            });
            function videoStudy(type_id,id,agent_id){
                window.location.href = labUser.path + '/webapp/agent/brandvod/detail/_v010004?id='+type_id+'&brand_id='+id+'&agent_id='+agent_id;
            };
            function headlineStudy(type_id,id,agent_id,sec_id){
                window.location.href = labUser.path + '/webapp/agent/headline/headlinestudy/_v010004?id='+type_id+'&brand_id='+id+'&agent_id='+agent_id+'&section_id='+sec_id;
            };
            //点击展示箭头方向    
            $(document).on('click','.catalog',function(){
                $(this).parent('.section').children('.study_section').toggleClass('none');
                if ($(this).children('.pull_down').attr('src')=='/images/agent/pull_down.png') {
                    $(this).children('.pull_down').attr('src','/images/agent/pull_up.png');
                } else{
                    $(this).children('.pull_down').attr('src','/images/agent/pull_down.png');
                }
            });


    //获取问答数据
         function getQAdetail(id){
            var param={};
                param['brand_id'] = id;
            var url = labUser.agent_path + '/academy/gain-answer-datas-list/_v010004';
            ajaxRequest(param,url,function(data){
                if(data.status){
                    var qaHtml = '';
                    if(data.message){
                        $.each(data.message,function(i,j){
                            qaHtml +='<li class="tl bgwhite mb1-33 pl1-5"><div class="fline f15 ques_height ">Q: '+j.question+'</div>';
                            qaHtml += '<div class="f14 color999 pt1-5 pr1-5 pb1-5 ">'+j.answer+'</div></li>'
                        });
                     }else{
                            qaHtml +='<div class="mt8 tc"><img src="/images/agent/no_ques.png" style="width:50%;"><div>';
                     }
                        
                        $('.QA_list').html(qaHtml);           
                }
            })

         }

    //资料页nav切换
    $(document).on('click','.datas_sel>div',function(){
        var index= $(this).index();
        $(this).addClass('stepblue').siblings('div').removeClass('stepblue');
        if(index == 0){
            $('.brand_chapter').addClass('none');
            $('.QA_list').removeClass('none');
        }else if(index == 1){
            $('.brand_chapter').removeClass('none');
            $('.QA_list').addClass('none');
        }

    })

//客户
    function brandCustomer(obj){
        var follows = obj.following_customers || '';
        var success = obj.success_customers || '';
        if(follows.length == 0 && success.length == 0){
            $('.nocustomer').remove();
            $('#brand_customer').children('div').append('<div class="tc f15 color999" style="padding-top:50%;"><img src="/images/agent/nocustomer.png" style="width:18.5rem;height:12.1rem;"><p>还没有任何客户哦</p></div>')
        }else{
            var folHtml = '';
                $('#fol_num').text('('+follows.length+')');
                $('#suc_num').text('('+success.length+')');
            if(follows.length >0){
                 $.each(follows,function(i,j){
                    folHtml += '<div class="customer_flex p1 mb1" data-uid="'+j.uid+'" data-source="'+j.source+'"><div><img src="'+j.avatar+'" alt="header" class="l mr1 customer_img">';
                    folHtml += '<div class="l"><p class="f15 mb05"><span>'+j.nickname+'</span>';
                    if(j.gender == '女'){
                        folHtml += '<img src="/images/agent/girl.png" alt="性别" class="gender"></p>'
                    }else if(j.gender == '男'){
                        folHtml += '<img src="/images/agent/boy.png" alt="性别" class="gender"></p>'
                    };
                    folHtml += '<p class="f12 color999 mb05">'+j.city+'</p></div></div>';
                    folHtml += '<div class="tr f11"><p class="mb05">'+unix_to_md(j.begin_time)+' 开始跟单</p><p class="mb05">已跟单'+j.followed_days+'天</p></div></div>'; 
                 });
                 $('.followcustomers').html(folHtml);
            }else{
                $('.followcustomers').parent('.nocustomer').remove();
            };
            if(success.length >0){
                $.each(success,function(i,j){
                    var sucHtml = '';
                    sucHtml += '<div class="bgf5 mb1-33 is_show_suc"><div class="customer_flex p1 dashline" data-uid="'+j.uid+'" data-source="'+j.source+'">'
                    sucHtml += '<div> <img src="'+j.avatar+'" alt="header" class="l mr1 customer_img"><div class="l">';
                    sucHtml += '<p class="f15 mb05"><span>'+j.nickname+'</span>'
                    if(j.gender == '女'){
                        sucHtml += '<img src="/images/agent/girl.png" alt="性别" class="gender"></p>'
                    }else if(j.gender == '男'){
                        sucHtml += '<img src="/images/agent/boy.png" alt="性别" class="gender"></p>'
                    };
                    sucHtml += '<p class="f12 color999 mb05">'+j.city+'</p></div></div>';
                    sucHtml += '<div class="tr f11"><p class="mb05">'+unix_to_md(j.begin_time)+' 开始跟单</p><p class="mb05">已跟单'+j.followed_days+'天</p></div></div>';
                    sucHtml += '<ul class="followlists follow_'+i+'">';
                    sucHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/link.png" class="link_img"></span>与投资人形成代理关系</p></li>';
                    sucHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/right.png" class="right_img "></span>获得投资人电话及其他通讯方式</p></li>';
                    sucHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户参加OVO发布会</p></li>';
                    sucHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户总部或门店考察</p></li>';
                    sucHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，交付线上首付</p></li>';   
                    sucHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，线下尾款补齐</p></li>';   
                    sucHtml += '<li class="see_button mt05" data-url="'+j.address+'" data-uid="'+j.uid+'" data-id="'+j.contract_id+'"><button class="e-contract">查看电子合同</button><button class="view_draw">查看提成详情</button></li>'
                    sucHtml +='</ul></div>';
                    $('#suc_customer').append(sucHtml);
                    var ul_obj = $('.follow_'+i);  
                    var eventArr = [];
                      if(j.event && j.event.length>0){
                        for(var a=0;a < j.event.length;a++){
                          if(a == 0 || j.event[a].schedule != j.event[a-1].schedule){
                            eventArr.push(j.event[a]);
                          };                          
                        }
                      }; 
                 
                    if(eventArr.length >0){
                        $.each(eventArr,function(x,y){
                            if(y.schedule ==1){
                                ul_obj.children('li').eq(0).removeClass('none');              
                            }else if(y.schedule == 2){
                                ul_obj.children('li').eq(1).removeClass('none'); 
                            }else{
                                ul_obj.children('li').eq(y.schedule-1).find('img').attr({'src':'/images/agent/right.png'}).addClass('right_img').removeClass('error_img'); 
                            };
                            ul_obj.children('li').eq(y.schedule-1).children('p').removeClass('color999');
                            ul_obj.children('li').eq(y.schedule-1).append('<p class="color999 f11">'+unix_to_yeardate2(y.event_time)+'</p>');
                            
                        })
                    }            
                });                                
                if(success.length >3){
                    $('.is_show_suc').children('.followlists').addClass('none');
                    $('.is_show_suc').append('<div class="c2873ff f12 tc pt1 pb1 spreadOut">展开<span class="showdetail"></span></div> ');
                };                       
            }else{
                $('#suc_customer').parent().remove();
            };

         }

    };


    //展开or收起
    $(document).on('click','.spreadOut',function(){
        console.log($(this).children('.showdetail'));
        if($(this).children('.hidedetail').length >0){
            $(this).parent('.is_show_suc').children('.followlists').addClass('none');
            $(this).html('展开<span class="showdetail"></span>');
        }else{
            $(this).parent('.is_show_suc').children('.followlists').removeClass('none');
            $(this).html('收起<span class="showdetail hidedetail"></span>');
        }    
    });
   //查看电子合同
   $(document).on('click','.e-contract',function(){
        var URL = $(this).parent('li').attr('data-url')
        window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+URL;
   });
   //查看付款详情
   // $(document).on('click','.view_payment',function(){
   //      var contract_id=$(this).parent('li').attr('data-id');
   //      var customer_id =$(this).parent('li').attr('data-uid');
   //      window.location.href = labUser.path +'webapp/agent/brand/payment/_v010002?id='+contract_id+'&customer_id='+customer_id+'&agent_id='+uid;
   // });
   //查看提成详情
   $(document).on('click','.view_draw',function(){
        var contract_id=$(this).parent('li').attr('data-id');
        var customer_id =$(this).parent('li').attr('data-uid');
        window.location.href = labUser.path +'webapp/agent/brand/commission/_v010002?id='+contract_id+'&customer_id='+customer_id+'&agent_id='+uid;
   });

   //跳转至客户详情
   $(document).on('click','.customer_flex',function(){
        var customer_id = $(this).attr('data-uid');
        var type = $(this).attr('data-source');
        if(type != '8'){
            window.location.href = labUser.path + 'webapp/agent/customer/detail/_v010002?customer_id='+customer_id+'&agent_id='+uid;
        }        
        
   })
    // 图文详情 项目介绍 图集
    function getMore(result) {
        // console.log(result);
        $('.pic_text').html(result.brand.detail);
           //公司详情
        if ($('#brand_company').text()!=='') {
            $('.brand-company-h').removeClass('none');
        }
        if(result.brand.detail_img){
            $.each(result.brand.detail_img, function(i, item) {
                    var str = '';
                    str += '<img src="' + item.src + '" class="onimgs" alt="" data-picindex="'+i+'">';
                $('#brand_images').append(str);
            });
            $('#brand_images').addClass('pb8');
        }
        
        if (isiOS || isAndroid) {
            $('.onimgs').on('click', function () {
                var this_index = $(this).data('picindex');
                var imgary = $(this).parent().children('img');
                var photos = [];
                $.each(imgary, function (index, item) {
                    var photo = {
                        url: $(this).attr('src'),
                        selected: 0
                    };
                    if ($(this).data('picindex') == this_index) {
                        photo.selected = 1;
                    }
                    photos.push(photo);
                });
                var jsonData = JSON.stringify(photos);
                lookBigPhoto(jsonData);
            });
        }
    }
    //加盟简介，优势，条件
    function getJoin(result) {
        var brand = result.brand;
        if (brand.league ==''||brand.league==undefined) {
            $('#brand_j_1').parent().addClass('none');
        }
        if (brand.advantage ==''||brand.advantage==undefined) {
            $('#brand_j_2').parent().addClass('none');
        }
        if (brand.prerequisite==''||brand.prerequisite==undefined) {
            $('#brand_j_3').parent().addClass('none');
        }
        $('#brand_j_1').html(brand.league);
        $('#brand_j_2').html(brand.advantage);
        $('#brand_j_3').html(brand.prerequisite);

    }
   
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

    //点击查看更多精彩视频
    $(document).on('click','#moreVideo',function(){
        if(shareFlag){
            tips('请至App查看');
        }else{
            toBrandwall();
        }
    })
    //点击相关视频跳转
    $(document).on('click','#relativevideo li',function(){
        var id=$(this).attr('data-id');
        if(shareFlag){
            window.location.href = labUser.path +'webapp/agent/vod/detail?id='+id+'&agent_id='+uid+'is_share=1';
        }else{
            window.location.href = labUser.path +'webapp/agent/vod/detail?id='+id+'&agent_id='+uid;
        }
    })

    // //点击课程中的视频查看
    // $(document).on('click','#agent_videos li',function(){
    //     var video_id=$(this).attr('data-id'); 
    //     if(shareFlag){
    //         window.location.href = labUser.path +'webapp/agent/vod/detail?id='+video_id+'&agent_id='+uid+'is_share=1&is_brand='+id;
    //     }else{           
    //         window.location.href = labUser.path +'webapp/agent/vod/detail?id='+video_id+'&agent_id='+uid+'&is_brand='+id;
    //         $(this).find('.A_videos_img').append('<img src="/images/agent/learned.png" class="learned_logo">');     
    //     };
    // })

    // //点击课程资讯查看
    // $(document).on('click','#brand_news li',function(){
    //     var new_id=$(this).attr('data-id');
    //     if(shareFlag){
    //         window.location.href = labUser.path +'webapp/agent/headline/detail?id='+new_id+'&agent_id='+uid+'is_share=1&is_brand='+id;
    //     }else{
    //         window.location.href = labUser.path +'webapp/agent/headline/detail?id='+new_id+'&agent_id='+uid+'&is_brand='+id;
    //         $(this).find('.border-top999').text('已打卡').addClass('cfd4d4d');
    //     }
         
    // })

    //申请品牌代理
    $(document).on('click','#brand_btns_app',function(){
        var is_public = $(this).prop('is_public');
        if(is_public){
            window.location.href = labUser.path +'webapp/agent/brand/apply?id='+id+'&agent_id='+uid;
        }else{
            $('.fixed-bg').removeClass('none');
            $('.certification').removeClass('none');
        }
        
    });

    //点击取消实名认证
    $(document).on('click','.cancel',function(){
        $('.fixed-bg').addClass('none');
        $('.certification').addClass('none');
    });
    //跳转至实名认证页面
    $(document).on('click','.makesure',function(){
        $('.certification').addClass('none');
        $('.fixed-bg').addClass('none');
        toCompleteAuthentication();
    })

     // js去掉所有html标记的函数：
    function delHtmlTag(str)
    {
        return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    }
   
    //判断品牌标题字数过长缩小字号
    function brandName(str) {
        if (str.length>=20) {
            $('#brand_name').addClass('f14');
        }
    }
   
    //移动端title栏显示是否有视频标签
    function is_showvideo(obj){
        if(!(obj.videos)|| obj.videos.length == 0){
            $('#brand_video').remove();
            if(!shareFlag){
                showVideo(0);
            } 
        }else{
            getVideos(obj);
            if(!shareFlag){
                showVideo(1);
            }
            
        }
    };
    //是否显示经纪人相关
    function is_agent(obj){
        var tolearn =0;
        if(obj.process_status < 1){
            $('#brand_btns_app').removeClass('none');
            // $('#brand_lesson').remove();
            $('#brand_customer').remove();
            $('#brand_chapter').remove();
            $('#brand_QA').remove();
            $('#brand_data').remove();
            
            if(!shareFlag){
                showLesson(0);
                is_showvideo(obj);
            };
            
            
        }else if(obj.process_status >= 1 && obj.process_status <=3){
            tolearn = 1;
            $('#brand_customer').remove();
            $('#brand_data').remove();
            $('#brand_video').remove();
            // $('#swiper-2').remove(); 
            $('.detail_head').remove();      
            getDataDetail(id,uid,obj.process_status);
            getQAdetail(id);
            if(!shareFlag){
                showLesson(1);
                showVideo(0);
            }
                  
        }else if(obj.process_status == 4){
            $('.business').removeClass('none');
            $('#brand_seek').removeClass('none');
            $('#brand_chapter').remove();
            $('#brand_QA').remove(); 
            brandCustomer(obj);
            getDataDetail(id,uid,obj.process_status);
            getQAdetail(id);

            if(!shareFlag){
                showLesson(2);
                is_showvideo(obj);
            }        
        };
        var pages = 0;
        mySwiper = new Swiper('#swiper-container1',{
                resistanceRatio :0, //滑动到最后一页不能继续滑动
                onSlideChangeStart: function(swiper){
                	pages++;
                	console.log(pages)
                    var index = mySwiper.activeIndex; //获取滑动后当前的页面索引
                    if(!shareFlag){
                        changeBrandTitle(index);
                    };
                    if(tolearn ==1){
                           if(swiper.activeIndex == 2 ){
                                $('#inter_learn').addClass('none');
                           }else{
                                $('#inter_learn').removeClass('none');
                           };
                        }
                },
                observer:true,//修改swiper自己或子元素时，自动初始化swiper
                observeParents:true,//修改swiper的父元素时，自动初始化swiper
                onInit: function(swiper){
                        $('#brand_detail').removeClass('none'); 
                    }
            }); 
    };
    //进入学习模块
    $(document).on('click','#inter_learn',function(){
        mySwiper.slideTo(2,200,true);
        changeBrandTitle(2);
    })

    //点击查看咨询任务
    $('#brand_seek').click(function(){
        checkConsults(id,uid)
    })

   
    //分享出去的标语
    $(document).on('click ', '.share-li', function() {
        var text =$(this).text();
        $(this).addClass('share-li-sel');
        $('.share-title').addClass('none');
        showShare();
        $('.fixed-bg').addClass('none');
        $(this).removeClass('share-li-sel');
    });



    //是否分享
    function shareDetail(result) {
        var selfObj = result.brand;
        if (shareFlag) {
            $('.install-app').removeClass('none');
            $('#brand_sort').addClass('none');
            $('#brand_num').text('已申请加盟');
            $('#brand_asks').removeClass('none');
            $('.brand-s').addClass('none');
            $('#brand_btns_share').removeClass('none');
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
                // var sharetitle = selfObj.title;
                var sharetitle = '不错的项目，你也看看~';
                var wxurl = labUser.api_path + '/weixin/js-config';
                var desptStr = removeHTMLTag(selfObj.title);
                var nowhitespace = desptStr.replace(/&nbsp;/g, '');
                var despt = cutString(desptStr, 60);
                // var nowhitespaceStr = cutString(nowhitespace, 60);
                var nowhitespaceStr = "项目："+selfObj.title+'\r\n'+"行业："+selfObj.category_name;
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
                            wx.onMenuShareTimeline({//分享到朋友圈
                                title:sharetitle, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: selfObj.logo, // 分享图标
                                success: function() {
                                    // 用户确认分享后执行的回调函数
            
                                },
                                cancel: function() {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                            wx.onMenuShareAppMessage({//分享给朋友
                                title: sharetitle,
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
                                    // sencondShare('relay');
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

function toPacket() {
    if (isAndroid) {
        javascript:myObject.toPacket();
    } else if (isiOS) {
        var data = {
        };
        window.webkit.messageHandlers.toPacket.postMessage(data);
    }
}
function onEvent(action,str,obj) {
    if (isAndroid) {
        javascript:jsUmsAgent.onEvent(action,str,obj);
    } else if (isiOS) {
        var data = {
            'eventId':action,
            'id':obj.id,
            'type':obj.type
        };
        window.webkit.messageHandlers.onEvent.postMessage(data);
    }
}



//品牌是否有关联视频，如果没有不展示视频的标签 type:1 展示；0 不展示
function showVideo(type){
    if (isAndroid) {
        javascript:myObject.showVideo(type);
    } else if (isiOS) {  
        var data={
            'type':type
        }
        window.webkit.messageHandlers.showVideo.postMessage(data);
    }
}

function showLesson(type){
    if (isAndroid) {
        javascript:myObject.showLesson(type);
    } else if (isiOS) {  
        var data={
            'type':type
        }
        window.webkit.messageHandlers.showLesson.postMessage(data);
    }
}

//跳转实名认证页面
    function toCompleteAuthentication(){
        if (isAndroid) {
            javascript:myObject.toCompleteAuthentication();
        } else if (isiOS) {  
            var data={};
            window.webkit.messageHandlers.toCompleteAuthentication.postMessage(data);
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

function showShare() {
    // shareOut('title', window.location.href, '', 'header', 'content');
    var args = getQueryStringArgs(),
        id = args['id'] || '0';
    var title = $('.share-li-sel').text();
    var type= 'brand';
    var pageUrl =window.location.href;
    var img = $('#brand_logo').attr('src');
    var header = '';
    var content = "项目："+ cutString($('#brand_name').text(),8)+'\r\n'+"行业："+$('#category_name').text();
    var sharecontent=$('#brand_logo').data('sharecontent') || content;
    var weibo = wechat =title+'-'+'点击了解更多品牌信息';
      agentShare(title, pageUrl, img, header, sharecontent,type,id,weibo,wechat);      
 
    
};
function unix_to_md(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '月' + D + '日 ' ;
}
  
