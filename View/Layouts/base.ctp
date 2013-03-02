<!doctype html>
<html lang="en">
<head>
  <title>
    <?php echo $this->fetch('title'); ?>
    - <?php echo Configure::read('Site.title'); ?>
 </title>

  <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css(array(
      '/common/css/bootstrap.min',
      '/common/css/layout',
      '/common/css/style',
      '/common/css/datepicker',
    ));

    echo $this->Html->script(array(
      '/common/js/jquery.min',
      '/common/js/lodash',
      '/common/js/bootstrap.min',
      '/common/js/bootstrap-datepicker',
      '/common/js/common',
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
