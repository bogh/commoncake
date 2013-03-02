<?php

App::uses('AppModel', 'Model');

class CommonAppModel extends AppModel {

    public $actsAs = array('Containable');

    public $recursive = -1;

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

}
