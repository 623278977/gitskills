var inapp = (window.location.href).indexOf('is_share') < 0 ? true : false;
var args = getQueryStringArgs();
var s_id = args['uid'] || '0';
;var Comment = {
    /**评论列表
     * param:参数
     * flag：加载最新页=reload/下一页标志=null
     * type:'video'，'live','activity','opp--(opportunity)'
     **/
    getCommentList: function (param, flag, type) {
        if (flag) {
            var params = {};
            params['page'] = param.page;
            params['page_size'] = param.page_size;
            params['uid'] = param.uid;
            params['id'] = param.id;
            params['type'] = param.commentType;
            params['section'] = param.section;
            var url = labUser.api_path + '/comment/list';
            ajaxRequest(params, url, function (data) {
                if (data.status) {
                    /**精彩数量**/
                    $(".amazeComment .num").html('0');
                    /**总数量**/
                    $(".allComment .num").html('0');
                    $("#amazeComment").empty();
                    $("#allComment").empty();
                    $('#pullUp').data('pagenow', 1);
                    getComment(data.message, param.page_size, type, flag);
                    if (isiOS || isAndroid) {
                        $('.onimgs').on('click', function () {
                            var this_index = $(this).data('picindex');
                            var imgary = $(this).parent().children('img');
                            var photos = [];
                            $.each(imgary, function (index, item) {
                                var photo = {
                                    url: $(this).attr('src'),
                                    selected: 0
                                };
                                if ($(this).data('picindex') == this_index) {
                                    photo.selected = 1;
                                }
                                photos.push(photo);
                            });
                            var jsonData = JSON.stringify(photos);
                            lookBigPhoto(jsonData);
                        });
                    }
                    else {
                        $('.onimgs').on('click', function () {
                            $('#showimg').attr('src', $(this).attr('src'));
                            $('#bigimg').removeClass('none');
                            $('#bigimg').one('click', function () {
                                $('#bigimg').addClass('none');
                            });
                        });
                    }
                }
                else {
                    if (data.message.all_count == 0) {
                        $('#nocommenttip').show();
                    }
                    else {
                        $('#nocommenttip').hide();
                    }
                }
            });
        }
        else {//加载下一页评论
            var params = {};
            params['page'] = param.page;
            params['page_size'] = param.page_size;
            params['uid'] = param.uid;
            params['id'] = param.id;
            params['type'] = param.commentType;
            var url = labUser.api_path + '/comment/list';
            ajaxRequest(params, url, function (data) {
                if (data.status) {
                    $('#pullUp').data('pagenow', params.page);
                    getComment(data.message, param.page_size, type, flag);
                    if (isiOS || isAndroid) {
                        $('.onimgs').on('click', function () {
                            var this_index = $(this).data('picindex');
                            var imgary = $(this).parent().children('img');
                            var photos = [];
                            $.each(imgary, function (index, item) {
                                var photo = {
                                    url: $(this).attr('src'),
                                    selected: 0
                                };
                                if ($(this).data('picindex') == this_index) {
                                    photo.selected = 1;
                                }
                                photos.push(photo);
                            });
                            var jsonData = JSON.stringify(photos);
                            lookBigPhoto(jsonData);
                        });
                    }
                    else {
                        $('.onimgs').on('click', function () {
                            $('#showimg').attr('src', $(this).attr('src'));
                            $('#bigimg').removeClass('none');
                            $('#bigimg').one('click', function () {
                                $('#bigimg').addClass('none');
                            });
                        });
                    }
                }
            });
        }
    },
    //最新动态评论列表
    getFreshList: function (param) {
        var params = {};
        params['type'] = param.type;
        params['uid'] = param.uid;
        params['id'] = param.id;
        params['fromId'] = param.fromId;
        params['update'] = param.update;
        params['fecthSize'] = param.fecthSize;
        var url = labUser.api_path + '/comment/fresh-list';
        ajaxRequest(params, url, function (data) {
            if (data.status) {
                freshList(data.message);
                $('#scroller').css('top', '0');
                if (isiOS || isAndroid) {
                    $('.onimgs').on('click', function () {
                        var this_index = $(this).data('picindex');
                        var imgary = $(this).parent().children('img');
                        var photos = [];
                        $.each(imgary, function (index, item) {
                            var photo = {
                                url: $(this).attr('src'),
                                selected: 0
                            };
                            if ($(this).data('picindex') == this_index) {
                                photo.selected = 1;
                            }
                            photos.push(photo);
                        });
                        var jsonData = JSON.stringify(photos);
                        lookBigPhoto(jsonData);
                    });
                }
                else {
                    $('.onimgs').on('click', function () {
                        $('#showimg').attr('src', $(this).attr('src'));
                        $('#bigimg').removeClass('none');
                        $('#bigimg').one('click', function () {
                            $('#bigimg').addClass('none');
                        });
                    });
                }

            }
        });
    },
    /**新增评论**/
    //区分直播、点播
    addComment: function (param, replay) {
        var params = {};
        params['uid'] = param.uid;
        params['type'] = param.commentType;
        params['post_id'] = param.id;
        params['content'] = param.content;
        if (replay == 'yes') {
            params['upid'] = param.upid;
            params['p_nickname'] = param.p_nickname;
            params['pContent'] = param.pContent;
        }
        else {
            params['upid'] = "";
            params['p_nickname'] = "";
            params['pContent'] = "";
        }
        var url = labUser.api_path + '/comment/add';
        ajaxRequest(params, url, function (data) {
            if (data.status) {
                if (param.commentType == 'Live') {
                    var parameter = {
                        "type": param.type,
                        "uid": param.uid,
                        "id": param.id,
                        "fromId": $('#commentflag').data('maxid'),
                        "update": "new",
                        "fecthSize": 0
                    };
                    Comment.getFreshList(parameter);
                    $('#subcomments').css('backgroundColor', '#999');
                }
                else {
                    //改成刷新列表
                    Comment.getCommentList(param, 'reload');
                }
                $('#subcomments').data('replay', 'no');
                //var datas = data.message;
                //if (params.upid == "") {
                //    addCommentHtml(datas.comment);  //新增
                //} else {
                //    replyCommentHtml(datas.comment, params);  //回复
                //}
                /**新增全部数量加1**/
                //var i = $(".allComment .num").text();
                //addnum(i);
                //$('#send_comment').data('replay', 'no');
                /**新增精彩数量加1**/
                // var j=$(".amazeComment .num").text();
                // j++;
                // $(".amazeComment .num").html(j);
            }
        });

    },

    /**删除评论**/
    deleteComment: function (param) {
        var params = {};
        params['uid'] = param.uid;//用户id
        params['id'] = param.commentid;//评论的id
        var url = labUser.api_path + '/comment/delete';
        ajaxRequest(params, url, function (data) {
        });
        /**点击删除-1**/
        var i = $(".allComment .num").text();
        deletenum(i);
    },

    /**评论点赞**/
    zan: function (id, type) {
        var param = {};
        param["uid"] = s_id;
        param["id"] = id;
        param["type"] = type;
        var url = labUser.api_path + '/comment/zhan';
        ajaxRequest(param, url, function (data) {
        });
    }
};
//flag:刷新、null下一页，type:'live','video','opp'
function getComment(result, pageSize, type, flag) {
    //直播刷新评论
    if (type == 'live') {
        if (flag) {
            $('#commentflag').data('maxid', result.max_id);
            $('#commentflag').data('minid', result.min_id);
        }
        else {
            $('#commentflag').data('minid', result.min_id);
        }
        if (result.all_count > 0) {
            $("#allComment").show();
            if (result.all_count > 10) {
                $('#scroller').css('paddingBottom', '4rem');
                $('#morecm').removeClass('none');
            }
        }
    }
    else if (type == 'activity'){
        if (flag) {
            $('#commentflag').data('maxid', result.max_id);
            $('#commentflag').data('minid', result.min_id);
        }
        else {
            $('#commentflag').data('minid', result.min_id);
        }
        $('#plun').html(result.all_count+'次');
        $('#commentnum').html(result.all_count);
        //总数
        if (result.all_count > 0) {
            $('#nocommenttip').hide();
            if (result.all_count > 8) {
                $('#morecm').removeClass('none');
            }
        } else {
            $('#nocommenttip').show();
        }
    }
    else
    {
        /**精彩数**/
        $(".amazeComment .num").html(result.amaze_count);
        /**总数**/
        $(".allComment .num").html(result.all_count);
        //精彩数
        if (result.amaze_count > 0) {
            $(".amazeComment,#amazeComment").show();
        } else {
            $(".amazeComment,#amazeComment").hide();
        }
        //总数
        if (result.all_count > 0) {
            $(".allComment,#allComment").show();
            $('#nocommenttip').hide();
            if (result.all_count > 10) {
                $('#scroller').css('paddingBottom', '4rem');
                $('#morecm').removeClass('none');
            }
        } else {
            $(".allComment,#allComment").hide();
            $('#nocommenttip').show();
        }
    }
    if (result.data.length > 0) {
        if (result.data.length < pageSize) {
            $('#scroller').css('paddingBottom', '0');
            $('#morecm').addClass('none');
        }
        var commentHtml = "";
        var allHtml = "";
        $.each(result.data, function (index, item) {
            if (item.type == "amaze") {
                if (item.form == 'normal') {
                    commentHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    commentHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    commentHtml += '<dd class="contentDd">';
                    commentHtml += '<p class="name">' + item.c_nickname + '</p>';
                    /**是否有父评论**/
                    if (item.pId == null) {
                        commentHtml += '<p class="comment" style="float:left;margin-top:-3rem;">' + entitiestoUtf16(item.content) + '</p>';
                        if (item.images.length > 0) {
                            commentHtml += '<div class="pics">';
                            $.each(item.images, function (index, item) {
                                commentHtml += '<img src="' + item + '" alt="" class="onimgs" data-picindex="' + index + '">';
                            });
                            commentHtml += '</div>';
                        }
                    } else {
                        commentHtml += '<p class="comment" >回复<span class="blue">@' + item.p_nickname + '</span>:' + item.content + '</p>';
                        commentHtml += '<div class="original">';
                        if (item.pStatus == '0') {//删除
                            commentHtml += '<p>评论已删除</p>';
                        }
                        else {
                            commentHtml += '<p>' + item.p_nickname + ':' + entitiestoUtf16(item.pContent) + '</p>';
                            if (item.pImages.length > 0) {
                                commentHtml += '<div class="pics">';
                                $.each(item.pImages, function (index, item) {
                                    commentHtml += '<img src="' + item + '" alt="" class="onimgs" data-picindex="' + index + '">';
                                });
                                commentHtml += '</div>';
                            }
                        }
                        commentHtml += '</div>';
                    }
                    commentHtml += '</dd>';
                    commentHtml += '<dd>';
                    commentHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    commentHtml += '</p>';
                    commentHtml += '</dd>';
                    commentHtml += '<div id="tips" class="coperate">';
                    if (item.c_uid == labUser.uid) {
                        //是自己的评论
                        commentHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                        commentHtml += '<div class="arrow_down"></div>';
                    } else {
                        //不是自己的评论
                        commentHtml += '<div class="content"><span class="reply">回复</span></div>';
                        commentHtml += '<div class="arrow_down"></div>';
                    }
                    commentHtml += '</div>';
                    commentHtml += '<div class="clearfix"></div>';
                    commentHtml += '</dl>';
                }
                else if (item.form == 'flower') {
                    commentHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    commentHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    commentHtml += '<dd  class="contentDd">';
                    commentHtml += '<p class="name">' + item.c_nickname + '</p>';
                    commentHtml += '<p class="comment">' + item.content + '</p>';
                    commentHtml += '</dd>';
                    commentHtml += '<dd>';
                    commentHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    commentHtml += '</p>';
                    commentHtml += '</dd>';
                    commentHtml += '<div class="clearfix"></div>';
                    commentHtml += '</dl>';
                }
                else if (item.form == 'reward') {
                    commentHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    commentHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    commentHtml += '<dd  class="contentDd">';
                    commentHtml += '<p class="name">' + item.c_nickname + '</p>';
                    commentHtml += '<div class="comment reward">' + item.content + '<span class="awardflag"></span></div>';
                    commentHtml += '</dd>';
                    commentHtml += '<dd>';
                    commentHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        commentHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    commentHtml += '</p>';
                    commentHtml += '</dd>';
                    commentHtml += '<div class="clearfix"></div>';
                    commentHtml += '</dl>';
                }
            } else if (item.type == "all") {
                if (item.form == 'normal') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    /**是否有父评论**/
                    if (item.pId == null) {
                        allHtml += '<p class="comment">' + entitiestoUtf16(item.content) + '</p>';
                        if (item.images.length > 0) {
                            allHtml += '<div class="pics">';
                            $.each(item.images, function (index, item) {
                                allHtml += '<img src="' + item + '" alt="" class="onimgs" data-picindex="' + index + '">';
                            });
                            allHtml += '</div>';
                        }
                    } else {
                        allHtml += '<p class="comment">回复<span class="blue">@' + item.p_nickname + '</span>:' + entitiestoUtf16(item.content) + '</p>';
                        allHtml += '<div class="original">';
                        if (item.pStatus == '0') {
                            allHtml += '<p>评论已删除</p>';
                        }
                        else {
                            allHtml += '<p>' + item.p_nickname + ':' + entitiestoUtf16(item.pContent) + '</p>';
                            if (item.pImages.length > 0) {
                                allHtml += '<div class="pics">';
                                $.each(item.pImages, function (index, item) {
                                    allHtml += '<img src="' + item + '" alt="" class="onimgs" data-picindex="' + index + '">';
                                });
                                allHtml += '</div>';
                            }
                        }
                        allHtml += '</div>';
                    }
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div id="tips" class="coperate">';
                    if (item.c_uid == labUser.uid) {
                        //是自己的评论
                        allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    } else {
                        //不是自己的评论
                        allHtml += '<div class="content"><span class="reply">回复</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    }
                    allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'flower') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    allHtml += '<p class="comment">' + item.content + '</p>';
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    //allHtml += '<div id="tips" class="coperate">';
                    //if (item.c_uid == labUser.uid) {
                    //    //是自己的评论
                    //    allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                    //    allHtml += '<div class="arrow_down"></div>';
                    //} else {
                    //    //不是自己的评论
                    //    allHtml += '<div class="content"><span class="reply">回复</span></div>';
                    //    allHtml += '<div class="arrow_down"></div>';
                    //}
                    //allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'reward') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    allHtml += '<div class="comment reward">' + item.content + '<span class="awardflag"></span></div>';
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'brand') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    /**是否有父评论**/
                    if (item.pId == null) {
                        var keywordhtml = '';
                        allHtml += '<p class="comment">成功购买了 <span class="colorf63">' + item.brand_good.goods_title + '</span></p>';
                        allHtml += '<section class="brandcontain nomp" style="position:relative;">';
                        allHtml += '<div class="brandcontent">';
                        allHtml += '<img src="' + item.brand_good.logo + '" alt="">';
                        allHtml += '<div class="branddetail f12" style="position:absolute;">';
                        allHtml += '<p class="f14"><span>' + cutString(item.brand_good.name, 10) + '</span><span class="color666">【' + item.brand_good.zone_name + '】</span></p>';
                        allHtml += '<p>';
                        allHtml += '<em class="brand-sort">' + item.brand_good.category_name + '</em> <span class="brand-st pl05">' + item.brand_good.investment_min + ' 万元 - ' + item.brand_good.investment_max + ' 万元</span>';
                        allHtml += '</p>';
                        allHtml += '<p class="brand-keyword">';
                        if (item.brand_good.keywords.length > 0) {
                            $.each(item.brand_good.keywords, function (index, oneitem) {
                                keywordhtml += '<span>' + oneitem + '</span>';
                            });
                            allHtml += keywordhtml;
                        }
                        allHtml += '</p>';
                        allHtml += '</div>';
                        allHtml += '<div class="clearfix"></div>';
                        allHtml += '</div>';
                        allHtml += '</section>';
                    }
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div id="tips" class="coperate">';
                    if (item.c_uid == labUser.uid) {
                        //是自己的评论
                        allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    } else {
                        //不是自己的评论
                        allHtml += '<div class="content"><span class="reply">回复</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    }
                    allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
            }
        });
        $("#amazeComment").append(commentHtml);
        $("#allComment").append(allHtml);
    } else if (result.data.length < 1) {
        //$(".pullUpLabel").html("数据全部加载完毕");
    }

}
//动态评论
function freshList(result) {
    if (result.all_count > 0) {
        $('#nocommenttip').hide();
        // $("#allComment").removeClass('none');
        $("#allComment").show();
        //used in activity
        $('#plun').html(result.all_count+'次');
        $('#commentnum').html(result.all_count);
    }
    else {
        $('#nocommenttip').show();
        // $("#allComment").addClass('none');
        $("#allComment").hide();
    }
    if (result.data.length > 0) {
        $('#commentflag').data('maxid', result.max_id);
        var allHtml = "";
        $.each(result.data, function (index, item) {
            if (item.type == "all") {
                if (item.form == 'normal') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    /**是否有父评论**/
                    if (item.pId == null) {
                        allHtml += '<p class="comment">' + entitiestoUtf16(item.content) + '</p>';
                        if (item.images.length > 0) {
                            allHtml += '<div class="pics">';
                            $.each(item.images, function (index, item) {
                                allHtml += '<img src="' + item + '" alt="" class="onimgs" data-picindex="' + index + '">';
                            });
                            allHtml += '</div>';
                        }
                    }
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div id="tips" class="coperate">';
                    if (item.c_uid == labUser.uid) {
                        //是自己的评论
                        allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    } else {
                        //不是自己的评论
                        allHtml += '<div class="content"><span class="reply">回复</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    }
                    allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'flower') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    allHtml += '<p class="comment" style="width:100%;height:auto;margin-left:1rem;margin-bottom:1rem;">' + item.content + '</p>';
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    //allHtml += '<div id="tips" class="coperate">';
                    //if (item.c_uid == labUser.uid) {
                    //    //是自己的评论
                    //    allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                    //    allHtml += '<div class="arrow_down"></div>';
                    //} else {
                    //    //不是自己的评论
                    //    allHtml += '<div class="content"><span class="reply">回复</span></div>';
                    //    allHtml += '<div class="arrow_down"></div>';
                    //}
                    //allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'reward') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    allHtml += '<div class="comment reward">' + item.content + '<span class="awardflag"></span></div>';
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
                else if (item.form == 'brand') {
                    allHtml += '<dl data-commentuid = "' + item.c_uid + '" data-commentid = "' + item.id + '" data-zan="' + item.is_zhan + '">';
                    allHtml += '<dt><img alt="" src="' + item.avatar + '"></dt>';
                    allHtml += '<dd  class="contentDd">';
                    allHtml += '<p class="name">' + item.c_nickname + '</p>';
                    /**是否有父评论**/
                    if (item.pId == null) {
                        var keywordhtml = '';
                        allHtml += '<p class="comment">成功购买了 <span class="colorf63">' + item.brand_good.goods_title + '</span></p>';
                        allHtml += '<section class="brandcontain nomp" style="position:relative;">';
                        allHtml += '<div class="brandcontent">';
                        allHtml += '<img src="' + item.brand_good.logo + '" alt="">';
                        allHtml += '<div class="branddetail f12" style="position:absolute;">';
                        allHtml += '<p class="f14"><span>' + cutString(item.brand_good.name, 10) + '</span><span class="color666">【' + item.brand_good.zone_name + '】</span></p>';
                        allHtml += '<p>';
                        allHtml += '<em class="brand-sort">' + item.brand_good.category_name + '</em> <span class="brand-st pl05">' + item.brand_good.investment_min + ' 万元 - ' + item.brand_good.investment_max + ' 万元</span>';
                        allHtml += '</p>';
                        allHtml += '<p class="brand-keyword">';
                        if (item.brand_good.keywords.length > 0) {
                            $.each(item.brand_good.keywords, function (index, oneitem) {
                                keywordhtml += '<span>' + oneitem + '</span>';
                            });
                            allHtml += keywordhtml;
                        }
                        allHtml += '</p>';
                        allHtml += '</div>';
                        allHtml += '<div class="clearfix"></div>';
                        allHtml += '</div>';
                        allHtml += '</section>';
                    }
                    allHtml += '</dd>';
                    allHtml += '<dd>';
                    allHtml += '<p class="intro"><span class="l">' + item.created_at + '</span>';
                    if (item.is_zhan == 1) {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="greenzan"></i><em class="num">' + item.likes + '</em></span>';
                    } else {
                        allHtml += '<span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">' + item.likes + '</em></span>';
                    }
                    allHtml += '</p>';
                    allHtml += '</dd>';
                    allHtml += '<div id="tips" class="coperate">';
                    if (item.c_uid == labUser.uid) {
                        //是自己的评论
                        allHtml += '<div class="content"></span><span class="delete">删除</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    } else {
                        //不是自己的评论
                        allHtml += '<div class="content"><span class="reply">回复</span></div>';
                        allHtml += '<div class="arrow_down"></div>';
                    }
                    allHtml += '</div>';
                    allHtml += '<div class="clearfix"></div>';
                    allHtml += '</dl>';
                }
            }
        });
        $("#allComment").prepend(allHtml);
    }
}
//新增评论,添加html
function addCommentHtml(datas) {
    var commentHtml = "";
    commentHtml += '<dl data-commentuid = "' + labUser.uid + '" data-commentid = "' + datas.id + '" data-zan = "0">';
    commentHtml += '<dt><img alt="" src="' + labUser.avatar + '"></dt>';
    commentHtml += '<dd  class="contentDd">';
    if (labUser.uid == 0) {
        commentHtml += '<p class="name">' + datas.nickname + '</p>';
    }
    else {
        commentHtml += '<p class="name">' + labUser.nickname + '</p>';
    }
    commentHtml += '<p class="comment">' + datas.content + '</p>';
    commentHtml += '</dd>';
    commentHtml += '<dd>';
    commentHtml += '<p class="intro"><span class="l">' + unix_to_datetime(datas.created_at) + '</span><span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">0</em></span></p>';
    commentHtml += '</dd>';
    commentHtml += '<div id="tips" class="coperate">';
    //是自己的评论
    commentHtml += '<div class="content"></span><span class="delete">删除</span></div>';
    commentHtml += '<div class="arrow_down"></div>';
    commentHtml += '</div>';
    commentHtml += '<div class="clearfix"></div>';
    commentHtml += '</dl>';
    $("#allComment").prepend(commentHtml).show();
}

