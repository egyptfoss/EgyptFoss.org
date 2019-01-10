<div class="modal fade" id="all-reviews-modal" data-offset="0" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php _e('Reviews','egyptfoss'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="row form-group">
          <div id="service-reviews" class="col-md-12">
          </div>
          <button type="button" class="btn btn-link" id="load_more_reviews" data-offset="0" data-count="0" style="display: none;">
            <?php _e("Load more...", "egyptfoss"); ?>
          </button>
        </div>
        <i class="fa fa-circle-o-notch fa-spin loading_reviews hidden" style="margin-left: 46%;"></i>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e('Ok','egyptfoss'); ?></button>
      </div>
    </div>
  </div>
</div>