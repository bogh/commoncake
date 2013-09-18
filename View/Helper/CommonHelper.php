<?php

App::uses('AppHelper', 'View/Helper');

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

    public $layoutClass = 'default';

    public function afterRender($viewFile) {
        if ($this->request->is('backend')) {
            $this->layoutClass = $this->_View->layout;

            $this->_View->layout = "Common.{$this->layoutClass}";

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
    public function date($value = null, $format = ADMIN_DATE) {
        return $value ? $this->Time->format(ADMIN_DATE, $value) : 'N/A';
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
        $title = __($title);

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
            'escape' => false,
            'class' => 'btn btn-mini'
        );
        foreach ($actions as $title => $options) {
            $linkOptions = array();
            if (isset($options['options'])) {
                $linkOptions = $options['options'];
            }
            $linkOptions['title'] = __($title);

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
        $prefix = $this->request->prefix;
        $menu = Configure::read("Menu.{$prefix}");
        if (empty($menu)) {
            return '';
        }

        $nav = '';
        foreach ($menu as $title => $options) {
            list($item, $active) = $this->_menuLink($title, $options);
            $open = false;

            // if it's submenu
            if (isset($options['submenu'])) {
                $submenu = '';
                foreach ($options['submenu'] as $t => $o) {
                    list($sItem, $sActive) = $this->_menuLink($t, $o, true);

                    if ($sActive && !$active) {
                        $active = $open = true;
                    }
                    $submenu .= $this->Html->tag('li', $sItem, array(
                        'class' => $sActive ? 'active' : ''
                    ));
                }
                $item .= $this->Html->tag('ul', $submenu, array('class' => 'submenu'));
            }
            $class = $active ? 'active' : '';
            $class .= $open ? ' open' : '';
            $nav .= $this->Html->tag('li', $item, compact('class'));
        }

        return $this->Html->tag('ul', $nav, array('class' => 'nav nav-list'));
    }

    protected function _menuLink($title, $options = array(), $submenu = false) {
        $linkOptions = array('escape' => false);

        $options = Hash::merge(array(
            'link' => array('action' => 'index')
        ), $options);

        // icon
        $_title = '';
        if (!$submenu && isset($options['icon']) && $options['icon']) {
            $_title .= "<i class=\"icon icon-{$options['icon']}\"></i> ";
        }

        $_title .= $this->Html->tag('span', __($title), array('class' => 'menu-text'));

        if (isset($options['submenu'])) {
            $linkOptions['class'] = 'dropdown-toggle';
            $options['link'] = '#';
            $_title .= '<b class="arrow icon-angle-down"></b>';
        }

        if (isset($options['options'])) {
            $linkOptions = Hash::merge($linkOptions, $options['options']);
        }

        $active = $this->isActive($options['link']);
        return array($this->link($_title, $options['link'], $linkOptions), $active);
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
            '<i class="icon-plus icon-white"></i> ' . __($title),
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
                'interval' => false,
                'date_interval' => false,
                'datepicker' => false,
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

            $q = $this->request->query;

            if (!$options['interval']) {
                $inputs[$field]['type'] = $options['type'];
                $inputs[$field]['value'] = (isset($q[$field]) ? $q[$field] : '');

                if ($options['datepicker']) {
                    $inputs[$field]['div'] = 'input datepicker';
                }

            } else {
                $f1 = "start_{$field}";
                $f2 = "end_{$field}";

                $inputs[$f2]['type'] = $inputs[$f1]['type'] = $options['type'];

                $inputs[$f1]['value'] = (isset($q[$f1]) ? $q[$f1] : '');
                $inputs[$f2]['value'] = (isset($q[$f2]) ? $q[$f2] : '');

                if ($options['datepicker']) {
                    $inputs[$f1]['div'] = $inputs[$f2]['div'] =  'input datepicker';
                }

            }

        }

        $link = '';
        $class = 'common-filter';
        if (!isset($this->request->query['filter'])) {
            $class = 'common-filter hide';
            $link = $this->Html->tag('button', '<i class="icon-search icon-white"></i> ' . __('Filter'), array(
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

            $this->Form->submit(__('Filter'), array('class' => 'btn btn-info')),

            $this->Html->link(__('Cancel'), array(
                'controller' => $this->params['controller'],
                'action' => $this->params['action']
            ), array('class' => 'btn')),

            $this->Form->end()
        )), array('id' => "{$modelClass}-filter"));

        return $out;
    }

    public function pagination($actions = array()) {
        $actions = (array) $actions;
        $bulk = array();
        foreach ($actions as $a) {
            $bulk[] = $this->Form->submit(__($a), array(
                'name' => 'action',
                'div' => false,
                'class' => 'btn btn-mini btn-primary'
            ));
        }

        $numbers = $this->Paginator->numbers(array(
            'tag' => 'li',
            'separator' => false,
            'currentTag' => 'a'
        ));

        return $this->Html->tag('footer', implode(array(
            $this->Html->div('submit_link', implode($bulk)),
            $this->Html->tag('ul', $numbers)
        )), array(
            'class' => 'pagination'
        ));
    }

    /**
     * Outputs a checkbox for use in index pages for bulk updates
     */
    public function actionCheck($value) {
        return $this->Form->checkbox('id.', array(
            'value' => $value,
            'hiddenField' => false
        ));
    }

    /**
     * Include jquery ui in your theme
     */
    public function jUi() {
        // $this->Html->css('/common/css/smoothness/jquery-ui.min', null, array(
        //     'inline' => false
        // ));
        // $this->Html->script('/common/js/jquery-ui.min', array('inline' => false));
    }

    /**
     * Auto includes elements
     * If out is set will append to it and return it, otherwise will return the
     * output of the elements
     */
    public function autoInclude($out = null) {
        $c = '';
        if (isset($this->_View->viewVars['autoInclude'])) {
            $autoInclude = (array) $this->_View->viewVars['autoInclude'];
            foreach ($autoInclude as $element) {
                $c .= $this->_View->element($element);
            }
        }
        if ($out === null) {
            return $c;
        } elseif (is_string($out)) {
            return $out . $c;
        } elseif (is_array($out)) {
            $out[] = $c;
            return $out;
        }
    }

    /**
     * Create a form element with inputs having div.control-group as class
     * @param mixed $model see FormHelper::create
     * @param array $options see FormHelper::create
     * @return string
     */
    public function createForm($model = null, $options = array(), $inputs = null, $after = '') {
        if (is_array($model) && empty($options)) {
            $options = $model;
            $model = null;
        }

        $options = Hash::merge(array(
            'inputDefaults' => array(
                'div' => 'control-group',
                'label' => array('class' => 'control-label'),
                'between' => '<div class="controls">',
                'after' => '</div>',
                'class' => 'span6',
                'error' => array(
                    'attributes' => array('class' => 'help-inline', 'wrap' => 'span')
                ),
                'format' => array('before', 'label', 'between', 'input', 'error', 'after')
            ),
            'class' => 'form-horizontal'
        ), $options);

        $out = $this->Form->create($model, $options);
        if ($inputs) {
            $out .= $this->Form->inputs($inputs);
            $out .= $after;
            $out .= $this->endForm();
        }
        return $out;
    }

    public function endForm() {
        return $this->Html->div('space-4', '') .
            $this->Html->div('form-actions', implode(array(
                $this->Form->button('<i class="icon-ok bigger-110"></i> Submit', array(
                    'escape' => false,
                    'class' => 'btn btn-info'
                ))
            )));
    }

    /**
     * Compose a table structure combining HtmlHelper methods
     *
     * @param  array  $headers  Param for HtmlHelper::tableHeaders
     * @param  array  $cells    Param for HtmlHelper::tableCells
     * @param  array  $odd      Param for HtmlHelper::tableCells
     * @param  array  $even     Param for HtmlHelper::tableCells
     * @param  boolean $count   Param for HtmlHelper::tableCells
     * @param  array   $table   Table html attributes
     *
     * @return string
     */
    public function table($headers, $cells, $odd = null, $even = null, $count = false, $table = array()) {
        $out = $this->Html->tableHeaders($headers);
        $out .= $this->Html->tableCells($cells, $odd, $even, $count);

        $table = Hash::merge(array(
            'class' => 'table table-striped table-bordered table-hover'
        ), $table);
        return $this->Html->tag('table', $out, $table);
    }

    public function widgetBox($header, $content, $span = 6) {
        return $this->Html->div("span{$span} widget-container-span",
            $this->Html->div('widget-box', implode(array(
                $this->Html->div('widget-header', $header),
                $this->Html->div('widget-body', $this->Html->div('widget-main ', $content))
            ))));
    }

}
