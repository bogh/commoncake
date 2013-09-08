<?php

App::uses('AuthComponent', 'Controller/Component');

class CommonComponent extends Component {

    public $components = array(
        'Session',
        'Paginator',
        'Auth',
    );

    protected $_controller = null;

    protected $_request = null;

    public function initialize(Controller $controller) {
        $this->_controller = $controller;
        $this->_request = $controller->request;

        $this->_settings();
        $this->_detectors();
        $this->_prefix();
    }

    public function startup(Controller $controller) {
        $this->_layout();
        $this->_auth();
    }

    /**
     * Sets current app layout based on the header HTTP_X_LAYOUT
     */
    protected function _layout() {
        $layout = env('HTTP_X_LAYOUT');

        if ($layout) {
            $this->_controller->layout = strtolower($layout);
        }
    }

    /**
     * Load Common Settings from DB
     */
    protected function _settings() {
        ClassRegistry::init('Common.CommonSetting')->appSettings();
    }

    protected function _detectors() {
        $prefixes = Configure::read('Common.backend_prefixes', array());
        $detector = function ($request) use ($prefixes) {
            if (empty($prefixes)) {
                return !empty($request->prefix);
            } else {
                return in_array($request->prefix, $prefixes);
            }

            return false;
        };
        $this->_request->addDetector('backend', array('callback' => $detector));
        $this->_request->addDetector('json', array('param' => 'ext', 'value' => 'json'));
    }

    /**
     * Set Auth::sessionKey based on prefix and call Controller::_{$prefix} method
     *
     * If no prefix present than we will set it to `guest`
     */
    protected function _prefix() {
        if (isset($this->_request->prefix)) {
            $prefix = $this->_request->prefix;
        } else {
            $prefix = 'guest';
        }

        AuthComponent::$sessionKey = 'Auth.' . Inflector::camelize($prefix);

        $method = "_{$prefix}";
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function _auth() {
        // Allowed actions
        if (property_exists($this->_controller, 'allowedActions')) {
            $this->Auth->allow($this->_controller->allowedActions);
        }
    }

    public function success($message) {
        $this->Session->setFlash($message, 'Common.flash', array(
            'class' => 'success'
        ));
    }

    public function error($message = 'Please review your form.', $title = 'Error!') {
        $this->Session->setFlash($message, 'Common.flash', compact('title') + array(
            'class' => 'error'
        ));
    }

    public function info($message) {
        $this->Session->setFlash($message, 'Common.flash', array(
            'class' => 'info'
        ));
    }

    public function warning($message, $title = 'Well done!') {
        $this->Session->setFlash($message, 'Common.flash', compact('title') + array(
            'class' => 'warning'
        ));
    }

    public function login($redirect = array('action' => 'index')) {
        if ($this->_request->isPost()) {
            if ($this->Auth->login()) {
                // redirect
                $this->_controller->redirect($redirect);
            } else {
                $this->error('Your login information is incorrect.');
            }
        }
    }

    public function logout() {
        $this->_controller->redirect($this->Auth->logout());
    }

    /**
     * List model
     */
    public function index($options = array()) {
        $this->_bulk();
        $_defaults = array(
            'conditions' => array(),
            'limit' => 50
        );
        $options = Hash::merge($_defaults, $options);

        $modelClass = $this->_modelClass();
        $variable = Inflector::variable(Inflector::pluralize($modelClass));
        if (method_exists($this->$modelClass, 'filter') &&
                isset($this->_request->query['filter'])) {
            $options['conditions'] = Hash::merge($options['conditions'],
                $this->$modelClass->filter($this->_request->query));
        }
        $this->Paginator->settings[$modelClass] = $options;

        $this->set($variable, $this->Paginator->paginate($modelClass));
    }

    /**
     * Handle bulk operations
     */
    protected function _bulk() {
        $modelClass = $this->_modelClass();
        $data = $this->_request->data;

        if (!$this->_request->is('post') || !isset($data[$modelClass])) {
            return;
        }
        if (!isset($data['action'])) {
            return;
        }

        $action = strtolower($data['action']);
        $data = $data[$modelClass];

        if (!isset($data['id']) || !method_exists($this->$modelClass, 'actions')) {
            return;
        }

        $ids = $data['id'];
        if (empty($ids)) {
            return;
        }

        $result = $this->$modelClass->actions($action, $ids);
        if ($result === false) {
            $this->error('There has been an error applying the action. Please try again!');
        } elseif (is_string($result)) {
            $this->success($result);
        }
    }

    protected function _modelClass() {
        return $this->_controller->modelClass;
    }

    /**
     * Edit model
     */
    public function edit($id = null, $options = array()) {
        $_defaults = array(
            'method' => 'save',
            'redirect' => array('action' => 'index')
        );

        $options = Hash::merge($_defaults, $options);
        $modelClass = $this->_modelClass();
        $method = $options['method'];
        if (!empty($this->_request->data)) {
            if ($data = $this->$modelClass->$method($this->_request->data)) {
                $this->success("{$modelClass} has been saved!");

                if (isset($options['callback']) && is_callable($options['callback'])) {
                    $options['callback']($data, $this->$modelClass);
                }

                if (is_array($options['redirect'])) {
                    $this->_controller->redirect($options['redirect']);
                } else {
                    $this->set('redirect', $options['redirect']);
                }
            } else {
                $this->error();
            }
        } elseif (!empty($id)) {
            $this->_request->data = $this->$modelClass->findById($id);
        }
    }

    public function delete($id, $options = array()) {
        $_defaults = array(
            'redirect' => array('action' => 'index')
        );

        $options = Hash::merge($_defaults, $options);
        $modelClass = $this->_modelClass();
        if ($this->$modelClass->delete($id)) {
            $this->_info("{$modelClass} has been deleted!");
        } else {
            $this->error("There has been an error trying to delete the {$modelClass}!");
        }
        $this->_controller->redirect($options['redirect']);
    }

    public function view($id, $options = array()) {
        $options = Hash::merge(array(
            // 'recursive' => 1
        ), $options);

        $modelClass = $this->_modelClass();
        $variable = Inflector::variable($modelClass);

        $this->set($variable, $this->$modelClass->findById($id));

    }


}
