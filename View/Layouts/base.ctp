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
      '/common/css/style'
    ));

    echo $this->fetch('css');

    echo $this->Html->script('/common/js/jquery.min');

    echo $this->Html->script(array(
      '/common/js/lodash',
      '/common/js/bootstrap.min',
      '/common/js/common'
    ));

    echo $this->Js->writeBuffer();

    echo $this->fetch('script');

    if (isset($assets)) {
      echo $assets;
    }

  ?>
</head>
<body class="<?php echo $this->layout; ?>-layout">
  <?php echo $this->element('Common.header'); ?>

  <?php echo $this->Session->flash(); ?>

  <?php echo $this->fetch('content'); ?>

  <?php echo $this->element('Common.confirm'); ?>
</body>
</html>
