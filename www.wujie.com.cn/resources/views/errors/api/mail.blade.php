<HTML>
    <HEAD>
        <TITLE>商圈接口异常，请及时维护！</TITLE>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <STYLE>
            html{color:#000;background:#FFF;}
            body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}
            table{border-collapse:collapse;border-spacing:0;}
            fieldset,img{border:0;}
            address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}
            li{list-style:none;}caption,th{text-align:left;}
            q:before,q:after{content:'';}
            abbr,acronym{border:0;font-variant:normal;}
            sup{vertical-align:text-top;}
            sub{vertical-align:text-bottom;}
            input,textarea,select{font-family:inherit;font-size:inherit;font-weight:inherit;}
            input,textarea,select{*font-size:100%;}
            legend{color:#000;}
            html { background: #eee; padding: 10px }
            img { border: 0; }
            #sf-resetcontent { width:970px; margin:0 auto; }
            {!!$css!!}
        </style>
    </HEAD>
    <BODY>
        <h2>请求地址：</h2>
        {{$url}} &nbsp;&nbsp;&nbsp;
        <h2>请求方式：</h2>
        {{$method}}
        <h2>请求参数：</h2>
        <pre>
            {{var_export($request, true)}}
        </pre>
        <h2>请求头信息：</h2>
        <pre>
            {{var_export($header, true)}}
        </pre>
        <h2>异常内容：</h2>
        <pre>
            {!!$content!!}
        </pre>
    </BODY>
</HTML>