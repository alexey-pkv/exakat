<?php
/*
 * Copyright 2012-2017 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
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


namespace Exakat\Analyzer\Php;

use Exakat\Config;
use Exakat\Analyzer\Analyzer;

class Incompilable extends Analyzer {
    public function analyze() {
        $r = Analyzer::$datastore->getRow('compilation'.$this->config->phpversion[0].$this->config->phpversion[2]);

        $this->rowCount       = count($r);
        $this->processedCount = 1; 
        $this->queryCount     = 0; 
        $this->rawQueryCount  = 0; 

        // This is not actually done here....
        return true;
    }
    
    public function toArray() {
        $report = array();
        
        foreach($this->config->other_php_versions as $version) {
            $r = Analyzer::$datastore->getRow('compilation'.$version);
            
            foreach($r as $l) {
                $l['version'] = $version;
                $report[] = $l;
            }
        }
        
        return $report;
    }

    public function hasResults() {
        foreach($this->config->other_php_versions as $version) {
            $r = Analyzer::$datastore->getRow('compilation'.$version);
            
            if (count($r) > 0) { return true; }
        }
        return false;
    }
}

?>
