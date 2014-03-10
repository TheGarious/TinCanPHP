<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

class ContextActivities implements VersionableInterface
{
    use ArraySetterTrait, FromJSONTrait;

    protected $category;
    protected $parent;
    protected $grouping;
    protected $other;

    private static $directProps = array(
        'category',
        'parent',
        'grouping',
        'other',
    );

    public function __construct() {
        //
        // TODO: need to handle the single or multiple handling, need to detect
        //       based on existence of 'objectType' property I suspect, unless
        //       instanceof on Activity will do better
        //
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);
        }

        foreach (
            [
                'category',
                'parent',
                'grouping',
                'other',
            ] as $k
        ) {
            $method = 'set' . ucfirst($k);

            if (! isset($this->$k)) {
                $this->$method(array());
            }
        }
    }

    public function asVersion($version) {
        $result = array();

        foreach (self::$directProps as $k) {
            $inner = $this->$k;
            if (isset($inner) && count($inner) > 0) {
                $result[$k] = array();

                foreach ($inner as $act) {
                    array_push($result[$k], $act->asVersion($version));
                }
            }
        }
        return $result;
    }

    private function _listSetter($prop, $value) {
        if (is_array($value)) {
            if (isset($value['id'])) {
                array_push($this->$prop, new Activity($value));
            }
            else {
                foreach ($value as $k => $v) {
                    if (! $value[$k] instanceof Activity) {
                        $value[$k] = new Activity($value[$k]);
                    }
                }
                $this->$prop = $value;
            }
        }
        elseif ($value instanceof Activity) {
            array_push($this->$prop, $value);
        }
        else {
            throw new \InvalidArgumentException('type of arg1 must be Activity, array of Activity properties, or array of Activity/array of Activity properties');
        }
        return $this;
    }

    public function setCategory($value) { return $this->_listSetter('category', $value); }
    public function getCategory() { return $this->category; }
    public function setParent($value) { return $this->_listSetter('parent', $value); }
    public function getParent() { return $this->parent; }
    public function setGrouping($value) { return $this->_listSetter('grouping', $value); }
    public function getGrouping() { return $this->grouping; }
    public function setOther($value) { return $this->_listSetter('other', $value); }
    public function getOther() { return $this->other; }
}
