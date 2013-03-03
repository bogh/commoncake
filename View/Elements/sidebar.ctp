<aside id="sidebar" class="column">
    <div class="current-user">
        <i class="icon-user"></i>
        <?php
            echo AuthComponent::user(ClassRegistry::init('User')->displayField);
        ?>
    </div>

    <?php echo $this->Common->userMenu(); ?>
</aside>
