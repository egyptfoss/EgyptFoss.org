
<header class="page-header wiki-page-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php $this->html('title') ?></h1>
                <?php
                    // this variable is used in related documents templates
                    $section_slug = 'fosspedia';
                    include( locate_template( 'template-parts/content-related_documents.php' ) );
                ?>
            </div>
        </div>
    </div>
</header>
<div id="wrapper" class="container wiki-container">

    <!-- start navbar -->


        <div class="navbar navbar-default" role="navigation" id="wiki-header">
            <div class="container">
                <ul class="nav nav-tabs page-tabs">
                    <li tabindex="-1" accesskey="c" title="<?php _e("View the content page [c]","egyptfoss"); ?>"
                        class="<?php if($_SERVER['REQUEST_URI'] == $nav["views"]["view"]["href"]) { echo 'active'; } ?>" >
                        <a href="<?php echo $nav["views"]["view"]["href"];?>">
                            <?php echo $nav["views"]["view"]["text"];?>
                        </a>
                    </li>
                    <li role="presentation" class="<?php if($_SERVER['REQUEST_URI'] == $nav["views"]["history"]["href"]) { echo 'active'; } ?>" >
                        <a tabindex="-1" accesskey="h" title="<?php _e("Past revisions of this page [h]","egyptfoss"); ?>" href="<?php echo $nav["views"]["history"]["href"];?>">
                            <?php echo $nav["views"]["history"]["text"];?>
                        </a>
                    </li>
                    <?php if ($wgGroupPermissions['*']['edit'] || $wgMediaWikiBootstrapSkinAnonNavbar || $this->data['loggedin']) : ?>
                        <?php $navTemp = $this->data['content_actions']['edit'];
                        if ($navTemp) {
                        ?>
                            <li class="<?php if($_SERVER['REQUEST_URI'] == $nav["views"]["edit"]["href"]) { echo 'active'; } ?>" >
                                <a  href="<?php echo $nav["views"]["edit"]["href"];?>" id="b-edit">
                                    <i class="fa fa-edit"></i> <?php echo $nav["views"]["edit"]["text"];?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php $navTemp = $this->data['content_actions']['protect'] || $this->data['content_actions']['unprotect'];
                        $protectAction = isset($this->data['content_actions']['protect']) ? "protect" : "unprotect";
                        if ($navTemp) {
                        ?>
                            <li role="presentation" class="<?php if($_SERVER['REQUEST_URI'] == $nav["actions"][$protectAction]["href"]) { echo 'active'; } ?>" >
                                <a tabindex="-1" accesskey="h" title="<?php _e(strtoupper($protectAction)." this page [h]","egyptfoss");?>" href="<?php echo $nav["actions"][$protectAction]["href"];?>">
                                    <?php echo $nav["actions"][$protectAction]["text"];?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php $navTemp = $this->data['content_actions']['delete'];
                        if ($navTemp) {
                            $deleteAction = $this->data['action_urls']["delete"];
                        ?>
                            <li class="<?php if($_SERVER['REQUEST_URI'] == $deleteAction["href"]) { echo 'active'; } ?>" >
                                <a  href="<?php echo $deleteAction["href"];?>" id="<?php echo $deleteAction["id"]?>">
                                    <i class="fa fa-delete"></i> <?php echo $deleteAction["text"];?>
                                </a>
                            </li>
                        <?php } ?>
                    <?php endif; ?>
                </ul>

                <ul class="secondary-options rfloat">
                    <?php
                    if($_SERVER['REQUEST_URI'] == $nav["views"]["view"]["href"]){

                      ?>
                    <li style="display:inline">
                    <div class="share-profile lfloat">
                        <a class="shareWiki"><i class="fa fa-share"></i> <?php _e('Share', 'egyptfoss') ?>
                            <div class="share-box">
                                <?php
                                $url = site_url().$_SERVER['REQUEST_URI'];
                                ?>
                                <?php echo do_shortcode('[Sassy_Social_Share url='.$url.']'); ?>
                            </div>
                        </a>
                    </div>
                  </li>
                    <?php } ?>
                    <?php $this->renderNavigation(array('UPLOAD')); ?>
                </ul>
				<?php
                # Page options & menu
//                    $this->renderNavigation(array('PAGE-OPTIONS'));
                /*
                  # This content in other languages
                  if ($this->data['language_urls']) {
                  $this->renderNavigation(array('LANGUAGES'));
                  }
                 */
                # Edit button
//                    $this->renderNavigation(array('EDIT'));
                # Actions menu
//                    $this->renderNavigation(array('ACTIONS'));
                # Sidebar items to display in navbar
//                    $this->renderNavigation(array('SIDEBARNAV'));
                # Toolbox
//                    if (!isset($portals['TOOLBOX'])) {
//                        $this->renderNavigation(array('TOOLBOX'));
//                    }
                # Personal menu (at the right)
//                    $this->renderNavigation(array('PERSONAL'));
                # Search box (at the right)
//                    if ($wgSearchPlacement['top-nav']) {
//                        $this->renderNavigation(array('TOP-NAV-SEARCH'));
//                    }
                ?>
            </div><!--/.container-fluid -->
        </div> <!-- /navbar -->


    <div id="mw-page-base" class="noprint"></div>
    <div id="mw-head-base" class="noprint"></div>

<?php
if ($this->data['loggedin']) {
    $userStateClass = "user-loggedin";
} else {
    $userStateClass = "user-loggedout";
}
?>

    <?php
    if ($wgGroupPermissions['*']['edit'] || $this->data['loggedin']) {
        $userStateClass += " editable";
    } else {
        $userStateClass += " not-editable";
    }
    ?>

    <!-- content -->
    <section id="content" class="mw-body <?php echo $userStateClass; ?>">
        <div id="top"></div>
        <div class="row">
            <div id="mw-js-message" class="col-md-12" style="display:none;"<?php $this->html('userlangattributes') ?>></div>
        </div>

<?php if ($this->data['sitenotice']): ?>
            <!-- sitenotice -->

            <div class="row">
                <div class="col-md-12">

                    <div id="siteNotice" class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php $this->html('sitenotice') ?>
                    </div>

                </div>
            </div>

            <!-- /sitenotice -->
<?php endif; ?>

        <div class="clearfix"></div>
        <div id="bodyContent" class="row">
<?php if ($this->data['newtalk']): ?>
                <!-- newtalk -->

                <div class="usermessage col-xm-12 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10">

                    <div class="alert alert-success">
                        <i class="fa fa-comments"></i>
    <?php $this->html('newtalk') ?>
                    </div>

                </div>


                <!-- /newtalk -->
<?php endif; ?>


            <?php if ($this->data['showjumplinks']): ?>
                <!-- jumpto -->
                <div id="jump-to-nav" class="mw-jump">
                <?php $this->msg('jumpto') ?>
                    <a href="#mw-head"><?php $this->msg('jumptonavigation') ?></a>,
                    <a href="#p-search"><?php $this->msg('jumptosearch') ?></a>
                </div>
                <!-- /jumpto -->
<?php endif; ?>

            <!-- innerbodycontent -->
            <div id="innerbodycontent">
                <div id="other_language_link" class="lang_switch rfloat">
<?php
if ($this->data['language_urls']) {
    $this->renderNavigation(array('LANGUAGES'));
}
?>
                </div>
                <div class="col-md-12">
                    <!-- subtitle -->
                    <div id="contentSub" <?php $this->html('userlangattributes') ?>><?php $this->html('subtitle') ?></div>
                    <!-- /subtitle -->
<?php if ($this->data['undelete']): ?>
                        <!-- undelete -->
                        <div id="contentSub2"><?php $this->html('undelete') ?></div>
                        <!-- /undelete -->
<?php endif; ?>
<?php $this->html('bodycontent'); ?>
                </div>
            </div>
            <!-- /innerbodycontent -->

<?php if ($this->data['printfooter']): ?>
                <!-- printfooter -->
                <div class="printfooter">
                <?php $this->html('printfooter'); ?>
                </div>
                <!-- /printfooter -->
                <?php endif; ?>
<?php if ($this->data['catlinks']): ?>
                <!-- catlinks -->
                <div class="row">
                    <div class="col-xm-12 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10">
    <?php $this->html('catlinks'); ?>
                    </div>
                </div>
                <!-- /catlinks -->
<?php endif; ?>
<?php if ($this->data['dataAfterContent']): ?>
                <!-- dataAfterContent -->
                <div class="row">
                    <div class="col-xm-12 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10">
    <?php $this->html('dataAfterContent'); ?>
                    </div>
                </div>
                <!-- /dataAfterContent -->
<?php endif; ?>
            <div class="visualClear"></div>
            <!-- debughtml -->
            <?php $this->html('debughtml'); ?>
            <!-- /debughtml -->
        </div>
    </section>
    <!-- /content -->

</div>
<!-- /#wrapper -->
<script>
if(window.mw){
mw.loader.load(["ext.wikiEditor.toolbar","ext.wikiEditor.dialogs","ext.wikiEditor.toolbar.hideSig","ext.wikiEditor.preview"],null,true);
}
</script>
<script> var heateorSssSharingAjaxUrl = '<?php echo get_admin_url() ?>admin-ajax.php', heateorSssCloseIconPath = '<?php echo plugins_url( '../images/close.png', __FILE__ ) ?>', heateorSssPluginIconPath = '<?php echo plugins_url( '../images/logo.png', __FILE__ ) ?>', heateorSssHorizontalSharingCountEnable = <?php echo isset( $this->options['hor_enable'] ) && ( isset( $this->options['horizontal_counts'] ) || isset( $this->options['horizontal_total_shares'] ) ) ? 1 : 0 ?>, heateorSssVerticalSharingCountEnable = <?php echo isset( $this->options['vertical_enable'] ) && ( isset( $this->options['vertical_counts'] ) || isset( $this->options['vertical_total_shares'] ) ) ? 1 : 0 ?>, heateorSssSharingOffset = <?php echo isset( $this->options['alignment'] ) && $this->options['alignment'] != '' && isset( $this->options[$this->options['alignment'].'_offset'] ) && $this->options[$this->options['alignment'].'_offset'] != '' ? $this->options[$this->options['alignment'].'_offset'] : 0; ?>;
  <?php
  if ( isset( $this->options['horizontal_counts'] ) && isset( $this->options['horizontal_counter_position'] ) ) {
    echo in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceHorizontalSvgWidth = true;' : '';
    echo in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceHorizontalSvgHeight = true;' : '';
  }
  if ( isset( $this->options['vertical_counts'] ) ) {
    echo isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceVerticalSvgWidth = true;' : '';
    echo ! isset( $this->options['vertical_counter_position'] ) || in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceVerticalSvgHeight = true;' : '';
  }
  ?>
  var heateorSssUrlCountFetched = [];
  function heateorSssPopup(e){window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes")}
</script>
