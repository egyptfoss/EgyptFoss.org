<?php
/**
 * Template Name: Respond Screen.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					Creating Website for my Company
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row content-area">
   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#achievement-modal">Large modal</button>
    <div class="col-md-3 responses-sidebar">
			<h3>Responses</h3>
			<ul class="nav nav-tabs" role="tablist">
					<li class="active">
					<a href="#all" data-toggle="tab">All</a>
				</li>
				<li>
					<a href="#read" data-toggle="tab">Read</a>
				</li>
				<li>
					<a href="#unread" data-toggle="tab">Unread</a>
				</li>
			</ul>
				<div class="tab-content nano">
													<div role="tabpanel" class="tab-pane nano-content active" id="all">
															<ul class="responses-list-user">
					<li>
					<span class="unread-indicator">
									<i class="fa fa-check-circle "></i>
								</span>
						<div class="user-cell unread-response">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ahmed Badran</a>
								<br>

									<div class="short-line">
												You: have you tried to play music have you tried to play music have you tried to play music
								</div>
							</div>
						</div>
						<div class="last-message-date">
					<span title="17 Nov, 2016">	17 Nov</span>
						</div>
					</li>
					<li>
					<span class="read-indicator">
									<i class="fa fa-check-circle "></i>
								</span>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Yomna Fahmy</a>
								<br>

						<div class="short-line">
												You: have you tried to play music have you tried to play music have you tried to play music
								</div>
							</div>
						</div>
						<div class="last-message-date">
					<span title="17 Nov, 2016">	17 Nov</span>
						</div>
					</li>
					<li>
						<span class="read-indicator">
									<i class="fa fa-check-circle "></i>
								</span>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ashraf Kotb</a>
								<br>

							<div class="short-line">
												You: have you tried to play music have you tried to play music have you tried to play music
								</div>
							</div>
						</div>
						<div class="last-message-date">
					<span title="17 Nov, 2016">	17 Nov</span>
						</div>
					</li>
								<li>
									<span class="read-indicator">
									<i class="fa fa-check-circle "></i>
								</span>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ashraf Kotb</a>
								<br>

							</div>
						</div>
					</li>
				</ul>
			</div>
								<div role="tabpanel" class="tab-pane nano-content" id="read">
															<ul class="responses-list-user">
					<li>
						<span class="read-indicator">
									<i class="fa fa-check-circle "></i>
								</span>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ahmed Badran</a>
								<br>

							</div>
						</div>
					</li>
					<li>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Yomna Fahmy</a>
								<br>

							</div>
						</div>
					</li>
					<li>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ashraf Kotb</a>
								<br>

							</div>
						</div>
					</li>
				</ul>
			</div>
			<div role="tabpanel" class="tab-pane nano-content" id="unread">
															<ul class="responses-list-user">
					<li>
						<div class="user-cell">
							<div class="avatar">
								<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
							</div>
							<div class="user-name">
								<a href="#">Ahmed Badran</a>
								<br>

							</div>
						</div>
					</li>

				</ul>
			</div>
				</div>
    </div>
    <div  class="single-request-content col-md-6">
			<div class="conv-body">
				<div class="conv-header">
				<div class="conv-avatar">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="Ahmed Badran" />
				</div>
				<div class="conv-user-name">
					<h4><a href="#">Mohamed Ibrahim</a></h4>
					<small>active right now</small>
				</div>
				<div class="archive-thread text-right">
            	<a href="javascript:void(0);" class="btn btn-link"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
            </div>
					</div>
					<div class="nano">
						<div class="conv-thread nano-content">
						<div class="empty-state-thread hidden">
							<img src="<?php echo get_template_directory_uri(); ?>/img/empty_thread.svg" alt="">
							<p>Start conversation by writing your response below</p>
							<img src="<?php echo get_template_directory_uri(); ?>/img/direction-arrow.svg" alt="">
						</div>
						<div class="alert alert-warning">
						<i class="fa fa-archive"></i>
							Thread is archived, no further messages
						</div>
							<div class="conv-date">Yesterday, 10:43 PM</div>
							<div class="response-row me">
					<p><span class="user-name">Me:</span>	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed efficitur pretium lacus, eget commodo nisi posuere ac. Vivamus sed metus a magna viverra finibus at a ligula.</p>
						<p>	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed efficitur pretium lacus, eget commodo nisi posuere ac. Vivamus sed metus a magna viverra finibus at a ligula.</p>
						<div class="message-time-stamp">
							<i class="fa fa-clock-o"></i> 13/12/2030 - 12:33 P.M
						</div>
							</div>
								<div class="response-row you">
					<p>
					<span class="user-name">Mohamed:</span>
						Duis pellentesque lectus nibh, eget bibendum magna dictum a. Praesent sit amet bibendum arcu, id scelerisque orci. Ut purus lectus, commodo in vulputate ac, accumsan sed orci. Nullam quam leo
					</p>
						<div class="message-time-stamp">
							<i class="fa fa-clock-o"></i> 13/12/2030 - 12:33 P.M
						</div>
							</div>
						</div>
					</div>
			<div class="conv-compose">
<div class="message-text-composer">
	<textarea name="name" rows="2"placeholder="Write your replay..." value=""></textarea>
</div>
<div class="options-btns text-right">
	<button type="button" name="button" class="btn btn-primary btn-sm">
		<i class="fa fa-send"></i>
		Send
	</button>
</div>
			</div>
			</div>
    </div>
		<div class="col-md-3">
		<h3>Rate This Service</h3>
	<div class="provider-rating"></div>
	<span class="live-rating"></span>
	<br>
	<small><i class="fa fa-check"></i></small>
	<div class="modal fade" id="add-review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel">Write Your Feedback</h3>
      </div>
      <div class="modal-body">
       <div class="row">
           <div class="col-md-12">
             <div class="form-group">
            <textarea name="write-review" id="" class="form-control" cols="30" rows="4" placeholder="Write Your Review For This Service..." autofocus></textarea>
        </div>  
           </div>
       </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Submit Review</button>
      </div>
    </div>
  </div>
</div>
<h3>Description</h3>
<div class="panel panel-default">
<div class="panel-body">
		<p>
			Aliquam lectus sem, aliquam a vehicula ut, convallis et ipsum. Proin iaculis lorem metus. Nam risus velit, fermentum ac efficitur ut, malesuada et tortor. Duis ultricies luctus tellus ac cursus. Suspendisse a risus augue. Curabitur aliquam rhoncus ex id porttitor. Suspendisse pharetra ipsum sed feugiat auctor.
		</p>

</div>
</div>
<div class="text-center">
	<a href="#"><i class="fa fa-external-link"></i> View Full Request Details</a>
</div>
    </div>
    </div><!-- #primary -->
	</div>
</div>

<div class="modal fade" id="achievement-modal" tabindex="-1" role="dialog" aria-labelledby="achievement">
  <div class="modal-dialog" role="modal-dialog">
    <div class="modal-content achievement-modal-content">
     <audio  id="notification-sound">
         <source src="<?php echo get_template_directory_uri(); ?>/sounds/01.ogg" type="audio/ogg">
  <source src="<?php echo get_template_directory_uri(); ?>/sounds/01.mp3" type="audio/mpeg">
     </audio>
      <div class="modal-body text-center">
        <div class="row">
            <div class="col-md-12">
                <img src="<?php echo get_template_directory_uri(); ?>/img/badges/badge-news-lvl1.png" class="achivement-badge" alt="Badge">
                <h3>Achievement Unlocked</h3>
                <p>You have been awarded <strong>News Contributer Badge LVL 1</strong> for posting 10+ news articles at Egypt FOSS Platform.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a href="#" class="btn btn-primary">View Your Badges</a>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer();?>
