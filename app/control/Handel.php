<?php
if ($route == "login"){
    $code = $_POST["code"];//接收login.php传过来的用户输入的代码
    if ($code == $_SESSION['code']){//判断session里存的code和用户输入的code是否一致
        echo "<script>window.location='../view/login.php';alert('验证码验证成功，请重新输入！！')</script>";
    }else{
        echo "<script>window.location='../view/login.php';alert('验证码不正确，请重新输入！！')</script>";
    }

}
?>