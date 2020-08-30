<?php

namespace Test\Structures;

use Test\Analyzer;

include_once dirname(__DIR__, 2).'/Test/Analyzer.php';

class AlwaysFalse extends Analyzer {
    /* 3 methods */

    public function testStructures_AlwaysFalse01()  { $this->generic_test('Structures/AlwaysFalse.01'); }
    public function testStructures_AlwaysFalse02()  { $this->generic_test('Structures/AlwaysFalse.02'); }
    public function testStructures_AlwaysFalse03()  { $this->generic_test('Structures/AlwaysFalse.03'); }
}
?>