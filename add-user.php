<?php
$user = $_POST["user"];
$name = $_POST["name"];
$pwd = $_POST["pwd"];
$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT name FROM login_user WHERE username='" . $user . "'";
$result = $conn->query($sql);
if($result->num_rows > 0){
	echo "user already exists.";
}else{
$sql = "INSERT INTO login_user (username,name,password) VALUES(?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss",$user,$name,$pwd);
$stmt->execute();
$stmt->close();
$conn->close();
echo "Successfully signed up";}
?>
