<?php
class IXPackageSource extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$languages = getAllLanguages ();
		foreach ( $languages as $language ) {
			$page = ModuleHelper::getFirstPageWithModule ( "package_source", $language );
			if ($page) {
				$folders = scandir ( PACKAGE_SOURCE_BASE_PATH );
				foreach ( $folders as $dir ) {
					if (is_dir ( PACKAGE_SOURCE_BASE_PATH . "/" . $dir ) and ! in_array ( $dir, array (
							"..",
							"." 
					) )) {
						$listFile = Path::resolve ( PACKAGE_SOURCE_BASE_PATH . "/" . $dir . "/list.txt" );
						if (file_exists ( $listFile )) {
							$list = file_get_contents ( $listFile );
							$list = normalizeLN ( $list, "\n" );
							$list = explode ( "\n", $list );
							$list = array_filter ( $list );
							foreach ( $list as $package ) {
								$descFile = Path::resolve ( PACKAGE_SOURCE_BASE_PATH . "/" . $dir . "/descriptions/" . $package . ".txt" );
								$description = "";
								if (file_exists ( $descFile )) {
									$description = file_get_contents ( $descFile );
								}
								$identifier = $language . "/package_source/" . basename ( $dir ) . "/" . basename ( $package );
								$url = $page->systemname . ".html?ulicms_version=" . basename ( $dir ) . "&package=" . basename ( $package );
								$title = get_translation ( "PACKAGE_FOR_VERSION", array (
										"%paket%" => basename ( $package ),
										"%version%" => basename ( $dir ) 
								) );
								$content = implode ( " ", array (
										basename ( $package ),
										basename ( $package ),
										$title,
										$description,
										basename ( $dir ),
										" Package Source Paketquelle Paket Module Extension Quelle Erweiterung Plugin Addon Download Free Modul" 
								) );
								
								$controller->saveDataset ( $identifier, $url, $title, $content, $language );
							}
						}
					}
				}
			}
		}
	}
}