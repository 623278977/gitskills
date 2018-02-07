@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="http://cdn.bootcss.com/Swiper/3.3.0/css/swiper.css">
    <link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>

@stop
@section('main')
    <section id="act_container" class="none">
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
        <p style="padding-left: 1rem;" class="f14">完善信息</p>
        <form action="post"  id="brand_note">
            <p>
                <label for="name">姓名</label><input type="text" id="brand_realname" placeholder="请输入姓名">
            </p>
            <p>
                <label for="name">联系电话</label><input type="text" id="brand_tel" placeholder="请输入手机号">
            </p>
            <p class="relative none" id="brand_getcode">
                <label for="name">验证码</label><input type="text" id="brand_code" placeholder="请输入短信验证码">
                <input  name="countss" id="brand_count" value="获取验证码" type="button" />
            </p>
            <p>
                <label for="name">地区</label><input type="text" readonly id="brand_area" data-id='' placeholder="请选择所在城市">
            </p>
            <p id="p_address">
                <label for="name">地址</label><input type="text" id="brand_add"placeholder="请填写完整地址">
            </p>
                <div class="relative">
                    <textarea name="" id="brand_textarea"  maxlength="150" placeholder="咨询/留言内容" ></textarea>
                     <div class="brand-holder"><img src="{{URL::asset('/')}}/images/edit_hold.png" alt="" style="width:1.4rem;"></div>
                     <div class="brand-left">150</div>
                </div>
                
                
        </form>
        <a href="javascript:;" class="btn btn-brand-sub" id="brand-post">提交</a>
    </section>
    <div class="brand-area none">
        <div class="head"> 选择你所在的省市  </div>
        <div class="area-con">
            <ul class="province">
            </ul>
            <ul class="city">
            </ul>
        </div>
    </div>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/zepto/swiper.min.js"></script>
    <script src="http://cdn.bootcss.com/Swiper/3.3.0/js/swiper.min.js"></script>
    <script>
    Zepto(function () {
        new FastClick(document.body);
        var args= getQueryStringArgs();
        document.title = '我要留言';
        var urlPath = window.location.href,
            id = {{$id}},
            uid = args['uid']; 
            var address = [];
            $('#brand_area').val('')
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false; 
        if (shareFlag) {
            $('#brand_getcode').removeClass('none');
            $('#brand_realname').val('');
            $('#brand_tel').val('');
            uid = 0;
            $('#p_address').addClass('none');
        }

        var noteDetail = {
            // 获取用户详情
            detail:function (user_outh) {
                var param= {};
                param['user_outh'] = user_outh;
                var url = labUser.api_path + '/user/getuserdetail';
                // var url = '/api/user/getuserdetail';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        getNoteDetail(data.message);
                    }
                })
            },
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
            // 验证码提交
            // postCode:function (tel,code,type) {
            //     var param= {};
            //     param['username'] = tel;
            //     param['code'] = code;
            //     param['type'] = type;
            //     var url = labUser.api_path + '/identify/checkidentify';
            //     // var url = '/api/identify/checkidentify';
            //     ajaxRequest(param,url,function (data) {
            //         if (data.status) {
            //             $('#brand-post').data('send','ok');     
            //         }else{
            //             $('#brand-post').data('send','false');   
            //             alert(data.message);  
            //         }
            //     })
            // },
            // 留言内容提交
            postDetail:function (id,uid,name,tel,area,add,textarea,type,code) {
                var param= {};
                param['id'] = id;
                param['uid'] = uid;
                param['realname'] = name;
                param['mobile'] = tel;
                param['zone_id'] = area;
                param['address'] = add;
                param['consult'] = textarea;
                param['type'] = type;
                param['code'] = code;
                var url = labUser.api_path + '/brand/message';
                // var url = '/api/brand/message';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        // postDetail(data.message);
                        alert('留言成功');
                        if (shareFlag) {
                            window.location.href= labUser.path+'webapp/brand/detail?&is_share&pagetag=02-1-2&id='+id+'&uid='+uid;
                        }else{
                            // window.location.href= labUser.path+'webapp/brand/detail?&pagetag=02-1-2&id='+id+'&uid='+uid;
                            // window.history.back();
                            // return false;
                            historyBack(1);
                        }
                    }else{
                        alert(data.message);
                    }
                })
            },
            //获取验证码
            getCode:function (username,type) {
                var param={};
                param['username'] = username;
                param['type'] =type;
                var url = labUser.api_path + '/identify/sendcode';
                // var url = '/api/identify/sendcode';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        console.log('cg');
                    }
                })
            },
            //获取接口省市地址
            areaDetail:function () {
                var param= {};
                param['id'] = id;
                var url = labUser.api_path + '/zone/ajaxallzones';
                // var url = '/api/zone/ajaxallzones';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        localArea(data.message);
                    }
                })
            }
        };
        noteDetail.detail(uid);
        noteDetail.brandDetail(id,uid);
        noteDetail.areaDetail(id);
        function localArea(result) {
            $.each(result,function (i,item){
                var areas={};
                areas['id']=item.id;
                areas['name']=item.name;
                areas['cityList']=item.cityList;
                address.push(areas);
            });
            var str = '';
            for (var i = 0; i < address.length; i++) {
                str+='<li class="pro" data-i='+i+'>'+address[i].name+'</li>';
            }
            $('.province').append(str);
           
        }
        $(document).on('click','.province li',function () {
            $(this).addClass('sel').siblings().removeClass('sel');
            var index = $(this).attr('data-i');
            $('.city').empty();
            var str='';
            $.each(address[index].cityList,function (i,item) {
                str+='<li class="city" data-i='+i+' data-id='+item.id+'>'+item.name+'</li>';
            });
            $('.city').append(str);
            $('#brand_area').val($(this).text())
        });
        $(document).on('click','.city li',function () {
            $(this).addClass('sel').siblings().removeClass('sel');
            var text = $('#brand_area').val();
            text = text.split(' ')[0];
            text = text +' '+ $(this).text();
            $('#brand_area').val(text);
            var zoneId = $(this).attr('data-id');
            $('#brand_area').attr('data-id',zoneId)
            $('.brand-area').addClass('none');
        });
        $('#brand_area').on('click',function () {
            $('.brand-area').removeClass('none');
            setTimeout(function () {
                var c = window.document.body.scrollHeight;
                window.scroll(0, c);
            }, 500);
            return false;
        });

        $('.brand-area .head span').on('click',function () {
            $('.brand-area').addClass('none')
        });
        var inputtext = document.getElementById('brand_textarea');
        inputtext.oninput = function () {
            var text = this.value;
            var left = 150 - text.length;
            $('.brand-left').text(left);
        }
        $('#brand_textarea').on('focus',function () {
            $('.brand-holder').addClass('none');
        }).on('blur',function () {
            if ($(this).val().length==0) {
                $('.brand-holder').removeClass('none');
            }           
        });
