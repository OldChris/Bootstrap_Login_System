<?php
require_once makeAbsRootPath("root") . "app-menu-functions.php";
function page_header($loggedIn=true)
{
	$PageTitle=$GLOBALS['App_Name'] . ' : ' . $GLOBALS['App_Description']; //. ", " . $menucontext_level1;
	echo '<!doctype html>' . PHP_EOL;
	echo '<html lang="en">' . PHP_EOL;
	echo '  <head>' . PHP_EOL;
	echo '    <!-- Required meta tags -->'. PHP_EOL;
	echo '    <meta charset="utf-8">' . PHP_EOL;
    echo '    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">' . PHP_EOL;
	echo ' ' . PHP_EOL;
	echo '    <!-- Bootsrtap CSS -->'. PHP_EOL;
    echo '    <link rel="stylesheet" href=' . $GLOBALS['App_BootstrapCSS'] . '>' . PHP_EOL;
	echo '    <link href="custom.css" rel="stylesheet">' . PHP_EOL;
	echo ' ' . PHP_EOL;
	echo '    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" >' . PHP_EOL;
	echo '    <meta name="generator" content=" Chris van Gorp">' . PHP_EOL;
	echo '    <meta name="rights" content="' . $GLOBALS['App_Copyright'] . '"/>' . PHP_EOL;
	echo '    <meta name="description" content="' . $GLOBALS['App_Description'] . '">' . PHP_EOL;
	echo '    <meta name="keywords" content="' . $GLOBALS['App_Keywords'] . '">' . PHP_EOL;
    if ($loggedIn ==true)
	{
		if ($GLOBALS['App_PageRefreshSeconds'] > 0)
		{
			echo '<meta http-equiv="REFRESH" content="' . $GLOBALS['App_PageRefreshSeconds'] .'">' . PHP_EOL;
		}
	}
    $DefaultErrors="";
	if ($DefaultErrors != "")
	{
		echo '<!--  found some errors in configuration file -->' . PHP_EOL;
		echo '<!--  ' . $DefaultErrors . ' -->' . PHP_EOL;
	}
	echo '        <script>' . PHP_EOL;
	echo '           var IDLE_TIMEOUT = ' . $GLOBALS['SessionTimeout'] . ';' . PHP_EOL;
	echo '           var APP_NAME = "' . $GLOBALS['App_Name'] . '";' . PHP_EOL;
	echo '        </script>' . PHP_EOL;
	echo '        <script src="js/script.js"></script>' . PHP_EOL;
	echo     '    <title>' . $PageTitle . '</title>' . PHP_EOL;  // " " . $App_Version . " " . $App_Copyright . 
	echo '  </head>' . PHP_EOL;
	echo '<body>' . PHP_EOL;
	echo '<a id="TopOfPage"></a>' . PHP_EOL; 
	$BrandText=$GLOBALS['App_Name'] ;
    if ($loggedIn == false)
	{	
		if ($GLOBALS['SessionRegisterAllowed'] == true)
		{
			$UserText= "Welcome, please login or sign up";
		}
		else
		{
			$UserText= "Welcome, please login";
		}
		echo PHP_EOL;
		echo '<nav class="navbar navbar-expand-md navbar-light" style="background-color:' . $GLOBALS['App_TopNavbarColor'] . ';">' . PHP_EOL;
		echo '  <span class="navbar-brand">' . $BrandText . '</span>' . PHP_EOL;
		echo '  <span class="navbar-text">'  . $UserText . '</span>' . PHP_EOL;
		echo ' </nav>' . PHP_EOL;
	} else
	{
		$UserText= "User " . $_SESSION['email'];
		echo '<nav class="navbar navbar-expand-md navbar-light" style="background-color:' . $GLOBALS['App_TopNavbarColor'] . ';">' . PHP_EOL;
		echo '  <div class="container-fluid">' . PHP_EOL;
		echo '    <a class="navbar-brand" href="' . $GLOBALS['App_HomeScript'] . '">' . $BrandText . '</a>' . PHP_EOL;
	//	echo '    <span class="navbar-text text-nowrap">'  . $UserText . '</span>' . PHP_EOL;
		echo '     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL;
    	echo '      <span class="navbar-toggler-icon"></span>' . PHP_EOL;
  		echo '    </button>' . PHP_EOL;
  		echo '    <div class="collapse navbar-collapse" id="navbarSupportedContent">' . PHP_EOL;
    	echo '      <ul class="navbar-nav me-auto mb-2 mb-lg-0">' . PHP_EOL;
      	echo '        <li class="nav-item">' . PHP_EOL;
        echo '          <a class="nav-link" href="' . makeAbsSitePath("session") . 'user-info.php">My Info</a>' . PHP_EOL;
		echo '        </li>' . PHP_EOL;
		// role based app menu items 
		$role=get_SESSION("role", $GLOBALS['SessionDefaultSignupRole']);
		make_menu($role);
		
		// 
		echo '      </ul>' . PHP_EOL;
        echo '      <div class="nav navbar-right">' . PHP_EOL;
		echo '        <a class="btn btn-outline-secondary btn-sm" href="' . makeAbsSitePath("session") . 'logout-user.php">Logout</a>' . PHP_EOL;
		echo '      </div>' . PHP_EOL;
		echo '    </div>' . PHP_EOL;
		echo '  </div>' . PHP_EOL;
		echo '</nav>' . PHP_EOL;

	}
	echo '<div class="container-fluid" style="margin-bottom:30px;">' . PHP_EOL;
	
}
function make_menu($role)
{
	$directory=makeAbsRootPath("App_data") ;
	$filename=$directory . '/menudef_' . $role . '.txt';
	if (is_dir($directory))
	{
		if (file_exists($filename))
		{
			$file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
			$lines = explode("\n", $file);
			$prevlevel=0;
			$prevmenu="";
			$prevfunction="";
			$isValid=false;
			$indent_l1=0;
			$indent_l2=4;
			$indent_l3=8;
			$indent_char=" ";
			$menu_data = array();
			$num_menu=0;
			$menu_l1="";
			$menu_l2="";
			$menu_l3="";
			// unweed comment and invalid lines
			for($i=0;$i<count($lines);$i++)
			{
				$line= preg_replace('/[\r\n]+/','', $lines[$i]);
				if(strpos($line, "\t") !== false)
				{
					$line= preg_replace('/\t/','', $line);
				}
				if ((substr($line,0,1 ) != "#") AND (substr($line,0,2) != "//"))
				{
					if ((substr($line,0,$indent_l2) == str_repeat($indent_char, $indent_l2)) AND (substr($line,$indent_l2,1)  != $indent_char))
					{
						$isValid=TRUE;
						$level=2;
					}
					else if ((substr($line,0,$indent_l3) == str_repeat($indent_char,$indent_l3)) AND (substr($line,$indent_l3,1)  != $indent_char))
					{
						$isValid=TRUE;
						$level=3;
					}
					else if (substr($line,$indent_l1,1)  != $indent_char)
					{
						$isValid=TRUE;
						$level=1;
					}
					else
					{
						$isValid=FALSE;
					}
					if ($isValid)
					{
						$items=explode("=", $line);
						if (count($items) == 2)
						{ 
							$menu=trim($items[0]);
							$function=trim($items[1]);
							switch ($level)
							{
								case 1:
									$menu_l1=$menu;
									$menu_l2="";
									$menu_l3="";
									break;
								case 2:
									$menu_l2=$menu;
									$menu_l3="";
									break;
								case 3:
									$menu_l3=$menu;
									break;
							}
							if (function_exists($function))
							{
								$menu_data[$num_menu]['level']=$level;
								$menu_data[$num_menu]['menu_l1']=$menu_l1;
								$menu_data[$num_menu]['menu_l2']=$menu_l2;
								$menu_data[$num_menu]['menu_l3']=$menu_l3;
								$menu_data[$num_menu]['function']=$function;
								$num_menu+=1;
							}
						} 
						else
						{
					//		echo "error in menu def " . $line;
						}
					}
				}
			}
			//print_r($menu_data);
			
			for ($j=0;$j<count($menu_data)-1;$j++)
			{
				$level = $menu_data[$j]['level'];
				$menu_l1 = $menu_data[$j]['menu_l1'];
				$menu_l2 = $menu_data[$j]['menu_l2'];
				$menu_l3 = $menu_data[$j]['menu_l3'];
				$function = $menu_data[$j]['function'];
				$enc_function=base64_url_encode($function, $_SESSION['secret']);
				$breadcrumb="&bc1=" . $menu_l1 . "&bc2=" . $menu_l2 . "&bc3=" . $menu_l3;
				$menu_href = $GLOBALS['App_HomeHref'] . "?menu_func=" . $enc_function . $breadcrumb;
				switch ($menu_data[$j]['level'])
				{
					case 1:
						switch ($menu_data[$j+1]['level'])
						{
							case 1:
								echo '        <li class="nav-item">' . PHP_EOL;
								echo '          <a class="nav-link" href="' . $menu_href . '">' . $menu_l1 . '</a>' . PHP_EOL;
								echo '        </li>' . PHP_EOL;
						
								break;
							case 2:
								echo '        <li class="nav-item dropdown">' . PHP_EOL;
								echo '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . PHP_EOL;
								echo $menu_l1 . PHP_EOL;
								echo '          </a>' . PHP_EOL;
								echo '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">' . PHP_EOL;
							break;
							case 3:
							//	echo "level equals 3, ERROR";
								break;
						}
						break;
					case 2:
						switch ($menu_data[$j+1]['level'])
						{
							case 1:
								echo '            <li><a class="dropdown-item" href="' . $menu_href .'">' . $menu_l2 . '</a></li>' . PHP_EOL;
								echo '          </ul>' . PHP_EOL;
							    echo '        </li>' . PHP_EOL;
							   	break;
							case 2:
								echo '            <li><a class="dropdown-item" href="' . $menu_href .'">' . $menu_l2 . '</a></li>' . PHP_EOL;
								break;
							case 3:
							//	echo "level equals 3, ERROR";
								break;
						}
	
						break;
					case 3:
						switch ($menu_data[$j+1]['level'])
						{
							case 1:
							//	echo "level equals 1";
								break;
							case 2:
							//	echo "level equals 2";
								break;
							case 3:
							//	echo "level equals 3, ERROR";
								break;
						}
						break;
				}
	
			}
			// laatste entry
			$last=count($menu_data)-1;
			$level = $menu_data[$last]['level'];
			$menu_l1 = $menu_data[$last]['menu_l1'];
			$menu_l2 = $menu_data[$last]['menu_l2'];
			$menu_l3 = $menu_data[$last]['menu_l3'];
			$function = $menu_data[$last]['function'];
			$enc_function=base64_url_encode($function, $_SESSION['secret']);
			$breadcrumb="&bc1=" . $menu_l1 . "&bc2=" . $menu_l2 . "&bc3=" . $menu_l3;
			$menu_href = $GLOBALS['App_HomeHref'] . "?menu_func=" . $enc_function . $breadcrumb;
			switch ($level)
			{
				case 1:
					echo '        <li class="nav-item">' . PHP_EOL;
					echo '          <a class="nav-link" href="' . $menu_href . '">' . $menu_l1 . '</a>' . PHP_EOL;
					echo '        </li>' . PHP_EOL;
				    break;
				case 2:
					echo '            <li><a class="dropdown-item" href="' . $menu_href .'">' . $menu_l2 . '</a></li>' . PHP_EOL;
					echo '          </ul>' . PHP_EOL;
					echo '        </li>' . PHP_EOL;
				break;
				case 3:
					break;
			}
		}
		else
		{
			echo formatUserMessage("menu file not found", "R");
		}
	}
	else
	{
		echo formatUserMessage("menu folder not found", "R");
	}
}
function breadcrumbsBS($menu_l1=Null, $menu_l2=Null, $menu_l3=Null)
{
	$href= $GLOBALS['App_HomeHref'];
	echo '<nav aria-label="breadcrumb">' . PHP_EOL;
	echo '  <ol class="breadcrumb">' . PHP_EOL;

	if ($menu_l3 != "")
	{
		echo '<li class="breadcrumb-item"><a href="' . $href . '">Home</a></li>' . PHP_EOL;	
		echo '<li class="breadcrumb-item"><a href="#">' . $menu_l1 . '</a></li>' . PHP_EOL;	
		echo '<li class="breadcrumb-item"><a href="#">' . $menu_l2 . '</a></li>' . PHP_EOL;	
		echo '<li class="breadcrumb-item active" aria-current="page">' . $menu_l3 . '</li>' . PHP_EOL;
	}
	elseif ($menu_l2 != "")
	{
		echo '<li class="breadcrumb-item"><a href="' . $href . '">Home</a></li>' . PHP_EOL;	
		echo '<li class="breadcrumb-item active">' . $menu_l1 . '</li>' . PHP_EOL;	
		echo '<li class="breadcrumb-item active" aria-current="page">' . $menu_l2 . '</li>' . PHP_EOL;
	}
	elseif ($menu_l1 != "")
	{
		echo '<li class="breadcrumb-item"><a href="' . $href . '">Home</a></li>' . PHP_EOL;
		echo '<li class="breadcrumb-item active" aria-current="page">' . $menu_l1 . '</li>' . PHP_EOL;
	}
	else
	{
		echo '<li class="breadcrumb-item active" aria-current="page">Home</li>' . PHP_EOL;
	}
	echo '  </ol>' . PHP_EOL;
	echo '</nav>' . PHP_EOL;

}

