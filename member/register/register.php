
<?php session_start(); ?>
<?php
include("../mysql_connect.php");

$id = $_POST['id'];
$pw = $_POST['pw'];
$pwcheck = $_POST['pwcheck'];
$username = $_POST['username'];
$nickname = $_POST['nickname'];
$email = $_POST['email'];

$register_result = array();
//驗證碼

/*		$Code = $_REQUEST["Turing"]; 
		if (!isset($_SESSION['turing_string'])){
			$ok = 1; 
		}
		else if(strtoupper($_SESSION['turing_string'])==strtoupper($Code)){
			$ok = 1;
		}
		else{
		   	$ok = 0;
			echo "<script type= 'text/javascript'>";
			echo "alert('您的驗證碼輸入錯誤');";
			echo "window.location.href= 'register.php';";
			echo "</script>";
		}*/
//判斷帳號密碼是否為空值
//確認密碼輸入的正確性
if($id != null && $pw != null && $pwcheck != null && $pw == $pwcheck){
		header('Content-Type: application/json; charset=utf-8');
		
		//檢查
		$checkid = "Select * From mem_db Where id = '$id'";
		$result = mysql_query($checkid);
		if(mysql_fetch_array($result)){
			/*echo "<script type='text/javascript'>";
			echo "alert('此帳號已有人使用');";
			echo "window.location.href = 'register.php';";
			echo "</script>";*/
            
            $register_result["result"] = "fail";
            $register_result["msg"] = "帳號已有人使用";
            echo json_encode($register_result);
            return;
		}
		/*
		$checkemail = "Select * From mem_db Where email = '$email'";
		$result = mysql_query($checkemail);
		if(mysql_fetch_array($result)){
			echo "<script type='text/javascript'>";
			echo "alert('此信箱已有人使用');";
			echo "window.location.href = 'register.php';";
			echo "</script>";
		}
		 */
		 
        //新增資料進資料庫語法
        $pw = md5($pw);
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($_SERVER['HTTP_VIA'])){
			$ip =$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip =$_SERVER['REMOTE_ADDR'];
		}
        $sql = "insert into mem_db (id, pw, username, nickname, email, ip) values ('$id', '$pw', '$username', '$nickname', '$email', '$ip')";       
        
        if(mysql_query($sql)){
        	$sql = "select * from mem_db where id = '$id'";
            $result = mysql_query($sql);
            $row = @mysql_fetch_array($result);
        	//register time
        	$TW_times=date("Y-m-d H:i:s", mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')));
			$time_query="update mem_db set register_time='$TW_times' where id='$id'";
			mysql_query($time_query);
			
            $_SESSION['id'] = $id;
            $_SESSION['foremailid'] = $id; //for email check id
            $_SESSION['email'] = $email;
            $_SESSION['No'] = $row['No'];
            $_SESSION['check_login'] = 1;
			
			include("mailsender.php");
            
            /*echo "<script type='text/javascript'>";
			echo "alert('註冊成功, 請至信箱收取認證信以啟用帳號');";
			echo "window.location.href = 'mailsender.php';";
			echo "</script>";*/
            $register_result["result"] = "success";
            $register_result["msg"] = "註冊成功, 請至信箱收取認證信以啟用帳號";
			$register_result["member_data"]["username"] = $username;
            $register_result["member_data"]["nickname"] = $username;
            $register_result["member_data"]["photo"] = "user_images/thumb/default.png";
			
            echo json_encode($register_result);
            return;
        }
        else{
            /*echo "<script type='text/javascript'>";
			echo "alert('註冊失敗');";
			echo "window.location.href = 'login.php';";
			echo "</script>";*/
			$register_result["result"] = "fail";
            $register_result["msg"] = "未知原因註冊失敗 請稍後重試";
            echo json_encode($register_result);
            return;
        }
}
else{
        echo "<script type='text/javascript'>";
		echo "alert('您無權限觀看此頁面');";
		echo "window.location.href = 'http://movement.ee.ncku.edu.tw/';";
		echo "</script>";
}
?>
