@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/page_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/government_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>

@stop
@section('main')
    <section id="act_container" class="">
    	<div class="zb-search">
	        <form id="zbSearch">
	            <div class="main">
	                <div class="zb-sel"><span data-sel='' id="zb-select">全部</span><i class="triangle-down"></i>
	                    <ul class="zb-sel-ul hide">
	                        <i class="triangle-up"></i>
	                        <li><a href="javascript:;" class="all" data-sel=''>全部</a></li>
	                        <li><a href="javascript:;" class="activity" data-sel=1>活动</a></li>
	                        <li><a href="javascript:;" data-sel=2>视频</a></li>
	                        <li><a href="javascript:;" data-sel=3>直播</a></li>
	                    </ul>    
	                </div>
	                <input type="text" class="zb-hold" placeholder="请输入。。。"></input>
	                <input type="reset" value="取消" class="zb-search-cancel"></input>
	                <div class="clearfix"></div>
	            </div>
                <input type="text" class="zb-hold" placeholder="请输入。。。" style="display: none;"></input>
	        </form>
	    </div>
        <div id="massive-search">
            <div id="massive-all">
                
                <div class="massive-sel massive-other">
                    
                </div>
                <div class=" massive-sel massive-activity">
                                
                </div>
                 <div class=" massive-sel massive-video">
                                
                </div>
                 <div class=" massive-sel massive-live">
                                
                </div>
            </div>
               
            
        </div>
	   
    </section>
@stop

