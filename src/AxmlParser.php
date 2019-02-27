<?php
namespace Palmero;

class AxmlParser {

	public static function parse($axml) {
		$axml = str_replace(PHP_EOL, '', $axml);
		$object = [];

		$lastKey = "";
		$lastSubKey = "";
		$lastValue = "";
		$lastWord = "";

		$inValue = false;
		$inQuotes = false;
		$inObject = false;
		$checkForSpace = false;

		for ($i = 0; $i < strlen($axml); $i++) {
			BEGIN:

			if ($checkForSpace) {
				$checkForSpace = false;
				if ($axml[$i] != " ") {
					$lastSubKey = "";
					$inObject = false;
					$inValue = false;
				} else {
					$i++;
					goto BEGIN;					
				}
			}

			if (!$inValue) {
				if (in_array($axml[$i], [ ':', '=' ])) {
					$lastKey = $lastWord;
					$lastWord = "";
					$inValue = true;
				} else {
					$lastWord .= $axml[$i];	
				}
			} else {
				if (!$inQuotes && !$inObject) {
					if ($axml[$i] == "'") {
						$inQuotes = true;
					}
					if ($axml[$i] == " ") {
						$inObject = true;
					}
				} else if ($inObject) {
					if ($inQuotes) {
						if ($axml[$i] == "'") {
							if ($lastSubKey == "") {
								$object[$lastKey][] = $lastValue;
							} else {
								$lastSubKey = str_replace('=', '', $lastSubKey);
								$object[$lastKey][$lastSubKey] = $lastValue;
							}
							$lastSubKey = "";
							$lastValue = "";
							$checkForSpace = true;
							$inQuotes = false;
						} else {
							$lastValue .= $axml[$i];
						}
					} else if ($axml[$i] == "'") {
						$inQuotes = true;
					} else {
						$lastSubKey .= $axml[$i];
					}
				} else if ($inQuotes) {
					if ($axml[$i] == "'") {
						$inQuotes = false;
						$inValue = false;
						$object[$lastKey] = $lastWord;
						$lastWord = '';
					} else {
						$lastWord .= $axml[$i];
					}
				} 
			}
		}

		return $object;
	}

}
