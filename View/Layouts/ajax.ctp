<?php
    echo $this->Session->flash();
    if (isset($redirect)) {
        switch ($redirect) {
            case 'close':
                echo $this->Html->scriptBlock("window.common.modal.close(500);");
                break;
            case 'refresh':
                echo $this->Html->scriptBlock("window.common.reload(500);");
                break;
        }
    } else {
        echo $this->fetch('content');
    }