//回复评论，添加html
function replyCommentHtml(datas, param) {
    var commentHtml = "";
    commentHtml += '<dl data-commentuid = "' + labUser.uid + '" data-commentid = "' + datas.id + '" data-zan = "0">';
    commentHtml += '<dt><img alt="" src="' + labUser.avatar + '"></dt>';
    commentHtml += '<dd  class="contentDd">';
    commentHtml += '<p class="name">' + labUser.nickname + '</p>';
    commentHtml += '<p class="comment">回复<span class="blue">@' + param.p_nickname + '</span>:' + datas.content + '</p>';
    commentHtml += '<p class="original"><span class="dark_gray">' + param.p_nickname + ':</span>' + param.pContent + '</p>';
    commentHtml += '</dd>';
    commentHtml += '<dd>';
    commentHtml += '<p class="intro"><span class="l">' + unix_to_datetime(datas.created_at) + '</span><span data-commentid = "' + item.id + '" class="r JS_zan"><i class="zan"></i><em class="num">0</em></span></p>';
    commentHtml += '</dd>';
    commentHtml += '<div id="tips" class="coperate">';
    //是自己的评论
    commentHtml += '<div class="content"></span><span class="delete">删除</span></div>';
    commentHtml += '<div class="arrow_down"></div>';
    commentHtml += '</div>';
    commentHtml += '</div>';
    commentHtml += '<div class="clearfix"></div>';
    commentHtml += '</dl>';
    $("#allComment").prepend(commentHtml).show();
}

