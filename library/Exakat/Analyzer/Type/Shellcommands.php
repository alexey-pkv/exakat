<?php
/*
 * Copyright 2012-2019 Damien Seguy – Exakat SAS <contact(at)exakat.io>
 * This file is part of Exakat.
 *
 * Exakat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Exakat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Exakat.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://exakat.io/>.
 *
*/

namespace Exakat\Analyzer\Type;

use Exakat\Analyzer\Analyzer;

class Shellcommands extends Analyzer {
    /* PHP version restrictions
    protected $phpVersion = '7.4-';
    */

    public function dependsOn() : array {
        return array('Complete/PropagateConstants',
                    );
    }

    public function analyze() {
        // ` ls -1`
        $this->atomIs('Shell');
        $this->prepareQuery();

        // shell_exec('ls -1') 
        $this->atomFunctionIs(array('\\exec', '\\shell_exec', '\\system', '\\proc_open'))
             ->outWithRank('ARGUMENT')
             ->atomIs(array('Concatenation', 'Heredoc', 'String'), self::WITH_CONSTANTS);
        $this->prepareQuery();
    }
}

?>