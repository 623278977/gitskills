/**
 * Created by wangcx on 2016/5/26.
 */
//上传弹出窗口
$(function(){
    $("#change_head").click(function () {
        $("#modal").show();
        $("body").css({overflow:"hidden",paddingRight:"17px"})
    });
    $("#head_close").click(function(){
        $("#modal").hide();
        $("body").css({overflow:"visible",padding:0})
    });

/* 头像预览 */
    $("#head_clip").Jcrop({
        onChange:showPreview,
        onSelect:showPreview,
        aspectRatio:1
    });
    //简单的事件处理程序，响应自onChange,onSelect事件，按照上面的Jcrop调用
    function showPreview(coords){
        if(parseInt(coords.w) > 0){
            //计算预览区域图片缩放的比例，通过计算显示区域的宽度(与高度)与剪裁的宽度(与高度)之比得到
            var rx1 = $("#preview_box_1").width() / coords.w;
            var ry1 = $("#preview_box_1").height() / coords.h;
            var rx2 = $("#preview_box_2").width() / coords.w;
            var ry2 = $("#preview_box_2").height() / coords.h;
            //通过比例值控制图片的样式与显示
            $("#crop_preview_1").css({
                width:Math.round(rx1 * $("#head_clip").width()) + "px",	//预览图片宽度为计算比例值与原图片宽度的乘积
                height:Math.round(rx1 * $("#head_clip").height()) + "px",	//预览图片高度为计算比例值与原图片高度的乘积
                marginLeft:"-" + Math.round(rx1 * coords.x) + "px",
                marginTop:"-" + Math.round(ry1* coords.y) + "px"
            });
            $("#crop_preview_2").css({
                width:Math.round(rx2 * $("#head_clip").width()) + "px",	//预览图片宽度为计算比例值与原图片宽度的乘积
                height:Math.round(rx2 * $("#head_clip").height()) + "px",	//预览图片高度为计算比例值与原图片高度的乘积
                marginLeft:"-" + Math.round(rx2 * coords.x) + "px",
                marginTop:"-" + Math.round(ry2* coords.y) + "px"
            });
        }
    }

});
