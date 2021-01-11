<?php 
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/paths.php");
require(makeAbsRootPath("root") . "app-defaults.php");
require(makeAbsRootPath("session") . "connection.php");
$email = "";
$name = "";
$errors = array();
$ipaddress=get_ipaddress();
$hostname=get_remotehostname($ipaddress);


if(isset($_SESSION['session_time']) )
{
    $session_life = time() - $_SESSION['session_time']; 
    if($session_life > $SessionRefresh)
    {
        session_regenerate_id();
    }
    if($session_life > $SessionTimeout * 2 )
    {
        header('location: /session/session-inactive.php');         
    }
}
$_SESSION['session_time']=time();
//
if(isset($_POST['signup']))
{
    $name =strip_tags(stripslashes($_POST['name']));
    $email =strip_tags(stripslashes($_POST['email']));
    $password = strip_tags(stripslashes($_POST['password']));
    $cpassword =strip_tags(stripslashes($_POST['cpassword']));
    if($password !== $cpassword)
    {
        $errors['password'] = "Confirm password not matched!";
    }
    elseif (validPassword($password) == FALSE)
    {
        $errors['password']= $SessionPwdRequirements;
    }

    $query = "SELECT * FROM $SessionTableUsers WHERE email=:email";
    if ($stmt = $con->prepare($query))
    {
        $stmt->bindParam(':email', $email);
        $stmt->execute();   
        $data=$stmt->fetchAll();
        if (count($data) > 0)
        {
            $errors['email'] = "The email that you have entered does already exist!";
        }
    }
    if(count($errors) === 0){
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "notverified";
        $role=$SessionDefaultSignupRole;
        $now=db_now();
        $code_time=time(); 
        $query = "INSERT INTO $SessionTableUsers (name, email, role, password, code, code_time, status)
                   values(:name, :email, :role, :encpass, :code, :code_time, :status)";
        if ($stmt = $con->prepare($query))
        {
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':encpass', $encpass);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':code_time', $code_time);
            $stmt->bindParam(':status', $status);
            $res=$stmt->execute();
        }
        if($res){
            $subject = $App_Name . ", Email Verification Code";
            $message = "Your verification code is $code";
            $sender = "From: " . $App_Email;
            if(mail($email, $subject, $message, $sender)){
                $info = "We've sent a verification code to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['password'] = $password;
                header('location: user-otp.php');
                exit();
            }else{
                $errors['otp-error'] = "Failed while sending code!";
            }
        }else{
            $errors['db-error'] = "Failed while inserting data into database!";
        }
    }
}
    //if user click verification code submit button
