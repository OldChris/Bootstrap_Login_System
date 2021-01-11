<?php require_once "sessionControl.php"; ?>
<?php
require makeAbsRootPath("root") . "app-defaults.php";
require makeAbsRootPath("root") . "app-core.php";
page_header(false);
?>
   <div class="starter-template text-center py-3 px-3">
            <div class="col-md-4 offset-md-4 form login-form">
                <form action="login-user.php" method="POST" autocomplete="">
                    <h2 class="text-center">Login Form</h2>
                    <p class="text-center">Login with your email and password.</p>
                    <?php
                    if(count($errors) > 0)
                    {
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach($errors as $showerror){
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    if(isset($_SESSION['signup-failed']))
                    {
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $_SESSION['signup-failed']; ?>
                        </div>
                        <?php
                    }

                    ?>
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="Email Address" required value="<?php echo $email ?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="link forget-pass text-left"><a href="forgot-password.php">Forgot password?</a></div>
                    <div class="form-group">
                        <input  class="btn btn-primary" type="submit" name="login" value="Login">
                    </div>
                    <?php
                    		if ($GLOBALS['SessionRegisterAllowed'] == true)
                            {
                               echo '<div class="link login-link text-center">Not yet a member? <a href="signup-user.php">Signup now</a></div>';                            
                            }
                    ?>
                </form>
            </div>
    </div>
<?php
page_footer(false);
?>
