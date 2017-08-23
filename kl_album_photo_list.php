<?php
!defined('EMLOG_ROOT') && exit('access deined!');
$album = isset($_GET['album']) ? intval($_GET['album']) : '';
$kl_album_info = Option::get('kl_album_info');
$kl_album_info = unserialize($kl_album_info);
$albumname = '';
$albumlist_option_str = '';
$head = '';
foreach($kl_album_info as $val){
	if($val['addtime'] == $album){
		$albumname = $val['name'];
		if(isset($val['head'])) $head .= $val['head'];
	}else{
		$albumlist_option_str .= "<option value='{$val['addtime']}'>{$val['name']}</option>";
	}
}
?>
 <div class="heading-bg  card-views">
  <ul class="breadcrumbs">
  <li><a href="./"><i class="fa fa-home"></i> 首页</a></li>
  <li class="active"><?php echo $albumname;?> 中的相片</li>
 </ul>
</div>
<?php echo $warning_msg; ?>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<div class="form-group text-center">
<input id="xiangceliebiao" type="button" value="返回" class="btn btn-primary" />
<input id="shangchuan" type="button" value="上传" class="btn btn-primary " />
<input id="baocunpaixu" type="button" value="保存排序" class="btn btn-primary" />
<input id="chongzhipaixu" type="button" value="重置" class="btn btn-primary" />
</div>
</div></div></div>
</div>
<div class="row">
<div class="col-lg-8">
<div class="panel panel-default card-view">
<div class="table-wrap ">
<div class="table-responsive" id="gallery" >
<table class="table table-striped table-bordered mb-0">
<tbody id="sortable">
<?php
$kl_album = Option::get('kl_album_'.$album);
if(is_null($kl_album)){
	$condition = " and album={$album} order by id desc";
}else{
	$idStr = empty($kl_album) ? 0 : $kl_album;
	$condition = " and id in({$idStr}) order by substring_index('{$idStr}', id, 1)";
}
$DB = Database::getInstance();
$query = $DB->query("SELECT * FROM ".DB_PREFIX."kl_album WHERE 1 {$condition}");

if($DB->num_rows($query) == 0){
	echo '<p  class="form-group text-center">此相册还没有上传过相片！</p>';
	echo '<p  class="form-group text-center"><a href="./plugin.php?plugin=kl_album-master&kl_album_action=upload&album='.$album.'"><b>现在就去上传</b></a></p>';	
}

$photos = array();
while($photo = $DB->fetch_array($query)){
$photos[] = $photo;
}
foreach($photos as $val):
$photo_size = empty($val['w']) ? kl_album_change_image_size($val['id'], EMLOG_ROOT.substr($val['filename'],2)) : array('w'=>$val['w'], 'h'=>$val['h']);
?>
<tr>
  <td width="22">
  <input type="checkbox" value="<?php echo $val['id']; ?>"  />
  </td>
  
<td class="glyphicon-move" width="110" >

<img class="<?php echo $val['id'] == $head ? 'fengmian' : 'notfengmian'; ?>" id="img_<?php echo $val['id']; ?>" src="<?php echo $val['filename']; ?>" width="<?php echo $photo_size['w'];?>" height="<?php echo $photo_size['h'];?>" />
</td>

