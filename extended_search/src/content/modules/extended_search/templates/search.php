<?php
$result = null;
$subject = "";
if (! empty ( $_GET ["q"] )) {
	$subject = htmlspecialchars ( $_GET ["q"], ENT_QUOTES, "UTF-8" );
	$controller = ControllerRegistry::get ( "SearchController" );
	$result = $controller->search ( $subject, getCurrentLanguage ( true ) );
}
?>
<form action="<?php echo buildSEOURL();?>" class="search-form"
	method="get">
	<label for="q"><?php translate("search_subject")?></label> <input
		type="search" required="true" name="q"
		value="<?php  Template::escape($subject);?>" results="10"
		autosave="<?php echo md5 ( $_SERVER ["SERVER_NAME"] );?>"
		class="form-control"> <input type="submit"
		value="<?php translate("submit");?>" class="form-control">

</form>
<?php if($result){?>
<?php if(count($result) > 0){?>
<ol class="search-subjects">
<?php
		foreach ( $result as $dataset ) {
			?>
<li><a href="<?php Template::escape($dataset->url);?>"><?php Template::escape($dataset->title);?></a></li>
<?php }?>

	</ol>
<?php }?>
<?php
} else {
	?>
	translate("no_results_found");
	<?php
}