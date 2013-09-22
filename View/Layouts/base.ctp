<!doctype html>
<html lang="en">
<head>
    <title>
        <?php echo $this->fetch('title'); ?>
        - <?php echo Configure::read('Setting.site_title'); ?>
 </title>

    <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css(array(
            '/common/css/bootstrap.min',
            '/common/css/bootstrap-responsive.min',
            '/common/css/font-awesome.min',
            '/common/css/ace-fonts',
            '/common/css/ace.min',
            '/common/css/ace-responsive.min',
            '/common/css/ace-skins.min',
            '/common/css/style'
        ));

        echo $this->fetch('css');

        echo $this->Html->script(array(
            '/common/js/jquery-2.0.3.min',
            '/common/js/bootstrap.min',
            '/common/js/ace-elements.min',
            '/common/js/uncompressed/ace-extra',
            '/common/js/uncompressed/ace'
        ));

        echo $this->Js->writeBuffer();

        echo $this->fetch('script');

        if (isset($assets)) {
            echo $assets;
        }
    ?>
</head>
<body class="<?php echo $this->Common->layoutClass; ?>-layout">

    <div class="main-container container-fluid">
        <?php
            echo $this->Session->flash();
            echo $this->fetch('content');
        ?>
    </div>

    <?php echo $this->element('Common.confirm'); ?>
</body>
</html>
