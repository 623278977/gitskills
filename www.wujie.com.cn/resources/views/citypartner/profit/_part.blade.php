<div class="member">
    <div class="performance">
        <h2>业绩明细</h2>
        <form>
            <label for="">选择成员</label>
        <!--    <select name="" id="member">
                <option value="{{ $uid }}">自己</option>
                @foreach($members as $m)
                    <option value="{{ $m->uid }}" @if(isset($member) && $member== $m->uid) selected @endif>{{ $m->realname }}</option>
                @endforeach
            </select>
            -->
            <!--改动开始处-->
            <input type="text" class="select" id="member" value="{{$name}}" code="{{$uid}}" readOnly="true" />
            <ul class="option hide">
                <li value="{{$uid}}"> 自己 </li>
                @foreach($members as $m)
                    <li value="{{$m->uid}}"> {{ $m->realname }} </li>
                @endforeach
            </ul>
             <!--改动结束处-->  
        </form>
        <p class="num"><span>@if($member==$uid){{sprintf("%.2f",($totalMyAchievement)/10000)}}@else{{sprintf("%.2f",($totalMyAchievement+$totalTeamAchievement)/10000)}}@endif</span>万元</p>
        <p>总业绩额</p>
        <table>
            <caption>{{$name}}（{{$peroid}}）每月业绩额</caption>
            <thead>
            <tr>
                <th>月份</th>
                @for($i=$begin;$i<=$end;$i++)
                    <th>{{$i}}</th>
                @endfor
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>业绩额（万元）</td>
                @if($member==$uid)
                    @foreach($monthAchievement as $ma)
                        <td><span>{{ sprintf("%.2f",($ma['my'])/10000) }}</span></td>
                    @endforeach
                @else
                    @foreach($monthAchievement as $ma)
                        <td><span>{{ sprintf("%.2f",($ma['my']+$ma['team'])/10000) }}</span></td>
                    @endforeach
                @endif
            </tr>
            </tbody>
        </table>
        <table>
            <caption>{{$name}}（{{$peroid}}）业绩明细</caption>
            <thead>
            <tr>
                <th>时间</th>
                <th>名称</th>
                <th>业绩额（万元）</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $list)
                <tr>
                    <td>{{ date("Y.m.d",$list->arrival_at) }}<p>{{ date("H:i",$list->arrival_at) }}</p></td>
                    <td><div title="">{{ $list->title }}</div></td>
                    <td><span>{{ sprintf("%.2f",$list->amount/10000) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p>@if( $lists->total() > $lists->perPage()){{$lists->currentPage()==1 ? 1:($lists->currentPage()-1)*$lists->perPage()+1}}-{{$lists->currentPage()*($lists->perPage())}}条，共<span>{{ $lists->total() }}条</span><a href="{{$lists->nextPageUrl()}}">下一页&gt;</a><a href="{{ $lists->url($lists->total()-1) }}">尾页&gt;&gt;</a>@else{{$lists->currentPage()==1 ? 1:($lists->currentPage()-1)*$lists->perPage()+1}}-{{$lists->currentPage()*($lists->perPage())}}条，共<span>{{ $lists->total() }}条</span> @endif</p>
    <script>
        $('#member').change(function(){
            var member = $(this).val();
            var peroid = $('#peroid').html();
            var member = $('#member').val();
            var uid =$("input[name='uid']").val();
            ajaxRequest({member:member,peroid:peroid,uid:uid,member:member},'/citypartner/profit/detail',function(data){
                $("#part").html(data.message);
            });
        });

         //新添js      
     $(function(){
        $(".select").click(function(event){
            event.stopPropagation();
           $(this).next("ul").toggleClass("hide");
        })
        $(".option>li").mouseover(function(){
            $(this).addClass("choose");
            $(this).siblings("li").removeClass("choose")
        });
        $(".option>li").click(function(){
            var text=$(this).text();
            var code=$(this).val();
//            $(this).parent("ul").prev(".select").val( text);
            $(this).parent("ul").prev(".select").attr('code',code);
            $(this).parent("ul").addClass("hide");
            var peroid = $('#peroid').html();
            var member = $('#member').attr('code');
            var uid =$("input[name='uid']").val();
            ajaxRequest({member:member,peroid:peroid,uid:uid,member:member},'/citypartner/profit/detail',function(data){
                $("#part").html(data.message);
            });
        });
        $(document).click(function(){
            $(".option").addClass("hide");
        })
    });
   //-------  //     
    </script>
</div>