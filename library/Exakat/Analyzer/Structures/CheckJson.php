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

namespace Exakat\Analyzer\Structures;

use Exakat\Analyzer\Analyzer;

class CheckJson extends Analyzer {
    public function analyze() {
        // json_decode() (no checks)
        $this->atomFunctionIs(array('\\json_encode', '\\json_decode'))
             ->hasNoTryCatch()
             ->goToExpression()
             ->hasNoNextSibling('EXPRESSION')
             ->back('first');
        $this->prepareQuery();

        // json_decode() (no checks)
        $this->atomFunctionIs(array('\\json_encode', '\\json_decode'))
             ->hasNoTryCatch()
             ->goToExpression()
             ->nextSibling('EXPRESSION')
             ->noFunctionInside(array('\\json_last_error', '\\json_last_error_msg'))
             ->back('first');
        $this->prepareQuery();
    }
}

?>
