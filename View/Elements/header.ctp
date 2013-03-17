<header id="header">
    <hgroup>
        <h1 class="site_title">
            <a class="js" data-placement="bottom" data-delay="1000"
                rel="tooltip scroll-to-top" title="Scroll to top">
                <?php echo Configure::read('Site.title'); ?>
                <i id="can-scroll" class="icon-arrow-up icon-white"
                    style="display: none"></i>
            </a>
        </h1>
        <h2 class="section_title"
            rel="scroll-to-top"><?php echo $this->fetch('title'); ?></h2>
        <div class="btn_view_site">
        <?php
            if (!isset($viewSite)) {
                $viewSite = array('View Site', '/');
            }
            echo $this->Html->link($viewSite[0], $viewSite[1], array(
                'target' => '_blank'
            ));
        ?>
        </div>
    </hgroup>
</header>