if(isset($_POST['check']))
{
    $_SESSION['info'] = "";
    $otp_code =strip_tags(stripslashes($_POST['otp']));
    $expiry_time=time() - $SessionOTPExpiry;
    $query = "SELECT * FROM $SessionTableUsers WHERE code=:otp_code ";
    if ($stmt = $con->prepare($query))
    {
        $stmt->bindParam(':otp_code', $otp_code);
        $res=$stmt->execute();
        $data=$stmt->fetchAll();
        if (count($data) > 0)
        {
            $fetch_code = $data[0]['code'];
            $email = $data[0]['email'];
            $name = $data[0]['name'];
            $role = $data[0]['role'];
            $code_time=$data[0]['code_time'];
            if ($code_time > $expiry_time)
            {
              //  echo "code not expired";
                $code = 0;
                $status = 'verified';
                $query = "UPDATE $SessionTableUsers SET code = :code, status = :status WHERE code = :fetch_code";
                if ($stmt = $con->prepare($query))
                {
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':status', $status);
                    $stmt->bindParam(':fetch_code', $fetch_code);
                    $res=$stmt->execute();
                    if($res)
                    {
                        $_SESSION['name'] = $name;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role;
                        $_SESSION['secret'] = bin2hex(random_bytes(20));
                        $_SESSION['session_time']=time();
                        log_user_access($email, $ipaddress, $hostname, "signup succesfull");
                        header('location: ../home.php');
                        exit();
                    }
                    else
                    {
                        $errors['otp-error'] = "Failed while updating code!";
                    }
                }
            }
            else
            {
                //echo "code is expired";
                $query = "DELETE FROM  $SessionTableUsers WHERE code = :fetch_code";
                if ($stmt = $con->prepare($query))
                {
                    $stmt->bindParam(':fetch_code', $fetch_code);
                    $res=$stmt->execute();
                    if($res)
                    {
                        $_SESSION['name'] = "";
                        $_SESSION['email'] = "";
                        log_user_access($email, $ipaddress, $hostname, "signup canceled");
                        $info = "signup code expired, try again or contact the administrator";
                        $_SESSION['signup-failed'] = $info;
                        header('location: login-user.php');                    }
                    else
                    {
                        $errors['otp-error'] = "Failed while canceling signup!";
                    }
                }
            }
        }
        else
        {
            $errors['otp-error'] = "Your code is invalid!";
        }
    }
    else
    {
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

    //if user click login button
if(isset($_POST['login']))
{
    $email = strip_tags(stripslashes( $_POST['email']));
    $password = strip_tags(stripslashes($_POST['password']));

    $query = "SELECT * FROM $SessionTableUsers WHERE email=:email";
    if ($stmt = $con->prepare($query))
    {
        $stmt->bindParam(':email', $email);
        $stmt->execute();   
        $data=$stmt->fetchAll();
        if (count($data) == 1)
        {
            $fetch_pass = $data[0]['password'];
            if(password_verify($password, $fetch_pass))
            {
                $expiry_time=time() - $SessionPwdExpiry;
                $_SESSION['email'] = $email;
                $status = $data[0]['status'];
                $code= $data[0]['code'];
                $role = $data[0]['role'];
                $name = $data[0]['name'];
                $pwdChanged=$data[0]['dt_pwchanged'];
                if (strtotime($pwdChanged) < $expiry_time)
                {  // password has expired
                    //echo 'pwd is expired';
                    $_SESSION['email'] = $email;
                    $info = "Your password expired. Please create a new password that you don't use on any other site.";
                    $_SESSION['info'] = $info;
                    header('location: new-password.php');
                    exit();
                }
                if (validPassword($password) == FALSE)
                {
                    $_SESSION['info'] = $SessionPwdRequirements;
                    header('location: new-password.php');
                    exit();
                }
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['secret'] = bin2hex(random_bytes(20));
                $_SESSION['session_time']=time();
                // update last login in user table and reset status to verified
                if($status == 'verified' AND $code == 0)
                {
                    $query = "UPDATE $SessionTableUsers SET dt_lastlogin = :dt_lastlogin WHERE email = :email";
                }
                else
                {
                    $query = "UPDATE $SessionTableUsers SET dt_lastlogin = :dt_lastlogin, code =0, status = 'verified' WHERE email = :email";
                }
                $now=db_now();
                if ($stmt = $con->prepare($query))
                {
                    $stmt->bindParam(':dt_lastlogin', $now);
                    $stmt->bindParam(':email', $email);
                    $res=$stmt->execute();
                }
                log_user_access($email, $ipaddress, $hostname, "login succesfull");
                header('location: ../home.php');
            
            }
            else
            {
                log_user_access($email, $ipaddress, $hostname, "login failed");
                if (checkbrute($email, $ipaddress))
                {
                    $errors['email'] = "brute force detected!";
                    log_user_access($email, $ipaddress, $hostname, "login failed, brute force detected");
                }
                else
                {
                    $errors['email'] = "Incorrect email or password!";
                }
            }
        }
        else
        {
            if ($GLOBALS['SessionRegisterAllowed'])
            {
                $errors['email'] = "It's look like you're not yet a member! Click on the bottom link to signup.";
            }
            else
            {
                $errors['email'] = "It's look like you're not yet a member! Please contact the administrator to be added as a member.";
            }
        }
    }
    
}

    //if user click continue button in forgot password form
if(isset($_POST['check-email']))
{
    $email = strip_tags(stripslashes( $_POST['email']));
    $query = "SELECT * FROM $SessionTableUsers WHERE email=:email";
    if ($stmt = $con->prepare($query))
    {
        $stmt->bindParam(':email', $email);
        $stmt->execute();   
        $data=$stmt->fetchAll();
        if (count($data) > 0)
        {
            $code = rand(999999, 111111);
            $query = "UPDATE $SessionTableUsers SET code = $code WHERE email=:email";
            if ($stmt = $con->prepare($query))
            {
                $stmt->bindParam(':email', $email);
                $res=$stmt->execute();   
                

                if($res){
                    $subject = "Password Reset Code";
                    $message = "Your password reset code is $code";
                    $sender = "From: " . $App_Email;
                    if(mail($email, $subject, $message, $sender)){
                        $info = "We've sent a passwrod reset otp to your email - $email";
                        $_SESSION['info'] = $info;
                        $_SESSION['email'] = $email;
                        header('location: reset-code.php');
                        exit();
                    }else{
                        $errors['otp-error'] = "Failed while sending code!";
                    }
                }else{
                    $errors['db-error'] = "Something went wrong!";
                }
            }
        }
        else
        {
            $errors['email'] = "This email address does not exist!";
        }
    }
}

    //if user click check reset otp button
if(isset($_POST['check-reset-otp']))
{
    $_SESSION['info'] = "";
    $otp_code =strip_tags(stripslashes($_POST['otp']));
    $query = "SELECT * FROM $SessionTableUsers WHERE code=:otp_code";
    if ($stmt = $con->prepare($query))
    {
        $stmt->bindParam(':otp_code', $otp_code);
        $stmt->execute();   
        $data=$stmt->fetchAll();
        if (count($data) > 0)
        {
            $email = $data[0]['email'];
            $_SESSION['email'] = $email;
            $info = "Please create a new password that you don't use on any other site.";
            $_SESSION['info'] = $info;
            header('location: new-password.php');
            exit();
        }
    }
    else
    {
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

    //if user click change password button
if(isset($_POST['change-password']))
{
    $_SESSION['info'] = "";
    $password = strip_tags(stripslashes($_POST['password']));
    $cpassword =strip_tags(stripslashes($_POST['cpassword']));
    if($password !== $cpassword){
        $errors['password'] = "Confirm password not matched!";
    }
    elseif (validPassword($password) == FALSE)
    {
        $errors['password']= $SessionPwdRequirements;
    }
    else
    {
        $code = 0;
        $email = $_SESSION['email']; //getting this email using session
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $now=db_now();
        // get existing pwd to see if user chooses new pwd
        $query = "SELECT `password` FROM $SessionTableUsers WHERE email=:email";
        if ($stmt = $con->prepare($query))
        {
            $stmt->bindParam(':email', $email);
            $res=$stmt->execute();   
            if($res)
            {
                $data=$stmt->fetchAll();
                $oldPwd=$data[0]['password'];
            }
          //  echo 'pwd is ' . $oldPwd . ",  new pwd is " . $encpass;
            if ($encpass == $oldPwd)
            {
//echo 'old password re use';
            }
        }
        $query = "UPDATE $SessionTableUsers SET code=:code, password=:encpass, dt_pwchanged=:dt_pwchanged WHERE email=:email";
        if ($stmt = $con->prepare($query))
        {
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':encpass', $encpass);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':dt_pwchanged', $now);
            $res=$stmt->execute();   
            if($res)
            {
                $info = "Your password changed. Now you can login with your new password.";
                $_SESSION['info'] = $info;
                header('Location: password-changed.php');
            }
            else
            {
                $errors['db-error'] = "Failed to change your password!";
            }
        }
    }
}
    
   //if login now button click
if(isset($_POST['login-now']))
{
        header('Location: login-user.php');
}


function validPassword($password)
{
// Validate password strength
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < $SessionPwdMinLength)
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function log_user_access($username, $ipaddress, $hostname, $status)
{
    $now=db_now();
    if ($username == "")
    {
        $username="unknown";
    }
	$query = "INSERT INTO `" . $GLOBALS['SessionTableUsersLog'] . "`
              (`logdatetime`, `ipaddress`, `hostname`, `username`, `status`)
              VALUES(?, ?, ?, ?, ?)";
	//$db_Connection->setAttribute(PDO::ATTR_EMULATE_PREPARES,false); 
	if ($stmt = $GLOBALS['con']->prepare($query))
	{
		$stmt->execute(array($now, $ipaddress, $hostname, $username, $status));   
	}
	else
	{
		echo "in Else part <br>" ;
		die();
	}
}

function db_now_utc()
{
    $now_utc=gmdate("Y/m/d H:i:s"); // Prints "2011/03/20 07:16:17
    return $now_utc;
}

function db_now()
{
    $now = new DateTime();
    $now= $now->format('Y/m/d H:i:s'); // Prints "2011/03/20 07:16:17
    return $now;
}

function checkbrute($username, $ipaddress) {

    $now = db_now();
    $valid_attempts = 3;
	$valid_interval = 120; 
//    $query = "SELECT `logdatetime`  FROM `" . $db_user_log_table . "` WHERE (username=:username  OR ipaddress=:ipaddress AND `status` LIKE 'login failure%' AND DATE_SUB('" . $now . "',INTERVAL " . $valid_interval . " SECOND) <= `logdatetime`";
    $query = "SELECT `logdatetime`  FROM `" . $GLOBALS['SessionTableUsersLog'] . "` WHERE (`username` = '" . $username . "' OR `ipaddress` = '" . $ipaddress . "') AND `status` LIKE 'login fail%' AND DATE_SUB('" . $now . "',INTERVAL " . $valid_interval . " SECOND) <= `logdatetime`";
	if ($stmt = $GLOBALS['con']->prepare($query))
	{
	//	$stmt->execute(array($username, $ipaddress));    // Execute the prepared query.
		$stmt->execute();    
		$data=$stmt->fetchAll();
		if (count($data) > $valid_attempts)
		{
		   return true;
		} else {
		   return false;
		}
	}
}


function LogFileSQL($text, $usermessage,$interactive=True)
{
	$myFile = $GLOBALS['App_logging'] . "/log_SQL_Errors.txt";
    // handle macros in usermessage
    $now= db_now();
	$macros = array("<datetime>", "<user>", "<ipaddress>");
	$values = array($now, "--user-", "--ip-address-");
	$usermessage = str_replace($macros, $values, $usermessage);
	$text=$now . " " . $text;
	if ($interactive == True)
	{
		if ($usermessage <> "") 
		{
		  echo $usermessage;
		  $text.= PHP_EOL . " User message issued = " . $usermessage . PHP_EOL;
		}
		else 
		{
		  $text.= PHP_EOL . " No user message issued." . PHP_EOL;
		}
	}
	// write to logfile
    $fh = fopen($myFile, 'a') or die("can't open logfile");
	fwrite($fh, PHP_EOL . $text);
    fclose($fh);
}

function get_ipaddress()
{
 if(!empty($_SERVER['HTTP_CLIENT_IP']))
 {
   $ip = $_SERVER['HTTP_CLIENT_IP'];
 }
 elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
 {
   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
 }
 else
 {
   $ip = $_SERVER['REMOTE_ADDR'];
 }
 return $ip;
}

function get_remotehostname($ipaddress)
{
	$hostname = gethostbyaddr($ipaddress);
	return $hostname;
}
function get_domainname($ipaddress)
{
	
	
	
}

?>