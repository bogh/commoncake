<header id="header">
  <hgroup>
    <h1 class="site_title">
      <a class="js" data-placement="bottom" data-delay="1000" rel="tooltip scroll-to-top" title="Scroll to top"><?php echo Configure::read('Site.title'); ?></a>
      <i id="can-scroll" class="icon-arrow-up icon-white" style="display: none"></i>
    </h1>
    <h2 class="section_title" rel="scroll-to-top"><?php echo $this->fetch('title'); ?></h2>
    <div class="btn_view_site">
      <a href="#">View Site</a>
    </div>
  </hgroup>
</header>
