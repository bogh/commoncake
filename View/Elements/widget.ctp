<?php
    extract(array(
        'span' => 6
    ), EXTR_SKIP);
?>
<div class="span<?php echo $span; ?> widget-container-span">
    <div class="widget-box">
        <div class="widget-header">
            <h5>
                <?php echo $title; ?>
            </h5>
            <?php if (isset($toolbar)): ?>
                <div class="widget-toolbar"><?php echo $toolbar; ?></div>
            <?php endif ?>
        </div>
        <div class="widget-body">
            <div class="widget-main"><?php echo $content; ?></div>
        </div>
    </div>

</div>
