<?php
/**
 * Template Name: Single Dataset.
 *
 * @package egyptfoss
 */

get_header(); ?>
<div vocab="http://schema.org/" typeof="dataset">
  <div property="publisher" typeof="Organization">
    <meta property="name" content="EgyptFOSS" />
    <div property="logo" typeof="ImageObject">
      <meta property="url" content="<?php echo get_template_directory_uri(); ?>/img/logo.png">
    </div>
  </div>
  <meta property="dateModified" content="<?php echo mysql2date('d F Y', $post->post_modified) ?>">
  <meta property="mainEntityOfPage" content="<?php echo home_url() . "/open-datasets/" ?>">
  <header class="page-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
            <h1 property="name"><?php echo $post->post_title; ?></h1>
        </div>
      </div>
    </div>
  </header><!-- .entry-header -->

  <div class="container">
    <div class="row">
      <?php
      $ef_messages = getMessageBySession("ef_dataset_messages");
      if(isset($ef_messages['success'])): ?>
        <div class="alert alert-success">
        <?php foreach ($ef_messages['success'] as $success) { ?>
          <i class="fa fa-check"></i> <?php echo $success; ?>
        <?php } ?>
        </div>
        <div class="clearfix"></div>
      <?php elseif( !empty( $ef_messages['warning'] ) ):?>
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> <?php echo $ef_messages['warning']; ?>
        </div>
      <?php endif; ?>

      <?php $add_resources = getMessageBySession("ef_open_dataset_resources_add_messages");
          if(!empty($add_resources))
          {?>
          <div class="alert alert-success"><?php
          echo "<i class='fa fa-check'></i>" . $add_resources['success'] . "<br/>";
          ?>
          </div>
      <?php } ?>

    </div>
    <div class="row">
      <div class="col-md-9">
              <div class="created-by-name">
                <?php echo get_avatar( $post->post_author, 32 );
                  $user_data = get_registration_data($post->post_author);
                  $userRDFAType = (($user_data['type'] == "Entity")) ? "Organization" : "Person";
                ?>
                <span property="creator" typeof="<?php echo $userRDFAType ?>">
                    <?php if(bp_core_get_username($post->post_author) != '') { ?>
                      <a href="<?php echo home_url()."/members/".bp_core_get_username($post->post_author).'/about/' ?>">
                         <?php echo bp_core_get_user_displayname($post->post_author); ?>
                      </a>
                    <?php } else {  ?>
                     <?php echo bp_core_get_user_displayname($post->post_author);
                    }?>
                    <meta property="name" content="<?php echo bp_core_get_user_displayname($post->post_author); ?>">
                  </span>
                  <span class="post-date date-display" itemprop="datePublished" property="datePublished">
                    <i class="fa fa-clock-o"></i>
                  <?php echo mysql2date('d F Y', $post->post_date); ?>
                  </span>
              </div>
      </div>
      <div class="col-md-3">
                <div class="share-product">
                      <?php if(get_post_status() == "publish"){ ?>
                        <div class="share-profile rfloat">
                          <a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share', 'egyptfoss') ?>
                            <div class="share-box">
                              <?php echo do_shortcode('[Sassy_Social_Share]');?>
                            </div>
                          </a>
                        </div>
                      <?php } ?>
                    </div>
      </div>
    </div>
    <div class="row">
      <div id="primary" class="content-area col-md-9">
        <div class="row">
          <div class="col-md-12">
            <h3><?php _e('Description','egyptfoss') ?></h3>
            <p property="description"><?php the_field('description'); ?></p>
            <?php $usage_hints = get_field( 'usage_hints' ); ?>
            <?php if($usage_hints): ?>
              <h3><?php _e('Usage hints','egyptfoss') ?></h3>
              <p><?php echo $usage_hints; ?></p>
            <?php endif; ?>
            <h3><?php _e('References','egyptfoss') ?></h3>
            <p property="sameAs">
              <?php
              $ref = preg_replace("/(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s<]{2,}|www\.[^\s]+\.[^\s<]{2,})/","<a href='$1'>$1</a> " ,  get_field('references'));
              echo $ref;
               ?>
            </p>
            <h3><?php _e('Link to source','egyptfoss') ?></h3>
            <p><a href="<?php the_field('source_link'); ?>" rel="nofollow"><?php the_field('source_link'); ?></a></p>
          </div>
        </div>
          <div class="row">
            <div class="col-md-12 comments-section">
              <?php
              if ( comments_open() || get_comments_number() ) :
                comments_template();
              endif;
              ?>
            </div>
          </div>
      </div><!-- #primary -->
      <div class="col-md-3 side-bar">
        <h3><?php _e('Dataset Info.','egyptfoss') ?></h3>
        <ul class="list-group basic-info-box">
          <li class="list-group-item">
            <?php $type = get_term(get_field('dataset_type'), 'dataset_type'); ?>
            <strong><?php _e('Type','egyptfoss') ?></strong>
            <?php echo ((pll_current_language() == 'ar' && $type->name_ar != '' && $type->name_ar != null) ? $type->name_ar : $type->name); ?>
          </li>
          <li class="list-group-item">
            <?php $license = get_term(get_field('datasets_license'), 'datasets_license'); ?>
            <strong><?php _e('License','egyptfoss') ?></strong>
            <?php echo ((pll_current_language() == 'ar' && $license->name_ar != '' && $license->name_ar != null) ? $license->name_ar : $license->name); ?>
          </li>
          <li class="list-group-item">
            <?php $theme = get_term(get_field('theme'), 'theme'); ?>
            <strong><?php _e('Theme','egyptfoss') ?></strong>
            <?php echo ((pll_current_language() == 'ar' && $theme->name_ar != '' && $theme->name_ar != null) ? $theme->name_ar : $theme->name); ?>
          </li>
          <li class="list-group-item">
            <strong><?php _e('Publisher','egyptfoss') ?></strong> <?php the_field('publisher'); ?>
          </li>
          <li class="list-group-item">
            <strong><?php _e('Interests','egyptfoss') ?></strong>
              <?php $interests = get_field('interest', $post->ID, $format_value = true);
              if ( ! empty( $interests ) ) { ?>
                <?php foreach ( $interests as $interest_id) :
                  $interest = get_term( $interest_id, 'interest' );?>
                  <span class="interest-badge">
                    <?php _e("$interest->name", "egyptfoss"); ?>
                  </span>
                <?php endforeach; ?>
              <?php } ?>
          </li>
          <?php $published_date = get_post_meta( $post->ID, 'published_date', TRUE ); ?>
          <?php if( $published_date ): ?>
            <li class="list-group-item">
                <strong><?php _e('Published date','egyptfoss') ?></strong>
                <span property="dateCreated"><?php echo mysql2date( 'j F Y', $published_date ) ?></span>
            </li>
          <?php endif; ?>
        </ul>
        <?php
            $attachments_ids = get_post_meta($post->ID,'resources_ids',true);
            $resources = get_post_meta($post->ID,'resources',true);
            $publish_attachs = $pending_attachs = array();

            // get published attachments
            if( !empty( $attachments_ids ) ) {
              //load all attachments by post meta
              $attachments = explode("|||", $attachments_ids);

              if ( $attachments ) {
                foreach ( $attachments as $attach ) {
                  $attach_info = ef_get_attach_info( $attach );
                  if( $attach_info ) {
                    $publish_attachs[] = ef_get_attach_info( $attach );
                  }
                }
              }
            }

            // get pending attachments
            if( !empty( $resources ) ) {
              //load resources under approval added by logged in user
              for( $i = 0; $i < $resources; $i++ ) {
                $resource_status = get_post_meta($post->ID, 'resources_'.$i.'_resource_status', true);
                $resource_id = get_post_meta($post->ID, 'resources_'.$i.'_upload', true);
                if($resource_status == 'pending') {
                  //load post to check if post added by me
                  $attach = get_post( $resource_id );
                  if( $attach != NULL && $attach->post_author == get_current_user_id() ) {

                    $attach_info = ef_get_attach_info( $resource_id );

                    if( $attach_info ) {
                      $pending_attachs[] = $attach_info;
                    }
                  }
                }
              }
            }

            // get count of total attachments
            $attach_count = count( $publish_attachs ) + count( $pending_attachs );
        ?>
        <h3 style="font-size: 1.3em;">
          <?php
            echo __('Downloads & Resources','egyptfoss');
            echo $attach_count?" ({$attach_count})":"";
            echo '<br>';
          ?>
        </h3>
        <span class="lfloat sort-container">
            <a href="javascript:;" class="lfloat" id="ef-sort-files">
                <span id="sort-label"><?php _e( 'Sort', 'egyptfoss' ); ?></span>
                &nbsp;&nbsp;<i class="fa fa-angle-down"></i>
            </a>
            <ul class="sort-list">
                <li class="list-group-item" id="sort-by-name" data-label="<?php _e( 'Sort By Name', 'egyptfoss' ); ?>"><a href="javascript:;"><?php _e( 'By Name', 'egyptfoss' ); ?></a></li>
                <li class="list-group-item" id="sort-by-size" data-label="<?php _e( 'Sort By Size', 'egyptfoss' ); ?>"><a href="javascript:;"><?php _e( 'By Size', 'egyptfoss' ); ?></a></li>
                <li class="list-group-item" id="sort-by-type" data-label="<?php _e( 'Sort By Type', 'egyptfoss' ); ?>"><a href="javascript:;"><?php _e( 'By Type', 'egyptfoss' ); ?></a></li>
            </ul>
        </span>

        <ul class="files-list list-group">
            <?php if( empty( $attachments_ids ) && empty( $resources ) ): ?>
              <li class="list-group-item text-center clearfix"><?php _e( 'No resources', 'egyptfoss' ); ?></li>
            <?php else:

                $i = 1;
                foreach( $publish_attachs as $attach ):

                  echo '<li class="list-group-item file-item">'
                          . '<a class="file-name" href="javascript:;" title="'. $attach['title'] .'" data-toggle="modal" data-target="#myModal-'. $attach['id'] .'">'. $attach['name'] .'</a>'
                          . '<a href="#" onclick="loadDescription(\''.$attach['id'].'\',\''.$post->ID.'\');return false;" class="get-description" data-id="'.$attach['id'].'" data-toggle="modal" data-target="#get-description"><i class="fa fa-info-circle"></i> </a>'
                          . '<br>'
                          . '<span class="file-type" data-format="'.$attach['ext'].'">' . $attach['ext'] . '</span> '
                          . '<span class="file-size">' . $attach['size'] . '</span>'
                          . '<span class="file-bytes" style="display: none;">' . $attach['bytes'] . '</span>'
                          . '<span property="distribution" typeof="DataDownload">'
                            . '<meta property="fileFormat" content="'. $attach['ext'] .'" />'
                            . '<meta property="contentURL" content="'. $attach['url'] .'">'
                          . '</span>';
                  echo '</li>';

                  $view_url = $attach['url'];
                  if( in_array( $attach['ext'], array( 'doc', 'docx', 'xls', 'xlsx' ) ) ) {
                    $view_url = 'https://docs.google.com/viewer?url=' . $view_url;
                  }
                  ?>
                    <!-- Modal -->
                    <div id="<?php echo "myModal-" . $attach['id']; ?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title"><?php _e( 'Copy the link or', 'egyptfoss' ); ?> <a href="<?php echo $attach['url']; ?>" style="width: auto;" download><?php _e( 'start downloading', 'egyptfoss' ); ?></a> <?php _e( 'now', 'egyptfoss' ); ?></h4>
                          </div>
                          <div class="modal-body">
                              <input type="text" onFocus="this.setSelectionRange(0, this.value.length)" class="input form-control url-to-be-copied" value="<?php echo $attach['url']; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus style="text-align: center;"/>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e( 'Close' ); ?></button>
                            <a href="<?php echo $view_url; ?>" class="btn btn-primary" target="__blank"><?php _e( 'View', 'egyptfoss' ); ?></a>
                            <button type="button" class="btn btn-primary" onClick="copyText(this)"><?php _e( 'Copy To clipboard', 'egyptfoss' ); ?></button>
                            <span class="lfloat copied-message" style="margin-top: 10px;color: #4caf50;"></span>
                          </div>
                        </div>

                      </div>
                    </div>
                  <?php
                endforeach;

                foreach( $pending_attachs as $attach ):
                  echo '<li class="list-group-item file-item">'
                          . '<a class="file-name" href="javascript:;" title="'. $attach['title'] .'" data-toggle="modal" data-target="#myModal-'. $attach['id'] .'">'. $attach['name'] .'</a>'
                          . '<a href="#" onclick="loadDescription(\''.$attach['id'].'\',\''.$post->ID.'\');return false;" class="get-description" data-id="'.$attach['id'].'" data-toggle="modal" data-target="#get-description"><i class="fa fa-info-circle"></i> </a>'
                          . '<br>'
                          . '<span class="file-type" data-format="'.$attach['ext'].'">' . $attach['ext'] . '</span> '
                          . '<span class="file-size">' . $attach['size'] . '</span>' . '<br/>'
                          . '<span class="pending-approval"><i class="fa fa-history"></i>'.__('Pending Approval','egyptfoss').'</span>'
                          . '<span class="file-bytes" style="display: none;">' . $attach['bytes'] . '</span>';
                  echo '</li>';

                  $view_url = $attach['url'];
                  if( in_array( $attach['ext'], array( 'doc', 'docx', 'xls', 'xlsx' ) ) ) {
                    $view_url = 'https://docs.google.com/viewer?url=' . $view_url;
                  }
                  ?>
                    <!-- Modal -->
                    <div id="<?php echo "myModal-" . $attach['id']; ?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?php _e( 'Copy the link or', 'egyptfoss' ); ?> <a href="<?php echo $url; ?>" style="width: auto;" download><?php _e( 'start downloading', 'egyptfoss' ); ?></a> <?php _e( 'now', 'egyptfoss' ); ?></h4>
                          </div>
                          <div class="modal-body">
                              <input type="text" onFocus="this.setSelectionRange(0, this.value.length)" class="input form-control url-to-be-copied" value="<?php echo $url; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus style="text-align: center;"/>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e( 'Close' ); ?></button>
                            <a href="<?php echo $view_url; ?>" class="btn btn-primary" target="__blank"><?php _e( 'View', 'egyptfoss' ); ?></a>
                            <button type="button" class="btn btn-primary" onClick="copyText(this)"><?php _e( 'Copy To clipboard', 'egyptfoss' ); ?></button>
                            <span class="lfloat copied-message" style="margin-top: 10px;color: #4caf50;"></span>
                          </div>
                        </div>

                      </div>
                    </div>
                  <?php
                endforeach;

              ?>
              <?php if( $post->post_status == 'publish' && count( $publish_attachs ) ): ?>
                <li class="list-group-item text-center clearfix download-all-files">
                  <?php $url = site_url( 'data-download' ) . '/' . $post->ID; ?>
                  <a href="#" class="btn btn-link download-all" data-toggle="modal" data-target="#myModal">
                      <?php _e("Download All","egyptfoss"); ?>
                  </a>

                  <script>

                    function copyText(element) {
                      var container = jQuery( element ).closest( '.modal-dialog' );
                      var link = container.find('.url-to-be-copied');
                      link.focus();
                      document.execCommand('copy');
                      container.find('.copied-message').html( "<?php _e( 'Copied!', 'egyptfoss' ); ?>" );
                      setTimeout(
                        function() {
                          container.find('.copied-message').html("");
                        }, 3000);
                    }

                  </script>

                  <!-- Modal -->
                  <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><?php _e( 'Copy the link or', 'egyptfoss' ); ?> <a href="<?php echo $url; ?>" style="width: auto;" download><?php _e( 'start downloading', 'egyptfoss' ); ?></a> <?php _e( 'now', 'egyptfoss' ); ?></h4>
                        </div>
                        <div class="modal-body">
                            <input type="text" onFocus="this.setSelectionRange(0, this.value.length)" class="input form-control url-to-be-copied" value="<?php echo $url; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus style="text-align: center;"/>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e( 'Close' ); ?></button>
                          <button type="button" class="btn btn-primary" onClick="copyText(this)"><?php _e( 'Copy To clipboard', 'egyptfoss' ); ?></button>
                          <span class="lfloat copied-message" style="margin-top: 10px;color: #4caf50;"></span>
                        </div>
                      </div>

                    </div>
                  </div>
                </li>
              <?php endif;
              endif; ?>
        </ul>
        <?php if ( $post->post_status == 'publish'): ?>
          <div class="add-resources-link text-center">
              <?php if ( !is_user_logged_in() ) { ?>
                 <a class="add-resources-btn" href="<?php echo home_url( pll_current_language().'/login/?redirected=addresourcesopendataset&redirect_to='.(home_url()."/open-dataset/add-resources/?did=".$post->ID )); ?>" class="btn btn-light rfloat"><i class="fa fa-plus"></i>
                   <?php _e("Add More Resources","egyptfoss"); ?></a>
              <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                 <a href="<?php echo home_url()."/open-dataset/add-resources/?did=".$post->ID ?>" class="btn btn-link add-resources-btn">
                     <i class="fa fa-plus"></i>
                     <?php _e("Add More Resources","egyptfoss"); ?>
                 </a>
              <?php } else { ?>
                 <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                 <a href="javascript:void(0)" class="btn btn-link disabled add-resources-btn" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">                    <i class="fa fa-plus"></i>
                     <?php _e("Add More Resources","egyptfoss"); ?></a>
              <?php } ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="modal fade" id="get-description" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php _e("Resource Description", "egyptfoss"); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row form-group">
            <div class="col-md-12">
              <p class="resource-description" id="resource-description">
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Close" name="button"><?php _e("Cancel", "egyptfoss"); ?></button>
          </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer();?>
