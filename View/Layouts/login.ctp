<?php
    $this->extend('Common.base');
    $this->assign('body-class', 'login-layout');
?>


<div class="main-container container-fluid">
    <div class="main-content">
        <div class="row-fluid">
            <div class="span12">
                <div class="login-container">
                    <div class="row-fluid">
                        <div class="center">
                            <h1>
                                <i class="icon-leaf green"></i>
                                <span class="red">Alliance</span>
                                <span class="white">Admin</span>
                            </h1>
                            <h4 class="blue">&copy; Company Name</h4>
                        </div>
                    </div>

                    <div class="space-6"></div>

                    <div class="row-fluid">
                        <div class="position-relative">
                            <div id="login-box" class="login-box visible widget-box no-border">
                                <div class="widget-body">
                                    <div class="widget-main">
                                        <?php echo $this->fetch('content'); ?>
                                    </div><!--/widget-main-->

                                </div><!--/widget-body-->
                            </div><!--/login-box-->
                        </div><!--/position-relative-->
                    </div>
                </div>
            </div><!--/.span-->
        </div><!--/.row-fluid-->
    </div>
</div>