//        .on('keyup',function (argument) {
//            var left = 150 -$(this).val().length;
//            $('.brand-left').text(left);
//        });
       
        $(document).on('click','#brand_count',function () {
            var username = $('#brand_tel').val(),
                type='authorize';
            if (username == '') {
                alert('请输入手机号');
                return false
            }else if(!(/^1[34578]\d{9}$/.test(username))){
                alert('手机号格式有误');
                return false
            }
            settime(this);
            noteDetail.getCode(username,type);
        })
        // 倒计时
        var countdown=60; 
        function settime(obj) {             
                if (countdown == 0) { 
                    obj.removeAttribute("disabled");    
                    obj.value="获取验证码"; 
                    obj.style.backgroundColor="#8ec5e9";

                    countdown = 60; 
                    return;
                } else { 
                    obj.setAttribute("disabled", true); 
                    obj.style.backgroundColor="#ddd;"
                    obj.value="重新发送(" + countdown + ")"; 
                    countdown--;
                 } 
            setTimeout(function() { 
                settime(obj) }
                ,1000) 
        }
        function getNoteDetail(result) {
            $('#brand_realname').val(result[0].realname);
            $('#brand_tel').val(result[0].username);
            if (uid ==0) {
                $('#brand_realname').val('');
                $('#brand_tel').val('');
            }
        };
        function getBrandDetail(result) {
            if (!shareFlag) {
                setPageTitle('在线提交意向');
            }  
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
        $('#brand-post').on('click',function () {
            var name= $('#brand_realname').val(),
                tel =$('#brand_tel').val(),
                area =$('#brand_area').attr('data-id'),
                add =$('#brand_add').val(),
                code = $('#brand_code').val(),
                textarea =$('#brand_textarea').val();
            if(shareFlag){
                var type = 'authorize';
                // noteDetail.postCode(tel,code,type);
                 var param= {};
                param['username'] = tel;
                param['code'] = code;
                param['type'] = type;
                var url = labUser.api_path + '/identify/checkidentify';
                    // var url = '/api/identify/checkidentify';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        type = shareFlag?'html5':'app';
                        add = '空';
                        noteDetail.postDetail(id,uid,name,tel,area,add,textarea,type,code); 
                    }else{
                        alert(data.message);  
                    }
                })
            }else{
                if (name ==''||tel==''||area==''||add==''||textarea=='') {
                    alert('请填写完整');
                    return false
                }
                type = shareFlag?'html5':'app';
                noteDetail.postDetail(id,uid,name,tel,area,add,textarea,type);
            }
        });


        //     var type = shareFlag?'html5':'app';
        //         if (shareFlag) {
        //             add =$('#brand_add').val()+'空';
        //             if (name ==''||tel==''||area==''||add==''||code==''||textarea=='') {
        //                 alert('请填写完整');
        //                 return false
        //             }
        //             // var type = 'authorize';
        //             // noteDetail.postCode(tel,code,type);
        //             type = shareFlag?'html5':'app';
        //             noteDetail.postDetail(id,uid,name,tel,area,add,textarea,type,code);    
        //         }else{
        //             if (name ==''||tel==''||area==''||add==''||textarea=='') {
        //                 alert('请填写完整');
        //                 return false
        //             }
        //             noteDetail.postDetail(id,uid,name,tel,area,add,textarea,type);
        //         }
          
        // });
       
    });
   </script>
   <script>	

   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var title = $('#resultName').text();
            var url = window.location.href;
            var img = $('.zb_banner>img').attr('src');
            var header = '专版';
            var content = cutString($('.zb_p span').text(), 18);
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
          //返回上一级
        function historyBack(num) {
            if (isAndroid) {
                javascript:myObject.historyBack(num);
            } else if (isiOS) {
                var data = {
                    "id": num
                }
                window.webkit.messageHandlers.historyBack.postMessage(data);
            }
        }

   </script>
@stop