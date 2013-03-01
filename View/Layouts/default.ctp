<?php $this->extend('Common.base'); ?>

<section id="secondary_bar">
    <div class="user">
        <i class="icon-user"></i>
        <?php
            echo AuthComponent::user('full_name');

            echo $this->Html->link('<i class="icon-off"></i> Logout', [
                'controller' => 'users', 'action' => 'logout'
            ], ['class' => 'logout', 'escape' => false, 'rel' => 'direct']);
        ?>
    </div>
</section>

<?php echo $this->element('Common.sidebar'); ?>

<section id="main" class="column">
    <?php
        echo $this->Session->flash();
        echo $this->fetch('content');
    ?>
</section>
