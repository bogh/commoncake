<?php $this->extend('Common.base'); ?>

<div class="main-content">
    <div class="row-fluid">
        <div class="span12">
            <div class="login-container">
                <div class="row-fluid">
                    <div class="center">
                        <h1>
                            <i class="icon-leaf green"></i>
                            <span class="red"><?php echo Configure::read('Setting.company_name'); ?></span>
                            <span class="white">Admin</span>
                        </h1>
                        <h4 class="blue">&copy; <?php echo Configure::read('Setting.company_name'); ?></h4>
                    </div>
                </div>

                <div class="space-6"></div>

                <div class="row-fluid">
                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <?php echo $this->Session->flash(); ?>
                                    <?php echo $this->fetch('content'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
