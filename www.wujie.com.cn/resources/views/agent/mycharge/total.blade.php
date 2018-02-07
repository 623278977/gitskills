@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/mycharges.css" rel="stylesheet" type="text/css"/>
    <style>
      .chart-container{
        padding:1.5rem;
        position: relative;
        color: #fff;
        background: #ff5a00;
        border-radius: 0.5rem;
        background: -webkit-gradient(linear, 0 0, 0 bottom, from(#ffa300), to(#ff5a00));  
        background: -moz-linear-gradient(top, #ffa300, #ff5a00); 
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff9000', endColorstr='#ff5a00',GradientType=0 );
      }
      #mychart{
        width: 100%;
      }
    </style>
@stop
@section('main')
    <section class="containerBox pl1-5 pr1-5" id="containerBox" >
        <div>
          <p class="f15 pt1">累计获得佣金总额</p>
          <p class="f12 ml1 mt05" style="color:#fdab31">累计获得佣金提成(元)</p>
          <p class= 'f11 color999 ml1 mb05'>*不包含本季度</p>
        </div>
        <div class="chart-container" >
            <div class="canvas_box" >
               <canvas id="mychart"></canvas>
            </div>     
        </div>
                   
    </section>
@stop
@section('endjs')
    <!-- <script type='text/javascript' src='{{URL::asset('/')}}/js/agent/dist/Chart.bundle.min.js'></script> -->
    <script>
       Zepto(function(){    
              var args = getQueryStringArgs();
              var agent_id = args['agent_id'] || 0;
              var url = labUser.agent_path + '/user/commission-records/_v010000';
              ajaxRequest({'agent_id':agent_id},url,function(data){
                  if(data.status){  
                      // var datas = data.message.reverse();
                      var datas = data.message;
                      datas.unshift({'quarter':'未开始','my_commission':''});
                      drawLine(data.message);

                  }
              })
        //数据源处理
              function drawLine(data){
                var ctx = document.getElementById("mychart").getContext("2d");
                var mychart = document.getElementById("mychart");
                var canvas_w = $('.canvas_box').width(); 
                var canvas_h = $('.canvas_box').height();
                var n;
                if(window.devicePixelRatio){
                  n = window.devicePixelRatio;
                  console.log(n);
                }
             
                mychart.width = canvas_w*n;              
                mychart.height = canvas_h*n;              
  
                //数据源提取
                var len = data.length;
                var xArr = [], yArr = [], tmp_yArr = [];
                for(var i=0; i<len; i++){
                  xArr.push(i * (canvas_w/len-5));
                  tmp_yArr.push(data[i].my_commission);
                }
                var tmp_minY = Math.min.apply(Math, tmp_yArr);//最小值
                var tmp_maxY = Math.max.apply(Math, tmp_yArr);//最大值
                if(tmp_maxY - tmp_minY <= 100){
                  for(var i=0; i<len; i++){
                    yArr.push(tmp_yArr[i] - tmp_minY + 50);//与最小的做比较
                  }
                }
                else{//如果相差太大会导致图表不美观
                  for(var i=0; i<len; i++){
                    // yArr.push(tmp_yArr[i] / 500);
                    yArr.push(tmp_yArr[i]* canvas_h/(tmp_maxY-tmp_minY));
                  }
                }

                var minY = Math.min.apply(Math, yArr);
                var maxY = Math.max.apply(Math, yArr);

                //画折线
                ctx.lineWidth = n;
                ctx .strokeStyle = '#fff';     
                for(var i=0 ;i<len; i++){
                  var x = xArr[i];
                  var y = maxY - yArr[i] + minY;            
                  if(yArr[i] == maxY){
                    ctx .lineTo((x+30)*n, (y+10)*n);
                  }else{
                    ctx .lineTo((x+30)*n, (y-40)*n);
                  }
                }
                ctx .stroke();

               
                //画点
                for(var i=0; i<len; i++){
                    var x = xArr[i];
                    var y = maxY - yArr[i] + minY;
                    var xMemo = data[i].quarter;
                    var yMemo;
                    if(i==0){
                        yMemo =data[i].my_commission;
                    }else{
                       yMemo = "¥" + data[i].my_commission;
                    }
    
                    ctx.beginPath();           
                    if(yArr[i] == maxY){
                        ctx.fillStyle = "#ffbc80";
                        ctx.arc((x+30)*n, (y+10)*n, 6*n, 0, 2*Math.PI);//画点
                        ctx.fill();
                        ctx.beginPath();
                        ctx.fillStyle = "#fff";
                        ctx.arc((x+30)*n, (y+10)*n, 3*n, 0, 2*Math.PI);//画点
                        ctx.fill();
                        ctx.fillStyle='#ffc599';
                        ctx.font=10*n+"px Microsoft Yahei";//2017/10/23调试新增
                        ctx.fillText(xMemo, (x+15)*n , (y+35)*n );
                        ctx.fillStyle='#fff';
                        ctx.fillText(yMemo, (x+10)*n , (y+50)*n);
                    }else{
                        ctx.fillStyle = "#ffbc80";
                        ctx.arc((x+30)*n, (y-40)*n, 6*n, 0, 2*Math.PI);//画点
                        ctx.fill();
                        ctx.beginPath();
                        ctx.fillStyle = "#fff";
                        ctx.arc((x+30)*n, (y-40)*n, 3*n, 0, 2*Math.PI);//画点
                        ctx.fill();
                        ctx.font=10*n +"px Microsoft Yahei";//2017/10/23调试新增
                        ctx.fillStyle='#ffc599';
                        ctx.fillText(xMemo, (x+15)*n , (y-15)*n);
                        ctx.fillStyle='#fff';
                        ctx.fillText(yMemo, (x+10)*n , y);
                    };
                  
                };
              };          

       })
   
 
    </script>  
@stop