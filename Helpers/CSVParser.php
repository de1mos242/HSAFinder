<?php

/**
 * Description of CSVParser
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
class CSVParser {
    public function ParseLineOld($line) {
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

    public function ParseLine($line) {
    	$minElements = 10;
    	$quote = '"';
    	$result = array();
    	$element = "";
    	$inQuotes = false;
        $wasDQuote = false;
    	for ($i=0;$i<mb_strlen($line);$i++) {
    		$curChar = mb_substr($line, $i, 1);
    		if ($curChar == ',' && !$inQuotes) {
    			$result[] = $element;
    			$element = '';
                $wasDQuote = false;
    		} 
    		elseif ($curChar == $quote) {
    			if ($i>0 && mb_substr($line, $i-1, 1) == $quote && !$wasDQuote) {
    				$element .= $quote;
                    $wasDQuote = true;
    			}
    			elseif ($i<mb_strlen($line)-1 && mb_substr($line, $i+1, 1) != $quote){
    				$inQuotes = !$inQuotes;
                    $wasDQuote = false;
    			}
    		}
    		else {
    			$element .= $curChar;
                $wasDQuote = false;
    		}
    	}
    	$result[] = $element;

    	for ($i=count($result);$i<$minElements;$i++) {
			$result[] = "";
		}
    	return $result;
    }
}

?>
