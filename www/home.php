<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/session/sessionControl.php"); ?>
<?php 
$email = $_SESSION['email'];
$secret = $_SESSION['secret'];
if($email != false && $secret != false){
    $query = "SELECT * FROM $SessionTableUsers WHERE email=:email";
    if ($stmt = $con->prepare($query))
    {
        $stmt->execute(array($email));    // Execute the prepared query.
        $data=$stmt->fetchAll();
        if (count($data) == 1)
        {
            $status = $data[0]['status'];
            $code = $data[0]['code'];
            if($status == "verified")
            {
                if($code != 0)
                {
                    header('Location: session/reset-code.php');
                }
            }else
            {
                header('Location: session/user-otp.php');
            }
        }
    }
}else{
    header('Location: ' . makeAbsSitePath("session") . 'login-user.php');
}
require_once makeAbsRootPath("root") . "app-defaults.php";
require_once makeAbsRootPath("root") . "app-core.php";

page_header(true);
breadcrumbsBS(get_GET('bc1'), get_GET('bc2'), get_GET('bc3'));
$enc_function=get_GET('menu_func');
$menu_function=base64_url_decode($enc_function, $_SESSION['secret']);
if ($menu_function != "")
{
    menu_function($menu_function);
}
else
{
    echo '<div class="starter-template text-center py-3 px-3">' . PHP_EOL;
    echo '<h1>Welcome ' . $_SESSION['name'] . '</h1>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
    echo '<div class="starter-template  py-3 px-3">' . PHP_EOL;
    echo '<p> this is a demo site build with PHP and Bootstrap 5.0 beta   </p>' . PHP_EOL;
    echo '<p> it is responsive (can be used on desktops and Mobile phones)   </p>' . PHP_EOL;
  //  echo '<br>' . PHP_EOL;
    echo '<p>It supports (amongst many other features):</p>' . PHP_EOL;
    echo '<li>login and signup</li>' . PHP_EOL;
    echo '<li>password reset (forgot password)</li>' . PHP_EOL;
    echo '<li>password lifetime</li>' . PHP_EOL;
    echo '<li>session timeout on inactivity</li>' . PHP_EOL;
    echo '<li>A menu system that is file driven per user role</li>' . PHP_EOL;
    echo '<p>Enjoy your visit!</p>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
 page_footer(true);
?>