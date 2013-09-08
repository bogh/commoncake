<?php

App::uses('AppController', 'Controller');

class CommonAppController extends AppController {

    public $components = array(
        'Session',
        'RequestHandler'
    );


}
