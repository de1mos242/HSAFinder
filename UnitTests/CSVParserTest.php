<?php

/**
 * Description of CSVParser
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'Helpers/CSVParser.php';

class CSVParserTest extends PHPUnit_Framework_TestCase {
    private $fixture = NULL;
    
    protected function setUp() {
        $this->fixture = new CSVParser();
    }

    public function testParseString() {
        $a = "first";
        $b = '"second, value';
        $c = "third";
        $line = $a.',""'.$b.'","'.$c.'"';
        $csv = $this->fixture->ParseLine($line);
        $this->AssertEquals($a, $csv[0]);
        $this->AssertEquals($b, $csv[1]);
        $this->AssertEquals($c, $csv[2]);
    }
}

?>
