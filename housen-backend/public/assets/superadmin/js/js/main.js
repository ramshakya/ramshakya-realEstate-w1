jQuery( function( $ ){	
	/*Action : ajax
 	* used to: submit forms
 	* Instance of: Jquery vailidate libaray
	* @JSON 
 	**/
	jQuery.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		}
	});
	$(".post-fund-button").addClass("disabled");
 	$("#staff").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	$(".post-fund-button").addClass("active");
	    },
		submitHandler: function(form){
			
			var formData = new FormData($("#staff")[0]);
			//console.log(formData);
			$.ajax({
			  	beforeSend:function(){
			  		$("#staff").find('button').attr('disabled',true);
					$("#staff").find('button>i').show(); 
			  	},
			  	url: $("#staff").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
			  		 
				  	if(response.success){
				  		if(response.to){
				  			toastr.error(response.message,'Error');
				  			//toastr.error('errors messages');
				  		}else{
				         toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}else{
							if (response.reload !='') {
				        	//location.reload();
				        }
						}
					}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#staff").find('button').attr('disabled',false);
					$("#staff").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#staff").find('button').attr('disabled',false);
	                    $("#staff").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
 	$("#add_experienceform").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			
			var formData = new FormData($("#add_experienceform")[0]);
			$.ajax({
			  	beforeSend:function(){
			  		$("#add_experienceform").find('button').attr('disabled',true);
					$("#add_experienceform").find('button>i').show(); 
			  	},
			  	url: $("#add_experienceform").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.reload !='') {
				        	location.reload();
				        }else if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#add_experienceform").find('button').attr('disabled',false);
					$("#add_experienceform").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#add_experienceform").find('button').attr('disabled',false);
	                    $("#add_experienceform").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
 	$("#listing_form").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			
			var formData = new FormData($("#listing_form")[0]);
			$.ajax({
			  	beforeSend:function(){
			  		$("#listing_form").find('button').attr('disabled',true);
					$("#listing_form").find('button>i').show(); 
			  	},
			  	url: $("#listing_form").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.redirect_url !='') {
							setTimeout(function(){
								console.log(response.redirect_url);
								location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#listing_form").find('button').attr('disabled',false);
					$("#listing_form").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#listing_form").find('button').attr('disabled',false);
	                    $("#listing_form").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
	$("#form1").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			var formData = new FormData($("#form1")[0]);
			$.ajax({
			  	beforeSend:function(){
			  		$("#form1").find('button').attr('disabled',true);
					$("#form1").find('button>i').show(); 
			  	},
			  	url: $("#form1").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
			  		console.log(response);
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.reload !='') {
				        	location.reload();
				        }else if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#form").find('button').attr('disabled',false);
					$("#form").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#form").find('button').attr('disabled',false);
	                    $("#form").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
	$("#add_skill").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			var formData = new FormData($("#add_skill")[0]);
			$.ajax({
			  	beforeSend:function(){
			  		$("#add_skill").find('button').attr('disabled',true);
					$("#add_skill").find('button>i').show(); 
			  	},
			  	url: $("#add_skill").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
			  		console.log(response);
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.reload !='') {
				        	location.reload();
				        }else if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#add_skill").find('button').attr('disabled',false);
					$("#add_skill").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#add_skill").find('button').attr('disabled',false);
	                    $("#add_skill").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
	$("#changePass").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			
			var formData = new FormData($("#changePass")[0]);
			$.ajax({
			  	beforeSend:function(){
			  		$("#changePass").find('button').attr('disabled',true);
					$("#changePass").find('button>i').show(); 
			  	},
			  	url: $("#changePass").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response);
				        if (response.reload !='') {
				        	//location.reload();
				        }else if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#form").find('button').attr('disabled',false);
					$("#form").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#form").find('button').attr('disabled',false);
	                    $("#form").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});



 	$("body").on("click", ".enterPostBotton", function () {
        $.ajax({
		  	url: ajaxurl+'/post/create',
		  	type: 'POST',
		  	data:$('#post_form').serialize()+ "&type=OnlyTextPost",
		  	success:function(response){
	  		 	if(response.success){
			        toastr.success(response.message,'Success');
			        if (response.reload !='') {
			        	location.reload();
			        }else if (response.redirect_url !='') {
						setTimeout(function(){
							 location.href = response.redirect_url;
						},1000);
					}
			  	}else{
				  
			  	}
			},
			error:function(status,error){
				var errors = JSON.parse(status.responseText);
				var msg_error = '';
				if(status.status == 401){
                    $("#post_form").find('button').attr('disabled',false);
                    $("#post_form").find('button>i').hide();  
					$.each(errors.error, function(i,v){	
						msg_error += v[0]+'!</br>';
					});
					toastr.error( msg_error,'Opps!'); 
				}else{
					toastr.error(errors.message,'Opps!');
				} 				
          	}		  
		});	
		return false;
    });
 	$("#resetPasswordForm").validate({
          errorPlacement: function (error, element) {
             return;
          },
          highlight: function(element) {
                $(element).addClass('is-invalid');
                $(element).parent().addClass("error");
            },
            unhighlight: function(element) {
              $(element).parent().removeClass("error");
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
          submitHandler: function(form){
            var formData = new FormData($("#form")[0]);
            $.ajax({
                beforeSend:function(){
                  $("#resetPasswordForm").find('button').attr('disabled',true);
                $("#resetPasswordForm").find('button>i').show(); 
                },
                url: $("#resetPasswordForm").attr('action'),
                data: formData,
                type: 'POST',
                processData: false,
                contentType: false,
                success:function(response){
                	console.log(response);
                  if(response.success){
                      toastr.success(response.message,'Success');
                      console.log(response);
                      if (response.reload !='') {
                        location.reload();
                      }else if (response.redirect_url !='') {
                    setTimeout(function(){
                       location.href = response.redirect_url;
                    },1000);
                  }
                  }else{
                  
                  }
                },
                complete:function(){
                  $("#resetPasswordForm").find('button').attr('disabled',false);
                $("#resetPasswordForm").find('button>i').hide(); 
                },
                      error:function(status,error){
                var errors = JSON.parse(status.responseText);
                var msg_error = '';
                if(status.status == 401){
                            $("#resetPasswordForm").find('button').attr('disabled',false);
                            $("#resetPasswordForm").find('button>i').hide();  
                  $.each(errors.error, function(i,v){ 
                    msg_error += v[0]+'!</br>';
                  });
                  toastr.error( msg_error,'Opps!'); 
                }else{
                  toastr.error(errors.message,'Opps!');
                }         
                      }     
            }); 
            return false;
          }
        });

	$("body").on("click", ".saveLike", function () {
		var post_id 	= $(this).data('postid');
		var user_post_id 	= $(this).data('userpostid');
        $.ajax({
		  	url: ajaxurl+'/saveLike',
		  	type: 'POST',
		  	data: { 
		  		post_id:post_id, 
		  		user_post_id:user_post_id, 
		  	},
		  	success:function(response){
	  		 	if(response.success){
	  		 		$("#htmlViewLike").html(response.html);
			  	}
			},
			error:function(status,error){
				var errors = JSON.parse(status.responseText);
				var msg_error = '';
				if(status.status == 401){
                    $(".saveLike").find('button').attr('disabled',false);
                    $(".saveLike").find('button>i').hide();  
					$.each(errors.error, function(i,v){	
						msg_error += v[0]+'!</br>';
					});
					toastr.error( msg_error,'Opps!'); 
				}else{
					toastr.error(errors.message,'Opps!');
				} 				
          	}		  
		});	
		return false;
    });

	$("body").on("submit", ".saveComment", function () {
        $.ajax({
		  	url: ajaxurl+'/saveComment',
		  	type: 'POST',
		  	data: $(this).serialize(),
		  	success:function(response){
	  		 	if(response.success){
	  		 		console.log(response.html);
	  		 		location.reload();
			      /// 	$(this).parent('.first-post-input-div').next.find('.comment_wrap').append(response.html);
			  	}
			},
			error:function(status,error){
				var errors = JSON.parse(status.responseText);
				var msg_error = '';
				if(status.status == 401){
                    $(".saveComment").find('button').attr('disabled',false);
                    $(".saveComment").find('button>i').hide();  
					$.each(errors.error, function(i,v){	
						msg_error += v[0]+'!</br>';
					});
					toastr.error( msg_error,'Opps!'); 
				}else{
					toastr.error(errors.message,'Opps!');
				} 				
          	}		  
		});	
		return false;
    });

	$("body").on("click", ".shoReplyTextBox", function () {
		var comment_id = $(this).data('commentid');
		$(".shoReplyTextBox_"+comment_id).show();
	});

	$("body").on("submit", ".saveCommentReply", function () {
        $.ajax({
		  	url: ajaxurl+'/saveCommentReply',
		  	type: 'POST',
		  	data: $(this).serialize(),
		  	success:function(response){
	  		 	if(response.success){
			        //toastr.success(response.message,'Success');
			        if (response.reload !='') {
			        	location.reload();
			        }else if (response.redirect_url !='') {
						setTimeout(function(){
							 location.href = response.redirect_url;
						},1000);
					}
			  	}else{
				  
			  	}
			},
			error:function(status,error){
				var errors = JSON.parse(status.responseText);
				var msg_error = '';
				if(status.status == 401){
                    $(".saveComment").find('button').attr('disabled',false);
                    $(".saveComment").find('button>i').hide();  
					$.each(errors.error, function(i,v){	
						msg_error += v[0]+'!</br>';
					});
					toastr.error( msg_error,'Opps!'); 
				}else{
					toastr.error(errors.message,'Opps!');
				} 				
          	}		  
		});	
		return false;
    });

	//focus comment box
    focusMethod = function getFocus(id) {
	  	document.getElementById(id).focus();
	}
});

//share social media links
(function(){
  	var shareButtons = document.querySelectorAll(".share-btn");
  	if (shareButtons) {
      	[].forEach.call(shareButtons, function(button) {
	      	button.addEventListener("click", function(event) {
				var width = 650,
	            height = 450;
	        	event.preventDefault();
	        	window.open(this.href, 'Share Dialog', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width='+width+',height='+height+',top='+(screen.height/2-height/2)+',left='+(screen.width/2-width/2));
	      	});
	    });
	}
})();
 