<?php

App::uses('AppModel', 'Model');

class CommonAppModel extends AppModel {

    public $actsAs = array('Containable');

    public $recursive = -1;

    public $findMethods = array('last' => true);

    protected function _findLast($state, $query, $results = array()) {
        if($state == 'before') {
            $query['limit'] = 1;
            if ($this->hasField('created')) {
                $query['order'] = "{$this->alias}.created DESC";
            } else {
                $query['order'] = "{$this->alias}.{$this->primaryKey} DESC";
            }
            return $query;
        } elseif ($state === 'after') {
            if (empty($results[0])) {
                return array();
            }
            return $results[0];
        }
        return $results;
    }

    protected function _hasManyDelete() {
        // check for deleted hasMany
        foreach ($this->hasMany as $alias => $settings) {
            $pk = $this->$alias->primaryKey;
            $ids = Hash::extract($this->data, "/{$alias}/{$pk}");

            $this->$alias->deleteAll(array(
                "{$alias}.{$settings['foreignKey']}" => $this->id,
                "{$alias}.{$pk} NOT" => $ids
            ));
        }
    }

    /**
     * Validate order of dates
     */
    public function validateDateOrder($value, $other, $reverse = false) {
        $data = $this->data[$this->alias];
        $keys = array_keys($value);
        $value = $value[$keys[0]];

        if ($reverse === true) {
            return strtotime($data[$other]) <= strtotime($value);
        }

        return strtotime($value) <= strtotime($data[$other]);
    }

    public function filter($query) {
        $conditions = array();
        $modelFilters = static::$filters;
        // check for fileds with no options
        foreach ($modelFilters as $k => $v) {
            if (is_numeric($k)) {
                unset($modelFilters[$k]);
                $modelFilters[$v] = array();
            }
        }

        foreach ($modelFilters as $f => $o) {
            if (isset($query[$f]) && !empty($query[$f])) {
                if ($this->hasField($f)) {
                    // check for _id stuff
                    $fieldName = $f;
                } else {
                    $fieldName = "{$f}_id";
                    if (!$this->hasField($fieldName)) {
                        $fieldName = null;
                    }
                }
                if ($fieldName) {
                    $options = Hash::merge(array(
                        'type' => 'text',
                        'condition' => ''
                    ), $o);

                    // build query fiters
                    switch ($options['condition']) {
                        case 'like':
                            $conditions[] = array(
                                "{$this->alias}.{$fieldName} LIKE" => "%{$query[$f]}%"
                            );
                            break;
                        default:
                            $conditions[] = array(
                                "{$this->alias}.{$fieldName} {$options['condition']}" => $query[$f]
                            );
                            break;
                    }
                }
            }
        }

        return $conditions;
    }

}
