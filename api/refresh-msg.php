<?php  
session_start();
if(count($_POST) > 0 && ($_SESSION["username"]==$_POST["user"])){
	$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	$user1 = $_POST["user"];
	$sql = "SELECT * FROM unread WHERE user1='" . $user1 . "'";
	$result = $conn->query($sql);
	$data = array();
	if($result->num_rows > 0){
		$cnt = $result->num_rows;
		$sql = "DELETE FROM unread WHERE user1='" . $user1 . "' LIMIT " . $cnt;
		$conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			$sql = "INSERT INTO conversation (user1,user2,message,msg_time,sender) VALUES(?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			array_push($data,array("message"=>$row["message"],"msg_time"=>date_format(date_create($row["msg_time"]),'H:i | M j'),"sender"=>$row["user2"]));
			$user1 = $_POST["user"];
			$user2 = $row["user2"];
			$message = $row["message"];
			$msg_time = $row["msg_time"];
			$sender = 0;
			$stmt->bind_param("ssssi",$user1,$user2,$message,$msg_time,$sender);
			$stmt->execute();
			$stmt->close();
		}
	}
	if(count($data) > 0){
		echo json_encode($data);
	}
	else{
		echo "empty";
	}
	$conn->close();
}
?>