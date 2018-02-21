<?php
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
?>
<?php if(Request::getVar("save")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<p>
	<label for="extended_search_order">
<?php translate("order_by");?>
</label> <select name="extended_search_order" id="extended_search_order">
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
		id="extended_search_sort_direction">
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
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>