<td>
<nobr>相片名称：</nobr>
<input type="hidden" name="sort[]" value="<?php echo $val['id']; ?>" />
<input name="tn_<?php echo $val['id']; ?>" type="text" value="<?php echo $val['truename']; ?>" class="o_bg_color form-control" onclick="getclick(this)" />
<nobr>相片描述：</nobr>
<input name="d_<?php echo $val['id']; ?>" type="text" value="<?php echo $val['description']; ?>" class="o_bg_color form-control" onclick="getclick(this)" />
<nobr>操　　作：</nobr>
<div class=" form-inline">
<input id="edit_<?php echo $val['id']; ?>" type="button" value="保存" class="btn btn-primary btn-sm" onclick="edit(<?php echo $val['id']; ?>)" />  <input id="fenmian_<?php echo $val['id']; ?>" type="button" value="封面" class="btn btn-danger btn-sm" onclick="setHead(<?php echo $val['id']; ?>)" />
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<div class="col-lg-4">
<div class="panel panel-default card-view">
<div class="form-group">
<input id="quanxuan" type="button" value="全选" class="btn btn-primary" />
<input id="fanxuan" type="button" value="反选" class="btn btn-primary" />
<input id="sanchu" type="button" value="删除" class="btn btn-primary" />
</div>
<div class="form-group">
<select id="album" class="form-control"><option value="">移动到..</option><?php echo $albumlist_option_str; ?></select>
</div>
<div class="form-group">
<input id="queding" type="button" value="确定" class="btn btn-primary " />
</div>
</div>
</div>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../content/plugins/kl_album-master/js/jquery.ui.sortable.min.js"></script>
<script type="text/javascript">
$("#menu_mg").addClass('active');
$("#kl_album").addClass('active-page');
setTimeout(hideActived,2600);
$(document).ready(function(){
$('#xiangceliebiao').click(
function(){
location.href='./plugin.php?plugin=kl_album-master&kl_album_action=display'
}
);
$('#shangchuan').click(function(){
location.href='./plugin.php?plugin=kl_album-master&kl_album_action=upload&album=<?php echo $album; ?>'
}
);

$('#fanxuan').click(function(){
$('div#gallery input:checkbox').each(function(){$(this).attr('checked',!this.checked)})});
	$('#sanchu').click(function(){var ids='';$('div#gallery input:checked').each(function(){ids=ids+$(this).val()+','});if(ids!=''){if(confirm('确定要删除所有选中的图片？')){$.post('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=del&sid='+Math.random(),{ids:ids,album:<?php echo $album; ?>},function(result){if($.trim(result)=='kl_album_successed'){window.location.reload()}else{alert('删除失败:'+result)}})}}else{alert('请勾选要删除的图片')}});
	$('#queding').click(function(){if($('#album').val()!==''){var ids='';$('div#gallery input:checked').each(function(){ids=ids+$(this).val()+','});if(ids!=''){if(confirm('确定要移动这些相片到选定的相册？')){$.post('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=move&sid='+Math.random(),{ids:ids,newalbum:$('#album').val()},function(result){if($.trim(result)=='kl_album_successed'){window.location.reload()}else{alert('移动失败:'+result)}})}}else{alert('请勾选要移动的图片')}}else{alert('请选择目的相册')}});

$('#quanxuan').click(function(){
$('div#gallery input:checkbox').each(function(){
$(this).attr('checked',true)
}
)
});
$('#baocunpaixu').click(function(){var ids='';$('div#gallery input[name^=sort]').each(function(){ids=ids+$(this).val()+',';});if(ids==''){alert('您的相册内貌似还没有上传图片哦')}else{$.post('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=photo_sort&sid='+Math.random(),{ids:ids,album:<?php echo $album; ?>},function(result){if($.trim(result)=='kl_album_successed'){
alert('保存成功')
}else{
alert('保存失败!'+result)}})}});
$('#chongzhipaixu').click(function(){
if(confirm('确定要重置排序？')){
$.get('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=photo_sort_reset&sid='+Math.random(),{album:<?php echo $album; ?>},function(result){if($.trim(result)=='kl_album_successed'){window.location.reload()}else{alert('重置失败：'+result)};});}});
	
$( "#sortable" ).sortable();$( "#sortable" ).disableSelection();
Sortable.create(sortable, {
  handle: '.glyphicon-move',
  animation: 150
});	
function getclick(el){$(el).removeClass('o_bg_color').addClass('no_bg_color');
}
})
function edit(num){
$.get('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=edit&sid='+Math.random(),
{
id:num,tn:$('div#gallery input[name^=tn_'+num+']').val(),
d:$('div#gallery input[name^=d_'+num+']').val()
},
function(result){
if($.trim(result)=='kl_album_successed'){
alert('保存成功');
}else{
alert('保存失败：'+result)
};
}
);}
function setHead(num){$.get('../content/plugins/kl_album-master/kl_album_ajax_do.php?action=setHead&sid='+Math.random(),{id:num,album:<?php echo $album; ?>},function(result){if($.trim(result).indexOf('kl_album_successed')!=-1){$('.fengmian').removeClass('fengmian').addClass('notfengmian');$('#img_'+num).addClass('fengmian');
alert('设置成功');
}else{alert('设置失败:'+result)}})}
</script>
