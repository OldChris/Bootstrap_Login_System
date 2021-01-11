<?php require_once "sessionControl.php"; ?>
<?php 
$email = $_SESSION['email'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];
if($email == false)
{
  header('Location: login-user.php');
}

require_once makeAbsRootPath("root") . "app-defaults.php";
require_once makeAbsRootPath("root")  . "app-core.php";
page_header(true);
breadcrumbsBS("My Info", "", "");
echo '<div class="starter-template text-center py-3 px-3">' . PHP_EOL;
echo '<h1>My Info</h1>' . PHP_EOL;
echo '<p>Name = ' . $name . '</p>' . PHP_EOL;
echo '<p>Email = ' . $email . '</p>' . PHP_EOL;
echo '<p>Role = ' . $role . '</p>' . PHP_EOL;
$minutes=ceil($SessionTimeout/60);
echo '<p>Session timeout = ' . $minutes . ' minutes</p>' . PHP_EOL;
echo '</div>' . PHP_EOL;
page_footer(true);
?>