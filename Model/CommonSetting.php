<?php
/**
 * Setting model (Pages plugin)
 *
 * Tine setarile pentru toata aplicatia in DB
 * Campuri:
 *  - name varchar(100) Name poate fi de forma Path1.Path2.Path3
 *  - value varchar(100)
 */
class CommonSetting extends CommonAppModel {

    /**
     * Cheia primara pentru model, "name" in acest caz, nu avem ID aici
     *
     * @var string
     */
    public $primaryKey = 'name';

    /**
     * validate
     *
     * @var array
     * @access public
     */
    public $validate = array();

    /**
     * Returneaza toate setarile din db sub forma de lista name -> value
     *
     * @return array
     */
    private function __appSettings() {
        return $this->__dbToConfig($this->find('list', array(
            'fields' => array('name', 'value')
        )));
    }

    /**
     * Aceasta metoda este opusul lui Set::flatten. Dintr-un array de forma 'key1.key2.key3' => value,
     * returneaza array('key1' => array('key2' => array('key3' => value)))
     *
     * @param array $data
     * @return array
     */
    private function __dbToConfig($data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result = Set::insert($result, $k, $v);
        }
        return $result;
    }

    /**
     * Salveaza multiple setari in db, sterge cache-ul
     *
     * @param array $data
     */
    public function saveSettings($data) {
        if (isset($data['_Token'])) {
            unset($data['_Token']);
        }
        $data = Set::flatten($data);

        $saveData = array();
        foreach ($data as $name => $value) {
            $saveData[] = compact('name', 'value');
        }
        if ($this->saveAll($saveData, array('validate' => false))) {
            Cache::delete('settings');
            return true;
        }

        return false;
    }

    /**
     * saveSetting
     *
     * @param mixed $name
     * @param mixed $value
     * @access public
     * @return void
     */
    public function saveSetting($name, $value = null) {
        $data = compact('name', 'value');
        if ($this->save($data)) {
            Cache::delete('settings');
            return true;
        }
        return false;
    }

    public function appSettings() {
        if (($appSettings = Cache::read('settings')) === false) {
          $appSettings = $this->__appSettings();
          Cache::write('settings', $appSettings);
        }

        Configure::write('Setting', $appSettings);
    }

}
