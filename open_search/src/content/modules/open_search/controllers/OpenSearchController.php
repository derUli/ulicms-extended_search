<?php
class OpenSearchController extends Controller {
	public function getXML() {
		return Template::executeModuleTemplate ( "open_search", "opensearch" );
	}
}