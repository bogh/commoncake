<?php $this->extend('Common.base'); ?>

<?php echo $this->element('Common.header'); ?>

<?php echo $this->element('Common.sidebar'); ?>

<div class="main-content">
    <div class="page-content">
        <?php
            switch (true) {
                case $this->fetch('page-header'):
                    $headerTitle = $this->fetch('page-header');
                    break;
                case $this->fetch('title');
                    $headerTitle = $this->fetch('title');
                    break;
                default:
                    $headerTitle = false;
                    break;
            }
        ?>
        <?php if ($headerTitle): ?>
            <div class="page-header position-relative">
                <h1><?php echo $headerTitle; ?></h1>
            </div>
        <?php endif ?>
        <div class="row-fluid">
            <div class="span12">
                <?php
                    echo $this->Session->flash();
                    echo $this->fetch('content');
                ?>
            </div>
        </div>
    </div>
</div>

<?php echo $this->Common->autoInclude(); ?>
