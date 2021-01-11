<?php require_once "sessionControl.php"; ?>
<?php
require makeAbsRootPath("root") . "app-defaults.php";
require makeAbsRootPath("root") . "app-core.php";
page_header(false);
//session_destroy();
if (isset($_SESSION['email']))
{
    $email=$_SESSION['email'];
}
else
{
    $email="no-email";
}

log_user_access($email, $ipaddress, $hostname, "inactivity logout");
session_destroy();
session_unset(); 
$minutes=ceil($SessionTimeout / 60);
?>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form login-form">
                <form action="login-user.php" method="POST">
                <h2 class="text-center">Session inactive after <?php echo $minutes; ?> minutes</h2>
                    <p class="text-center">please login again with your email and password.</p>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="login-now" value="Login Again">
                    </div>
                </form>
            </div>
        </div>
    </div>
     
<?php
page_footer(false);
?>
