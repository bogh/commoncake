<?php

App::uses('AppHelper', 'View/Helper');
App::uses('User', 'Model');

class CommonHelper extends AppHelper {

    /**
     * helpers
     *
     * @var string
     * @access public
     */
    public $helpers = array(
        'Html',
        'Time',
        'Js',
        'Paginator',
        'Form',
        'Upload.Upload'
    );

    public function afterRender($viewFile) {
        if ($this->request->is('backend')) {
            $layout = $this->_View->layout;
            $this->_View->layout = "Common.{$layout}";

            if (!$this->request->is('ajax')) {
                // Load extra assets
                $assets = '';
                if (isset($this->settings['css'])) {
                    $assets .= $this->Html->css((array) $this->settings['css']);
                }
                if (isset($this->settings['script'])) {
                    $assets .= $this->Html->script((array) $this->settings['script']);
                }

                if (isset($this->settings['ui']) && $this->settings['ui']) {
                    $this->jUi();
                }
                $this->_View->set(compact('assets'));
            }
        }
    }

    /**
     * _paginator
     *
     * @access protected
     */
    protected function _paginator() {
        if (!empty($this->request->query) && empty($this->_paginatorQuery)) {
            $keys = array('page', 'limit', 'sort', 'direction');
            $query = $this->request->query;
            foreach ($keys as $key) {
                if (isset($query[$key])) {
                    unset($query[$key]);
                }
            }
            $this->_paginatorQuery = $query;
        }

        $this->Paginator->options(array(
            'url' => $this->request->params['pass'] + array('?' => $this->_paginatorQuery)
        ));
    }

    public function bool($value) {
        if ($value) {
            $out = '<i class="icon-ok" rel="tooltip" title="On"></i>';
        } else {
            $out = '<i class="icon-remove" rel="tooltip" title="Off"></i>';
        }
        return $out;
    }

    /**
     * date
     *
     * @param mixed $value
     * @access public
     */
    public function date($value = null, $split = false) {
        if ($split) {
            return $value ? nl2br($this->Time->format(ADMIN_DATE_SPLIT, $value)) : 'N/A';
        } else {
            return $value ? $this->Time->format(ADMIN_DATE, $value) : 'N/A';
        }

    }

    /**
     * edit
     *
     * @param mixed $id
     * @access public
     */
    public function edit($id) {
        $url = array(
            'action' => 'edit',
            $id
        );
        $image = '<i class="icon-edit"></i> Edit';
        return $this->link($image, $url, array('escape' => false));
    }

    /**
     * view
     *
     * @param integer $id
     * @access public
     */
    public function view($id) {
        $url = array(
            'action' => 'view',
            $id
        );
        $image .= ' Details';
        return $this->link($image, $url, array('escape' => false));
    }

    /**
     * delete
     *
     * @param mixed $id
     * @access public
     */
    public function delete($id) {
        $url = array(
            'action' => 'delete',
            $id
        );
        $confirm = 'Are you sure you want to delete the selected record ?';
        $image = '<i class="icon-remove"></i> Delete';
        return $this->link($image, $url, array(
            'escape' => false
        ), $confirm);
    }

    public function key($title, $key) {
        $this->_paginator();
        $sortKey = $this->Paginator->sortKey();

        if ($key == $sortKey) {
            $sortDir = $this->Paginator->sortDir();
            if ($sortDir == 'asc') {
                $title .= '<i class="icon-chevron-up"></i>';
            } elseif ($sortDir == 'desc') {
                $title .= '<i class="icon-chevron-down"></i>';
            }

            $title .= $this->Paginator->link(
                '<i class="icon-ban-circle"></i>',
                array('order' => false),
                array(
                    'escape' => false,
                    'rel' => 'content'
                )
            );
        }

        return $this->Paginator->sort($key, $title, array('escape' => false));
    }

    public function isEdit($model, $primaryKey = 'id') {
        return isset($this->request->data[$model][$primaryKey]) &&
            !empty($this->request->data[$model][$primaryKey]);
    }

