/**
 * Created by jizx on 2017/1/5.
 */
(function ($, labUser) {
    var args = getQueryStringArgs();
    var activity_id = args['activity_id'],
        cj_id = args['id'];
    //var kaiguan = 1;
    /**跑马灯**/
    //var pmd1 = '';
    //var imgId = document.getElementById("imgId");
    //var imgId2 = document.getElementById("imgId2");
    var listArray = [];//中奖者数组，用来追加头像
    var winning = false;//中奖人id
    var is_winning = false;
    /**每隔5秒的抽奖定时器**/
    var ds = '';
    /**转动的定时器**/
    var MyMar = '';
    /**转动的定时器是否开启**/
    var MyMar_ismoving = false;
    /**快速音乐定时器**/
    var Fast = '';
    //该轮出几个名额
    var limit = '';
    /***是否在旋转中***/
    var zhuandong = false;
    /**要删除人的uid**/
    var deleteParam = {};
    var pmd_ismoving = false;
    //删除中奖者后隐藏对应li元素
    var _dethis = '';
    var param = {
        activity_id: activity_id,
        number: 0,
        prize_id: 0,
        good_id: 0,
        count: 1,
        selected: 0,
        apply_id: 0
    };
    var choujiang = {
        //初始化
        init: function (param) {
            var url = labUser.api_path + '/game/prizes';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    var currentPrize = data.message.currentType;
                    var prizes = data.message.prizes;
                    var prizeHtml = '';
                    $.each(prizes, function (index, item) {
                        prizeHtml += '<li data-imgsrc="prize' + index + '" data-num="' + item.num + '" data-good_id="' + item.goods_id + '" data-prize_id="' + item.id + '" data-type="' + item.type + '">' + item.title + '&nbsp;获得者</li>';
                    });
                    $('#prizetype').html(prizeHtml);
                    //所有奖项已抽完
                    if (currentPrize === false) {
                        var lilength = $('#prizetype li').length;
                        $('#prizetype li').eq(lilength - 1).addClass('active');
                        var baseWidth = $('#prizetype li').first().width();
                        $('#prizetype').css('left', -baseWidth * (lilength - 1));
                        //显示所有奖项已抽完
                        choujiang.showMessage('#tc2');
                        $('.totalNumber').html(0);
                        return;
                    }
                    $('#prizetype li').each(function (index, item) {
                        var _type = $(this).data('type');
                        if (_type == currentPrize) {
                            var baseWidth = $(this).width();
                            $('#prizetype').css('left', -baseWidth * index);
                            $(this).addClass('active').siblings().removeClass('active');
                            /**获取某一个奖项剩余中奖人数***/
                            choujiang.getNowinNumber({prize_id: $(this).data('prize_id')}, "changeNumber");
                            var url = labUser.api_path + '/game/nowinnumberbyoneprize';
                            ajaxRequest({prize_id: $('#prizetype li.active').data('prize_id')}, url, function (data) {
                                if (data.status) {
                                    var leftNum = data.message;
                                    var thisprizenum = $('#prizetype li.active').data('num');
                                    var initnum = 10;
                                    if ((thisprizenum < 3) || (thisprizenum == 3)) {
                                        initnum = 1;
                                    }
                                    else if ((10 > thisprizenum) && (thisprizenum> 3)) {
                                        initnum = 2;
                                    }
                                    else if ((20 > thisprizenum)&&(( thisprizenum> 10)||(thisprizenum == 10))) {
                                        initnum = 5;
                                    }
                                    else if ((thisprizenum > 20)||(thisprizenum == 20)) {
                                        initnum = 10;
                                    }
                                    var nowNum = leftNum < initnum ? leftNum : initnum;
                                    resetChouJ(nowNum);
                                }
                            });
                        }
                    });
                }
            });
        },
        /***获取未中奖用户**/
        //flag=['first','']
        getUsers: function (param, flag) {
            if (MyMar_ismoving == true) {
                return false;
            }
            /***判断这轮有没有抽完**/
            if (!choujiang.compareCount(param)) {
                //已抽完
                //弹出列表
                choujiang.showMessage('#tc_luckylist');
                //choujiang.showMessage('#tc1');//此轮已抽完
                choujiang.closeInterval();
                is_winning = false;
                return false;
            }
            /***这个奖项抽完**/
            choujiang.getNowinNumber(param, "checkNumber", flag);
        },
        /**点击按钮抽奖**/
        clickChoujiang: function (param) {
            choujiang.beginChoujiang(param);
        },
        /**抽奖 动画***/
        beginChoujiang: function (param) {
            zhuanDong();
        },
        /**比较 有没有超过限度**/
        compareCount: function (param) {
            if ((param.number < 3) || (param.number == 3)) {
                limit = 1;
            }
            else if ((10 > param.number) && (param.number > 3)) {
                limit = 2;
            }
            else if ((20 > param.number)&&(( param.number> 10)||(param.number == 10))) {
                limit = 5;
            }
            else if ((param.number > 20)||(param.number == 20)) {
                limit = 10;
            }
            if (param.count > limit) {
                /**重新来一轮**/
                $(".chuj,.stop").hide();
                $(".choujiang").show();
                pause_playFast();
                return false;
            } else {
                return true;
            }
        },
        /**抽取一个人获奖**/
        getOnePerson: function (param, callback) {
            var url = labUser.api_path + '/game/choujiang';
            ajaxRequest(param, url, callback);
        },
        /**获取中奖人**/
        getWinning: function (param, n) {
            if (!n) {
                n = 1;
                winning = false;
            }
            if (n > 5) {//请求次数过多，开奖失败
                winning = -1;
                return;
            }
            ajaxRequest(param, labUser.api_path + '/game/winning', function (data) {
                if (data.status) {
                    winning = data.message;
                } else {
                    choujiang.getWinning(param, ++n);
                }
            });
        },
        /**获取某一个奖项还有几个人未中奖
         * {prieze_id:'id'}***/
        getNowinNumber: function (param, type, flag) {
            var url = labUser.api_path + '/game/nowinnumberbyoneprize';
            ajaxRequest(param, url, function (data) {
                if (!data.status && param.prize_id > 0) {//失败再次请求
                    choujiang.getNowinNumber(param, type, flag);
                    return;
                }
                /**checkNumber  判断人数**/
                /**changeNumber 查询当前奖项剩余数量**/
                //  【点击抽奖】先判断当前奖项剩余数量
                if (type == 'checkNumber') {
                    if (parseInt(data.message)>0) {
                        /***还可以抽***/
                        choujiang.getNowinUsers(param);
                        if (flag != "first") {
                            myAuto_playSlow();
                        }
                    } else {
                        //中奖列表
                        choujiang.showMessage('#tc_luckylist');
                        /***不能抽**/
                        choujiang.showMessage('#tc2');
                        is_winning = false;
                        //出奖ing，停止按钮隐藏
                        $(".chuj,.stop").hide();
                        //开始抽奖按钮
                        $(".choujiang").show();
                        choujiang.closeInterval();
                        return false;
                    }
                    if (!choujiang.compareCount(param)) {
                        $('.chuj').hide();
                        $('.choujiang').show();
                        choujiang.closeInterval();
                        return false;
                    }
                }
                else if (type == "beginCheck") {
                    if (parseInt(data.message)>0) {
                        //此奖项数量有剩余
                        is_start(0, "first");
                        myAuto_start();
                        $('.choujiang').hide();
                        $('.chuj').hide();
                        $('.stop').show();
                        //reset。ul--#zhongjiang,ul--#luckylist和listArray
                        var thisNum = data.message > limit ? limit : data.message;
                        resetChouJ(thisNum);
                    } else {
                        //弹出中奖列表
                        //choujiang.showMessage('#tc_luckylist');
                        //已抽完图片提示
                        choujiang.showMessage('#tc2');
                        $('.chuj').hide();
                        $('.stop').hide();
                        $('.choujiang').show();
                        choujiang.closeInterval();
                        return false;
                    }
                } else {
                    //当前奖项剩余人数
                    $('.totalNumber').html(data.message);
                }
            });
        },
        /***抽奖弹框***/
        showMessage: function (id) {
            $(id).show();
        },
        /***下一步****/
        nextStep: function () {
            var currentPrize = $('#prizetype li.active');
            var nextStepParam = {
                prize_id: currentPrize.data('prize_id'),
                next_prize_id: currentPrize.next().data('prize_id')
            };
            var nextStepUrl = labUser.api_path + '/game/nextstep';
            ajaxRequest(nextStepParam, nextStepUrl, function (data) {
                //left:奖的剩余数
                if (parseInt(data.left) == '0') {
                    var initNum;
                    var next = currentPrize.next();
                    var nextnum = next.data('num');
                    if (nextnum) {//下一个奖项存在
                        if ((nextnum < 3) || (nextnum == 3)) {
                            initNum = 1;
                        }
                        else if ((10 > nextnum) && (nextnum> 3)) {
                            initNum = 2;
                        }
                        else if ((20 > nextnum)&&(( nextnum> 10)||(nextnum == 10))) {
                            initNum = 5;
                        }
                        else if ((nextnum > 20)||(nextnum == 20)) {
                            initNum = 10;
                        }
                        resetChouJ(initNum);
                        var current_index = next.index();
                        var baseWidth = next.width();
                        $('#prizetype').css('left', -baseWidth * current_index);
                        next.addClass('active').siblings().removeClass('active');
                        param.prize_id = next.data('prize_id');
                        choujiang.getNowinNumber(param, "changeNumber");
                        /**重新来一轮**/
                        if (zhuandong == true) {
                            $('.choujiang').hide();//点击抽奖
                            $('.stop').hide();//stop
                            $('.chuj').show();//出奖中ing
                        } else {
                            $(".chuj,.stop").hide();
                            $(".choujiang").show();
                        }
                    }
                    else {
                        //所有奖项都抽完了
                        choujiang.showMessage('#tc2');
                        return false;
                    }
                } else {
                    //left不为0
                    //当前奖项没抽完
                    choujiang.showMessage('#tc0');
                    return false;
                }
            });
        },
        /****获取未抽中用户***/
        getNowinUsers: function (param) {
            var url = labUser.api_path + '/game/getusers';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    var html = '';
                    if (data.message.length) {
                        $.each(data.message, function (i, n) {
                            html += '<li apply_id="' + n.apply_id + '">';
                            html += '<div class="person_head"><img src="' + n.avatar + '"></div>';
                            html += '<div class="person_infos">';
                            html += '<p>' + n.realname + '</p>';
                            html += '<p>' + n.tel + '</p>';
                            html += '</div>';
                            html += '</li> ';
                        });
                    }
                    $('#demo1').html(html);
                    choujiang.clickChoujiang(param);
                }
            });
        },
        /****获取未抽中用户***/
        getFirstNowinUsers: function (param) {
            var url = labUser.api_path + '/game/getusers';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    var html = '';
                    if (data.message.length) {
                        $.each(data.message, function (i, n) {
                            html += '<li tel="' + n.tel + '" >';
                            html += '<div class="person_head"><img src="' + n.avatar + '"></div>';
                            html += '<div class="person_news">';
                            html += '	<p class="name">' + n.realname + '</p>';
                            html += '<p class="tel">' + n.tel + '</p>';
                            html += '</div>';
                            html += '</li> ';
                        });
                    }
                    $('#demo1').html(html);
                    choujiang.clickChoujiang(param);
                }
            });

        },
        /**删除未领奖的用户**/
        deleteUser: function (deleteParam, deleteUrl, _this) {
            ajaxRequest(deleteParam, deleteUrl, function (data) {
                if (data.status) {
                    _this.hide();
                    $('.totalNumber').html(parseInt($('.totalNumber').html()) + 1);
                    $('#tc4').hide();
                    choujiang.showMessage('#tc3');
                }
            });
        },
        /***关闭定时器**/
        closeInterval: function () {
            clearInterval(ds);
            clearInterval(MyMar);
            //clearInterval(Fast);
            MyMar_ismoving = false;
        }
    };
    //下一个奖项
    $(document).on("click", ".nextStep", function () {
        choujiang.nextStep();
    });
    //删除中奖用户
    $(document).on("click", "#zhongjiang li", function () {
        var uid = $(this).attr('uid');
        choujiang.showMessage('#tc4');
        _dethis = $(this);
        deleteParam = {
            activity_id: activity_id,
            uid: uid,
            prize_id: $('#prizetype li.active').data('prize_id'),
            good_id: $('#prizetype li.active').data('good_id'),
            tel: $(this).attr('tel')
        };
        return false;
    });
    //中奖用户hover
    $(document).on('mouseenter', '#zhongjiang li', function () {
        $(this).css({'background-color': '#eee', 'cursor': 'pointer'});
        $(this).children('i.xcolor').removeClass('none');
    });
    $(document).on('mouseleave', '#zhongjiang li', function () {
        $(this).css({'background-color': '#fff', 'cursor': 'pointer'});
        $(this).children('i.xcolor').addClass('none');
    });

    /**停止btn、出奖btn 隐藏**/
    $('.stop,.chuj').hide();
    //立即抽奖
    $('.choujiang').click(function () {
        is_winning = false;
        /**先判断奖项有没有抽完***/
        choujiang.getNowinNumber({prize_id: $('#prizetype li.active').data('prize_id')}, "beginCheck");
    });
    /**停止的按钮点击**/
    $('.stop').click(function () {
        is_winning = true;
        is_start(1, "");
        $(".choujiang,.stop").hide();
        $(".chuj").show();
        myAuto_playSlow();
        pause_playFast();
    });
    //enter键
    $('body').on('keypress', function (event) {
        if (event.keyCode == 13 && is_winning === false && $("#tc0").css('display') == 'none' && $("#tc1").css('display') == 'none' && $("#tc2").css('display') == 'none' && $("#tc3").css('display') == 'none' && $("#tc4").css('display') == 'none' && $("#tc_luckylist").css('display') == 'none') {
            if ($('#stop').css('display') == 'none') {
                $('.choujiang').click();
            } else {
                $('.stop').click();
            }
        } else {
            $("#tc0,#tc1,#tc2,#tc3,#tc4").hide();
        }
    });
    //UP DOWN
    $('body').on('keypress',function (event) {
        if (event.keyCode == 33) {// Page Up
            if($('#choujiang').css('display')=='block'){
                $(".choujiang").click();
            }
        } else if (event.keyCode == 34) {//Page Down
            if($('#stop').css('display')=='block'){
                $(".stop").click();
            }
        }
    });
    //初始化
    choujiang.init({id: cj_id});
    /**关闭弹窗**/
    close("#tc0");
    close("#tc1");
    close("#tc2");
    close("#tc3");
    $('#tc_luckylist').hide();

    //关闭名单列表
    $('.closex').click(function () {
        $('#tc_luckylist').hide();
        return false;
    });
    //还是TA
    $("#tc4 .cancel").click(function () {
        $("#tc4").hide();
        return false;
    });
    //确定删除
    $('.suredelete').click(function () {
        var deleteUrl = labUser.api_path + '/game/delete';
        choujiang.deleteUser(deleteParam, deleteUrl, _dethis);
    });
    $('#tc0,#tc1,#tc2,#tc3').on('click', function () {
        $(this).hide();
    });
    /***获取未中奖用户**/
    function ds_choujiang() {
        choujiang.getUsers(param);
    }

    //获奖音乐
    function myAuto_win() {
        var myAuto_win = document.getElementById('myaudio1');
        myAuto_win.play();
    }

    //开始音乐
    function myAuto_start() {
        var myAuto_start = document.getElementById('myaudio2');
        myAuto_start.play();
    }

    //进行中音乐
    function myAuto_playFast() {
        var myAuto_playFast = document.getElementById('myaudio3');
        myAuto_playFast.play();
    }

    //停止进行中音乐
    function pause_playFast() {
        var myAuto_playFast = document.getElementById('myaudio3');
        myAuto_playFast.pause();
    }

    //慢下来音乐
    function myAuto_playSlow() {
        var myAuto_playSlow = document.getElementById('myaudio4');
        myAuto_playSlow.play();
    }

    //start=[0,1]   flag=[first,'']
    function is_start(start, flag) {
        var current_prize = $('#prizetype li.active');
        param.count = 1;
        param.number = current_prize.data('num');//对应num
        param.prize_id = current_prize.data('prize_id');//对应id
        param.good_id = current_prize.data('good_id');//对应goods_id
        choujiang.getUsers(param, flag);
        zhuanDong(start);
    }

    //close tc
    function close(obj) {
        var obj = $(obj);
        obj.hide();
    }

    //ajax
    function ajaxRequest(param, requestUrl, successCallback) {
        param['_token'] = labUser.token;
        $.ajax({
            type: 'POST',
            url: requestUrl,
            data: param,
            timeout: 20000,
            dataType: 'json',
            success: function (data) {
                if (successCallback && (successCallback instanceof Function)) {
                    successCallback(data);
                }
            },
            error: function (data) {
                console.log('http error');
            },
            complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                if (status == 'timeout') {//超时,status还有success,error等值的情况
                    console.log('xhr timeout');
                }
            }
        });
    };
    //get query string
    function getQueryStringArgs() {
        var qs = (location.search.length > 0 ? location.search.substring(1) : ''),
            arsg = {},
            items = qs.length ? qs.split('&') : [],
            item = null,
            name = null,
            value = null,
            len = items.length;
        for (var i = 0; i < len; i++) {
            item = items[i].split('=');
            name = decodeURIComponent(item[0]);
            value = decodeURIComponent(item[1]);
            if (name.length) {
                arsg[name] = value;
            }
        }
        return arsg;
    }

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

    //reset
    function resetChouJ(num) {
        $('#zhongjiang').html('');
        var baseHtml = '<li><img src="/choujiang/images/2017default.png" alt=""><p>暂无</p></li>';
        var listHtml = '';
        for (var i = 0; i < num; i++) {
            listHtml += baseHtml;
        }
        $('#luckylist').html(listHtml);
        listArray = [];
    }

    //int [min,max]
    function getRandom(min, max) {
        return Math.round(Math.random() * (max - min) + min);
    }

    //滚动
    function zhuanDong(start) {
        //clearInterval(pmd1);
        //pmd1 = setInterval('showImg()', 500);
        if ($('#demo1 li.default').size() == 0) {
            //1.摇杆往上
            //$(".gan").addClass("bottom");
            myAuto_playFast();
            var demo = document.getElementById("demo");
            var demo1 = document.getElementById("demo1");
            var demo2 = document.getElementById("demo2");
            var zhi = getRandom(200, 300);//转动次数
            if (is_winning && typeof(limit)=='number' && param.count < limit) {
                //获取中奖人
                choujiang.getWinning(param);
            }
            demo2.innerHTML = demo1.innerHTML;
            clearInterval(MyMar);
            //MyMar_ismoving = false;
            MyMar = setInterval(Marquee, 10); //中奖候选人往上滚动的定时器
            MyMar_ismoving = true;
            function Marquee() {
                if (start == 0) {
                    if (demo2.offsetTop - demo.scrollTop <= 0) {   //当滚动至demo1与demo2交界时
                        demo.scrollTop -= demo1.offsetHeight    //demo跳到最顶端
                    } else {
                        demo.scrollTop = demo.scrollTop + 50;//步进为50px
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
                        var selected = $('#demo1 li[apply_id=' + winning + ']').index();
                        demo.scrollTop = selected * 280;
                        //kaiguan = 1;
                        //$(".gan").removeClass("bottom");
                        pause_playFast();
                        param.selected = selected;
                        param.apply_id = winning;
                        winning = false;
                        choujiang.closeInterval();
                        //
                        ds = setInterval(ds_choujiang, 2000);
                        //clearInterval(pmd1);
                        //获取一个中奖人
                        choujiang.getOnePerson(param, function (data) {
                            if (data.status) {
                                var datas = data.message;
                                var userHtml = '';
                                if (datas.length) {
                                    $.each(datas, function (index, item) {
                                        userHtml += '<li  tel="' + item.realtel + '" class="userlist_' + item.uid + '" uid="' + item.uid + '">';
                                        userHtml += '<span class="head">' + item.realname + '</span>';
                                        userHtml += '<i class="xcolor r none">×</i>';
                                        userHtml += '<span class="r mr160">';
                                        userHtml += item.tel;
                                        userHtml += '</span>';
                                        userHtml += '<div class="clearfix"></div>';
                                        userHtml += '</li>';
                                        //一次只有一个
                                        listArray.push(item);
                                        var nowLi = $('#luckylist li').eq(listArray.length - 1);
                                        nowLi.find('img').attr('src', item.avatar);
                                        nowLi.find('p').html(item.realname);
                                    });
                                    if (parseInt(param.count) == 1) {
                                        $('#zhongjiang').html(userHtml);
                                    } else {
                                        $('#zhongjiang').append(userHtml);
                                    }

                                    $.each(datas, function (i, n) {
                                        $(".userlist_" + n.uid + "").find(".head").css("background-image", "url(" + n.avatar + ")");
                                    });
                                    param.count += parseInt(1);
                                    choujiang.getNowinNumber({
                                        prize_id: $('#prizetype li.active').data('prize_id')
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
        }
    }
})(jQuery, labUser);