function addnum(num) {
    var i = ++num;
    if (i > 0) {
        $('#nocommenttip').hide();
        $(".allComment,#allComment").show();
        $(".allComment .num").text(i);
    }
    else {
        $('#nocommenttip').show();
    }
}

function deletenum(num) {
    var i = --num;
    if (i > 0) {
        $('#nocommenttip').hide();
        $(".allComment .num").html(i);
    }
    else {
        $(".allComment,#allComment").hide();
        $(".allComment .num").text(0);
        $('#nocommenttip').show();
    }
}

// if (inapp) {
//     if (s_id > 0) {
//         $(document).on("click", ".JS_zan", function () {
//             var i = parseInt($(this).children('.num').text());
//             var id = $(this).parents("dl").data("commentid");
//             var zan = $(this).parents("dl").data("zan");
//             var type;
//             /**已经赞过**/
//             if (zan == 1) {
//                 $(this).addClass('flag');
//             } else {
//                 $(this).removeClass('flag');
//             }
//             if ($(this).hasClass('flag')) {
//                 $(this).removeClass('flag');
//                 i = i - 1;
//                 type = 0;
//                 zan = 0;
//                 $(this).children('.zan').removeClass('greenzan');
//             } else {
//                 $(this).addClass('flag');
//                 i = i + 1;
//                 type = 1;
//                 zan = 1;
//                 $(this).children('.zan').addClass('greenzan');
//             }
//             $(this).children('.num').text(i);
//             $(this).parents("dl").attr("data-zan", zan);
//             Comment.zan(id, type);
//         });
//     }
// }

function livingcomment(id,uid, subscribe){
                var param = {};
                param["id"] = id;
                param["uid"] = uid;
                param["type"] = subscribe;
                var url = labUser.api_path + "/comment/zhan";
                ajaxRequest(param, url, function(data) {
                    if (data.status) {   
                    }
                });
           }

$(document).on('click','.JS_zan i',function(){
    var subscribe;
    var id=$(this).parent().data('commentid');
    var args = getQueryStringArgs();
    var uid = args['uid'] || 0;  
    if($(this).hasClass('zan')){
        $(this).addClass('greenzan').removeClass('zan');
        var mub=$(this).next().text();
        $(this).next().text(mub-1+2);
        subscribe=1;
    }else{
        $(this).removeClass('greenzan').addClass('zan');
         var mub=$(this).next().text();
         $(this).next().text(mub-1);
        subscribe=0;
    }
    livingcomment(id,uid, subscribe) ;
})