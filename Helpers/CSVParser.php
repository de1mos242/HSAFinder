<?php

/**
 * Description of CSVParser
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
class CSVParser {
    public function ParseLine($line) {
        //return mb_split(",", $line);
        return str_getcsv($line);
    }
}

?>
