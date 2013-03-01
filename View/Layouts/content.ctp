<?php
    if (isset($refresh)) {
        echo $this->Html->scriptBlock("window.n.reload();");
    } else {
        echo implode(array(
            $this->fetch('script'),
            $this->fetch('css'),

            $this->Js->writeBuffer(array('onDomReady' => true)),

            $this->fetch('content'),
            $this->Html->scriptBlock(
                "document.title = '" . $this->fetch('title') . "';"
            )
        ));
    }

