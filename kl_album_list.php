<?php !defined('EMLOG_ROOT') && exit('access deined!'); ?>
<div class="heading-bg  card-views">
 <ul class="breadcrumbs">
  <li><a href="./"><i class="fa fa-home"></i> 首页</a></li>
 <li class="active">
相册列表
  </li>
 </ul>
</div>
<?php echo $warning_msg; ?>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<div class="form-group text-center">
<input id="xinjianxiangce" type="button" value="新建相册" class="lanniu btn btn-success" />
<input id="baocunpaixu" type="button" value="保存排序" class="lanniu  btn btn-info" />
<input id="xiangcepeizhi" type="button" value="相册配置" class="lanniu  btn btn-primary" />
</div>
</div></div></div>
</div>
<div class="row" id="gallery">
<div  id="sortable" >
<?php
$kl_album_info = Option::get('kl_album_info');
$kl_album_info = unserialize($kl_album_info);
$album_head1 = '../content/plugins/kl_album-master/images/only_me.jpg';
$album_head2 = '../content/plugins/kl_album-master/images/no_cover_s.jpg';
if(!is_array($kl_album_info) || empty($kl_album_info)){
	echo '<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<div class="form-group text-center">还未创建相册</div></div></div></div>';
}else{
	$DB = Database::getInstance();
	krsort($kl_album_info);
	foreach ($kl_album_info as $key => $val){
		if(!isset($val['name'])) continue;
		if(isset($val['head']) && $val['head'] != 0){
			$iquery = $DB->query("SELECT * FROM ".DB_PREFIX."kl_album WHERE id={$val['head']}");
			if($DB->num_rows($iquery) > 0){
				$irow = $DB->fetch_row($iquery);
				$coverPath = $irow[2];
				$photo_size = empty($irow['w']) ? kl_album_change_image_size($val['head'], EMLOG_ROOT.substr($coverPath,2)) : array('w'=>$irow['w'], 'h'=>$irow['h']);
			}else{
				$coverPath = $album_head2;
				$photo_size = array('w'=>100, 'h'=>100);
			}
		}else{
			$iquery = $DB->query("SELECT * FROM ".DB_PREFIX."kl_album WHERE album={$val['addtime']}");
			if($DB->num_rows($iquery) > 0){
				$irow = $DB->fetch_array($iquery);
				$coverPath = $irow['filename'];
				$photo_size = empty($irow['w']) ? kl_album_change_image_size($irow['id'], EMLOG_ROOT.substr($coverPath,2)) : array('w'=>$irow['w'], 'h'=>$irow['h']);
			}else{
				$coverPath = $album_head2;
				$photo_size = array('w'=>100, 'h'=>100);
			}
		}
		$pwd = isset($val['pwd']) ? $val['pwd'] : '';
		switch ($val['restrict']){
			case 'public':
				$kl_quanxian_footer_str = '<select class="o_bg_color" name="album_r_'.$key.'" onchange="album_r_change(this);"><option value="public" selected>所有人可见</option><option value="private">仅主人可见</option><option value="protect">密码访问</option></select><input type="text" name="album_p_'.$key.'" value="'.$pwd.'" class="o_bg_color" onclick="album_getclick(this)" onpaste="return false" style="width:55px;display:none;ime-mode:disabled;" />';
				$kl_img_str = '<img id="album_public_img_'.$key.'" class="card-user-img img-circle pull-left"  src="'.$coverPath.'" width="'.$photo_size['w'].'" height="'.$photo_size['h'].'" />';
				break;
			case 'private':
				$kl_quanxian_footer_str = '<select class="o_bg_color form-control" name="album_r_'.$key.'" onchange="album_r_change(this);"><option value="public">所有人可见</option><option value="private" selected>仅主人可见</option><option value="protect">密码访问</option></select><input type="text" name="album_p_'.$key.'" value="'.$pwd.'" class="o_bg_color" onclick="album_getclick(this)" onpaste="return false" style="width:55px;display:none;ime-mode:disabled;" />';
				$kl_img_str = '<span style="display:none;"><img id="album_public_img_'.$key.'"  class="card-user-img img-circle pull-left"  src="'.$coverPath.'" width="'.$photo_size['w'].'" height="'.$photo_size['h'].'" /></span><span><img id="album_private_img_'.$key.'"  class="card-user-img img-circle pull-left"  src="../content/plugins/kl_album-master/images/only_me.jpg" /></span><span style="display:none;"><img id="album_protect_img_'.$key.'"  class="card-user-img img-circle pull-left"  src="'.$coverPath.'" width="'.$photo_size['w'].'" height="'.$photo_size['h'].'" /></span>';
				break;
			case 'protect':
				$kl_quanxian_footer_str = '<select class="o_bg_color" name="album_r_'.$key.'" onchange="album_r_change(this);"><option value="public">所有人可见</option><option value="private">仅主人可见</option><option value="protect" selected>密码访问</option></select><input type="text" name="album_p_'.$key.'" value="'.$pwd.'" class="o_bg_color" onclick="album_getclick(this)" onpaste="return false" style="width:55px;ime-mode:disabled;" />';
$kl_img_str = '<span style="display:none;"><img id="album_public_img_'.$key.'"  class="card-user-img img-circle pull-left"  src="'.$coverPath.'" width="'.$photo_size['w'].'" height="'.$photo_size['h'].'" /></span><span style="display:none;"><img id="album_private_img_'.$key.'"  class="card-user-img img-circle pull-left"  src="../content/plugins/kl_album-master/images/only_me.jpg" /></span><span><img id="album_protect_img_'.$key.'"  class="card-user-img img-circle pull-left" src="'.$coverPath.'" width="'.$photo_size['w'].'" height="'.$photo_size['h'].'" /></span>';
				break;
		}
		echo '
<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12" >
<div class="panel panel-primary contact-card card-view" style="cursor:move;">
<div class="panel-heading">
<div class="pull-left">
<div  class="glyphicon-move pull-left user-img-wrap mr-15">
'.$kl_img_str.'
</div>
<div class="pull-left user-detail-wrap">	
<span class="block card-user-name">
'.$val['name'].'
</span>
<span class="block card-user-desn">
'.$val['description'].'
</span>
</div>
</div>
<div class="pull-right">
<a class="pull-left inline-block mr-15" href="./plugin.php?plugin=kl_album-master&kl_album_action=display&album='.$val['addtime'].'">
<i class="zmdi zmdi-edit txt-light"></i>
</a>
<a class="pull-left inline-block mr-15" onclick="album_del('.$val['addtime'].')">
<i class="zmdi zmdi-delete txt-light"></i>
</a>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body row">
<div class="user-others-details pl-15 pr-15">
<div class="form-group">
 <label>相册名称</label>						<input type="hidden" name="sort[]" value="'.$val['addtime'].'"/><input name="album_n_'.$key.'" type="text" value="'.$val['name'].'" class="o_bg_color form-control" onclick="album_getclick(this)" />
</div>
<div class="form-group">
 <label>创建时间</label>	
<input name="album_d_'.$key.'" type="text" value="'.$val['description'].'" class="o_bg_color form-control" onclick="album_getclick(this)"/>
</div>
<div class="form-group">
 <label>访问控制</label>	
'.$kl_quanxian_footer_str.'
<input id="album_edit_'.$key.'" type="button" value="保存" class="btn btn-info btn-sm pull-right" onclick="album_edit('.$key.')" />
</div>
<div>
</div>
</div>
</div>
</div>
</div>
</div>
';
}
}
?>
</div>
</div>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/jquery.ui.sortable.min.js"></script>
<script type="text/javascript">
$("#menu_mg").addClass('active');
$("#kl_album").addClass('active-page');
setTimeout(hideActived,2600);
$(document).ready(function(){
$('#xiangcepeizhi').click(function(){
location.href='./plugin.php?plugin=kl_album-master&kl_album_action=config'
}
);
$('#xinjianxiangce').click(function(){
if(confirm('确定要建立一个新相册？')){
$.get('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=album_create&sid='+Math.random(),{is_create:'Y'},
function(result){if($.trim(result)=='kl_album_successed'){window.location.reload()}else{alert('发生错误:'+result)}})}});
$('#baocunpaixu').click(function(){
var ids='';$('div#gallery input[name^=sort]').each(function(){
ids=ids+$(this).val()+',';});
if(ids==''){
alert('您貌似还木有创建相册哦')
}else{
$.post('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=album_sort&sid='+Math.random(),{ids:ids},
function(result){
if($.trim(result)=='kl_album_successed'){
alert('保存成功');
}else{
alert('保存失败!'+result)
}
}
)}});
})
$( "#sortable" ).sortable();$( "#sortable" ).disableSelection();
Sortable.create(sortable, {
  handle: '.glyphicon-move',
  animation: 150
});
function album_getclick(el){$(el).removeClass('o_bg_color').addClass('no_bg_color');};
function album_r_change(obj){
if($(obj).val()=='protect'){
$(obj).next().show()}else{
$(obj).next().hide()
}}
function album_edit(num){
if($('select[name^=album_r_'+num+']').val()=='protect' && $.trim($('input[name^=album_p_'+num+']').val())==''){
alert('您选择了密码访问，密码不可以为空哦~')}else{
if($.trim($('input[name^=album_n_'+num+']').val())==''){
alert('相册名称不可以为空哦~')
}else{
$.getJSON('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=album_edit&sid='+Math.random(),{
key:num,n:$('input[name^=album_n_'+num+']').val(),d:$('input[name^=album_d_'+num+']').val(),r:$('select[name^=album_r_'+num+']').val(),p:$('input[name^=album_p_'+num+']').val()},function(result){
if(result[0]=='Y'){
$('input[name^=album_n_'+num+'],input[name^=album_d_'+num+'],input[name^=album_p_'+num+']').removeClass('no_bg_color').addClass('o_bg_color');$('#album_public_img_'+num+',#album_private_img_'+num+',#album_protect_img_'+num).not($('#album_'+result[1]+'_img_'+num)).parent().hide();$('#album_'+result[1]+'_img_'+num).parent().show();
alert('保存成功');
}else{
alert('保存失败：'+result)};});
}
}}
function album_del(num){if(confirm('删除相册将一并删除该相册内所有相片，确定要删除？')){$.get('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=album_del&sid='+Math.random(),{album:num},function(result){if($.trim(result)=='kl_album_successed'){window.location.reload()}else{alert('发生错误:'+result)}})}}
</script>