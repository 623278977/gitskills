Zepto(function(){
	var args = getQueryStringArgs(),
		id=args['id'],
		uid = args['uid'] || 0,
		org_mark = args['share_mark'],
		origin_code = args['code'] || 0;
	var no_mes = '<div class="tc"><img src="/images/nomessage_icon1.png" style="width:400px;"></div>';
	var reg = /^1[34578]\d{9}$/;
	var Brand = {
		detail:function(id,uid){
			var url = labUser.api_path + '/brand/detail/_v020700';
			ajaxRequest({'id':id,'uid':uid,'type':'html5'},url,function(data){
				if(data.status){
					var brand = data.message.brand;
					var activity = data.message.activity;
					var str = '';
			        for (var i = 0; i < brand.products.length; i++) {
			            str += brand.products[i]+'&nbsp';
			        }
					$('.brand-img').attr('src',brand.logo);
					$('#brand_logo').attr('src',brand.logo);
					$('.brand-title').html(brand.name);//品牌名称
					$('#cates').html(brand.category_name);//分类
					$('#slogan').html(brand.slogan);//标语
					$('#invest').html(brand.investment_arrange+'元');//启动资金
					$('#products').html(str);//主营产品
					$('#storenum').html(brand.shops_num);//门店个数
					$('#sharenum').text(brand.share_num);//转发数
					$('#viewnum').text(brand.click_num);//浏览数
					$('#favnum').text(brand.favorite_count);//收藏数
					$('#visiters').text(brand.count_quiz);//提交项目问答人数
					$('#joiners').text(brand.count_tel);//申请加入人数

					var branderweima = brand.qrcode; 
					$('.branderwei').attr('src',branderweima);

					if(brand.detail == ''){
						$('.pruintro_detail').html(no_mes);
					}else{
						$('.pruintro_detail').html(brand.detail);//项目介绍
					};
					if(brand.summary == ''){
						$('.comintro_detail').html(no_mes);
					}else{
						$('.comintro_detail').html(brand.summary);//公司介绍
					}
					if(brand.league == ''){
						$('.policy_detail').html(no_mes);
					}else{
						$('.policy_detail').html(brand.league);//加盟政策
					}
					
					
					
				//相关报名的活动
					if(activity.length >0){
						var actHtml='';
						$.each(activity,function(i,j){
							actHtml +='<div class="w1172 activity" data-id="'+j.id+'"><div class="horn">活动</div>';
							actHtml +='<div class="act_img l"><img src="'+j.list_img+'"></div>';
							actHtml +=' <div class="act_intro l"><p class="fs24 b mb30">'+j.subject+'</p>';
							actHtml +='<p class="mb15"><em><img src="/images/020700pc/p3.png"></em>';
							actHtml +='<span class="time">'+unix_to_fulltime(j.begin_time)+' ~ '+unix_to_fulltime(j.end_time)+'</span></p>';	
							if(j.zone_name){
								var zoneHtml ='';
								$.each(j.zone_name,function(index,item){
									if(index<j.zone_name.length -1 ){
										zoneHtml += item +'、';
									}else{
										zoneHtml += item ;
									}
								});
								actHtml += '<p class="mb15"><em><img src="/images/020700pc/local.png"></em>';
								actHtml +='<span class="local">'+zoneHtml+'</span></p>';
							}
							if(j.num != 0){
								actHtml += '<p class="mb15"><em><img src="/images/020700pc/persons.png" alt=""></em><span class="persons">限额'+j.num +'人</span></p>'
							};
							actHtml +='</div><div class="sign_num"> <span class="fs14">已有'+j.sign_num+'人报名</span><button class="sign">我要报名</button></div>';
							actHtml += '<img src="/images/020700pc/erwei_icon.png" alt="" class="act_erwei" data-src='+j.qrcode+'></div>';
						})
						$('#rel_act').html(actHtml);
					}else{
						$('#rel_act').addClass('none');
					}
				//现场实景
					if(brand.detail_images.length > 0){
						var imgHtml = '';
						$.each(brand.detail_images,function(i,j){
							imgHtml += '<img src="'+j.src +'">';
						});
						$('.location_detail').html(imgHtml);
					}else{
						$('.location_detail').html(no_mes);
					};

				//浏览获得分销奖励（同时只对第一次浏览有奖励）
					var brands='brandID'+id;
					if (data.message.brand.share_reward_unit != 'none' && (!localStorage.getItem(brands))) {
		                // disfx(origin_mark, 'view', '0', origin_code);
		                getReward(origin_mark, 'view',0, origin_code)
		                localStorage.setItem(brands,id);
		            };

				}
			})
		},
		//项目问答
		questions:function(param){
			var url =labUser.api_path+'/brand/question/_v020700';
			ajaxRequest(param,url,function(data){
				if(data.status){
					var brandName=data.message.brand_name;
					var ansHtml = '';
					if(data.message.questions.length > 0){
						$.each(data.message.questions,function(i,j){
							ansHtml +='<li class="que_ans"><img src="'+j.avatar+'" alt="" class="l">';
							ansHtml += '<div class="r que_ans_detail"> <p class="fs18 mt30 mb30">';
							ansHtml += '<span class="ques">'+j.quiz+'</span> <span class="r fs14">'+j.created_at_formart+'</span></p>';
							ansHtml +=' <div class="ans_pro"><span class="cea5200 l bName">'+brandName+':</span>';
							ansHtml +='<span class="l ans_detail">'+j.answer+'</span></div></div></li>';
						});
						$('#questions').html(ansHtml);
					}else{
						$('#questions').html(no_mes);
					};
				}
			});
		},
		//提问
		toask:function(param){
			var url = labUser.api_path +'/brand/ask/_v020700';
			ajaxRequest(param,url,function(data){
				if(data.status){
					$('.tanchuang').text('发表成功').show();
					setTimeout(function(){
						$('.tanchuang').hide();
						$('.reset').click();
					},2000);
				}else{
					alert(data.message);
				}
			})
		},
		//提交留言
		// sendMessage:function(param){
		// 	var url = labUser.api_path +'/brand/message/_v020500';
		// 	ajaxRequest(param,url,function(data){
		// 		if(data.status){
		// 			$('.tanchuang').text('发表成功').show();
		// 			setTimeout(function(){
		// 				$('.tanchuang').hide();
		// 			},2000);
		// 		}
		// 	})
		// },
		//电话留存
		getTelNum:function(param){
			var url = labUser.api_path +'/brand/tel/_v020700';
			ajaxRequest(param,url,function(data){
				if(data.status){
					$('.tanchuang').text('发送成功').show();
					setTimeout(function(){
						$('.tanchuang').hide();
						$('#phonenum').val('');
					},2000);
				}
			})

		}

	};
	Brand.detail(id,uid);
	Brand.questions({'brand_id':id,'page':1});

	//页签切换
	$('.tab span').click(function(){
		$(this).addClass('active');
		$(this).siblings('span').removeClass('active');
		var index = $('.tab span').index(this);
		var indexDom = $('.items_intro>div').eq(index);
		$('.brand_pro').text('/'+$(this).text());
		indexDom.removeClass('none').siblings('div').addClass('none');;
		if(index == 4){
			$('#items').removeClass('none');
			$('.toask').text('我要提问').removeClass('none');
			$(window).scrollTop(indexDom.offset().top-300);
		}else if(index < 4){
			$('#items').addClass('none');
			$('.toask').addClass('none');
			$(window).scrollTop(indexDom.offset().top-75);
		}
		
	})
	//页签切换时的跳至锚点document.getElementById("divId").scrollIntoView();
	//选择回复时间
	$('.re_visit span').click(function(){
		$(this).find('img').attr('src','/images/020700pc/checked.png');
		$(this).siblings('span').find('img').attr('src','/images/020700pc/un_check.png');
		$('.re_visit').attr('type',$(this).attr('data-type'));
	})
	 //验证验证码
	 function check(params){
	 	var url = '/identify/verifycaptcha';
	 	var value = $('input[name="captcha"]').val();
	 	ajaxRequest({'captcha':value},url,function(data){
	 		if(data.status){
	 			Brand.toask(params);
	 		}else{
	 			error(data.message);
	 		}
	 	})
	 };
	 //点击我要提问跳转至相应锚点
	 $('.toask').click(function(){
	 	if ($('html').scrollTop()) {
            $('html').scrollTop(430);
            $('#realname').focus();
            return false;
        }
        $('body').scrollTop(430);
        $('#realname').focus();
        return false;
	 })
	 
	 //提问
	 $('.submit').click(function(){
	 	var params={};
	 		params['id']=id;
	 		// params['uid'] =uid;
	 		params['mobile'] = $('#telnum').val();
	 		params['realname'] = $('#realname').val();
	 		params['content']= $('#consult').val();
	 		params['reply_time_limit'] = $('.re_visit').attr('type');
	 		// params['type'] = 'html5';
	 	if(params['mobile'] =='' || params['realname']=='' || params['content']==''){
	 		error('请完善相关信息')
	 		return;
	 	}else if(!reg.test(params['mobile'])){
	 		error('手机号格式不正确')
	 		return;
	 	}
	 	check(params);	
	 })
	 //电话留存
	 $('#join').click(function(){
	 	var phone=$('#phonenum').val();
	 	if(phone == ''){
	 		error('请填写手机号');
	 		return;
	 	}else if(!reg.test(phone)){
	 		error('手机号码不正确');
	 		return;
	 	}
	 	Brand.getTelNum({'brand_id':id,'tel':phone});
	 })

	 //底部页脚
	 $('.close').click(function(){
	 	$('.footer').addClass('none');
	 })

	 //点击活动跳转
	 // $(document).on('click','.activity',function(){
	 // 	var actid = $(this).attr('data-id');
	 	
	 // })
	 $(document).on('click','.sign',function(){
	 	var actID = $(this).parents('.activity').attr('data-id');
	 	window.location.href = labUser.path + 'webapp/activity/sharepc/_v020700?id='+actID+'&is_share=1&share_mark='+org_mark;
	 })

	 //点击二维码
	 $('.small-erwei').click(function(){
	 	$('#act-erwei').attr('src',$(this).attr('src'));
	 	$('.fixed').removeClass('none');
	 	$('.erwei-kuang').addClass('a-bouncein');
	 })
	 //关闭二维码弹窗
	 $('.erwei-close').click(function(){
	 	$('.erwei-kuang').removeClass('a-bouncein');
	 	$('.fixed').addClass('none');
	 })
	 $('.fixed').click(function(){
	 	$('.erwei-close').click();
	 })

	 //活动二维码
	 $(document).on('click','.act_erwei',function(){
	 	var src = $(this).attr('data-src');
	 	$('#act-erwei').attr('src',src);
	 	$('.fixed').removeClass('none');
	 	$('.erwei-kuang').addClass('a-bouncein');
	 })

	 $(document).on('mouseenter','.act_erwei',function(){
	 	$(this).attr('src','/images/020700pc/erwei_icon_hover.png');
	 })
	  $(document).on('mouseout','.act_erwei',function(){
	 	$(this).attr('src','/images/020700pc/erwei_icon.png');
	 })
	  //提示框定位
    function positionTip(window_width,distance) {
        if(window_width>1332){
            $('#tip').css('right',distance+'px');
        }
        else{
            $('#tip').css('right','0px');
        }
        $(window).resize(function () {
            var windowWidth =  $(window).width();
            if(windowWidth < 1332){
                $('#tip').css('right','0px');
            }
            else{
                $('#tip').css('right',(windowWidth-1172)/2-60-20+'px');
            }
        });
        $(window).scroll(function () {
            var documentHeight = $(document).height(),
                    windowHeight = $(window).height(),
                    scrollBottom = $(window).scrollTop() + windowHeight;
            if (documentHeight <= scrollBottom) {
                $("#backtop").css('visibility','visible');
                $('.footer').css('z-index',10);
            }
            else{
                $("#backtop").css('visibility','hidden');
                 $('.footer').css('z-index',999);
            }
        });
    }

    var window_width=$(window).width(),
        current_ID = 'ovofbh',
        aid = '',//省ID
        dis=(window_width-1172)/2-60-20;
    positionTip(window_width,dis);
//当点击跳转链接后，回到页面顶部位置
    $("#backtop").click(function () {
        //$('body,html').animate({scrollTop:0},1000);
        if ($('html').scrollTop()) {
            $('html').scrollTop(0);
            return false;
        }
        $('body').scrollTop(0);
        return false;
    });

	 //错误提示
	 function error(str){
	 	$('.tishikuang ').text(str).show();
	 	setTimeout(function(){
	 		$('.tishikuang ').hide();
	 	},2000);
	 }
})