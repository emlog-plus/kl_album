<?php
function callback_init()
{
	$DB = Database::getInstance();
	$is_exist_album_query = $DB->query('show tables like "'.DB_PREFIX.'kl_album"');
	if($DB->num_rows($is_exist_album_query) == 0)
	{
		$dbcharset = 'utf8';
		$type = 'MYISAM';
		$add = $DB->getMysqlVersion() > '4.1' ? "ENGINE=".$type." DEFAULT CHARSET=".$dbcharset.";":"TYPE=".$type.";";
		$sql = "
CREATE TABLE `".DB_PREFIX."kl_album` (
`id` int(10) unsigned NOT NULL auto_increment,
`truename` varchar(255) NOT NULL,
`filename` varchar(255) NOT NULL,
`description` text,
`album` varchar(255) NOT NULL,
`addtime` int(10) NOT NULL default '0',
`w` smallint(5) NOT NULL DEFAULT '0',
`h` smallint(5) NOT NULL DEFAULT '0',
PRIMARY KEY  (`id`)
)".$add;
		$DB->query($sql);
	}else{
		$is_exist_new_columns_query = $DB->query('show columns from '.DB_PREFIX.'kl_album like "w"');
		if($DB->num_rows($is_exist_new_columns_query) == 0){
			$sql = "ALTER TABLE ".DB_PREFIX."kl_album ADD COLUMN `w` SMALLINT(5) DEFAULT 0 NOT NULL AFTER `addtime`, ADD COLUMN `h` SMALLINT(5) DEFAULT 0 NOT NULL AFTER `w`;";
			$DB->query($sql);
		}
	}

	kl_album_callback_do('n');

	kl_album_callback_checkhack();
}

function callback_rm()
{
	kl_album_callback_do('y');
}

function kl_album_callback_do($hide)
{
	global $CACHE;
	$DB = Database::getInstance();
	$CACHE->updateCache('options');
	$kl_album_config = Option::get('kl_album_config');

	if(is_null($kl_album_config)){
		$kl_album_config = $DB->escape_string(serialize(array()));
		$DB->query("INSERT INTO ".DB_PREFIX."options(option_name, option_value) VALUES('kl_album_config', '$kl_album_config')");
		$CACHE->updateCache('options');
	}

	$kl_album_info = Option::get('kl_album_info');

	if(is_null($kl_album_info)){
		$kl_album_info = $DB->escape_string(serialize(array()));
		$DB->query("INSERT INTO ".DB_PREFIX."options(option_name, option_value) VALUES('kl_album_info', '$kl_album_info')");
		$CACHE->updateCache('options');
	}

	$sql = "SELECT * FROM ".DB_PREFIX."navi where url='?plugin=kl_album-master' and isdefault='y'";
	$kl_album_navi = $DB->once_fetch_array($sql);
	if(empty($kl_album_navi)){
		if(Option::EMLOG_VERSION >= '5.1.0'){
			$DB->query("INSERT INTO ".DB_PREFIX."navi (naviname,url,newtab,hide,taxis,isdefault,`type`) VALUES('相册','?plugin=kl_album-master', 'n', '$hide', 2, 'y', 2)");
		}else{
			$DB->query("INSERT INTO ".DB_PREFIX."navi (naviname,url,newtab,hide,taxis,isdefault) VALUES('相册','?plugin=kl_album-master', 'n', '$hide', 2, 'y')");
		}
	}else{
		$Navi_Model = new Navi_Model();
		if(Option::EMLOG_VERSION >= '5.1.0'){
			$Navi_Model->updateNavi(array('hide'=>$hide, 'taxis'=>2, 'type'=>2), $kl_album_navi['id']);
		}else{
			$Navi_Model->updateNavi(array('hide'=>$hide), $kl_album_navi['id']);
		}
	}
	$CACHE->updateCache('navi');
}

function kl_album_callback_filelist($dir){
	$filelist = array();
	if(is_dir($dir) && $handle = @opendir($dir)){
		while($filename = @readdir($handle)){
			if(!in_array($filename, array('.', '..'))){
				$filename = $dir.'/'.$filename;
				if(is_dir($filename)){
					$filelist = array_merge($filelist, kl_album_callback_filelist($filename));
				}else{
					$extension = getFileSuffix($filename);
					if($extension == 'php') $filelist[] = $filename;
				}
			}
		}
		@closedir($handle);
	}
	return $filelist;
}

function kl_album_callback_checkhack(){
	$upload_dir = EMLOG_ROOT.'/content/plugins/kl_album-master/upload/';
	$filelist = kl_album_callback_filelist($upload_dir);
	foreach($filelist as $file){
		@unlink($file);
	}
}
