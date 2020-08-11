<?php
session_start();
if(isset($_SESSION["name"])==0){
	header("Location:login.php");
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Chatbox</title>
<meta charset="utf-8">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet" >
<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="mybg">

<?php
if(isset($_SESSION["name"])) {
	echo '<div class="spinner-wrapper"><div class="spinner">'.
			'<div class="double-bounce1"></div>'.
			'<div class="double-bounce2"></div>'.
		 '</div></div>';
	echo '<div align="center" class="pb-3 mt-1" >';
	echo "Welcome ".$_SESSION["name"].'. Click here to <a href="logout.php" tite="Logout">Logout.</a>';
	echo '</div>';
	echo '<div style="display:none;" id="user">'.$_SESSION["username"].'</div>';
}else {
	header("Location:login.php");
}
?>

<div class="container pl-0 pr-0" >
	<div class="row" >
		<div class="col-md-4 border border-info border-right-0 jumbotron jumbotron-fluid" style="height: 600px;overflow-y: auto">
			<div class="container">
				<div class="input-group mb-1">
				    <input type="text" id="search-chat" class="form-control" placeholder="Search or start a new chat">
				    <div class="input-group-append">
				    	<button class="btn btn-primary" type="submit" ><i class="fa fa-search" aria-hidden="true"></i></button>  
				    </div>
				</div>
				<div class="input-group mb-1">
				    <input type="text" class="form-control" id="friend" placeholder="Add a friend">
				    <div class="input-group-append">
				    	<button class="btn btn-primary" type="button" id="add-friend">Add</button>  
				    </div>
				    
				</div>
				<div class="info mb-1" align="center" >
						<div class="toast" id="info">
						</div>
					<!-- <a id="cross" style="display: none;" onclick="$('#cross').hide();$('#info').text('')" href="javascript:void(0)">&times;</a> -->
				</div>
				<div class="list-group" id="friend-list">
					<?php  
					$conn = new mysqli('localhost','root','','chatbox') or die('Unable To connect');
					// Check connection
					if ($conn->connect_error) {
					  die("Connection failed: " . $conn->connect_error);
					}
					$sql = "SELECT user2 FROM friends WHERE user1='" . $_SESSION["username"] . "'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while ($row = $result->fetch_assoc()) {
							$val = $_SESSION["username"] . "_" . $row["user2"];
							$result1 = $conn->query("SELECT name from login_user WHERE username='" . $row["user2"] . "'");
							$row1 = $result1->fetch_assoc();
							echo
							'<div class="list-group-item list-group-item-action pl-1 pr-0" style="cursor:pointer;" id="' . $row["user2"] . 
							'" onclick="open_convo(this.id,'. "'" . $val . "'" . ')" >' .
								'<div class="row">' .
								'<div class="col-md-8 pl-4">' .
						    	'<h6 style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">' .
						        	$row1["name"] .
						      	'</h6>' .
						      	'<p class="card-text" id="last-msg" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"></p>' .
						      	'</div>' .
						      	'<div class="col-md-4 pl-1">' .
						      	'<span id="last-msg-time"></span>'.
						      	'</div>' .
						      	'</div>' .
						    '</div>';
						}
					}
					?>
			  	</div>
			</div>
		</div>
		<div class="col-md-8 border border-info jumbotron jumbotron-fluid p-0" style="height: 600px;">
			<div class="card bg-primary text-white rounded-0 d-none" id="head-name" style="height: 50px">
    			<div class="card-header "></div>
  			</div>
			<div class="container pt-1 pb-1" id="active-chat" style="height: 498px;overflow-y: auto;">
			    <?php  
			    $result = $conn->query($sql);
			    if($result->num_rows > 0){
					while ($row = $result->fetch_assoc()) {
						$val = $_SESSION["username"] . "_" . $row["user2"];
						$sql = "SELECT message,msg_time,sender FROM conversation WHERE user1='" . 
								$_SESSION["username"] . "' AND user2='" . $row["user2"] . "'";
						$result1 = $conn->query($sql);
						echo '<div class="conversation" id="' . $val . '">';
						if($result1->num_rows > 0){
							while ($row1 = $result1->fetch_assoc()) {
								if($row1["sender"]==0){
									echo '<div class="text float-left" style="max-width:75%">';
									echo '<p class="card-header p-1 m-1 text-dark chat-left rounded" style="word-wrap: break-word;"><span>' .
										 htmlentities($row1["message"]);
									echo '</span> &nbsp;&nbsp; <small>'. date_format(date_create($row1["msg_time"]),'H:i | M j') .'</small>'. '</p>';
									echo '</div>';
									echo '<div class="clearfix"></div>';
								}else{
									echo '<div class="text float-right" style="max-width:75%">';
									echo '<p class="card-header p-1 m-1 text-dark chat-right rounded" style="word-wrap: break-word;"><span>' .
										 htmlentities($row1["message"]);
									echo '</span> &nbsp;&nbsp; <small>'. date_format(date_create($row1["msg_time"]),'H:i | M j') .'</small>'. '</p>';
									echo '</div>';
									echo '<div class="clearfix"></div>';
								}
								
							}
							
						}
						echo '</div>';
					}
				}
				$conn->close();
			    ?>
			</div>
			<div class="input-group d-none" id="send-msg">
			    <input type="text" class="form-control rounded-0" placeholder="Type a message" style="height: 50px">
			    <div class="input-group-append">
			    	<button class="btn btn-primary" type="submit" id="btn-send"><i class="fa fa-send-o" aria-hidden="true"></i></button>  
			    </div>
			</div>
		</div>
	</div>
</div>
<footer>
	<div class="container">
        <p class="copyright text-center">&copy; Copyright <strong>Gaurav Jaiswal</strong>. All Rights Reserved</p>
	</div>
</footer>

<audio id="myAudio">
	<source src="Bell.mp3" type="audio/mpeg">
</audio>
<script src="js/main.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {

		//Preloader
		preloaderFadeOutTime = 500;
		function hidePreloader() {
		var preloader = $('.spinner-wrapper');
		preloader.fadeOut(preloaderFadeOutTime);
		}
		hidePreloader();
	});
</script>
</body>
</html>