<?php

/**
 * Description of CSVParser
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
class CSVParser {
    public function ParseLine($line) {
    	$minElements = 10;
    	$keywords = preg_split("/,(?!(?:[^\\\",]|[^\\\"],[^\\\"])+\\\")/", $line);
		for ($i=0;$i<count($keywords);$i++) {
			$keywords[$i] = mb_ereg_replace("^\"|\"$", "", $keywords[$i]);
		}
		for ($i=count($keywords);$i<$minElements;$i++) {
			$keywords[] = "";
		}
		return $keywords;
    }
}

?>
