<?php
    include('../config/dbconn.php');
    session_start();
    $user_id = $_SESSION['to_otp'];

    if (isset($_POST["submit"])) {
        $query = mysqli_query($dbconn, "SELECT * FROM users WHERE user_id = $user_id");
        $data = mysqli_fetch_array($query);

        $otp_code = $data['otp_code'];
        $otp_code_from_user = $_POST["otp_code"];
        
        if($otp_code == $otp_code_from_user) {
            $_SESSION['id']=$user_id;
            header('Location: user_index.php');
            $remarks="has logged in the system at ";  
            mysqli_query($dbconn,"INSERT INTO logs(user_id,action,date) VALUES('$id','$remarks','$date')")or die(mysqli_error($dbconn));
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Kode OTP telah dikirimkan ke email anda</p>
    <form action="get_otp_code.php" method="POST">
        <label for="otp_code"></label>
        <input type="text" name="otp_code" id="otp_code" placeholder="Masukkan Kode OTP">
        <button type="submit" name="submit">Submit</button>
    </form>
</body>
</html>

