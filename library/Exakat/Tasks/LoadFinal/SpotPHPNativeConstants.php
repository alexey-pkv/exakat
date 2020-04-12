<?php declare(strict_types = 1);
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

namespace Exakat\Tasks\LoadFinal;

use Exakat\Analyzer\Analyzer;

class SpotPHPNativeConstants extends LoadFinal {
    private $PHPconstants = array();

    public function run() {
        if (empty($this->PHPconstants)) {
            return;
        }
        $constants = array_merge(...$this->PHPconstants);
        $constants = array_filter($constants, function ($x) { return strpos($x, '\\') === false;});
        $constantsPHP = array_values($constants);
        $constantsPHP = makeFullNsPath($constantsPHP, \FNP_CONSTANT);

        $query = $this->newQuery('SpotPHPNativeConstants');
        $query->atomIs('Identifier', Analyzer::WITHOUT_CONSTANTS)
              ->has('fullnspath')
              ->values('fullnspath')
              ->unique();
        $query->prepareRawQuery();
        if ($query->canSkip()) {
            $usedConstants = array();
        } else {
            $result = $this->gremlin->query($query->getQuery(), $query->getArguments());
            $usedConstants = $result->toArray();
        }

        if (empty($usedConstants)) {
            display('No Constants');
            return;
        }
        $used = array_intersect($usedConstants, $constantsPHP);
        if (empty($used)) {
            display('No PHP Constants');
            return;
        }

        $query = $this->newQuery('SpotPHPNativeConstants');
        $query->atomIs('Identifier', Analyzer::WITHOUT_CONSTANTS)
              ->has('fullnspath')
              ->hasNoIn('DEFINITION')
              ->fullnspathIs($used, Analyzer::CASE_SENSITIVE)
                ->returnCount();
        $query->prepareRawQuery();
        $result = $this->gremlin->query($query->getQuery(), $query->getArguments());
        display($result->toInt() . ' SpotPHPNativeConstants');
    }

    public function setPHPconstants(array $PHPconstants = array()) {
        $this->PHPconstants = $PHPconstants;
    }
}

?>
