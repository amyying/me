<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>The MultiMedia Release Platform</title>
<link rel="stylesheet" href="http://mmrp.reeqi.me/css/bootstrap.css" />
<link rel="stylesheet" href="http://mmrp.reeqi.me/admin/css/style.css" />
<link rel="stylesheet" href="http://mmrp.reeqi.me/css/external.css" />
<link rel="stylesheet" href="css/mmrp_v2.css" />
<style type="text/css">
.mmrp_sys_form .mmrp_sys_projinfo .mmrp_form_radio {width:auto;margin:0 3px 0 0;display:inline-block;}
.controls-code-area {margin:5px 0 10px;}
.hide {display:none;}
.error {color:red;}
</style>
<script type="text/javascript" src="http://static.paipaiimg.com/js/jquery.1.8.js?t=1.8.3"></script>
</head>
<body>
<!--导航组件 S-->
<header class="mmrp_sys_header">
    <section> 
        <h1>MMRP</h1>
        <ul class="mod_nav">
            <li><a href="创建活动.html">新建流程</a></li>
            <li><a href="流水记录.html">流水查询</a></li>
            <li><a href="统计.html">统计查询</a></li>
        </ul>
        <div class="mod_user"><a href="##">登录</a> [ pufentan | <a href="##">注销</a> ]</div>
    </section>
    <ul class="mod_nav_sub" style="display:none;">
        <li><a href="##">背景上传</a></li>
        <li><a href="##">配置修改</a></li>
        <li><a href="##">添加模块</a></li>
        <li><a href="##">预览效果</a></li>
        <li>
            <a href="##">保存</a><i class="arrow"></i>
            <ul class="mod_nav_child">
                <li><a href="##">保存为模板</a></li>
                <li><a href="##">保存为草稿</a></li>
            </ul>
        </li>
        <li><a href="##">内容发布</a></li>
    </ul>   
</header>
<!--导航组件 E-->
<div class="mmrp_sys_form">
    <form class="mmrp_sys_projinfo form-horizontal" enctype="multipart/form-data" action="/test/uploads" method="post" id="upload_form">
        <legend>基本信息配置</legend>
        <div class="control-group warning">
            <label class="control-label" for="focusedInput">需求名称</label>
            <div class="controls"><input type="text" placeholder="(如：杨千桦首唱会）将出现您要创建的网页的浏览器标题上" /><span class="help-inline">警告信息</span></div>
        </div>
        <div class="control-group success">
            <label class="control-label" for="focusedInput">需求描述</label>
            <div class="controls"><textarea rows="3" placeholder="80个字以内，展示在搜索引擎上；吸引人的描述，能带来更大的点击率哦！"></textarea><span class="help-inline">耶！</span></div>
        </div>
        <div class="control-group error">
            <label class="control-label" for="focusedInput">编辑授权</label>
            <div class="controls"><input type="text" onclick="document.getElementById('tat_table').style.display=''" placeholder="RTX帐号" /><span class="help-inline">欧，NO！</span></div>
        </div>
        <div class="control-group">
            <label class="control-label" for="focusedInput">活动路径</label>
            <div class="controls"><select id="js_page_type"><option value="vip">http://y.qq.com/vip/</option><option value="topic">http://y.qq.com/topic/</option></select></label><input type="text" class="path" placeholder="项目名_时间_活动名(英文字母)" /></div>
        </div>
        <div class="control-group">
            <label class="control-label" for="focusedInput">活动内容</label>
            <div class="controls">
                <p class="help-block"><input type="radio" name="uploadtype" class="mmrp_form_radio" id="upload_code"  /><span for="code">代码</span></p>
                <div class="controls-code-area hide" id="controls-code-area">
                    <textarea id="txta_uploadCode" name="" cols="30" rows="10"></textarea>
                </div>
            </div>
            <div class="controls">
                <p class="help-block"><input type="radio" name="uploadtype" class="mmrp_form_radio" id="upload_zip" /><span for="code">ZIP压缩包</span></p>
                <div class="controls-file-upload hide" id="controls-file-upload">
                    <input type="file" id="input_uploadFile" name="input_uploadFile" />
                    <p class="error" id="input_uploadFile_error"></p>
                </div>
            </div>
        </div>
        <div class="next"><button type="submit" class="btn btn-primary" id="form_submit">&nbsp;&nbsp;&nbsp;&nbsp;确定&nbsp;&nbsp;&nbsp;&nbsp;</button></div>
    </form>
</div>
<footer class="mmrp_sys_footer">
    <div>
        <p class="copyright"><span>&copy;</span> 2011-2012 <a href="about.htm">MMRP Dev.team</a> | <a href="http://icase.oa.com/response/contact?Dept=isux&amp;Name=mmrp&amp;Admin[0]=hugohua&amp;Admin[1]=williamsli&amp;Admin[2]=pufentan" target="_blank">意见反馈</a></p>
        <p class="support">Power by HTML5 &amp; CSS3. Support for <a href="http://www.google.com/chrome/" target="_blank">Chrome.</a></p>
    </div>
</footer>

<table cellspacing="0" cellpadding="0" id="tat_table" class="autofinish" style="display:none;position: absolute; top: 301px; left: 246px;"><tbody><tr  id="tat_tr1"><td id="tat_td1" pos="1"><font >w</font>_adamduan(段叶生)</td></tr><tr  id="tat_tr2"><td id="tat_td2" pos="2"><font >w</font>_ajpeng(彭爱娟)</td></tr><tr style="background-color: rgb(196, 228, 255); " id="tat_tr3"><td id="tat_td3" pos="3"><font >w</font>_akane(AndrewKane)</td></tr></tbody></table>
<script type="text/javascript">
$("#upload_code,#upload_zip").click(function(){
    if($(this).attr("id")=='upload_code'){
        $("#controls-code-area").removeClass("hide");
        $("#controls-file-upload").addClass("hide"); 
    }
    if($(this).attr("id")=='upload_zip'){
        $("#controls-code-area").addClass("hide");
        $("#controls-file-upload").removeClass("hide");
    }
});
$("#form_submit").click(function(){
        //zip上传
        if($("#upload_zip:checked").length==1){
            var fileZip = $.trim($("#input_uploadFile").val());
            var pattern = /^([a-zA-Z0-9_\/-:\\])+\.zip$/;
            var flag = pattern.test(fileZip);
            if(!flag||fileZip==""){
                $("#input_uploadFile_error").html("请上传zip后缀格式的压缩文件");//提示错误
                $("#input_uploadFile").click(function(){$("#input_uploadFile_error").html("");});//点击的时候，把错误去掉
                return false;
            }
        } else if ($("#upload_code:checked").length==1){
            var $uploadCode =$("#txta_uploadCode");
            if($uploadCode.val()=="") {
                $uploadCode.after('<p class="error">呀，为什么不输入点代码呢？</p>');
                $uploadCode.focus(function(){$(this).next("p").remove();});
            }
        } else {
            //两个都不填报错
            alert("填一个吧");
            return false;
        } 
        
        /*$.post("get.php",{},function(data){
            alert("dd");
        });*/
        $("#upload_form").submit();
    return false;
});

</script>
</body>
</html>