function menu_function($functionname, $argument="")
{
	$param_arr=[$argument];
	if (function_exists($functionname))
	{
		try
		{
			call_user_func_array($functionname, $param_arr);
		}
		catch(Exception $e)
		{
			echo "Exception caught with message: " . $e->getMessage() . "\n";
		}
	}
	else
	{
		echo 'Menu-function "' . $functionname . '" not found';		
	}
}
//
function base64_url_encode($input, $secret)
{
	return strtr(base64_encode($secret . $input), '+/=', '-_,');
}

function base64_url_decode($input, $secret)
{
	return substr(base64_decode(strtr($input, '-_,', '+/=')),strlen($secret),(strlen($input)-strlen($secret)));
}

function page_footer($loggedIn=False)
{
	echo '</div><!-- /.container -->' . PHP_EOL;
	if ($loggedIn == true)
	{
		echo '<nav class="navbar fixed-bottom navbar-expand-md py-0 navbar-light" style="background-color:' . $GLOBALS['App_BottomNavbarColor'] . ';">' . PHP_EOL;
	    echo '  <div class="container-fluid">' . PHP_EOL;
		echo '    <a class="navbar-brand" href="#TopOfPage">' . $GLOBALS['App_Name'] . ', ' . $GLOBALS['App_Description'] . '</a>' . PHP_EOL;
	    echo '    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFooter" aria-controls="navbarFooter" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL;
	    echo '      <span class="navbar-toggler-icon"></span>' . PHP_EOL;
	    echo '    </button>' . PHP_EOL;
	    echo '    <div class="collapse navbar-collapse" id="navbarFooter">' . PHP_EOL;
	    echo '      <ul class="navbar-nav me-auto mb-2 mb-lg-0">' . PHP_EOL;
	 //   echo '        <li class="nav-item">' . PHP_EOL;
	//	echo '          <a class="nav-link" href="' . makeAbsSitePath("session") . 'about.php">About</a>' . PHP_EOL;
	//    echo '        </li>' . PHP_EOL;
		echo '      </ul>' . PHP_EOL;
		echo '      <ul class="nav navbar-nav navbar-right">' . PHP_EOL;
		echo '      Designed by&nbsp;&nbsp;&nbsp;' . PHP_EOL;
		echo '        <a href="' . makeAbsSitePath("session") . 'about.php"><b>Chris van Gorp</b></a>' . PHP_EOL;
		echo '      </ul>' . PHP_EOL;
	    echo '    </div>' . PHP_EOL;
	    echo '  </div>' . PHP_EOL;
	    echo '</nav>' . PHP_EOL;
	}
	else
	{
		echo '<nav class="navbar fixed-bottom  py-0 navbar-light" style="background-color:' . $GLOBALS['App_BottomNavbarColor'] . ';">' . PHP_EOL;
//	    echo '<a class="navbar-brand" href="#">' . $App_Name . '</a>' . PHP_EOL;
	    echo '<p class="navbar-brand">' . $GLOBALS['App_Name'] . ', ' . $GLOBALS['App_Description'] . '</p>' . PHP_EOL;
	    echo '</nav>' . PHP_EOL;

	}
    echo '    <!-- Optional JavaScript -->' . PHP_EOL;
    echo '    <!-- jQuery first, then Popper.js, then Bootstrap JS -->' . PHP_EOL;
	echo $GLOBALS['App_BootstrapJS'] . PHP_EOL;
	echo "  </body>" . PHP_EOL;
	echo "</html>" . PHP_EOL;
}

