<?php
    if (isset($refresh)) {
        echo $this->Html->scriptBlock("window.common.reload();");
    } else {
        echo $this->Session->flash();
        echo $this->fetch('content');
    }