    public function isEmpty($string, $empty = 'N/A') {
        return empty($string) ? $empty : $string;
    }

    public function actions($actions) {
        $out = array();
        $defaults = array(
            'escape' => false
        );
        foreach ($actions as $title => $options) {
            $linkOptions = array();
            if (isset($options['options'])) {
                $linkOptions = $options['options'];
            }
            $linkOptions['title'] = $title;

            if (isset($options['confirm'])) {
                $linkOptions['data-confirm'] = $options['confirm'];
                $linkOptions['rel'] = false;
            }

            if (isset($options['modal']) && $options['modal']) {
                $linkOptions['rel'] = 'modal';
            }

            $link = '';
            if (isset($options['icon'])) {
                $link = "<i class=\"icon-{$options['icon']}\"></i>";
            }

            if (isset($options['button']) && $options['button']) {
                $link .= " {$title}";
                $link = $this->Html->tag('button', $link, array(
                    'class' => $options['button']
                ));
            }

            $out[] = $this->link(
                $link,
                $options['link'],
                Hash::merge($defaults, $linkOptions)
            );
        }
        return $this->Html->div('actions', implode($out));
    }

    public function navbar($links) {
        return $this->Html->nestedList($links, array('class' => 'nav nav-list'));
    }

    public function userMenu() {
        $menu = User::$menu;
        $prefix = $this->request->prefix;
        if (isset($menu[$prefix])) {
            $menu = $menu[$prefix];
        }
        $out = array();

        if (isset($menu['elements'])) {
            $elements = $menu['elements'];
            unset($menu['elements']);
        }

        foreach ($menu as $title => $links) {
            if (!$links) {
                continue;
            }

            $out[] = $this->Html->tag('h3', $title);

            $list = array();
            foreach ($links as $title => $options) {
                $linkOptions = array('escape' => false);

                // icon
                if (isset($options['icon']) && $options['icon']) {
                    $class = "icon icon-{$options['icon']}";
                    $title = "<i class=\"{$class}\"></i> " . $title;
                }
                if (isset($options['options'])) {
                    $linkOptions = Hash::merge($linkOptions, $options['options']);
                }

                if ($this->isActive($options['link'])) {
                    $linkOptions['class'] = 'active';
                }

                $list[] = $this->link($title, $options['link'], $linkOptions);
            }
            $out[] = $this->Html->nestedList($list);
        }

        $before = '';
        if (isset($elements)) {
            foreach ($elements as $e) {
                $before .= $this->_View->element($e);
            }
        }

        return $before . implode($out);
    }

    public function isActive($link) {
        return $this->url($link) === $this->request->here;
    }

    public function addLink($title = 'Add', $attrs = array(), $link = true) {
        $btnAttrs = array(
            'class' => 'btn btn-mini btn-add btn-success',
            'escape' => false
        );

        if ($link === true) {
            $link = array('action' => 'edit');
        } else {
            $btnAttrs += $attrs;
        }

        $button = $this->Form->button(
            '<i class="icon-plus icon-white"></i> ' . $title,
            $btnAttrs
        );

        if ($link === false) {
            return $button;
        }

        return $this->link(
            $button,
            $link,
            array('escape' => false) + $attrs
        );
    }

    public function widget($title, $url = null, $size = 'quarter') {
        $header = $this->Html->tag('header', '<h3>'.$title.'</h3>');
        $content = '';
        if ($url) {
            $content = $this->autoContent($url, array(
                'data-auto-refresh' => 20000
            ));
        }
        $article = $this->Html->tag('article', $header . $content, array(
            'class' => 'module width_'.$size
        ));
        return $article;
    }

    public function autoContent($url, $options = array()) {
        $defaults = array(
            'rel' => 'auto-content',
            'data-url' => $url,
            'data-loader' => true
        );

        $attrs = Hash::merge($defaults, $options);
        $initialContent = '';
        if ($attrs['data-loader']) {
            $initialContent .= $this->Html->div('loader medium');
        }
        return $this->Html->tag('div', $initialContent, $attrs);
    }

