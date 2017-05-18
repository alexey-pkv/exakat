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

namespace Exakat\Reports;

use Exakat\Analyzer\Analyzer;

class Codeflower extends Reports {
    const FILE_EXTENSION = '';
    const FILE_FILENAME  = 'codeflower';
    
    private $select = array();

    public function generate($folder, $name= 'codeflower') {
        $this->finalName = $folder.'/'.$name;
        $this->tmpName = $folder.'/.'.$name;

        $this->initFolder();
        
        $this->getNamespaces();
        $this->getFileDependencies();
        $this->getClassHierarchy();
        
        $this->cleanFolder();
    }

    private function getFileDependencies() {
        $res = $this->sqlite->query(<<<SQL
SELECT DISTINCT including, included 
    FROM filesDependencies
WHERE including != included AND
      type in ('EXTENDS')

SQL
        );

        while($row = $res->fetchArray(\SQLITE3_ASSOC)) {
            if (isset($files[$row['included']])) {
                $files[$row['included']][] = $row['including'];
            } else {
                $files[$row['included']] = array($row['including']);
            }
        }

        $root = new \Stdclass();
        $root->name = '\\';
        $root->size = 10;
        $root->children = array();

        foreach($files as $including => $included) {
            $c = new \Stdclass();
            $c->name = $including;
            $c->type = '';
            $c->size = 100;
            $c->children = array();
            
            foreach($included as $i) {
                $d = new \Stdclass();
                $d->name = $i;
                $d->type = '';
                $d->size = 100;
                $d->children = array();
                
                $c->children[] = $d;
            }
            
            $root->children[] = $c;
        }

        file_put_contents($this->tmpName.'/data/inclusions.json', json_encode($root));
        $this->select['By inclusions'] = 'inclusions.json';
    }

    private function getClassHierarchy() {
        $res = $this->sqlite->query(<<<SQL
SELECT name, cit.id AS id, extends, type, namespace
    FROM cit
    JOIN namespaces
        ON namespaces.id = cit.namespaceId
SQL
        );

        $classes = array();
        $classesId = array();
        while($row = $res->fetchArray(\SQLITE3_ASSOC)) {
            $class = $row['namespace'].'\\'.$row['name'];
            $classes[$row['extends']][$class] = array();

            $classesId[$row['id']] = $class;
        }
        
        foreach($classes as $id => $extends) {
            if (!is_int($id)) { continue; }
            
            foreach($classes as $id2 => $extends2) {
                if (isset($extends2[$classesId[$id]])) {
                    $classes[$id2][$classesId[$id]] = $classes[$id];
                    unset($classes[$id]);
                }
            }
        }
        
        print_r($classes);
        
        $root = new \Stdclass();
        $root->name = '\\';
        $root->size = 10;
        $root->children = array();
        $ns = array('' => $root);
        foreach($classes as $name => $extends) {
            $c = new \Stdclass();
            $c->name = $name;
            $c->size = 100;
            
            foreach($extends as $e => $extends2) {
                $d = new \Stdclass();
                $d->name = $e;
                $d->size = 100;

                $c->children[] = $d;
                if (!empty($extends2)) {
                    foreach($extends2 as $e3 => $extends3) {
                        $d3 = new \Stdclass();
                        $d3->name = $e3;
                        $d3->size = 100;
                        
                        $d->children[] = $d3;
                    }
                }
            }

            $root->children[] = $c;
        }

        file_put_contents($this->tmpName.'/data/classes.json', json_encode($root));
        $this->select['By class hierarchy'] = 'classes.json';
    }
    
    private function getNamespaces() {
        $res = $this->sqlite->query(<<<SQL
SELECT name, cit.id, extends, type, namespace
    FROM cit
    JOIN namespaces
        ON namespaces.id = cit.namespaceId
SQL
        );

        $root = new \Stdclass();
        $root->name = '\\';
        $root->size = 10;
        $root->children = array();
        $ns = array('' => $root);
        while($row = $res->fetchArray(\SQLITE3_ASSOC)) {
            $c = new \Stdclass();
            $c->name = $row['namespace'].'\\'.$row['name'];
            $c->type = $row['type'];
            $c->size = 100;
            
            if (!isset($ns[$row['namespace']])) {
                $d = explode('\\', $row['namespace']);
                array_shift($d);
                
                $name = '';
                $m = null;
                foreach($d as $e) {
                    $namep = $name;
                    $name .= '\\'.$e;
                    if (isset($ns[$name])) { continue; }

                    $n = new \Stdclass();
                    $n->name = $name;
                    $n->type = 'namespace';
                    $n->children = array();
                    
                    $ns[$name] = $n;
                    $ns[$namep]->children[] = $n;
                    $m = $n;
                }
            }
            $ns[$row['namespace']]->children[] = $c;
        }

        file_put_contents($this->tmpName.'/data/namespaces.json', json_encode($root));
        $this->select['By namespace'] = 'namespaces.json';
    }

    private function initFolder() {
        if ($this->finalName === 'stdout') {
            return "Can't produce Codeflower format to stdout";
        }

        // Clean temporary destination
        if (file_exists($this->tmpName)) {
            rmdirRecursive($this->tmpName);
        }

        // Copy template
        copyDir($this->config->dir_root.'/media/codeflower', $this->tmpName );
    }

    private function cleanFolder() {
        $html = file_get_contents($this->tmpName.'/index.html');
        $select = '';
        foreach($this->select as $name => $file) {
            $select .= "            <option value=\"data/$file\">$name</option>";
        }
        $html = str_replace('<SELECT>', $select, $html);
        file_put_contents($this->tmpName.'/index.html', $html);

        if (file_exists($this->finalName)) {
            rename($this->finalName, $this->tmpName.'2');
        }

        rename($this->tmpName, $this->finalName);

        if (file_exists($this->tmpName.'2')) {
            rmdirRecursive($this->tmpName.'2');
        }
    }

}

?>