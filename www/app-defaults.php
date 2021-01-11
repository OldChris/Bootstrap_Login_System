<?php
$site="prod";
if ($site == "prod") 
{
	$App_Name="YourNewWebsite";
	$App_SiteURL="https://mynewwebsite.com";
	$App_SessionName="sessiononmynewwebsite";
	$App_version="v1.2";
	$App_Copyright="(c) 2011-" . date('Y') . "Chris van Gorp";
} else
{
	$App_Name="YourNewWebsite-DEV";
	$App_SiteURL="https://dev.mynewwebsite.com";
	$App_SessionName="sessiononmynewwebsiteDev";
	$App_Version="v1.2";
	$App_Copyright="(c) 2011-" . date('Y') . "Chris Van Gorp";
}
$my_folder = dirname( realpath( __FILE__ ) ) . DIRECTORY_SEPARATOR;
$App_root=$my_folder; 
$App_HomeScript="/home.php";
$App_HomeHref="https://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $App_HomeScript;
$App_reports=$App_root . "reports";
$App_logging=$App_root . "logs";
$App_data= "data";
$App_Keywords="bootstrap";
$App_Email="yournewwebsite@mynewwebsite.com";
$App_EmailNobody="nobody@mynewwebsite.com";
$App_web_protocol = "https://";
$App_Bootstrap_local= true;
if ($App_Bootstrap_local)
{
	$App_BootstrapCSS="/bootstrap/css/bootstrap.min.css";
	$App_BootstrapJS="<script src=/bootstrap/js/bootstrap.bundle.min.js></script>";
}
else
{
	$App_BootstrapCSS='"https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous"';
	$App_BootstrapJS='<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>';
}
$App_PageRefreshSeconds=0;
$App_Description="Try it yourself!";
$App_TopNavbarColor="#ff9900";
$App_BottomNavbarColor="#ff9900";
// settings for database connection
// see if we are on the hosting server or local in Docker
if (strpos(strtolower($_SERVER['SERVER_NAME']), ".mynewwebsite.com") !== false)
{
	// your webhosting server
    // file containing passwords is one level up, outside webroot folder
	$db_access_filename="/alldomains/mynewwebsite.com/private_html/db_access.txt";
} 
else
{
	// your Docker development environment
	// in this case there is no need to hide the file
	$db_access_filename= "/var/www/html/db_access.txt";
}

$SessionTableUsers="users";
$SessionTableUsersLog="users_log";
$SessionRegisterAllowed=TRUE;
$SessionRoles="user,senior,admin";
$SessionDefaultSignupRole="user";  // one from the list above
$SessionRefresh=60;
$SessionTimeout=15 * 60;
$SessionOTPExpiry=100;  // seconds
$SessionPwdExpiry=2 * 30 * 86400;
$SessionPwdMinLength=8;
$SessionPwdRequirements="Password should be at least " . $SessionPwdMinLength . " characters in length and should include at least one upper case letter, one number, and one special character.";

if (file_exists($db_access_filename))
{
	$file = file_get_contents($db_access_filename, FILE_USE_INCLUDE_PATH);
	$lines = explode("\n", $file);
	for($i=0;$i<count($lines);$i++)
	{
		$line= preg_replace('/[\r\n]+/','', $lines[$i]);
		if(strpos($line, "\t") !== false)
		{
			$line= preg_replace('/\t/','', $line);
		}
		$items=explode("=", $line);
		if (count($items) == 2)
		{ 
			$var=trim($items[0]);
			$value=trim($items[1]);
			//echo "var=" . $var . ", value=" . $value;
			switch (strtolower($var))
			{
				// session database
				case "sessiondbhost":
					$SessionDbHost=$value;
					break;
				case "sessiondbname":
					$SessionDbName=$value;
					break;
				case "sessiondbusername":
					$SessionDbUsername=$value;
					break;
				case "sessiondbpassword":
					$SessionDbPassword=$value;
					break;
			//  app database
				case "appdbhost":
					$AppDbHost=$value;
					break;
				case "appdbname":
					$AppDbName=$value;
					break;
				case "appdbusername":
					$AppDbUsername=$value;
					break;
				case "appdbpassword":
					$AppDbPassword=$value;
					break;
			}
		}
	}
}

?>