<?php
$page = ModuleHelper::getFirstPageWithModule ( "extended_search" );
if (! $page) {
	$page = ModuleHelper::getFirstPageWithModule ( "search" );
}
if (! $page) {
	header ( "HTTP/1.0 404 Not Found" );
	echo 'Search Module is not included in any page';
	exit ();
}
$page = $page->slug;
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?> ';?>

<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"> <ShortName><?=get_homepage_title();?></ShortName>
<Description><?=get_homepage_title();?></Description> <Url
	type="text/html"
	template="<?php Template::escape(getBaseFolderURL());?>/<?php Template::escape($page)?>.html?q={searchTerms}" />
</OpenSearchDescription>