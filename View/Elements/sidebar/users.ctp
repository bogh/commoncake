<ul class="nav nav-list">
    <li class="nav-header">Users</li>
    <li>
        <?php echo $this->Html->link('<i class="icon-plus"></i>Create', array('action' => 'edit'), array(
            'escape' => false
        )); ?>
    </li>
    <li><?php echo $this->Html->link('Manage', array('action' => 'index')); ?></li>
</ul>
