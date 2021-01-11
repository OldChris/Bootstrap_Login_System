<?php require_once "sessionControl.php"; ?>
<?php
if($_SESSION['info'] == false){
    header('Location: login-user.php');  
}
require makeAbsRootPath("root") . "app-defaults.php";
require makeAbsRootPath("root") . "app-core.php";
page_header(false);

?>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form login-form">
            <?php 
            if(isset($_SESSION['info'])){
                ?>
                <div class="alert alert-success text-center">
                <?php echo $_SESSION['info']; ?>
                </div>
                <?php
            }
            ?>
                <form action="login-user.php" method="POST">
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="login-now" value="Login Now">
                    </div>
                </form>
            </div>
        </div>
    </div>
     
<?php
page_footer(false);
?>
