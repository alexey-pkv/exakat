<?php

namespace Test\Structures;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class CommonAlternatives extends Analyzer {
    /* 3 methods */

    public function testStructures_CommonAlternatives01()  { $this->generic_test('Structures/CommonAlternatives.01'); }
    public function testStructures_CommonAlternatives02()  { $this->generic_test('Structures/CommonAlternatives.02'); }
    public function testStructures_CommonAlternatives03()  { $this->generic_test('Structures/CommonAlternatives.03'); }
}
?>