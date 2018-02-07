$('body').addClass('bgcolor');
new FastClick(document.body);
// FastClick.attach(document.body)
 var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var origin_mark = args['share_mark'] || 0;//分销参数，分享页用
    var  origin_code = args['code'] || 0;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
    // if (shareFlag) {
    //     uid=0;
    // }

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

    //点击收藏--热区太小！
    $(document).on('click ', '.b-collect', function() {
        favourite();
        var obj={
            'type':'brand',
            'id':id
        }
        onEvent('favourite','',obj);
    });
    //分享出去的标语
    $(document).on('click ', '.share-li', function() {
     	var text =$(this).text();
     	$(this).addClass('share-li-sel');
        $('.share-title').addClass('none');
        // alert($(this).text());
        showShare();
        $('.fixed-bg').addClass('none');
        $(this).removeClass('share-li-sel');
    });

        // 按钮点击查看公司详情
    $(document).on('click', '#company_details', function() {
            window.location.href = labUser.path + 'webapp/brand/company/_v020502?id=' + id + '&uid=' + uid + shareUrl;
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
    $(document).on('click', '#brand_suggest ', function() {
        fadeBrand('#brand-mes', 'a-fadeinT');
        $('.brand-message').css('z-index', '99');
        var obj={
            'type':'brand',
            'id':id
        };
        onEvent('brand_detail_intent','',obj);
    });
    //分享出去的加盟意向点击弹出
    $(document).on('click', '.brand-share-ask ', function() {
        var type = $(this).attr('data-type');

        $('.brand-message').css('z-index', '99').data('type',type);
        fadeBrand('.brand-message-share', 'a-fadeinT');
        $('.brand-message-share').css('top',0);
         _czc.push(﻿["_trackEvent",'','brand_detail_intent']);  
       
    });
    // 点击蒙层关闭
    $(document).on('click', '.fixed-bg', function() {
        $(this).addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
        $('.share-reset').click();
        $('#packet_close').click();
        $('.share-title').addClass('none').removeClass('a-fadeinB');
    });
    function removeall() {
        $('.fixed-bg').addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
        // $('.brand-message input, .brand-message textarea').text('');
        $('.share-reset').click();
        // $('#packet_close').click();
        // $('.share-title').addClass('none').removeClass('a-fadeinB');
    }
    //点击项目问答跳到问答详情页(2.7新增.my-ques)
    $(document).on('click', '#brand_toquestion,.my-ques', function() {
        // var b_id = $(this).attr('data-id');
        onEvent('brand_detail_question','',{'type':'brand','id':id});
        
        window.location.href = labUser.path + 'webapp/brand/questions/_v020502?id=' + id + '&uid=' + uid +'&pagetag=025-1'+shareUrl;
        
    });
    //图文详情里的推荐品牌点击跳转
    $(document).on('click', '.brand-companys', function() {
        if(shareFlag){
            var b_id = $(this).data('id');
            window.location.href = labUser.path + 'webapp/brand/detail/_v020502?id=' + b_id + '&uid=0&pagetag=08-9'+ shareUrl;;
        }
        else{
            var b_id = $(this).data('id');
            if (isiOS) {
                pushToBrandDetail(b_id);
            }else if(isAndroid){
                window.location.href = labUser.path + 'webapp/brand/detail/_v020502?id=' + b_id + '&uid=' + uid+'&pagetag=08-9'+ shareUrl;;
            }
        }
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
        // getReward(share_mark,'intent',uid,origin_code);

        

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
        // var share_mark = $('#brand_name').attr('data-mark');
        // var code = $('#brand_name').attr('data-code');

        // getReward(share_mark,'intent',uid,origin_code);
        // sencondShare('intent');

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
                    // console.log(fund)
                    // funds(fund);
                }
            })
        },
        //品牌的详情信息获取
        detail: function(id, uid) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
             var code = $('#brand_name').attr('data-code');
            var url = labUser.api_path + '/brand/detail/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    shareDetail(data.message);
                    getDetail(data.message);
                    getMore(data.message);
                    getJoin(data.message);
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
                    // $('.fixed-bg, #brand-mes').addClass('none');
                    // removeall();
                    var obj={
                            'type':'brand',
                            'id':id
                        }
                     tips('提交成功');
                    // $('.fixed-bg').click();
                    // $('.b-reset').click();
                     
                    // if (isiOS) {
                        $('.fixed-bg').addClass('none');
                        $("input[name='realnames']").val('');
                        $("input[name='phones']").val('');
                        $("textarea[name='consults']").val('');
                        $('.brand-message').css('z-index', '-1');
                        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
                    // }
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
        var brand = result.brand;
        if (!shareFlag) {
            if (brand.fund == 0) {
                $('.brand-np').removeClass('none');
                // $('.brand-p').addClass('none');
            } else {
                $('.brand-p').removeClass('none');
                // $('.brand-np').addClass('none');
            }
        }

        // 顶部的轮播图
        if (urlPath.indexOf('detail')>0) {
            $.each(result.banners, function(i, item) {
            var str = '';
                str += '<div class="swiper-slide"><img src="' + item.src + '" alt="" /></div>';
                $('.swiper-brand').append(str);
                    var swiper = new Swiper('.swiper-container', {
                        pagination : '.swiper-pagination',
                        paginationType : 'custom',
                        // autoplay:'5000',
                        paginationCustomRender: function (swiper, current, total) {
                            return '<span class="f16">'+current+'</span>' + ' / ' + total;
                        }
                    });
            });
        }
       
        $('#brand_name').data('mark', brand.share_mark);
        $('#brand_name').data('code', brand.code);
        $('#brand_name').data('distribution',brand.distribution_id);
        $('.brand_fund').html('￥' + brand.fund);
        $('.b-fund').html(brand.fund);
        $('#brand_award').data('fund', brand.fund);
        $('#brand_award').data('fetch', brand.fetched_fund);
        $('#brand_fav').text(brand.favorite_count);
        $('#brand_fav2').text(brand.click_num);
        $('#brand_name').text(brand.name);
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
        $.each(brand.tags, function(i, item) {
            var str = '';
            // for (var i = 0; i < brand.tags.length; i++) {
            	str+='<li class="width33 fl lh45   white-bg "  > <span><em class="brand-tags"></em>' + item + '</span></li>'
            // }
            // str += '<li class="width33 fl lh45   white-bg"> <span><em class="brand-tags"></em>' + item[i] + '</span></li>';
            $('#brand_tags').append(str);
        });
        if (brand.tags.length==0) {
            $('#brand_tag_none').addClass('none');
        }
       
        //项目问答
        if (result.questions.length == 0) {
            $('.ques-asks').addClass('none');
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

        
        //底部收藏

        $('.b-collect').data('fav', result.relation.is_favorite);
        // $('.b-collect').attr('data-fav',result.relation.is_favorite);
        var favs = result.relation.is_favorite;
        // console.log('fav'+favs)
        if (favs == 0) {
            $('.b-collect').removeClass('brand-collected').addClass('brand-collect');
        } else if (favs == 1) {
            $('.b-collect').removeClass('brand-collect').addClass('brand-collected');
        }

    }
 
    // 图文详情 项目介绍 图集
    function getMore(result) {
        // console.log(result);
        $('#brand_more_detail').html(result.brand.detail);
        $.each(result.brands, function(index, item) {
            if(index >=5){
                return false;
            }
          
                var str = '';
                str += '<div class="white-bg brand-company brand-companys   fline" data-id="' + item.id + '" style="margin-bottom:1px">';
                str += '<img src="' + item.logo + '" alt="" class="company mr1-33 fl">';
                str += '<div class="fl width70">';
                str += '<em class="service f12 mr1">' + item.category_name + '</em><span class="f14 f-bold">' + item.name + '</span>';
                if (item.slogan!==undefined) {
                    str += '<div class="brand-desc f14 color999 mb05">' + cutString(delHtmlTag(item.slogan),12) + '</div>';
                }
                str += '<div class="brand-desc f14 color999 mb05">' + cutString(delHtmlTag(item.detail),36) + '</div>';
                str += '<p class="f14 mb05">';
                str += '<span class="color8a">投资额：</span> <span class="color-red">' + item.investment_min + ' ~ ' + item.investment_max + '万元</span>';
                // str+='<span class="color8a ml1">招商地区：</span><span class="color8a">华东地区</span>';
                str += '</p>';
                for (var i = 0, j = item.keywords.length; i < j; i++) {
                    str += '<a class="tags-key border-8a-radius">' + item.keywords[i] + '</a>';
                }
                str += '</div>';
                str += '<div class="clearfix"></div>';
                str += '</div>';
            
            $('#brand_brands').append(str);
        });
           //公司详情
        if ($('#brand_company').text()!=='') {
            $('.brand-company-h').removeClass('none');
        }
        $.each(result.brand.detail_images, function(i, item) {
            // for (var i = 0, j = result.brand.detail_images.length; i < j; i++) {
                var str = '';
                str += '<img src="' + item.src + '" class="onimgs" alt="" data-picindex="'+i+'">';
            // }
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
    //加盟简介
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
    function getHistory(result) {
        
    }
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
    //查看我的红包
    $(document).on('tap','.toPacket',function () {
        toPacket();
    });
    //点击跳转到机器人客服
    $(document).on('click','.brand-collect-contact',function(){
        toRobot(id);
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

   
    //收藏
    function favourite() {
        var fav = $('.b-collect').attr('data-fav');
        if (fav == 0) {
            collect(id, uid, 'do');
            // setFavourite(1);
            $('.b-collect').removeClass('brand-collect').addClass('brand-collected').data('fav', 1);
            // tips('收藏成功')
        } else {
            collect(id, uid, 'undo');
            // setFavourite(0);
            $('.b-collect').removeClass('brand-collected').addClass('brand-collect').data('fav', 0);
            // tips('取消收藏');
        }
    }

    function collect(id, uid, type) {
        var param = {};
        param['id'] = id;
        param['uid'] = uid;
        param['type'] = type;
        var url = labUser.api_path + '/brand/collect';
        ajaxRequest(param, url, function(data) {
            if (data.status) {
                // console.log('1');
                getBrandFavorite(id,type);
            } else {
                // alert(data.message);
                return false
            }
        });
    }
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
            $('#brand_more_share').removeClass('none');
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
// shareBrand();
function showShare() {
    // shareOut('title', window.location.href, '', 'header', 'content');
    var args = getQueryStringArgs(),
        id = args['id'] || '0';
    var title = $('.share-li-sel').text();
    var pageUrl =labUser.path+ 'webapp/brand/detail/_v020502?id=' + id + '&uid=' + uid;
    var img = $('#brand_logo').attr('src');
    var header = '';
    var content = "项目："+ cutString($('#brand_name').text(),8)+'\r\n'+"行业："+$('#category_name').text();
   
    // var share_mark= $('#brand_name').attr('data-mark');
    // var code = $('#brand_name').attr('data-code');

    if($('#brand_name').data('distribution') > 0){
        var args = getQueryStringArgs(),
                id = args['id'] || '0';
        var pageUrl =labUser.path+ 'webapp/brand/detail/_v020502?id=' + id + '&uid=' + uid+ '&share_mark=' + $('#brand_name').data('mark');//用来追踪原始分享者
        var share_mark = $('#brand_name').data('mark');
        var url = labUser.api_path + '/index/code/_v020500';
        ajaxRequest({}, url, function (data) {
            var code = data.message;//code
            pageUrl = pageUrl + '&code=' + code;
            shareOut(title, pageUrl, img, header, content, '', '', '', '', share_mark, code, 'share', 'brand', id);//分享
        });
    }else{
        var pageUrl = labUser.path+ 'webapp/brand/detail/_v020502?id=' + id + '&uid=' + uid ;
        shareOut(title, pageUrl, img, header, content,'','','','','','','','','');//分享
    }

};
  
