<?php
$controller = ModuleHelper::getMainController("ix_files");
$allTypes = $controller->getAllSupportedTypes();
$indexTypes = $controller->getIndexedFiletypes();
?>
<?php
if (Request::isPost()) {
    
    ?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php
}
?>
<form
	action="<?php Template::escape(ModuleHelper::buildAdminURL("ix_files"))?>"
	method="post">
	<?php csrf_token_html();?>
	<p>
		<strong><?php translate("index_file_types");?></strong>
	</p>
	<p>
		<?php
foreach ($allTypes as $type) {
    ?>
		    <input type="checkbox" name="extensions[]"
			id="extension-<?php Template::escape($type)?>"
			value="<?php Template::escape($type)?>"
			<?php if(in_array($type, $indexTypes)) echo "checked";?>> <label
			for="extension-<?php Template::escape($type)?>">*.<?php Template::escape($type);?></label>
		<br />
		    <?php
}
?>
	</p>
	<p>
		<button type="submit" class="btn btn-default"><i class="fa fa-save"></i> <?php translate("save");?></button>
	</p>
</form>