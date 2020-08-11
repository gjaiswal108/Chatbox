<?php  
session_start();
$data = json_decode(file_get_contents("php://input"));
if($_SERVER['REQUEST_METHOD'] == "POST" && ($_SESSION["username"] == $data->user)){
	$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	$sql = "INSERT INTO unread (user1,user2,message,msg_time) VALUES(?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$user1 = $data->friend;
	$user2 = $data->user;
	$message = $data->msg;
	date_default_timezone_set('Asia/Kolkata');
	$msg_time = date('Y-m-d H:i:s');
	$stmt->bind_param("ssss",$user1,$user2,$message,$msg_time);
	$stmt->execute();
	$stmt->close();
	$sql = "INSERT INTO conversation (user1,user2,message,msg_time,sender) VALUES(?,?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$sender = 1;
	$stmt->bind_param("ssssi",$user2,$user1,$message,$msg_time,$sender);
	$stmt->execute();
	$stmt->close();
	$conn->close();
	echo date_format(date_create($msg_time),'H:i | M j');
	
}
?>