var kaiguan = 1;
/**跑马灯**/
var pmd1 = '';
var imgId = document.getElementById("imgId");
var imgId2 = document.getElementById("imgId2");
var winning = false;
var is_winning = false;
function showImg() {
    if (imgId.style.visibility == "visible") {
        imgId.style.visibility = "hidden";
    } else {
        imgId.style.visibility = "visible";
    }
    if (imgId2.style.visibility == "visible") {
        imgId2.style.visibility = "hidden";
    } else {
        imgId2.style.visibility = "visible";
    }
}
function zhuanDong(start) {
    clearInterval(pmd1);
    pmd1 = setInterval('showImg()', 500);
    if (kaiguan && $('#demo1 li.default').size() == 0) {
        //1.摇杆往上
        $(".gan").addClass("bottom");
        myAuto_playFast();
        var demo = document.getElementById("demo");
        var demo1 = document.getElementById("demo1");
        var demo2 = document.getElementById("demo2");
        var speed = '';   //滚动速度值，值越大速度越慢
        var sj = Math.random() * 1 + 1;//[1,2)
        var zhi = Math.round(sj * 200, sj * 300);
        if (is_winning) {
            //获取中奖人
            choujiang.getWinning(param);
        }
        demo2.innerHTML = demo1.innerHTML    //克隆demo2为demo1
        function Marquee() {
            if (start == 0) {
                if (demo2.offsetTop - demo.scrollTop <= 0) {   //当滚动至demo1与demo2交界时
                    demo.scrollTop -= demo1.offsetHeight    //demo跳到最顶端
                } else {
                    demo.scrollTop = demo.scrollTop + 50;
                }
                myAuto_playFast();
            } else {
                if (zhi <= 0) {
                    if (winning === false) {
                        zhi = 30;
                    } else if (winning === -1) {//开奖失败
                        choujiang.closeInterval();
                        alert('开奖失败！');
                        return;
                    }
                }
                if (zhi > 0) {
                    zhuandong = true;
                    if (demo2.offsetTop - demo.scrollTop <= 0) {   //当滚动至demo1与demo2交界时
                        demo.scrollTop -= demo1.offsetHeight    //demo跳到最顶端
                    } else {
                        demo.scrollTop = demo.scrollTop + zhi;
                    }
                    zhi--;
                } else {
                    // 获奖者音乐
                    zhuandong = false;
                    myAuto_win();
//					    var selected=parseInt(Math.floor(demo.scrollTop/280));
                    var selected = $('#demo1 li[apply_id=' + winning + ']').index();
                    demo.scrollTop = selected * 280;
                    kaiguan = 1;
                    $(".gan").removeClass("bottom");
                    pause_playFast();
                    param.selected = selected;
//					    param.apply_id=$('#demo1').find('li').eq(selected).attr('apply_id');
                    param.apply_id = winning;
                    winning = false;
                    choujiang.closeInterval();
                    ds = setInterval(ds_choujiang, 2000);
                    clearInterval(pmd1);
                    choujiang.getOnePerson(param, function (data) {
                        if (data.status) {
                            var datas = data.message;
                            var userHtml = '';
                            if (datas.length) {
                                $.each(datas, function (i, n) {
                                    userHtml += '<li  tel="' + n.realtel + '" class="userlist_' + n.uid + '" uid="' + n.uid + '">';
                                    userHtml += '<span class="head">' + n.realname + '</span>';
                                    userHtml += '<span class="r">';
                                    userHtml += n.tel;
                                    userHtml += '</span>';
                                    userHtml += '</li>';
                                });

                                if (parseInt(param.count) == 1) {
                                    $('#zhongjiang').html(userHtml);
                                } else {
                                    $('#zhongjiang').append(userHtml);
                                }


                                $.each(datas, function (i, n) {
                                    $(".userlist_" + n.uid + "").find(".head").css("background-image", "url(" + n.avatar + ")");//就可以了
                                });
                                param.count += parseInt(1);
                                choujiang.getNowinNumber({
                                    prize_id: $('.five_step .active:last').attr('prize_id')
                                }, "changeNumber");
                            }
                            if (data.forwardUrl) {
                                choujiang.closeInterval();
                            }


                        } else {
                            choujiang.closeInterval();
                        }
                    });
                }
            }
        }

        clearInterval(MyMar);
        MyMar_ismoving = false;
        MyMar = setInterval(Marquee, 10);        //设置定时器
        MyMar_ismoving = true;
    }
}




