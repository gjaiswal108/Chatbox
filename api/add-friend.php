<?php
session_start();
if(count($_POST) > 0 && ($_SESSION["username"]==$_POST["user"])){
	$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	$_POST["friend"] = strtolower($_POST["friend"]);
	if($_POST["user"]==$_POST["friend"]){
		echo "Can't add your own id";
		return ;
	}
	$sql = "SELECT user1,user2 FROM friends WHERE user1='" . $_POST["user"] . "' AND user2='" . $_POST["friend"] . "'";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		echo "friend already added";
	}else{
		$sql = "SELECT name FROM login_user WHERE username='" . $_POST["friend"] . "'";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			$sql = "INSERT INTO friends (user1,user2) VALUES(?,?)";
			$stmt = $conn->prepare($sql);
			$user1 = $_POST["user"];
			$user2 = $_POST["friend"];
			$stmt->bind_param("ss",$user1,$user2);
			$stmt->execute();
			$stmt->close();
			echo "friend added";
		}else{
			echo "username doesn't exist";
		}
	}
	$conn->close();
}
?>