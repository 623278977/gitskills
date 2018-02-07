$('body').addClass('bgcolor');
new FastClick(document.body);
// FastClick.attach(document.body)
 var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var origin_mark = args['share_mark'] ;//分销参数，分享页用
    var  origin_code = args['code'] || 0;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
    var mySwiper;

Zepto(function() {
    //判断版本来调整顶部的悬浮条
    if (urlPath.indexOf('_v020502')!==-1) {
        $('.install-app').addClass('install-app2');
    }
    // 公用部分
    // 领取创业基金
    $(document).on('click', '#brand_award', function() {
        var fetch = $(this).data('fetch');
        var fund = $(this).data('fund');
        if (fetch) {
            tips('您已经领取过了');
            return false
        } else {
            brandDetail.award(id, uid, fund);
            fadeBrand('.brand-packet', 'a-bouncein');
            $('#brand_award').data('fetch', true);
        }
    });
   
    $(document).on('click','.toFound',function () {
        window.location.href= labUser.path + 'webapp/protocol/venture/_v020500?pagetag=025-3';
        return false;
    });

    //分享出去的标语
    $(document).on('click ', '.share-li', function() {
     	var text =$(this).text();
     	$(this).addClass('share-li-sel');
        $('.share-title').addClass('none');
        showShare();
        $('.fixed-bg').addClass('none');
        $(this).removeClass('share-li-sel');
    });

    //点击相关活动跳转
    $(document).on('click','.toAct',function(){
        var actID =$(this).attr('data_id');
        window.location.href = labUser.path +'webapp/activity/detail/_v020800?id='+actID+'&pagetag=02-2&uid='+uid + shareUrl;
    })
        // 按钮点击查看公司详情
    $(document).on('click', '#company_details', function() {
            window.location.href = labUser.path + 'webapp/brand/company/_v020800?id=' + id + '&uid=' + uid + shareUrl;
        })
        //创业基金的关闭按钮
    $(document).on('click tap', '#packet_close', function() {
            $('.brand-packet').removeClass('a-bouncein').addClass('a-bounceout');
            $('.fixed-bg').addClass('none');
            setTimeout(function() {
                $('.brand-packet').removeClass('a-bounceout').addClass('none');
            }, 1000);
        })
        //APP的发送加盟意向点击弹出
    // $(document).on('click', '#brand_suggest', function() {
    //    customerService(id);
    // });
//分享出去的加盟意向点击弹出
    $(document).on('click', '.brand-share-ask,#brand_suggest', function() {
        if(shareFlag){
            var type = $(this).attr('data-type');
            $('.brand-message').css('z-index', '199').data('type',type);
            fadeBrand('.brand-message-share', 'a-fadeinT');
            $('.brand-message-share').css('top',0);
             _czc.push(﻿["_trackEvent",'','brand_detail_intent']);  
        }else{
            customerService(id);
            // else {
            //     $('.fixed-bg').removeClass('none');
            //     $('.seekmodule').removeClass('none');
            
            // }  
        }  
    });
    //咨询弹窗确定按钮
    // $(document).on('click','.btn_makesure',function(){
    //     $('.seekmodule').addClass('none');
    //     $('.fixed-bg').addClass('none');
    //     // customerService(id);
    // })
    // 点击蒙层关闭
    $(document).on('click', '.fixed-bg', function() {
        $(this).addClass('none');
        $('.seekmodule').addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');//提交意向框 消失
        $('#commentback').addClass('none');//提问框消失
        $('.share-reset').click();
        $('#packet_close').click();
        $('.share-title').addClass('none').removeClass('a-fadeinB');
    });
    function removeall() {
        $('.fixed-bg').addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
        $('.share-reset').click();
       
    }
    //查看全部评价
    $(document).on('click','.toAlljudge',function(){
        if(shareFlag){
            tips('请至App中查看');
        }else{
             window.location.href = labUser.path + 'webapp/brand/allchat/_v020800?id='+id+'&uid='+uid;
        }
    })

    //点击项目问答跳到问答详情页(2.7新增.my-ques)
    $(document).on('click', '#brand_toquestion', function() {
        // var b_id = $(this).attr('data-id');
        onEvent('brand_detail_question','',{'type':'brand','id':id});   
        window.location.href = labUser.path + 'webapp/brand/questions/_v020502?id=' + id + '&uid=' + uid +'&pagetag=025-1'+shareUrl;
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
        share_mark = origin_mark;
        content_type = $('.brand-message-share').attr('data-type');

        if (phone == '' || realname == '' || consult == '') {
            tips('请填写完整');
            return false;
        }
        brandDetail.message(id, 0, phone, realname, consult, type, share_mark,content_type);
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
    var brandDetail = {
        // 创业基金
        award: function(id, uid, fund) {
            var param = {};
            param['brand_id'] = id;
            param['uid'] = uid;
            param['fund'] = fund;
            var url = labUser.api_path + '/brand/fetch-fund/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    
                }
            })
        },
        //品牌的详情信息获取
        detail: function(id, uid) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            if(shareFlag){
                param['type'] = 'html5'
            }else{
                param['type'] = "app";
            }
           
            var code = $('#brand_name').attr('data-code');
            var url = labUser.api_path + '/brand/detail/_v020900';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    shareDetail(data.message);
                    getDetail(data.message);
                    getMore(data.message);
                    getJoin(data.message);
                    if(data.message.videos.length == 0){
                       $('#brand_video').remove();
                    };
                    if(data.message.brand.is_recommend!='yes'){
                       $('.brand-p').removeClass('none');
                    }
                    mySwiper = new Swiper('#swiper-container1',{
                        resistanceRatio :0, //滑动到最后一页不能继续滑动
                        onSlideChangeStart: function(swiper){
                            var index = mySwiper.activeIndex; //获取滑动后当前的页面索引
                            if(!shareFlag){
                                changeBrandTitle(index);
                            };
                        },
                        observer:true,//修改swiper自己或子元素时，自动初始化swiper
                        observeParents:true,//修改swiper的父元素时，自动初始化swiper
                        onInit: function(swiper){
                                $('#brand_detail').removeClass('none'); 
                            }
                    }); 
                    //当主页面 没有显示时，spanHeight获取不到数值，因此放在这个位置
                    var dis = $('.dis_coin');
                    var spanHeight= parseFloat(dis.css('height')),spanLH=parseFloat(dis.css('line-height'));
                    if(spanHeight > spanLH*2){
                        dis.addClass('ui-nowrap-multi'); 
                    }else{
                        $('.more_icon').addClass('none');
                        dis.addClass('pb1-5');
                    }   
                    //没有相关视频时，隐藏视频标签页
                    is_showvideo(data.message);
                    favourite(data.message.relation); 
                   
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
        message: function(id, uid, mobile, realname, consult, type, share_mark, content_type) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            param['mobile'] = mobile;
            param['realname'] = realname;
            param['consult'] = consult;
            param['type'] = type;
            param['share_mark'] = share_mark;
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
    brandDetail.history(id,uid,'brand');

    function getDetail(result) {
        var brand = result.brand,comment = result.comment;
        if (!shareFlag) {
            if (brand.fund == 0) {
                $('.brand-np').removeClass('none');
                $('#creat_fund').addClass('none');
            } else {
                $('.brand-p').removeClass('none');
                
            }
        }
       
    // 顶部的轮播图
        $.each(result.banners, function(i, item) {
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
                }) 
    
        $('#brand_name').data('mark', brand.share_mark);
        $('#brand_name').data('code', brand.code);
        $('#brand_name').data('distribution',brand.distribution_id);
        $('.brand_fund').html('￥' + brand.fund);
        $('#creat_fund em').html(brand.fund+'元');
        $('.b-fund').html(brand.fund);
        $('#brand_award').data('fund', brand.fund);
        $('#brand_award').data('fetch', brand.fetched_fund);
        $('.zhuan').text(brand.share_num);
        $('.fav').text(brand.favorite_count);
        $('.view').text(brand.click_num);
        $('.brand_name').text(brand.name);
        $('#category_name2').text(brand.slogan);
        $('#category_name').text(brand.category_name);
        $('#brand_investment').text(brand.investment_min + ' ~ ' + brand.investment_max + ' 万元');
        console.log(brandSplits(brand.name));
        if (brand.slogan!=='') {
            $('#brand_sort2').removeClass('none');
        }
        var str = '';
        for (var i = 0; i < brand.products.length; i++) {
            str +='&nbsp'+ brand.products[i] ;
        }
        $('#brand_products').html(cutString(str,22));
        $('#brand_shops').text(brand.shops_num);
        $('#brand_click').html('<strong class="seen"></strong> ' + brand.click_num);
        // 品牌标签
        var tag_str = '';
        $.each(brand.tags, function(i, item) {
            	tag_str+='<li class="width33 fl lh45 white-bg "  > <span><em class="brand-tags"></em>' + item + '</span></li>';
        });
        $('#brand_tags').append(tag_str);
        if (brand.tags.length==0) {
            $('#brand_tag_none').addClass('none');
        }
       //评价
       if(comment){
            var judgeHtml ='';
            judgeHtml += '<div class=" fline lh45"><span class="tleft f16w ">评价</span><span class="color666 f12 ml05"> ('+brand.comments+')</span>';
            judgeHtml += '</div><div><p class="mt1-33"><img src="'+comment.avatar+'" alt="" class="judger_head mr1-33"><span class="f16">'+comment.nickname+'</span></p>';
            judgeHtml +='<p class="f12 color8a">'+comment.content+'</p></div>';
            judgeHtml += '<div class="tf fline tc lh45 "><button class="toAlljudge">查看全部评价</button></div>';
            $('#brand_judge').html(judgeHtml);
       }else{
            $('#brand_judge').parent('div').remove();
       };

        //项目问答
        if (result.questions.length == 0) {
            $('.ques-asks').addClass('none');
            $('.tf').removeClass('fline')
            if (shareFlag) {
                $('#brand_question').addClass('none');
            }
        }else{
            $('#brand_ques').text(result.questions[0].quiz);
            $('#brand_ans').text(result.questions[0].answer);
        }
        
        // 公司名片
        $('#brand_company').text(brand.company);
        $('#brand_company_add').text(brand.address);
        if (brand.is_auth === "no") {
            $('#brand_auth').addClass('none');
        }
        $('#brand_logo').attr('src', brand.logo);

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
        }else{
            $('.brand-company-h').css('padding-bottom','8rem');
        };
        
        if(result.inviter != '0' && !shareFlag){
            $('.seektips').text('将由您的邀请经纪人'+result.inviter.realname+' ('+result.inviter.city_name+') 进行品牌跟进！')
        }  
    };

    function getVideos(result){
            var videos = result.videos;
            $.each(videos, function(i, item){
                var str = '';
                    str += '<li class="ui-border-t" data-id="'+item.id+'"><div class="l video_img"><p class="playlogo mb0"><img src="/images/play.png" alt=""></p>';
                    str += '<img src="'+item.image+'" alt="" style="height:100%"></div>';
                    str +='<div class="video_intro "><p class="f16 mb0 h45">'+cutString(item.subject,22)+'</p>';
                    str +='<p class="f12 mb0 color8a">录制时间：<span>'+unix_to_datetime(item.created_at)+'</span></p>';
                    str +='<div class="f12 video_dis color8a">视频描述：<span>'+removeHTMLTag(item.description)+'</span></div></div>';
                    str +='<div class="clearfix"></div></li>'
                $('#relativevideo').append(str);
                $('.no-data').removeClass('none');
            });
            if (videos==''||videos==undefined||videos.length==0) {
                $('#sec_video').removeClass('bgwhite');
                $('.videoss').removeClass('none');
            }
        };


    // 图文详情 项目介绍 图集
    function getMore(result) {
        // console.log(result);
        $('.pic_text').html(result.brand.detail);
           //公司详情
        if ($('#brand_company').text()!=='') {
            $('.brand-company-h').removeClass('none');
        }
        $.each(result.brand.detail_images, function(i, item) {
                var str = '';
                str += '<img src="' + item.src + '" class="onimgs" alt="" data-picindex="'+i+'">';
            $('#brand_images').append(str);
        });
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
                    str+='<li class="share-li tleft lh45 white-bg border-8a-b"><em></em>'+item+'</li>'
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
    //分享佣金的展示与隐藏
    var dis_coin = $('.dis_coin'),more_icon =$('.more_icon img'),showMore = true;

    $(document).on('click','.more_icon',function(){
        if(showMore){
           dis_coin.removeClass('ui-nowrap-multi');
            more_icon.css({'transform':'rotate(180deg)',
                            '-webkit-transform':'rotate(180deg)',
                            '-o-transform':'rotate(180deg)'});
            showMore = false ;
        }else{
            dis_coin.addClass('ui-nowrap-multi');
            more_icon.css({'transform':'rotate(0deg)',
                            '-webkit-transform':'rotate(0deg)',
                            '-o-transform':'rotate(0deg)'});
            showMore = true ;
        }
    })
    //了解详细规则
    $(document).on('click','#knowdetail',function(){
        window.location.href =labUser.path + 'webapp/protocol/moreshare/_v020700';
    })
   
   // 点击我要佣金进行分享
   $(document).on('click','#get_coin,.getcoin',function(){
        shareBrand();
   })

    //查看我的红包
    $(document).on('tap','.toPacket',function () {
        toPacket();
    });
    //点击跳转到机器人客服
    $(document).on('click','.brand-collect-contact',function(){
        toRobot(id);
    })
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
            window.location.href = labUser.path +'webapp/vod/detail/_v020800?id='+id+'&uid='+uid+'&share_mark='+origin_mark+'is_share=1';
        }else{
            window.location.href = labUser.path +'webapp/vod/detail/_v020800?id='+id+'&uid='+uid+'&pagetag=05-4';
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
    //移动端title栏显示是否有视频标签
    function is_showvideo(obj){
        if(obj.videos.length == 0){
            $('#brand_video').remove();
            showVideo(0);
        }else{
            getVideos(obj);
            showVideo(1);
        }
    }
    // function collect(id, uid, type) {
    //     var param = {};
    //     param['id'] = id;
    //     param['uid'] = uid;
    //     param['type'] = type;
    //     var url = labUser.api_path + '/brand/collect';
    //     ajaxRequest(param, url, function(data) {
    //         if (data.status) {
    //             // console.log('1');
    //             getBrandFavorite(id,type);
    //         } else {
    //             return false
    //         }
    //     });
    // }
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
            // $('#brand_more_share').removeClass('none');
            var brands='brandID'+id;
             //转发后每产生一次阅读，获得奖励
            if (selfObj.share_reward_unit != 'none' && (!localStorage.getItem(brands))) {
                // disfx(origin_mark, 'view', '0', origin_code);
                getReward(origin_mark, 'view',0, origin_code)
                localStorage.setItem(brands,id);
            }
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

//二次分享、品牌
    function sencondShare(type){
        if($('#brand_name').data('distribution') > 0){
            var getcodeurl = labUser.api_path + '/index/code/_v020500';
            ajaxRequest({}, getcodeurl, function (data) {
                var newcode = data.message;//code
                var logsurl = labUser.api_path + "/share/share/_v020500";
                ajaxRequest({
                    uid: '0',
                    content: 'brand',
                    content_id: id,
                    source: 'weixin',
                    code:newcode,
                    share_mark: origin_mark
                }, logsurl, function (data) {
                    disfx(origin_mark, type, 0, newcode);
                });
            });
        }

    }

 //分享页,分销接口1
function disfx(share_mark, type, uid, code) {
    var url = labUser.api_path + "/share/collect-score/_v020500",
        share_mark = share_mark || $('#brand_name').data('mark');
    ajaxRequest({share_mark: share_mark, type: type, uid: uid, relation_id: code}, url, function (data) {
    });
}
//分享页，分享记录入库
function fxlogs(uid, content, content_id, source,code, share_mark) {
    var url = labUser.api_path + "/share/share/_v020500";
    ajaxRequest({
        uid: uid,
        content: content,
        content_id: content_id,
        source: source,
        code:code,
        share_mark: share_mark
    }, url, function (data) {
    });
}
// 移动端收藏
function getBrandFavorite(id,type){
    if (isAndroid) {
        javascript:myObject.getBrandFavorite(id,type);
    } 
    else if (isiOS) {
        var data = {
           "id":id,
           "type":type
        }
        window.webkit.messageHandlers.getBrandFavorite.postMessage(data);
    }
}
//跳转到移动端机器人客服
function toRobot(id) {
    if (isAndroid) {
        javascript:myObject.toRobot(id);
    } 
    else if (isiOS) {
        var data = {
           "id":id
        }
        window.webkit.messageHandlers.toRobot.postMessage(data);
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
//咨询客服
function customerService(id){
    if (isAndroid) {
        javascript:myObject.customerService(id);
    } 
    else if (isiOS) {
        var data = {
           "id":id
        }
        window.webkit.messageHandlers.customerService.postMessage(data);
    }
}
function showShare() {
    // shareOut('title', window.location.href, '', 'header', 'content');
    var args = getQueryStringArgs(),
        id = args['id'] || '0';
    var title = $('.share-li-sel').text();
    var pageUrl =labUser.path+ 'webapp/brand/detail/_v020803?id=' + id + '&uid=' + uid;
    var img = $('#brand_logo').attr('src');
    var header = '';
    var content = "项目："+ cutString($('#brand_name').text(),8)+'\r\n'+"行业："+$('#category_name').text();
        shareOut(title, pageUrl, img, header, content,'','','','','','','share','brand',id);//分享

};
  
