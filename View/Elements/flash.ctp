<?php
    $_icons = array(
        'success' => 'ok',
        'error' => 'remove',
        'info' => '',
        'warning' => '',
    );
    if (!isset($class)) {
        $class = 'alert-info';
    }
?>
<h4 class="flash-message <?php echo $class; ?>">
</h4>

<div class="alert alert-<?php echo $class; ?>">
    <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
    <strong>
        <i class="icon-<?php echo $_icons[$class]; ?>"></i>
        <?php
            if (isset($title)) {
                echo $title;
            }
        ?>
    </strong>
    <?php echo $message; ?>
    <br>
</div>
