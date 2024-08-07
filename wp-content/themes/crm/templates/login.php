<?php
/*
 * Template Name: CRM Login 
 */
if(is_user_logged_in()){
    header('Location: /');
    exit;
}
get_header();
?>
<section class="login-page">
    <div class="contact-container">
        <div class="left-side">
            <h1 class="welcome">Welcome Back!</h1>
            <div class="logdin-img"><img src="<?php echo CRM_THEME_DIR_URI;?>/images/sign-in.svg" alt="user"></div>
        </div>
        <div class="right-side">
            <form action="POST" class="form" id="crm-login-frm">
                <a href="javascript:;" class="logo"><img src="<?php echo CRM_THEME_DIR_URI;?>/images/imgpsh_fullsize_anim-logo.png" alt="logo"></a>
                <div class="formgroup">
                    <div class="form-item">
                        <label for="email">Email Address</label>
                        <input type="email" id="username-email" name="username" class="form-input" placeholder="Enter email address">
                    </div>
                    <div class="form-item">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Enter password">
                        <!-- <a href="javascript:;" class="forgot-password">Forgot Password</a> -->
                    </div>
                    <div class="form-item checkbox">
                        <label for="checkbox">Remember me</label>
                        <input type="checkbox" name="remember" id="checkbox" class="form-input">
                    </div>
                    <div class="form-item">
                        <div class="submit-message error" id="l-error" style="display:none;"></div>
                        <div class="submit-message success" id="l-success" style="display:none;"></div>
                    </div>
                    <button type="button" class="savbtn" id="crm-login-btn">Sign In</button>
                </div>
                <!-- <div class="other"><span>OR</span></div>
                <div class="createaccoutn">Are you new? <a href="javascript:;" class="forgot-password">Create An Account</a></div> -->
            </form>
        </div>
    </div>
</section>
<?php
get_footer();