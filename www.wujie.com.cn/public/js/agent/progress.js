function progress(num){
     if(0<num&&num<=5){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     }else if(5<num&&num<=10){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     }else if(10<num&&num<20){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     }else if(num==20){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(3).addClass('bar');//第四杠
        $('.ui_rank_container').addClass('silver').removeClass('norm gold');
     	$('#ui_text_rank').text('银牌经纪人')
     }else if(20<num&&num<=27){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(3).addClass('bar');//第四杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(0).addClass('bar');//第五杠

     }else if(27<num&&num<=36){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(3).addClass('bar');//第四杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(0).addClass('bar');//第五杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(1).addClass('bar');//第六杠

     }else if(36<num&&num<50){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(3).addClass('bar');//第四杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(0).addClass('bar');//第五杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(1).addClass('bar');//第六杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(2).addClass('bar');//第七杠

     }else if(50<=num){
     	$('.ui_progress_bar li').eq(1).find('span').eq(0).addClass('bar');//第一杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(1).addClass('bar');//第二杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(2).addClass('bar');//第三杠
     	$('.ui_progress_bar li').eq(1).find('span').eq(3).addClass('bar');//第四杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(0).addClass('bar');//第五杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(1).addClass('bar');//第六杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(2).addClass('bar');//第七杠
     	$('.ui_progress_bar li').eq(0).find('span').eq(3).addClass('bar');//第八杠
     	$('.ui_rank_container').addClass('gold').removeClass('norm silver');
     	$('#ui_text_rank').text('金牌经纪人')
     }else if(num==0){
          $('#ui_text_rank').text('铜牌经纪人')
     };
     // 改变当前的背景颜色和文本框的值；
     if(0<num&&num<20){
     	$('.ui_rank_container').addClass('norm').removeClass('silver gold');
     	$('#ui_text_rank').text('铜牌经纪人')
     }else if(20<num&&num<50){
     	$('.ui_rank_container').addClass('silver').removeClass('norm gold');
     	$('#ui_text_rank').text('银牌经纪人')
     }
}//最外层
