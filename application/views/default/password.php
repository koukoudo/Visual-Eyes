<section id="sec-reset-password">
    <h1>Change your password.</h1>

    <?php echo form_open('User/changePassword'); ?>
        <ul>
            <li>
                <input type="password" name="new-password" id="new-password" placeholder="new password" required autofocus>
            </li>
            <li>
                <input type="password" name="new-password-confirm" id="new-password-confirm" placeholder="confirm new password" required autofocus>
            </li>
            <li>
                <button type="submit" id="submit-change-password">Change</button>
            </li>
        </ul>
    </form>
</section>