<?php require_once "sessionControl.php"; ?>
<?php 
$email = $_SESSION['email'];
$role = $_SESSION['role'];
if($email == false)
{
  header('Location: login-user.php');
}

require_once makeAbsRootPath("root") . "app-defaults.php";
require_once makeAbsRootPath("root")  . "app-core.php";
page_header(true);
breadcrumbsBS("About this app", "", "");
echo '<div class="starter-template text-center py-3 px-3">' . PHP_EOL;
displayAbout();
echo '</div>' . PHP_EOL;
page_footer(true);

function displayAbout()
{
	echo '<h2>About ' . $GLOBALS['App_Name']  . '</h2>' . PHP_EOL;

    if ($GLOBALS['App_Bootstrap_local'])
    {
    	echo '<br>Bootstrap is loaded from local server<br>' . PHP_EOL;
    }
    else
    {
    	echo '<br>Bootstrap is loaded from CDN<br>' . PHP_EOL;
    }
    // https://docs.github.com/en/rest/reference/repos#get-the-latest-release
    $giturl ='https://github.com/OldChris/webbuilder';
    $gitapiurl ='https://api.github.com/repos/OldChris/webbuilder';
    $giturlVersion = $gitapiurl . '/releases';
    $headers=get_headers($giturlVersion);
    //echo 'headers[0] = ' . $headers[0] . PHP_EOL;

    $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context  = stream_context_create($options);
    $data = file_get_contents($giturlVersion, false, $context); 
    $user_data  = json_decode($data, true);
    if (empty($user_data))
    {
    	$tag='Unknown, no release info found on Github';
    }
    else
    {
    	$tag = $user_data['0']['tag_name'];
    }
    $appVersion=$GLOBALS['App_version'];
    /*
    if ($tag != $appVersion )
    {
	    echo formatUserMessage('This version : ' . $appVersion . ', Version on GitHub : ' . $tag , "W");
    }
    else
    {
	    echo formatUserMessage('This version : ' . $appVersion . ', Version on GitHub : ' . $tag , "I");
    }
  */
  //echo '<br><a href="' . $giturl . '" target="_blank">View / download source from Github</a> <br><br>' .PHP_EOL; 
	//echo '<br><a href="index.php?check=1">Check files and references to images and websites</a><br>' . PHP_EOL;
	
}

?>