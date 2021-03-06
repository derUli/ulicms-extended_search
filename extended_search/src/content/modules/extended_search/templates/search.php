<?php
$result = null;
$subject = "";
if (! empty ( $_GET ["q"] )) {
	$subject = $_GET ["q"];
	$controller = ControllerRegistry::get ( "SearchController" );
	$result = $controller->search ( $subject, getCurrentLanguage ( false ) );
}
?>
<form action="<?php echo buildSEOURL();?>" class="search-form"
	method="get">
	<label for="q"><?php translate("search_subject")?></label> <input
		type="search" required="true" name="q"
		value="<?php  Template::escape($subject);?>" results="10"
		autosave="<?php echo md5 ( $_SERVER ["SERVER_NAME"] );?>"> 
		<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?php translate("submit");?></button>
</form>
<?php if(!empty ( $subject)){?>
<?php if(count($result) > 0){?>
<p>
<ol class="search-subjects">
<?php
		foreach ( $result as $dataset ) {
			?>
<li><a href="<?php Template::escape($dataset->url);?>"><?php Template::escape($dataset->title);?></a></li>
<?php }?>

	</ol>
</p>
<?php
	} else {
		?>
<p class="no-results-found"><?php translate("no_results_found");?></p>
<?php
	}
}
