@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/column.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
              <!-- 顶部导师照片 -->
              <div class="ui_professor_pict">
                   <div class="ui_professor_text white">
                         <p class="f15 b ">+ 这是女神迪丽热巴</p>
                         <p class="f12"><span>小虾</span><span style="padding-left: 1rem">热巴的忠实粉丝</span><span class="fr">284学习</span></p>
                   </div>
              </div>
               <!-- 专栏介绍 -->
              <div class="ui_column_introduce color999">
                  <p class="f14 b color333">专栏介绍</p>
                  <p class="f12 ui-nowrap-multi">
                    迪丽热巴（Dilraba），1992年6月3日出生于新疆乌鲁木齐，中国内地女演员，毕业于上海戏剧学院。2013年，主演个人首部电视剧《阿娜尔罕》。2014年参演古装玄幻剧《古剑奇谭》，2015年主演校园魔幻网络剧《逆光之恋》 ，同年凭借都市爱情剧《克拉恋人》获得2015年国剧盛典最受欢迎新人女演员。2016年8月，主演都市爱情剧《麻辣变形计》。2017年1月，主演的都市爱情喜剧《漂亮的李慧珍》；同月，主演古装仙侠剧《三生三世十里桃花》，并凭借该剧提名上海电视节“白玉兰奖”最佳女配角；
                  </p>
              </div>
              <!-- 资讯部分 -->
              <article>
                   <div class="fline style f15 b color333 ui_add_hieght">
                     文章·Pages
                   </div>
                   <!-- <div class="ui_con color999">
                            <div class="padding">
                                  <ul class="ui_text_pict">
                                       <li>
                                           <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈</p>
                                           <p class="f12 ui-nowrap-multi">
                                              狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                              这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                              当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                           </p>
                                       </li>
                                       <li>
                                        <div class="ui_protect_pict fr"><img class="ui_pict1" src="/images/agent/ui1.png"/></div>
                                       </li>
                                  </ul>
                                  <p class="clear ui-border-b ui_row"></p>
                                  <ul class="ui_text_down clear f11">
                                        <li>
                                          <ul class="ui_flex">
                                              <li>
                                                <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                              </li>
                                              <li>
                                                <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                              </li>
                                              <li>
                                                <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                              </li>
                                          </ul>
                                        </li>
                                        <li>作者：无界商圈</li>
                                  </ul>
                                  <p class="clear margin"></p>
                              </div>
                            <div class="fline style"></div>
                   </div>
                   <div class="ui_con color999">
                          <div class="padding">
                                <ul class="ui_text_pict">
                                     <li>
                                         <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                         <p class="f12 ui-nowrap-multi">
                                            狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                            这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                            当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                         </p>
                                     </li>
                                     <li>
                                      <div class="ui_protect_pict fr"><img class="ui_pict1" src="/images/agent/ui2.png"/></div>
                                     </li>
                                </ul>
                                <p class="clear ui-border-b ui_row"></p>
                                <ul class="ui_text_down clear f11">
                                      <li>
                                        <ul class="ui_flex">
                                            <li>
                                              <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                            </li>
                                            <li>
                                              <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                            </li>
                                            <li>
                                              <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                            </li>
                                        </ul>
                                      </li>
                                      <li>作者：无界商圈</li>
                                </ul>
                                <p class="clear margin"></p>
                            </div>
                          <div class="fline style"></div>
                   </div>
                   <div class="ui_con color999">
                          <div class="padding">
                                <ul class="ui_text_pict">
                                     <li style="width:100%">
                                         <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                         <p class="f12 ui-nowrap-multi">
                                            狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                            这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                            当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                         </p>
                                     </li>
                                </ul>
                                <p class="clear ui-border-b ui_row"></p>
                                <ul class="ui_text_down clear f11">
                                      <li>
                                        <ul class="ui_flex">
                                            <li>
                                              <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                            </li>
                                            <li>
                                              <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                            </li>
                                            <li>
                                              <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                            </li>
                                        </ul>
                                      </li>
                                      <li>作者：无界商圈</li>
                                </ul>
                                <p class="clear margin"></p>
                            </div>
                          <div class="fline style"></div>
                 </div> -->
                <!--  下个版本用 -->
                 <!-- <button class="ui_get_more f15 color999 none">加载更多</button> -->
               </article>
               <footer>
                          <div class="fline style f15 b color333 ui_add_hieght">视频·Vlogs</div>
                          <ul class="ui_video">
                             <!--  <li>
                                <img class="ui_images" src="/images/agent/ui1.png"/>
                                <p class="color333 f13 ui_margin1">哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                <p class="f11 color999 ui_margin2">浏览量 4994949</p>
                              </li>
                               <li>
                                <img class="ui_images" src="/images/agent/ui1.png"/>
                                <p class="color333 f13 ui_margin1">哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                <p class="f11 color999 ui_margin2">浏览量 4994949</p>
                              </li>
                               <li>
                                <img class="ui_images" src="/images/agent/ui1.png"/>
                                <p class="color333 f13 ui_margin1">哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                <p class="f11 color999 ui_margin2">浏览量 4994949</p>
                              </li> -->
                          </ul>
                          <div class="clear"></div>
               </footer>
                 <div style="width:100%;height:5rem"></div> 
    </section>
@stop

@section('endjs')
<script type="text/javascript" src="/js/agent/_v010004/column.js"></script>
<script>
  $(document).ready(function(){
    $('title').text('专栏');
  })
</script>
@stop