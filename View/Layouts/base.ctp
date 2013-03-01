<!doctype html>
<html lang="en">
<head>
  <title>
    <?php echo $this->fetch('title'); ?>
 </title>

  <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css(array(
      '/admin/css/bootstrap.min',
      '/admin/css/layout',
      '/admin/css/style',
      '/admin/css/datepicker',
    ));

    echo $this->Html->script(array(
      '/admin/js/jquery.min',
      '/admin/js/lodash',
      '/admin/js/bootstrap.min',
      '/admin/js/bootstrap-datepicker',
      '/admin/js/common',
    ));

    echo $this->fetch('script');

    echo $this->Js->writeBuffer(array('onDomReady' => false));
  ?>
</head>
<body class="<?php echo $this->layout; ?>-layout">
  <?php echo $this->element('Common.header'); ?>

  <?php echo $this->Session->flash(); ?>

  <?php echo $this->fetch('content'); ?>

  <?php echo $this->element('Common.confirm'); ?>
</body>
</html>
