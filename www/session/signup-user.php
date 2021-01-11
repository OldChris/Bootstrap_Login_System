<?php require_once "sessionControl.php"; ?>
<?php 
require_once "../app-defaults.php";
require_once "../app-core.php";
page_header(false);
?>
     <div class="starter-template text-center py-3 px-3">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="signup-user.php" method="POST" autocomplete="">
                    <h2 class="text-center">Signup Form</h2>
                    <p class="text-center">It's quick and easy.</p>
                    <?php
                    echo '<div class="alert alert-warning text-center">' . PHP_EOL;
                    echo $GLOBALS['SessionPwdRequirements'];
                    echo '</div>' . PHP_EOL;

                    if(count($errors) == 1)
                    {
                        echo '<div class="alert alert-danger text-center">' . PHP_EOL;
                            foreach($errors as $showerror)
                            {
                                echo $showerror;
                            }
                        echo '</div>' . PHP_EOL;
                    }
                    elseif(count($errors) > 1)
                    {
                        echo '<div class="alert alert-danger">' . PHP_EOL;
                            foreach($errors as $showerror)
                            {
                                echo '<li>' . $showerror . '</li>' . PHP_EOL;
                            }
                        echo '</div>' . PHP_EOL;
                    }
                    ?>
                    <div class="form-group">
                        <input class="form-control" type="text" name="name" placeholder="Full Name" required value="<?php echo $name ?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="Email Address" required value="<?php echo $email ?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="cpassword" placeholder="Confirm password" required>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="signup" value="Signup">
                    </div>
                    <div class="link login-link text-center">Already a member? <a href="login-user.php">Login here</a></div>
                </form>
            </div>
        </div>
    </div>
<?php
page_footer(false);
?>