<?php
    if (isset($redirect)) {
        switch ($redirect) {
            case 'close':
                echo $this->Html->scriptBlock("window.common.modal.close();");
                break;
            case 'refresh':
                echo $this->Html->scriptBlock("window.common.reload();");
                break;
        }
    } else {
        echo implode(array(

            $this->fetch('css'),

            $this->Js->writeBuffer(array('onDomReady' => true)),
            $this->fetch('script'),

            $this->fetch('content'),
            $this->Html->scriptBlock(
                "document.title = '" . $this->fetch('title') . "';"
            )
        ));
    }