@section('endjs')
   <script>
        Zepto(function () {
            var $sel = $('.zb-sel');
            var $selUl = $('.zb-sel-ul');
            $sel.click(function (e) {
                $selUl.toggleClass('hide');
                $(document).one('click',function () {
                $selUl.addClass('hide');
                });
                e.stopPropagation();
            });
            var $sel_a =$('.zb-sel-ul li a');
            $sel_a.click(function () {
                var _text = $(this).text();
                var _data =$(this).attr('data-sel');
                $('.zb-sel span').text(_text);
                $('.zb-sel span').attr('data-sel',_data);
            });

           
            var searchId = {{$vip_id}};
                
                cate = $('#zb-select').attr('data-sel');  

            $('.zb-search-cancel').click(function () {
                $('.massive-sel').empty();     
            })

              

            $('.zb-hold').focus(function () {
                $(document).keypress(function(e) { 
                // 回车键事件 
                    if(e.which == 13) { 
                        var keywords =$('.zb-hold').val();
                        var category = $('#zb-select').attr('data-sel');
                        var param = {
                            "vip_id":searchId ,
                            "keywords":keywords,
                            "category":category,
                            "page":1,
                            "pageSize":2
                        };

                        $('.massive-sel').empty();                                     
                        searchDetail.detail(param.vip_id,param.keywords,param.category);
                        searchDetail.detailAll(param.vip_id,param.keywords,param.category,param.pageSize);

                    } 
                });
            })             
            

            var searchDetail = {
                detail: function(vip_id,keywords,category){
                    var param = {};
                    param["vip_id"] = vip_id;
                    param["keywords"] = keywords;
                    param["category"] = category;
                    var url = labUser.api_path + '/api/vip/search';
                    // var url = '/api/vip/search';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                           getSearchDetail(data.message,category);
                        }
                    });
                },
                detailAll: function(vip_id,keywords,category,pageSize){
                    var param = {};
                    param["vip_id"] = vip_id;
                    param["keywords"] = keywords;
                    param["category"] = category;
                    param["pageSize"] = pageSize;
                    var url = labUser.api_path + '/api/vip/search';
                    // var url = '/api/vip/search';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                           getSearchDetailAll(data.message,category,pageSize);
                        }
                    });
                }
            };      
                                   
            function getSearchDetail(result,category) {
                if(category==1){
                    $.each(result.vip_activity,function (i,item) {
                        var str='';
                        str=[                      
                            '<section class="zb zb-search">',
                                '<div class="clearfix"></div>',
                                '<div class="massive">',
                                    '<img src="" alt="">',
                                    '<div class="l">',
                                        '<div class="name">'+item.subject+'</div>',
                                        '<div class="clearfix"></div>',
                                        '<span>'+item.begin_time+'</span><span> '+item.city+'</span>',
                                    '</div>',
                                    '<div class="clearfix"></div>',
                                '</div>',
                            '</section>',                      
                        ].join('');
                        $('.massive-other').append(str);
                    });
                }
                else if (category==2) {
                    $.each(result.vip_video,function (i,item) {
                        var str1='';
                        str1=[                      
                            '<section class="zb zb-search">',
                                '<div class="clearfix"></div>',
                                '<div class="massive">',
                                    '<img src="" alt="">',
                                    '<div class="l">',
                                        '<div class="name">'+item.subject+'</div>',
                                        '<div class="clearfix"></div>',
                                        '<span>'+item.description+'</span>',
                                    '</div>',
                                    '<div class="clearfix"></div>',
                                '</div>',
                            '</section>',                      
                        ].join('');
                    $('.massive-other').append(str1);
                    });
                }else if (category ==3) {
                    $.each(result.vip_live,function (i,item) {
                        var str1='';
                        str1=[                      
                            '<section class="zb zb-search">',
                                '<div class="clearfix"></div>',
                                '<div class="massive">',
                                    '<img src="" alt="">',
                                    '<div class="l">',
                                        '<div class="name">'+item.subject+'</div>',
                                        '<div class="clearfix"></div>',
                                        '<span>'+item.description+'</span>',
                                    '</div>',
                                    '<div class="clearfix"></div>',
                                '</div>',
                            '</section>',                      
                        ].join('');
                    $('.massive-other').append(str1);
                    });
                }
            }
            function getSearchDetailAll(result,category,pageSize) {
                if (category =='') {
                    if (result.vip_activity.length>0) {
                        var str1='<div class="massive-head">'+result.vip_name+'</div>';
                        $('.massive-activity').append(str1);
                        $.each(result.vip_activity,function (i,item) {                          
                            var str2='';
                            str2=[   
                                '<section class="zb zb-search">',
                                    '<div class="clearfix"></div>',
                                    '<div class="massive">',
                                        '<img src="" alt="">',
                                        '<div class="l">',
                                            '<div class="name">'+item.subject+'</div>',
                                            '<div class="clearfix"></div>',
                                            '<span>'+item.begin_time+'</span><span> '+item.city+'</span>',
                                        '</div>',
                                        '<div class="clearfix"></div>',
                                    '</div>',
                                '</section>',  
                           ].join('');                           
                           $('.massive-activity').append(str2);                       
                        })  
                        var str3='<div class="seen_more" id="zb-liveMore">查看更多活动<span class="sj_icon"></span></div>'; 
                        $('.massive-activity').append(str3);
                    }
                    if (result.vip_video.length>0) {
                        var str1='<div class="massive-head">'+result.vip_name+'</div>';
                        $('.massive-video').append(str1);
                        $.each(result.vip_video,function (i,item) {                          
                            var str2='';
                            str2=[   
                                '<section class="zb zb-search">',
                                '<div class="clearfix"></div>',
                                '<div class="massive">',
                                    '<img src="" alt="">',
                                    '<div class="l">',
                                        '<div class="name">'+item.subject+'</div>',
                                        '<div class="clearfix"></div>',
                                        '<span>'+item.description+'</span>',
                                    '</div>',
                                    '<div class="clearfix"></div>',
                                '</div>',
                            '</section>',
                           ].join('');                           
                           $('.massive-video').append(str2);                       
                        })  
                        var str3='<div class="seen_more" id="zb-liveMore">查看更多点播视频<span class="sj_icon"></span></div>'; 
                        $('.massive-video').append(str3);
                    }  
                    if (result.vip_live.length>0) {
                        var str1='<div class="massive-head">'+result.vip_name+'</div>';
                        $('.massive-live').append(str1);
                        $.each(result.vip_live,function (i,item) {                          
                            var str2='';
                            str2=[   
                                '<section class="zb zb-search">',
                                '<div class="clearfix"></div>',
                                '<div class="massive">',
                                    '<img src="" alt="">',
                                    '<div class="l">',
                                        '<div class="name">'+item.subject+'</div>',
                                        '<div class="clearfix"></div>',
                                        '<span>'+item.begin_time+'</span><span> '+item.city+'</span>',
                                    '</div>',
                                    '<div class="clearfix"></div>',
                                '</div>',
                            '</section>',
                           ].join('');                           
                           $('.massive-live').append(str2);                       
                        })  
                        var str3='<div class="seen_more" id="zb-liveMore">查看更多点播直播<span class="sj_icon"></span></div>'; 
                        $('.massive-live').append(str3);
                    }  
                }
            }
        });
   </script>
        
@stop