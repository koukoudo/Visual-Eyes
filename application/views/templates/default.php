<!DOCTYPE html>
<html lang="en"> 
    <head>
        <title>visual-eyes | <?php echo $title; ?></title>

        <script src="https://d3js.org/d3.v3.min.js" async defer></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" async defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/canvg/1.4/canvg.js" aysnc defer></script>
        <script src="<?php echo base_url('js/visualize.js'); ?>" async defer></script>
        <script src="<?php echo base_url('js/captcha.js'); ?>" async defer></script>  
        <script src="<?php echo base_url('js/dashboard.js'); ?>" async defer></script>    
        <script src="<?php echo base_url('js/password.js'); ?>" async defer></script>      
        <script src="https://www.google.com/recaptcha/api.js?render=expicit" async defer></script>

        <script type="text/javascript">
            var BASE_URL = "<?php echo base_url(); ?>";
        </script> 

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="https://fonts.googleapis.com/css2?family=Khula&display=swap" rel="stylesheet">
        <link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/normalize.css'); ?>">   
        <link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/style.css'); ?>"> 
    </head>

    <body>
        <header>
            <img class="logo" src="<?php echo base_url(); ?>images/logo.png" />

            <nav id="nav" class="topnav">
                <?php 
                    if ($this->session->userdata('signed_in') === true) {
                        echo '<a '; if ($title == 'Dashboard') { echo 'class="active"'; } echo 'href="'.base_url('Dashboard').'">Dashboard</a>';
                        echo '<a '; if ($title == 'Visualize') { echo 'class="active"'; } echo 'href="'.base_url('Visualize').'">Create Visualizations</a>';
                    } 
                ?>
                <a <?php if ($title == 'Support') { echo 'class="active"'; } echo 'href="'.base_url('Support').'"'; ?>>Support Us</a>
                <a <?php if ($title == 'About') { echo 'class="active"'; } echo 'href="'.base_url('About').'"'; ?>>About</a>
            </nav>

            <nav id="nav-user" class="topnav">
                <?php
                    if ($this->session->userdata('signed_in') === true) {
                        echo '<a id="a-sign-out" href="'.base_url('User/signOut').'"><i class="material-icons">exit_to_app</i> Sign Out</a>';
                    } else {
                        echo '<a id="a-sign-in" '; if ($title == 'Sign In') { echo 'class="active"'; } echo 'href="'.base_url('User/signInView').'"><i class="material-icons">person</i> Sign In</a>';
                        echo '<a id="a-sign-up" '; if ($title == 'Sign Up') { echo 'class="active"'; } echo 'href="'.base_url('User/signUpView').'"><i class="material-icons">person_add</i> Sign Up</a>';                       
                    }
                ?>
            </nav>
        </header>

        <main>
            <div class="msg"> <?php echo $msg ?> </div>
            <?php echo $body; ?>
        </main>
    
        <footer>
            <p>© 2020 visual-eyes</p>
        </footer>
    </body>  
</html>