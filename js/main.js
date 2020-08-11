$(document).ready(function(){
	
	$("#active-chat").css("background-image","url('background.png')");
	$("#active-chat").hide();
	$("#add-friend").click(function(){
		let user = $("#user").text();
		let friend = $("#friend").val();
		let dataString = "user=" + user + "&friend=" + friend;

		$.ajax({
		      type: "POST",
		      url: "api/add-friend.php",
		      async: false,
		      data: dataString,
		      success: function(result){
		      	if(result=="friend added"){
		      		location.reload();
		      	}
		        $("#info").html(result);
		        $('.toast').toast({delay:3000});
		        $('.toast').toast('show');
		      },
		      error: function(xhr,result,error){
		        $("#info").text("Some error occured");
		      }
	      });
	});


	$("#btn-send").click(function(){
		let user = $("#user").text();
		let friend = $("#friend-list div.activ").attr("id");

		let msg = $('input[placeholder="Type a message"]').val();
		if(msg=="")return ;

		var dataObj = {"user":user,"friend":friend,"msg":msg};
		let convo_id = user + "_" + friend;
		$('input[placeholder="Type a message"]').val("");
		msg = msg.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		$.ajax({
			type: "POST",
			url: "api/send-msg.php",
			data: JSON.stringify(dataObj),
			contentType: 'application/json',
			success: function(result){
				let msg_html = msg + '</span> &nbsp;&nbsp; <small>' + result + '</small>';
				$("#" + friend + " #last-msg").html(msg);
				$("#" + friend + " #last-msg-time").html('<small>' + result + '</small>');
				msg_html = '<div class="text float-right" style="max-width:75%"><p class="card-header p-1 m-1 text-dark chat-right"' + 
							' style="overflow-wrap: break-word;"><span>' +
							msg_html + '</p></div><div class="clearfix"></div>';
				$("#" + convo_id).append(msg_html);
				$("#active-chat").scrollTop($("#active-chat").prop("scrollHeight"));
			},
			error: function(xhr,result,error){
				//$("#info").text("Some error occured");
			}
		});
	});
	$('input[placeholder="Type a message"]').keydown(function(e){
		if(e.which==13){
			$("#btn-send").trigger("click");
		}
	});
	$('input[placeholder="Add a friend"]').keydown(function(e){
		if(e.which==13){
			$("#add-friend").trigger("click");
		}
	});
	
});

var friend_lists = $("#friend-list div");
for(let i = 0; i < friend_lists.length; i+=1){
	let f_id = friend_lists[i].getAttribute("id");
	let msg = $("#" + $("#user").text() + "_" + f_id + " p span").last().text();
	msg = msg.replace(/</g, '&lt;').replace(/>/g, '&gt;');
	$("#" + f_id + " #last-msg").html(msg);
	let msg_time = $("#" + $("#user").text() + "_" + f_id + " p small").last().html();
	if(msg_time)
	$("#" + f_id + " #last-msg-time").html('<small>' + msg_time + '</small>');
}
refresh_msg();
function refresh_msg() {
	let user = $("#user").text();
	let dataString = "user=" + user;
	$.ajax({
		type: "POST",
		url: "api/refresh-msg.php",
		data: dataString,
		success: function(result){
			if(result=="empty")return ;
			let myObj = JSON.parse(result);
			for(let i = 0; i < myObj.length; i+=1){
				let msg = myObj[i].message;
				msg = msg.replace(/</g, '&lt;').replace(/>/g, '&gt;');
				let msg_html = msg + '</span> &nbsp;&nbsp; <small>' + myObj[i].msg_time + '</small>';
				$("#" + myObj[i].sender + " #last-msg").html(msg);
				$("#" + myObj[i].sender + " #last-msg-time").html('<small>' + myObj[i].msg_time + '</small>');
				msg_html = '<div class="text float-left" style="max-width:75%"><p class="card-header p-1 m-1 text-dark chat-left"' + 
							' style="overflow-wrap: break-word;"><span>' +
							msg_html + '</p></div><div class="clearfix"></div>';
				$("#" + user + "_" + myObj[i].sender).append(msg_html);
			}
			$("#active-chat").scrollTop($("#active-chat").prop("scrollHeight"));
			document.getElementById("myAudio").play();
		},
		error: function(xhr,result,error){
			//$("#info").text("Some error occured");
		}
	});
}
var r_msg = setInterval(refresh_msg,3000);

function open_convo(friend_id,convo_id) {
	if($("#"+friend_id+".activ").length){
		return;
	}
	$("#active-chat").show();
	$("#friend-list div.activ").removeClass("activ");
	$("#"+friend_id).addClass("activ");
	$("#active-chat div.opened").removeClass("opened");
	$("#"+convo_id).addClass("opened");

	$("#head-name").addClass("opened");
	$("#head-name div").text($("#friend-list div.activ h6").text());
	$(".d-none").removeClass("d-none");
	//$("#active-chat").css("height","498px");

	$("#active-chat").scrollTop($("#active-chat").prop("scrollHeight"));
}

$(document).ready(function(){
  $("#search-chat").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#friend-list div h6").filter(function() {
      $(this).parent().toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  //Preloader
	preloaderFadeOutTime = 500;
	function hidePreloader() {
		var preloader = $('.spinner-wrapper');
		preloader.fadeOut(preloaderFadeOutTime);
	}
	hidePreloader();
});
