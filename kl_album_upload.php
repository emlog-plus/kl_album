<?php !defined('EMLOG_ROOT') && exit('access deined!'); ?>
<script type="text/javascript">
$("#menu_mg").addClass('active');
$("#kl_album").addClass('active-page');
setTimeout(hideActived,2600);
</script>
<div class="heading-bg  card-views">
 <ul class="breadcrumbs">
  <li>
  <a href="./"><i class="fa fa-home"></i> 首页</a>
  </li>
 <li class="active">
上传相片
  </li>
 </ul>
</div>
<?php echo $warning_msg; ?>
<?php
$DB = Database::getInstance();
$kl_album_upload_max_filesize = kl_album_get_upload_max_filesize();
$is_exist_album_query = $DB->query('show tables like "'.DB_PREFIX.'kl_album"');
if($DB->num_rows($is_exist_album_query) == 0){
	echo '您还没有进行相册的初始配置。<br /><a href="./plugin.php?plugin=kl_album-master">现在就去配置</a>';
	exit;
}
$query = $DB->query("SELECT * FROM ".DB_PREFIX."options WHERE option_name='kl_album_info'");
if($DB->num_rows($query) == 0){
	echo "您还没有创建相册！<br /><a href='./plugin.php?plugin=kl_album-master&kl_album_action=create'>现在就去创建</a>";
	exit;
}else{
	$row = $DB->fetch_row($query);
	$kl = unserialize($row[2]);
	if(count($kl) == 0){
		echo "您还没有创建相册！<br /><a href='./plugin.php?plugin=kl_album-master&kl_album_action=create'>现在就去创建</a>";
		exit;
	}
}
if(isset($_GET['album'])){
	$addtimeArr = array();
	foreach ($kl as $v){
		$addtimeArr[] = $v['addtime'];
	}
	if(in_array($_GET['album'], $addtimeArr)) $whichalbum = $_GET['album'];
}
?>
<style type="text/css">
.swfupload{vertical-align: top;}
.uploadBar{display:block;width: 0px;height: 5px;border: solid 1px #445566;background-color: blue;overflow:hidden;}
.uploadCancel{border-bottom: solid 1px blue;cursor:pointer;}
.uploadFileList{width:100%;height: 5px;border: solid 1px #999999;}


TR.uploadTR{color:#234245;height:22px;}
TD.uploadTD{border-top: 1px dotted #CCCCCC;height:27px;}
TR.uploadTitle{vertical-align: middle;font-weight:700;background-color: #CCCDDD;height:22px;color:#000000}
</style>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/swfupload.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/swfupload.swfobject.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/fileprogress.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/handlers.js"></script>
<script type="text/javascript">
var swfu;
SWFUpload.onload = function () {
	var settings = {
		flash_url : "<?php echo BLOG_URL; ?>content/plugins/kl_album-master/js/swfupload.swf",
		upload_url: "<?php echo BLOG_URL; ?>content/plugins/kl_album-master/kl_album_ajax_do.php",
		post_params: {
		"PHPSESSID" : "<?php echo session_id(); ?>",
		"album" : <?php echo isset($_GET['album']) ? intval($_GET['album']) : 0;?>,
		"<?php echo AUTH_COOKIE_NAME;?>" : "<?php echo $_COOKIE[AUTH_COOKIE_NAME];?>"
		},
		file_size_limit : "<?php echo $kl_album_upload_max_filesize/1024; ?>",
		file_types : "*.jpg;*.jpeg;*.png;*.gif",
		file_types_description : "图片文件",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			uploadButtonId : "btnUpload",
			myFileListTarget : "idFileList"
		},
		debug: false,
		auto_upload:false,

		// Button Settings
		button_image_url : "../content/plugins/kl_album-master/images/XPButtonUploadText_61x22.png",	// Relative to the SWF file
		button_placeholder_id : "spanButtonPlaceholder",
		button_width: 61,
		button_height: 22,

		// The event handler functions are defined in handlers.js
		swfupload_loaded_handler : swfUploadLoaded,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete,	// Queue plugin event

		// SWFObject settings
		minimum_flash_version : "9.0.28",
		swfupload_pre_load_handler : swfUploadPreLoad,
		swfupload_load_failed_handler : swfUploadLoadFailed
	};

	swfu = new SWFUpload(settings);
}
function setPost(){var album = document.getElementById('album').value;swfu.addPostParam("album", album);}
jQuery(function($){
	$('#xiangceliebiao').click(function(){location.href='./plugin.php?plugin=kl_album-master&kl_album_action=display'});
	$('#jinruxiangce').click(function(){location.href='./plugin.php?plugin=kl_album-master&kl_album_action=display&album='+$('#album').val()});
})
</script>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<div class="form-group text-center">
<input id="jinruxiangce" type="button" value="返回相册" class="btn btn-primary btn-sm" /> <input id="xiangceliebiao" type="button" value="相册列表" class="btn btn-primary btn-sm" />
</div>
</div></div></div>
</div>

 <div class="form-group form-inline">
请先选择目的相册：
<select id="album" name="album" class="form-control" onchange="setPost();">
<?php
if(isset($whichalbum)){
	foreach ($kl as $album){
		if($album['addtime'] == $whichalbum){
			echo "<option value='{$album['addtime']}' selected>{$album['name']}</option>";
		}else{
			echo "<option value='{$album['addtime']}'>{$album['name']}</option>";
		}
	}
}else{
	foreach ($kl as $album){
		echo "<option value='{$album['addtime']}'>{$album['name']}</option>";
	}
}
?>
</select>
</div>

<div class="row">
<div class="col-lg-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<table class="table table-striped table-bordered mb-0">
<tr>
<td>
<span id="spanButtonPlaceholder"></span>
<input id="btnUpload" type="button" value="开始上传" class="btn btn-info btn-sm pull-right" /> （图片最大不能超过<?php echo $kl_album_upload_max_filesize/1048576; ?>M）
</td></tr></table>
<table id="idFileList" class="uploadFileList"><tr class="uploadTitle"><td height="25"><B>文件名</B></td><td width="70"><B>文件大小</B></td><td width="145"><B>状态</B></td>
<td width="35">操作</td>
</tr>
</table>
<div class="form-group text-center">
等待上传 <span id="idFileListCount">0</span> 个 ，成功上传 <span id="idFileListSuccessUploadCount">0</span> 个
</div>
<div id="divSWFUploadUI" style="visibility: hidden;"></div>
<noscript style="display: block; margin: 10px 25px; padding: 10px 15px;">很抱歉，相片上传界面无法载入，请检查浏览器是否支持JavaScript。或做刷新操作。</noscript>
<div id="divLoadingContent" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">相片上传界面正在载入，请稍后...,或进行刷新操作。	</div>
<div id="divLongLoading" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">相片上传界面载入失败，请确保浏览器已经开启对JavaScript的支持，并且已经安装可以工作的Flash插件版本。或做刷新操作。</div>
<div id="divAlternateContent" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">很抱歉，相片上传界面无法载入，请安装或者升级您的Flash插件。请访问： <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank">Adobe网站</a> 获取最新的Flash插件。</div>
</div>
</div>