function formatUserMessage($text, $severity=null)
{
	$bs_attribute="text-danger";
	$message_prefix="Error";
	if ($severity === null )
	{
		$errText='<p class="' . $bs_attribute . '"><b>' . $message_prefix . ' : ' .  $text . '</b></p>';
	}
	else
	{
		switch($severity)
	    {
			case 'R':  // Red
				$bs_attribute="text-danger";
				$message_prefix="Failure";
				break;
			case 'A':  // Amber
				$bs_attribute="text-warning";
				$message_prefix="Warning";
				break;
			case 'G':  // Green
				$bs_attribute="text-success";
				$message_prefix="Success";
				break;
			default:
				break;
		}
		$errText='<p class="' . $bs_attribute . '"><b>' . $message_prefix . ' : ' .  $text . '</b></p>';

	}
	return $errText;

}
function get_GET($name, $default="")
{
	$val=filter_input(INPUT_GET, $name, FILTER_SANITIZE_SPECIAL_CHARS);
	if (is_null($val))
	{
		$val=$default;
	}
	return $val;
}
 
function get_POST($name, $default="")
{
	$val=filter_input(INPUT_POST, $name, FILTER_SANITIZE_SPECIAL_CHARS);
	if (is_null($val))
	{
		$val=$default;
	}
	return $val;
}
function get_SESSION($name, $default="")
{
	$val=filter_input(INPUT_GET, $name, FILTER_SANITIZE_SPECIAL_CHARS);
	if (!isset($_SESSION[$name]))
	{
		$val=$default;
	}
	else
	{
		$val=$_SESSION[$name];
	}
	return $val;
}
function get_Cookie($cookie_name, $default="", $set = False)
{
	if(!isset($_COOKIE[$cookie_name]))
	{
	    $cookie_value=$default;
	    if ($set)
	    {
	    	set_Cookie($cookie_name, $default); 
		}
	} 
	else
	{
	    $cookie_value= $_COOKIE[$cookie_name];
	}
	return $cookie_value;
}

function set_Cookie($cookie_name, $value)
{
   	setcookie($cookie_name, $value, time() + (86400 * 30), "/"); // 86400 = 1 day
   	usleep(500);
}

?>