    public function help($text, $trigger = 'click') {
        $tooltip = $this->Html->div('helper-tooltip', implode(array(
            $text,
            $this->Html->div('left-arrow', '')
        )));

        return $this->Html->tag('span', $tooltip, array(
            'rel' => 'helper',
            'class' => 'help-icon',
            'data-trigger' => $trigger
        ));
    }

    // use this as a wrapper for displaying data in views (u motherfucker)
    public function data($data, $alt = null) {
        if (strlen($data)) {
            return $data;
        } elseif ($alt) {
            return $this->Html->tag('span', $alt, array('class' => 'helptext'));
        }
        return '';
    }

    /**
     * Create a link that is executed through ajax and loaded in content
     */
    public function link($title, $url = null, $options = array(), $confirmMessage = false) {
        if (!isset($options['rel'])) {
            $options['rel'] = 'content';
        }
        return $this->Html->link($title, $url, $options, $confirmMessage);
    }

    public function filter($modelClass, $fields = array()) {
        App::uses($modelClass, 'Model');
        if (!property_exists($modelClass, 'filters')) {
            return false;
        }

        $inputs = array(
            'filter' => array(
                'type' => 'hidden',
                'value' => 1
            ),
            'fieldset' => false
        );

        if (!$fields) {
            $modelFilters = $modelClass::$filters;
            foreach ($modelFilters as $k => $v) {
                if (is_numeric($k)) {
                    $fields[] = $v;
                    unset($modelFilters[$k]);
                    $modelFilters[$v] = array();
                } else {
                    $fields[] = $k;
                }
            }
        }

        foreach ($fields as $field) {
            $options = Hash::merge(array(
                'type' => 'text',
            ), $modelFilters[$field]);

            switch ($options['type']) {
                case 'select':
                    $var = Inflector::pluralize(Inflector::variable($field));
                    if (!isset($this->_View->viewVars[$var])) {
                        $Model = ClassRegistry::init(Inflector::classify($var));
                        if ($Model) {
                            $inputs[$field]['options'] = $Model->find('list');
                        }
                    }
                    $inputs[$field]['empty'] = '';
                    break;
            }

            $inputs[$field]['type'] = $options['type'];

            $q = $this->request->query;
            $inputs[$field]['value'] = (isset($q[$field]) ? $q[$field] : '');
        }

        $link = '';
        $class = 'common-filter';
        if (!isset($this->request->query['filter'])) {
            $class = 'common-filter hide';
            $link = $this->Html->tag('button', '<i class="icon-search icon-white"></i> Filter', array(
                'rel' => 'filter',
                'data-filter' => "#{$modelClass}-filter",
                'class' => 'toggle-filter btn btn-small btn-info',
                'escape' => false
            ));
        }

        $out = $link . $this->Html->div($class, implode(array(
            $this->Form->create(array(
                'type' => 'get',
                'novalidate' => true,
                'inputDefaults' => array('required' => false )
            )),

            $this->Form->inputs($inputs),

            $this->Form->submit('Filter', array('class' => 'btn btn-info')),

            $this->Html->link('Cancel', array(
                'controller' => $this->params['controller'],
                'action' => $this->params['action']
            ), array('class' => 'btn')),

            $this->Form->end()
        )), array('id' => "{$modelClass}-filter"));

        return $out;
    }

    public function pagination() {
        $numbers = $this->Paginator->numbers(array(
            'tag' => 'li',
            'separator' => false,
            'currentTag' => 'a'
        ));

        return $this->Html->tag('footer', $this->Html->tag('ul', $numbers), array(
            'class' => 'pagination'
        ));
    }

    /**
     * Include jquery ui in your theme
     */
    public function jUi() {
        $this->Html->css('/common/css/smoothness/jquery-ui.min', null, array(
            'inline' => false
        ));
        $this->Html->script('/common/js/jquery-ui.min', array('inline' => false));
    }

}
