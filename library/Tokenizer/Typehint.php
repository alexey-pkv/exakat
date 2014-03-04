<?php

namespace Tokenizer;

class Typehint extends TokenAuto {
    static public $operators = array('T_COMMA', 'T_OPEN_PARENTHESIS');
    
    public function _check() {
        $this->conditions = array(-1 => array('filterOut' => 'T_CATCH'),
                                   0 => array('token' => Typehint::$operators),
                                   1 => array('atom' => 'yes', 'token' => array('T_STRING', 'T_NS_SEPARATOR')),
                                   2 => array('atom' => array('Variable', 'Assignation', 'Identifier' )),
                                   3 => array('filterOut' => Assignation::$operators),
        );
        
        $this->actions = array('to_typehint'  => true,
                               'keepIndexed'  => true);
        $this->checkAuto();

        $this->conditions = array(-2 => array('filterOut' => 'T_CATCH'),
                                   0 => array('token' => Typehint::$operators),
                                   1 => array('token' => 'T_ARRAY', 'atom' => 'none'),
                                   2 => array('atom' => array('Variable', 'Assignation', 'Identifier'    )),
                                   3 => array('filterOut' => Assignation::$operators),
        );
        
        $this->actions = array('to_typehint'  => true,
                               'keepIndexed'  => true);
        $this->checkAuto();

        return $this->checkRemaining();
    }

    public function fullcode() {
        return 'fullcode.fullcode = fullcode.out("CLASS").next().code + " " + fullcode.out("VARIABLE").next().code; ';
    }
}

?>