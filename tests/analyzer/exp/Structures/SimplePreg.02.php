<?php

$expected     = array('preg_match(\'#/neo4j-4\.4\.jar#\', $x)',
                     );

$expected_not = array('preg_match(\'#/neo4j-\d\.\d\.\d\.jar#\', $x)',
                      'preg_match(\'#/neo4j-4+\.jar#\', $x)',
                     );

?>