<section id="container-sign" class="page-container">
    <section id="sign-sign-in" class="section-container sign">
        <?php echo form_open('User/signIn'); ?>
            <ul>
                <li>
                    <input type="text" name="email" placeholder="email" value="<?php if($this->input->cookie('user')) { echo $email; } ?>" autocomplete="off" required>
                </li>
                <li>
                    <input type="password" name="password" placeholder="password" value="<?php if($this->input->cookie('user')) { echo $pass; } ?>" autocomplete="off" required>
                </li>
                <li>
                    <label><input type="checkbox" id="check-remember" name="remember" value="yes" <?php if($this->input->cookie('user')) { echo "checked='true'"; } ?> />  Remember me</label>
                </li>
                <li>
                    <button type="submit" class="submit-sign">Sign In</button>
                </li>
                <li>
                    <a href='#' id="forgot-password">Forgot password?</a>
                </li>
                <li>
                    <label>Don't have an account? <a id="signin-larger" href="<?php echo base_url('User/signUpView'); ?>" />Sign Up</a></label>
                </li>
            </ul>
        </form>
    </section>
</section>
