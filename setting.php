<?php require_once __DIR__.DIRECTORY_SEPARATOR.'header.php'; ?>
<?php if (!$myUser->connected()) throw new Exception('Merci de bien vouloir vous connecter'); 

$settingMenu = array();
Plugin::callHook("menu_setting", array(&$settingMenu));
$_['section'] = !isset($_['section'])?'user':$_['section'];
?>

<div class="row">
	<div class="col-md-3">
		<div class="list-group">
		<?php foreach($settingMenu as $item): ?>
		  <a href="<?php echo $item['url']; ?>" class="list-group-item <?php echo $item['url']==$page.'?section='.$_['section']?'active':'' ?> "><i class="fa fa-<?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?></a>
		<?php endforeach; ?>
		</div>
	</div>
	<div class="col-md-9"><?php Plugin::callHook("content_setting"); ?></div>
</div>
	

<?php require_once __ROOT__.'footer.php' ?>
