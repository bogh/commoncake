<?php

class CommonComponent extends Component {

    private $__controller = null;

    public function startup(Controller $controller) {
        $this->__controller = $controller;

        $this->_layout();
    }

    /**
     * Sets current app layout based on the header HTTP_X_LAYOUT
     */
    protected function _layout() {
        $layout = env('HTTP_X_LAYOUT');

        if ($layout) {
            $this->__controller->layout = strtolower($layout);
        }
    }

}
