<?php

App::uses('AppController', 'Controller');

class CommonAppController extends AppController {

    public $helpers = array('Common.Common');

    public function beforeFilter() {
        parent::beforeFilter();

        $this->_prefix();
        $this->_security();
        $this->_auth();

        $this->_backend();
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

    protected function _backend() {
        $prefixes = array();
        if (property_exists($this, '_backendPrefixes')) {
            $prefixes = $this->_backendPrefixes;
        }
        $detector = function ($request) use ($prefixes) {
            if (empty($prefixes)) {
                return !empty($request->prefix);
            } else {
                return in_array($request->prefix, $prefixes);
            }

            return false;
        };
        $this->request->addDetector('backend', array('callback' => $detector));
    }

    /**
     * List model
     */
    protected function _index($options = array()) {
        $_defaults = array();
        $options = Hash::merge($_defaults, $options);

        $modelClass = $this->modelClass;
        $variable = Inflector::variable(Inflector::pluralize($modelClass));

        $this->Paginator->settings[$modelClass] = $options;

        $this->set($variable, $this->Paginator->paginate($modelClass));
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
            if ($this->$modelClass->$method($this->request->data)) {
                $this->_success("{$modelClass} has been saved!");
                $this->redirect($options['redirect']);
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
            $this->redirect($options['redirect']);
        } else {
            $this->_error("There has been an error trying to delete the {$modelClass}!");
        }
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
        $this->Session->setFlash($message, 'backend/flash', array(
            'class' => 'alert_warning'
        ));
    }

    protected function _error($message = 'Please review your form.') {
        $this->Session->setFlash($message, 'backend/flash', array(
            'class' => 'alert_error'
        ));
    }

    protected function _success($message) {
        $this->Session->setFlash($message, 'backend/flash', array(
            'class' => 'alert_success'
        ));
    }

    protected function _login() {
        if (!empty($this->request->data)) {
            if ($this->Auth->login()) {
                // redirect
                $this->redirect(array('action' => 'dashboard'));
            } else {
                $this->_error('Your login information is incorrect.');
            }
        }
    }

    protected function _logout() {
        $this->redirect($this->Auth->logout());
    }

}
