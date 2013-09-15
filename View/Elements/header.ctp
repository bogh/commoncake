<div class="navbar" id="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a href="#" class="brand">
                <small>
                    <i class="icon-leaf"></i>
                    <?php echo Configure::read('Setting.company_name'); ?> Admin
                </small>
            </a>

            <ul class="nav ace-nav pull-right">
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <small>Welcome,</small>
                            <?php echo AuthComponent::user('name'); ?>
                        </span>
                        <i class="icon-caret-down"></i>
                    </a>

                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
                        <li>
                            <?php echo $this->Html->link('<i class="icon-cog"></i> Settings', array(
                                'controller' => 'settings', 'action' => 'index'
                            ), array('escape' => false)); ?>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-user"></i>
                                Change password <!-- TODO -->
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <?php echo $this->Html->link('<i class="icon-off"></i> Logout', array(
                                'controller' => 'users', 'action' => 'logout'
                            ), array('escape' => false)); ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
