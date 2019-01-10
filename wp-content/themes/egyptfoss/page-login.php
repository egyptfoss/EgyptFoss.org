<?php
/**
 * Template Name: Login Template.
 *
 * @package egyptfoss
 */

get_header(); ?>
  <header class="page-header">
  <div class="container">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
  </div>
  </header><!-- .entry-header -->
<div class="container">
  <div class="row">
      <div id="primary" class="content-area col-md-12">
          <section class="login-form-wrapper">
            <!-- Nav tabs -->
     <ul class="nav nav-tabs" role="tablist">
       <li role="presentation" class="active"><a href="#login" aria-controls="login" role="tab" data-toggle="tab"><h4>Login to your Account</h4></a></li>
       <li role="presentation"><a href="#forgot" aria-controls="forgot" role="tab" data-toggle="tab"><h4>Forgot Password</h4></a></li>
     </ul>

     <!-- Tab panes -->
     <div class="tab-content">
       <div role="tabpanel" class="tab-pane active" id="login">
         <form action="" class="login-form">
                        <div class="form-group">
                            <label class="label" for="username-email">E-mail or Username</label>
                            <input value='' id="username-email" placeholder="E-mail or Username" type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="label" for="password">Password</label>
                            <input id="password" value='' placeholder="Password" type="text" class="form-control" />
                        </div>
                        <div class="input-group">
                          <div class="checkbox">
                            <label>
                              <input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                            </label>
                          </div>
                        </div>
                        <div class="social-login-wrap">
                          <a href="#" class="spritesmall sprite-facebook" data-toggle="tooltip" data-placement="top" title="Login With Facbook"></a>
                          <a href="#" class="spritesmall sprite-twitter" data-toggle="tooltip" data-placement="top" title="Login With Twitter"></a>
                          <a href="#" class="spritesmall sprite-linkedin" data-toggle="tooltip" data-placement="top" title="Login With Linkedin"></a>
                          <a href="#" class="spritesmall sprite-google-plus" data-toggle="tooltip" data-placement="top" title="Login With Google Plus"></a>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Login" />
                        </div>
                    </form>
       </div>
       <div role="tabpanel" class="tab-pane" id="forgot">
         <form action="" class="forgot-form">
                        <div class="form-group">
                            <label class="label" for="username-email">E-mail or Username</label>
                            <input value='' id="username-email" placeholder="E-mail or Username" type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                           <button type="submit" class="btn btn-primary btn-block"> Request new password</button>
                           
                        </div>
                    </form>
       </div>
     </div>
          </section>
  </div><!-- #primary -->
  </div>
</div>

<?php get_footer();?>