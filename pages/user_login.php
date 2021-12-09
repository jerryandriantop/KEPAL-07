<?php
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require '../vendor/autoload.php';

    $mail = new PHPMailer(true);

    session_start();
    include('../config/dbconn.php');
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        $user_unsafe=$_POST['username'];
        $pass_unsafe=$_POST['password'];

        $user = mysqli_real_escape_string($dbconn,$user_unsafe);
        $pass1 = mysqli_real_escape_string($dbconn,$pass_unsafe);

        // $pass=md5($pass1);
        $salt="a1Bz20ydqelm8m1wql";
        // $pass=$salt.$pass;
        $salted_password = hash('sha256', $pass1.$salt);

        date_default_timezone_set('Asia/Manila');
        $date = date("Y-m-d H:i:s");

        // $query = mysqli_query($dbconn,"SELECT * FROM `users` WHERE username='$user'");

        $query=mysqli_query($dbconn,"SELECT * FROM `users` WHERE username='$user' AND password='$salted_password'"); // 
        $res=mysqli_fetch_array($query);
        $id=$res['user_id'];
        
        $email_user = $res['email'];

        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $updated = mysqli_query($dbconn, "UPDATE users SET otp_code = $verification_code WHERE user_id = $id");

        try {

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; //SMTP::DEBUG_SERVER                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'jerryandrianto22@gmail.com';                     //SMTP username
            $mail->Password   = 'iaiumxiwygdnvfkg';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
            //Recipients
            $mail->setFrom('jerryandrianto22@gmail.com', 'Admin');
            $mail->addAddress($email_user);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
    
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Kode OTP Electric Shop';
            $mail->Body    = 'Kode OTP anda adalah: <b style="font-size: 30px">'. $verification_code .'</b>';
    
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            die($e);
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        if (mysqli_num_rows($query)<1){
            $_SESSION['msg']="Login Failed, User not found!";
            header('Location:user_login_page.php');
        }

        else{
            $res=mysqli_fetch_array($query);
            // $_SESSION['id']=$res['user_id'];
            $_SESSION['to_otp']=$id;
            header('Location: get_otp_code.php');
            // header('Location: user_index.php');
            
            
            }
        }
?>
