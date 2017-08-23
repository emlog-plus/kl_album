<?php
/**
 * kl_album_setting.php
 *design by KLLER
 */
!defined('EMLOG_ROOT') && exit('access deined!');
function plugin_setting_view()
{
	$warning_msg = '';
	if(Option::EMLOG_VERSION >= '5.0.0'){
		if(!is_writable('../content/plugins/kl_album-master/upload/')) $warning_msg = '<span class="error">kl_album-master/upload目录可能不可写，如果已经是可写状态，请忽略此信息。</span>';
	}else{
		$warning_msg = '<span class="error">当前版本的EM相册适用于大于5.0.0版本的博客程序。您的博客版本太低。</span>';
	}
	isset($_GET['kl_album_action']) ? $kl_album_action = $_GET['kl_album_action'] : $kl_album_action = '';
	switch ($kl_album_action){
		case 'upload':
			require('kl_album_upload.php');
			break;
		case 'display':
			if(isset($_GET['album'])){
				require('kl_album_photo_list.php');
			}else{
				require('kl_album_list.php');
			}
			break;
		case 'config':
			require('kl_album_config.php');
			break;
		default:
			require('kl_album_list.php');
			break;
	}
}
?>