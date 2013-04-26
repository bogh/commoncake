<?php

App::uses('AppController', 'Controller');

class CommonAppController extends AppController {

    public function beforeFilter() {
        $this->_detectors();

        parent::beforeFilter();

        $this->_prefix();
        $this->_security();
        $this->_auth();
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
        $this->request->addDetector('backend', array('callback' => $detector));

        $this->request->addDetector('json', array('param' => 'ext', 'value' => 'json'));
    }

    /**
     * Set Auth::sessionKey based on prefix and call Controller::_{$prefix} method
     *
     * If no prefix present than we will set it to `guest`
     */
    protected function _prefix() {
        if (isset($this->request->prefix)) {
            $prefix = $this->request->prefix;
        } else {
            $prefix = 'guest';
        }

        AuthComponent::$sessionKey = 'Auth.' . Inflector::camelize($prefix);

        $method = "_{$prefix}";
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function _security() {

    }

    protected function _auth() {
        // Allowed actions
        if (property_exists($this, '_allowedActions')) {
            $this->Auth->allow($this->_allowedActions);
        }
    }

    /**
     * List model
     */
    protected function _index($options = array()) {
        $this->_bulk();
        $_defaults = array(
            'conditions' => array(),
            'limit' => 50
        );
        $options = Hash::merge($_defaults, $options);

        $modelClass = $this->modelClass;
        $variable = Inflector::variable(Inflector::pluralize($modelClass));
        if (method_exists($this->$modelClass, 'filter') &&
                isset($this->request->query['filter'])) {
            $options['conditions'] = Hash::merge($options['conditions'],
                $this->$modelClass->filter($this->request->query));
        }
        $this->Paginator->settings[$modelClass] = $options;

        $this->set($variable, $this->Paginator->paginate($modelClass));
    }

    /**
     * Handle bulk operations
     */
    protected function _bulk() {
        $modelClass = $this->modelClass;
        $data = $this->request->data;

        if (!$this->request->is('post') || !isset($data[$modelClass])) {
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
            $this->_error('There has been an error applying the action. Please try again!');
        } elseif (is_string($result)) {
            $this->_success($result);
        }
    }

    /**
     * Edit model
     */
    protected function _edit($id = null, $options = array()) {
        $_defaults = array(
            'method' => 'save',
            'redirect' => array('action' => 'index')
        );

        $options = Hash::merge($_defaults, $options);
        $modelClass = $this->modelClass;
        $method = $options['method'];
        if (!empty($this->request->data)) {
            if ($data = $this->$modelClass->$method($this->request->data)) {
                $this->_success("{$modelClass} has been saved!");

                if (isset($options['callback']) && is_callable($options['callback'])) {
                    $options['callback']($data, $this->$modelClass);
                }

                if (is_array($options['redirect'])) {
                    $this->redirect($options['redirect']);
                } else {
                    $this->set('redirect', $options['redirect']);
                }
            } else {
                $this->_error();
            }
        } elseif (!empty($id)) {
            $this->request->data = $this->$modelClass->findById($id);
        }
    }

    protected function _delete($id, $options = array()) {
        $_defaults = array(
            'redirect' => array('action' => 'index')
        );

        $options = Hash::merge($_defaults, $options);
        $modelClass = $this->modelClass;
        if ($this->$modelClass->delete($id)) {
            $this->_success("{$modelClass} has been deleted!");
        } else {
            $this->_error("There has been an error trying to delete the {$modelClass}!");
        }
        $this->redirect($options['redirect']);
    }

    protected function _view($id, $options = array()) {
        $options = Hash::merge(array(
            // 'recursive' => 1
        ), $options);

        $modelClass = $this->modelClass;
        $variable = Inflector::variable($modelClass);

        $this->set($variable, $this->$modelClass->findById($id));

    }

    protected function _info($message) {
        $this->Session->setFlash($message, 'Common.flash', array(
            'class' => 'alert_warning'
        ));
    }

    protected function _error($message = 'Please review your form.') {
        $this->Session->setFlash($message, 'Common.flash', array(
            'class' => 'alert_error'
        ));
    }

    protected function _success($message) {
        $this->Session->setFlash($message, 'Common.flash', array(
            'class' => 'alert_success'
        ));
    }

    protected function _login($redirect = array('action' => 'index')) {
        if (!empty($this->request->data)) {
            if ($this->Auth->login()) {
                // redirect
                $this->redirect($redirect);
            } else {
                $this->_error('Your login information is incorrect.');
            }
        }
    }

    protected function _logout() {
        $this->redirect($this->Auth->logout());
    }

}
