<?php
function change_password_input_type()
{?>
<style>
.passwordField
  {
    border: 1px solid #E5E5E5;
    box-shadow: 1px 1px 2px rgba(200, 200, 200, 0.2) inset;
    color: #555;
    font-size: 17px;
    height: 30px;
    line-height: 1;
    margin-bottom: 16px;
    margin-right: 6px;
    margin-top: 2px;
    outline: 0px none;
    padding: 3px;
    width: 100%;
  }
</style>
  <script>
   document.addEventListener('DOMContentLoaded',function(){
   document.querySelectorAll("input[name='user_password']")[0].type = 'password';
   document.querySelectorAll("input[name='user_password']")[0].className = 'input passwordField';
  });   
  </script>
  <?php
}
add_action( 'wsl_process_login_new_users_gateway_start', "change_password_input_type" );