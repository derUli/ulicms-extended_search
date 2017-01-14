<?php
if (isset ( $_GET ["opensearch"] )) {
	$controller = ControllerRegistry::get ( "OpenSearchController" );
	$page = ModuleHelper::getFirstPageWithModule ( "extended_search" );
	if (! $page) {
		$page = ModuleHelper::getFirstPageWithModule ( "search" );
	}
	if (! $page) {
		header ( "HTTP/1.0 404 Not Found" );
		echo 'Search Module is not included in any page';
		exit ();
	}
	header ( "Content-type: application/opensearchdescription+xml" );
	echo $controller->getXML ();
	exit ();
}