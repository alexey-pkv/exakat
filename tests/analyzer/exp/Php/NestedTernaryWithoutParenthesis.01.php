<?php

$expected     = array('$d3 ? $e3 : $f3 ? $g3 : $h3', 
                      '$b1 ? $c1 ? $d1 : $e1 : $f1', 
                      '$b2 ? $c2 : $d2 ? $e2 : $f2',
                      '$b3 ? $c3 : $d3 ? $e3 : $f3 ? $g3 : $h3', 
                      '$d5 ? $e5 ? $i5 : $j5 : $f5 ? $g5 : $h5', 
                      '$d4 ? ($e4 ? $i4 : $j4) : $f4 ? $g4 : $h4', 
                      '$l5 ? $c5 : $d5 ? $e5 ? $i5 : $j5 : $f5 ? $g5 : $h5', 
                      '$b4 ? $c4 : $d4 ? ($e4 ? $i4 : $j4) : $f4 ? $g4 : $h4',
                      '$b5 ? $k5 : $l5 ? $c5 : $d5 ? $e5 ? $i5 : $j5 : $f5 ? $g5 : $h5', 
                      );

$expected_not = array('$b0 ? $c0 : $d0',
                     );

?>