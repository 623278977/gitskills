var choujiang;
choujiang = $.extend({
    /***获取未中奖用户**/
    getUsers: function (param, flag) {
        if (MyMar_ismoving == true) {
            return false;
        }
        /***判断这轮有没有抽完**/
        if (!choujiang.compareCount(param)) {
            choujiang.showMessage('#tc1');
            choujiang.closeInterval();
            is_winning=false;
            return false;
        }
        /***判断这个奖项有没有抽完**/
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
        if (param.number > 20) {
            limit = 10;
        } else if (param.number > 10) {
            limit = 5;
        } else if (param.number > 3) {
            limit = 2;
        } else if (param.number <= 3) {
            limit = 1;
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
                //callback(data);
            } else {
                getWinning(param, ++n);
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
            /**changeNumber  改变人数**/
            if (type == 'checkNumber') {
                if (parseInt(data.message)) {
                    /***还可以抽***/
                    choujiang.getNowinUsers(param);
                    if (flag != "first") {
                        myAuto_playSlow();
                    }
                } else {
                    /***不能抽**/
                    choujiang.showMessage('#tc2');
//                    $('.chuj').hide();
//                    $('.choujiang').show();
                    $(".chuj,.stop").hide();
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
            } else if (type == "beginCheck") {
                if (parseInt(data.message)) {
                    is_start(0, "first");
                    myAuto_start();
                    $('.stop').show();
                    $('#zhongjiang').html('');
                } else {
                    choujiang.showMessage('#tc2');
                    $('.chuj').hide();
                    $('.choujiang').show();
                    choujiang.closeInterval();
                    return false;
                }
            } else {
                $('.totalNumber').html(data.message);
            }
        });
    },
    /***抽奖弹框***/

    showMessage: function (id) {
        $(id).show().click(function () {
            $(this).hide();
        });
    },
    /***下一步****/
    nextStep: function () {
        var nextStepParam = {
            prize_id: $('.five_step .active:last').attr('prize_id'),
            next_prize_id: $('.five_step .active:last + div').attr('prize_id')
        };
        var nextStepUrl = labUser.api_path + '/game/nextstep';
        ajaxRequest(nextStepParam, nextStepUrl, function (data) {
            if (!parseInt(data.left)) {
                var first = $('.five_step .active:last');
                var next = first.next();
                next.addClass('blue').addClass('active');
                var imgsrc = next.attr('imgsrc');
                if (imgsrc) {
                    if (data.image){
                        $('.currentPrize img').attr('src', data.image);
                    }else{
                        $('.currentPrize img').attr('src', "../../choujiang/images/" + imgsrc + "_j.png");
                    }
                } else {
                    choujiang.showMessage('#tc2');
                    return false;
                }
                param.prize_id = next.attr('prize_id');
                choujiang.getNowinNumber(param, "changeNumber");
                /**重新来一轮**/
                if (zhuandong == true) {
                    $('.choujiang').hide();
                    $('.stop').hide();
                    $('.chuj').show();
                } else {
                    $(".chuj,.stop").hide();
                    $(".choujiang").show();
                }
            } else {
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
                        html += '<div class="person_news">';
                        html += '   <p class="name">' + n.realname + '</p>';
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
                        html += '   <p class="name">' + n.realname + '</p>';
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
});