<?php $this->extend('Common.base'); ?>

<?php echo $this->element('Common.header'); ?>

<?php echo $this->element('Common.sidebar'); ?>

<div class="main-content">
    <?php
        echo $this->Session->flash();
        echo $this->fetch('content');
    ?>
</div>
<?php echo $this->Common->autoInclude(); ?>
