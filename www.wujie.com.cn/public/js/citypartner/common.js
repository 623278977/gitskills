function ajaxRequest(param, requestUrl, successCallback) {

	param['_token'] = $('input[name="_token"]').val();
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data: param,
		// headers: {'X-CSRF-TOKEN': labUser.token},
		dataType: 'json',
		success: function(data) {

			if (successCallback && (successCallback instanceof Function)) {
				successCallback(data);
			}
		},
		error: function(data){
            $("#doSave").removeAttr('disabled');
            var errorinfo = '';
            var return_data = data.responseJSON ? data.responseJSON : eval('('+data.responseText+')');
			$.each(return_data,function(i,j){
				if(errorinfo == ''){
					errorinfo = j[0];
					$('#info').html(errorinfo);
					$('#error').css('display','block');
					setTimeout('$("#error").hide("slow")',2000);
				}
			})
		}
	});
};
function ajaxRequestPwd(param, requestUrl, successCallback) {

	param['_token'] = $('input[name="_token"]').val();
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data: param,
		// headers: {'X-CSRF-TOKEN': labUser.token},
		dataType: 'json',
		success: function(data) {

			if (successCallback && (successCallback instanceof Function)) {
				successCallback(data);
			}
		},
		error: function(data){
			$("#doSave").removeAttr('disabled');
			var errorinfo = '';
			var return_data = data.responseJSON ? data.responseJSON : eval('('+data.responseText+')');
			$.each(return_data,function(i,j){
				if(errorinfo == ''){
					errorinfo = j[0];
					$('#info').html(errorinfo);
					$('#error').css('display','block');
					$('#img_captcha').trigger('click');
					setTimeout('$("#error").hide("slow")',2000);
				}
			})
		}
	});
};
/**
 * 过滤首尾空格
 */
function LTrim(str) {

	return str.replace(/^\s+/, "");
}
/**
 * 参数说明： 根据长度截取先使用字符串，超长部分追加… str 对象字符串 len 目标字节长度 返回值： 处理结果字符串
 */
function cutString(str, len) {

	var str_len = str.length;
	if (str_len <= len) {
		return str;
	}
	else {
		return str.substring(0, len) + '...';
	}
}
// html转义
function encodeHtml(str) {

	var s = "";
	if (str.length == 0)
		return "";
	s = str.replace(/&/g, "&gt;");
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	s = s.replace(/ /g, "&nbsp;");
	s = s.replace(/\'/g, "&#39;");
	s = s.replace(/\"/g, "&quot;");
	s = s.replace(/\n/g, "<br>");
	return s;
}
var openBrowse;
var fileUpload;
//$(function() {

	openBrowse = function(filename, file) {
		var ie = navigator.appName == "Microsoft Internet Explorer" ? true : false;
		if (ie) {
                   file= '#'+file;
			$(file).click();
			var file = $(file).val();
			$(filename).val(file);
		}
		else {
			var a = document.createEvent("MouseEvents");// FF的处理
			a.initEvent("click", true, true);
			document.getElementById(file).dispatchEvent(a);
		}

	};
	function uploadCallBack(name, path,filetype, filesize,filename, input_name) {
		//var img_show = '/public/images/upload/' + path + '/' + name;
		//var img = '/public/images/upload/' + path + '/' + name;
//        $("input[name='"+input_name+"']").val(path);
        $("#cut-path").val(path);
        $("input[name='"+input_name+"_name']").val(filename);
        $("input[name='"+input_name+"_filetype']").val(filetype);
        $("input[name='"+input_name+"_filesize']").val(filesize);
        $("img[name='"+input_name+"']").attr("src", name);
		//$("#photo_show").attr("src", img_show);
		$("#"+input_name+"_name").html("文件名: "+filename+" 文件大小: "+filesize);
		var route = '/citypartner/business/download?path='+path;
		$("#"+input_name+"_href").attr('href',route);
	}
	fileUpload = function(fileId, input_name,size) {
            var data={};
            if(size){
                size=size.split(':');
                if(size.length==2){
                    data.width=size[0];
                    data.height=size[1];
                }
            }
		$.ajaxFileUpload({
			data: data ,
			url: uploadUrl ,
			secureuri: false, // 是否启用安全提交
			dataType: 'json', // 数据类型
			fileElementId: fileId, // 表示文件域ID
			// 提交成功后处理函数 html为返回值，status为执行的状态
			success: function(html, status) {
                            if(html.status){
                                html=html.message;
                                uploadCallBack(html.url,html.path,  html.filetype, html.filesize, html.filename, input_name);
                            }else{
                                alert(html.message);
                            }
				
			},

			// 提交失败处理函数
			error: function(html, status, e) {
			    console.log(html); 
			}
		})
	}
//});
function showMessage($str){
	alert($str);
}
/**ajax分页**/
