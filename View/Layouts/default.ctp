<?php $this->extend('Common.base'); ?>

<?php echo $this->element('Common.sidebar'); ?>

<section id="main" class="column">
    <?php
        echo $this->Session->flash();
        echo $this->fetch('content');
    ?>
</section>
<?php echo $this->Common->autoInclude(); ?>
