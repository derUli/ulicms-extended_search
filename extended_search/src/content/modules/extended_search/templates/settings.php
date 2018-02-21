<?php
$acl = new ACL();
echo ModuleHelper::buildMethodCallForm("SearchSettingsController", "save");
$orderUptions = array(
    "relevance",
    "title",
    "url"
);

$sortDirections = array(
    "asc",
    "desc"
);

$order = Settings::get("extended_search_order");
$sort_direction = Settings::get("extended_search_sort_direction");
$extended_search_rebuild_index_on_clear_cache = Settings::get("extended_search_rebuild_index_on_clear_cache");
$extended_search_rebuld_index_cron = Settings::get("extended_search_rebuld_index_cron");
?>
<?php if(Request::getVar("save")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<?php if(Request::getVar("rebuild") and $acl->hasPermission("search_rebuild_index")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("search_index_was_rebuild");?>
		</div>
<?php }?>
<fieldset>
	<p>
		<label for="extended_search_order">
<?php translate("order_by");?>
</label> <select name="extended_search_order" id="extended_search_order"
			<?php if(!$acl->hasPermission("search_settings_change")) echo "disabled";?>>
<?php

foreach ($orderUptions as $option) {
    ?>
<option value="<?php esc($option)?>"
				<?php if($option == $order){echo "selected";}?>><?php translate($option);?></option>
<?php
}
?>
</select>
	</p>

	<p>
		<label for="extended_search_sort_direction">
<?php translate("sort_direction");?>
</label> <select name="extended_search_sort_direction"
			id="extended_search_sort_direction"
			<?php if(!$acl->hasPermission("search_settings_change")) echo "disabled";?>>
<?php

foreach ($sortDirections as $direction) {
    ?>
<option value="<?php esc($direction)?>"
				<?php if($direction == $sort_direction){echo "selected";}?>><?php translate($direction);?></option>
<?php
}
?>
</select>
	</p>
	<p>
		<input type="checkbox"
			name="extended_search_rebuild_index_on_clear_cache"
			id="extended_search_rebuild_index_on_clear_cache" value="1"
			<?php if(!$acl->hasPermission("search_settings_change")) echo "disabled";?>
			<?php if($extended_search_rebuild_index_on_clear_cache) echo "checked";?>>
		<label for="extended_search_rebuild_index_on_clear_cache"><?php translate("rebuild_index_on_clear_cache");?></label>
	</p>
	<p>
		<input type="checkbox" name="extended_search_rebuld_index_cron"
			id="extended_search_rebuld_index_cron" value="1"
			<?php if(!$acl->hasPermission("search_settings_change")) echo "disabled";?>
			<?php if($extended_search_rebuld_index_cron) echo "checked";?>> <label
			for="extended_search_rebuld_index_cron"><?php translate("rebuild_index_cron");?></label>
	</p>
</fieldset>
<fieldset>
	<p>
		<strong><?php translate("search_index_build_date");?></strong><br />
<?php echo Settings::get("extended_search_last_index_build_date") ? strftime("%x %X", Settings::get("extended_search_last_index_build_date")) : get_translation("never");?>
</p>
<?php if( $acl->hasPermission("search_rebuild_index")){?>
<p>
		<a
			href="<?php echo ModuleHelper::buildMethodCallUrl("SearchController", "rebuildNow");?>"
			class="btn btn-default"><?php translate("rebuild_index");?></a>
	</p>
<?php }?>
</fieldset>
<p class="voffset3">
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>