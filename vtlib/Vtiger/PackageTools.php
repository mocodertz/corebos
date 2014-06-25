<?php
include_once('vtlib/Vtiger/Zip.php');

// Low level package tools
class PackageTools {

	static function buildModulePackage($moduleName, $buildPath) {
		$fileName = $moduleName . '.zip';
		if (is_file($buildPath . '/' . $fileName)) {
			unlink($buildPath . '/' . $fileName);
		}
		// first we check for the files
		if (!is_dir("modules/$moduleName")) {  // check for module directory
			throw new Exception("Module directory missing for {$moduleName}");
		}
		if (!file_exists("modules/$moduleName/manifest.xml")) {  // check for manifest
			throw new Exception("Module manifest missing for module {$moduleName}");
		}
		if (!is_dir("modules/$moduleName/language")) {  // check for language directory
			throw new Exception("Module language directory missing for module {$moduleName}");
		}
		// Export as Zip
		$zip = new Vtiger_Zip($buildPath . '/' . $fileName);
		// Add manifest file
		$zip->addFile("modules/$moduleName/manifest.xml",'manifest.xml');
		// Copy module directory
		$zip->copyDirectoryFromDisk("modules/$moduleName");
		// Copy templates directory of the module (if any)
		if(is_dir("Smarty/templates/modules/$moduleName")) {
			$zip->copyDirectoryFromDisk("Smarty/templates/modules/$moduleName","templates");
		}
		// Copy cron files of the module (if any)
		if(is_dir("cron/modules/$moduleName")) {
			$zip->copyDirectoryFromDisk("cron/modules/$moduleName","cron");
		}
		$zip->save();
	}

	static function buildBundlePackage($bundleName, $moduleList, $manifestData, $buildPath) {
		$fileName = $bundleName . '.zip';
		if (is_file($buildPath . '/' . $fileName)) {
			unlink($buildPath . '/' . $fileName);
		}
		$tmpPath = "build/{$bundleName}";
		@mkdir($tmpPath);
		foreach ($moduleList as $moduleName) {
			self::buildModulePackage($moduleName, $tmpPath);
		}
		$manifestDoc = new SimpleXMLElement("<?xml version='1.0'?><module/>");
		$manifestDoc->addChild('name', $bundleName);
		$manifestDoc->addChild('version', $manifestData['version']);
		$manifestDoc->addChild('modulebundle', 'true');
		$xmlDependencies = $manifestDoc->addChild('dependencies');
		$xmlDependencies->addChild('vtiger_version', $manifestData['vtiger_version']);
		$xmlDependencies->addChild('vtiger_max_version', $manifestData['vtiger_max_version']);
		$xmlModuleList = $manifestDoc->addChild('modulelist');
		$index = 1;
		foreach ($moduleList as $moduleName) {
			$xmlModule = $xmlModuleList->addChild('dependent_module');
			$xmlModule->addChild('name', $moduleName);
			$xmlModule->addChild('install_sequence', $index);
			$xmlModule->addChild('filepath', $moduleName . '.zip');
			$index++;
		}
		$manifestDoc->asXML($tmpPath . '/' . 'manifest.xml');
		$zip = new Vtiger_Zip($buildPath . '/' . $fileName);
		$zip->addFile($tmpPath . '/' . 'manifest.xml', 'manifest.xml');
		foreach ($moduleList as $module) {
			$zip->addFile($tmpPath . '/' . $module . '.zip', $module . '.zip');
		}
		$zip->save();
	}

	static function buildLangPackage($languageCode, $languageName, $buildPath) {
		$fileName = $languageName . '.zip';
		if (is_file($buildPath . '/' . $fileName)) {
			unlink($buildPath . '/' . $fileName);
		}
		// first we check for the files
		if (!is_file("include/language/{$languageCode}.manifest.xml")) {  // check for manifest
			throw new Exception("Manifest missing for language package {$languageName}");
		}
		// Export as Zip
		$zip = new Vtiger_Zip($buildPath . '/' . $fileName);
		// Add manifest file
		$zip->addFile("include/language/{$languageCode}.manifest.xml",'manifest.xml');
		// Add calendar files
		$zip->copyFileFromDisk('jscalendar/','jscalendar/','calendar-setup.js');
		$zip->copyFileFromDisk('jscalendar/lang/','jscalendar/lang/','calendar-'.substr($languageCode, 0, 2).'.js');
		//$zip->copyFileFromDisk('modules/Emails/language/','modules/Emails/language/','phpmailer.lang-'.$languageCode.'.php');
		// Copy module/include language files
		foreach (glob("{modules,include}/*/language/{$languageCode}.lang.{php,js}",GLOB_BRACE) as $langfile) {
			$fname = basename($langfile);
			$dname = dirname($langfile);
			$zip->copyFileFromDisk($dname,$dname,$fname);
		}
		$zip->copyFileFromDisk('include/language/','include/language/',$languageCode.'.lang.php');
		$zip->copyFileFromDisk('include/js/','include/js/',$languageCode.'.lang.js');
		$zip->save();
	}

}
