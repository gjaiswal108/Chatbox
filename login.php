<?php
session_start();
$message="";
if(count($_POST)>0) {
	
	$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	$_POST["uname"] = strtolower($_POST["uname"]);
	$sql = "SELECT * FROM login_user WHERE username=? AND password=BINARY ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ss",$_POST["uname"],$_POST["pwd"]);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows == 1) {
		$row=$result->fetch_assoc();
		$_SESSION["username"] = $row['username'];
		$_SESSION["name"] = $row['name'];
	} else {
		$message = "Invalid Username or Password!";
	}
	$stmt->close();
	$conn->close();
}
if(isset($_SESSION["name"])) {
header("Location:index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Please Login</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</head>
<body style="background-color: #8fd8ff">

<div class="container pt-5" style="width:30%">
	<div class="jumbotron bg-light shadow-lg p-5 rounded">
		<form name="frmUser" id='login_form' method="post" action="">
			<div class="message text-center text-danger"><?php if($message!="") { echo $message; echo '<script type="text/javascript">
								setTimeout(()=>$(".message").fadeOut("slow"),2000);</script>'; } ?>
			</div>
			<h2 class="text-center">Login</h2>
			<div class="form-group">
			    <label for="uname">Username:</label>
			    <input type="text" class="form-control" placeholder="Enter username" id="uname" name="uname">
			</div>
			<div class="form-group">
			    <label for="pwd">Password:</label>
			    <input type="password" class="form-control" placeholder="Enter password" id="pwd" name="pwd">
			</div>
			<div align="center">
				<input type="submit" class="btn btn-primary mr-2" value="Login">
				<button type="reset" class="btn btn-primary ml-2" onclick="$('.message').hide()">Reset</button>
			</div>
		</form>
	</div>
</div>
<div class="container pt-5" style="width:30%">
	<p>For a quick demo, you can use following user-id:</p>
	<ul class="list-group">
		<li class="list-group-item list-group-item-primary"><b>username:</b> user1, <b>password:</b> user1@2020</li>
		<li class="list-group-item list-group-item-primary"><b>username:</b> user2, <b>password:</b> user2@2020</li>
	</ul>
</div>
</body>
</html>