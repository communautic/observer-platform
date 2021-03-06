/* comments Object */
function evalsComments(name) {
	this.name = name;


	this.formProcess = function(formData, form, poformOptions) {
		var title = $("#evals input.title").fieldValue();
		if(title == "") {
			setTimeout(function() {
				title = $("#evals input.title").fieldValue();
				if(title == "") {
					$.prompt(ALERT_NO_TITLE, {submit: setTitleFocus});
				}
			}, 5000)
			return false;
		} else {
			formData[formData.length] = { "name": "title", "value": title };
		}
		
		formData[formData.length] = processListApps('management');
		formData[formData.length] = processCustomTextApps('management_ct');
		//formData[formData.length] = processStringApps('commentstart');
		//formData[formData.length] = processStringApps('commentend');
		formData[formData.length] = processDocListApps('documents');
		formData[formData.length] = processListApps('comment_access');
		//formData[formData.length] = processListApps('comment_status');
	 }
	 
	 
	 this.formResponse = function(data) {
		 switch(data.action) {
			case "edit":
				$("#evals3 ul[rel=comments] span[rel="+data.id+"] .text").html($("#evals .item_date").val() + ' - ' +$("#evals .title").val());
					switch(data.access) {
						case "0":
							$("#evals3 ul[rel=comments] span[rel="+data.id+"] .module-access-status").removeClass("module-access-active");
						break;
						case "1":
							$("#evals3 ul[rel=comments] span[rel="+data.id+"] .module-access-status").addClass("module-access-active");
						break;
					}
			break;
		}
	}
	
	
	this.poformOptions = { beforeSubmit: this.formProcess, dataType: 'json', success: this.formResponse };


	this.getDetails = function(moduleidx,liindex,list) {
		var id = $("#evals3 ul:eq("+moduleidx+") .module-click:eq("+liindex+")").attr("rel");
		$('#evals').data({ "third" : id});
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/evals/modules/comments&request=getDetails&id="+id, success: function(data){
			$("#evals-right").html(data.html);
			
			if($('#checkedOut').length > 0) {
					$("#evals3 ul[rel=comments] .active-link .icon-checked-out").addClass('icon-checked-out-active');
				} else {
					$("#evals3 ul[rel=comments] .active-link .icon-checked-out").removeClass('icon-checked-out-active');
				}
			
			if(list == 0) {
				switch (data.access) {
					case "sysadmin": case "admin":
						evalsActions(0);
					break;
					case "guest":
						evalsActions(5);
					break;
				}
			} else {
				switch (data.access) {
					case "sysadmin": case "admin" :
						if(list == "<li></li>") {
							evalsActions(3);
						} else {
							evalsActions(0);
						}
					break;
					case "guest":
						if(list == "<li></li>") {
							evalsActions();
						} else {
							evalsActions(5);
						}
					break;
				}
				
			}
			initEvalsContentScrollbar();
			}
		});	
	}


	this.actionNew = function() {
		var module = this;
		var cid = $('#evals input[name="id"]').val()
		module.checkIn(cid);
	
		var id = $('#evals').data('second');
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/evals/modules/comments&request=createNew&id=' + id, cache: false, success: function(data){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/evals/modules/comments&request=getList&id="+id, success: function(list){
				$("#evals3 ul[rel=comments]").html(list.html);
				$('#evals_comments_items').html(list.items);
				var liindex = $("#evals3 ul[rel=comments] .module-click").index($("#evals3 ul[rel=comments] .module-click[rel='"+data.id+"']"));
				$("#evals3 ul[rel=comments] .module-click:eq("+liindex+")").addClass('active-link');
				var moduleidx = $("#evals3 ul").index($("#evals3 ul[rel=comments]"));
				module.getDetails(moduleidx,liindex);
				setTimeout(function() { $('#evals-right .focusTitle').trigger('click'); }, 800);
				}
			});
			}
		});
	}


	this.actionDuplicate = function() {
		var module = this;
		var cid = $('#evals input[name="id"]').val()
		module.checkIn(cid);
		var id = $("#evals").data("third");
		var pid = $("#evals").data("second");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/evals/modules/comments&request=createDuplicate&id=' + id, cache: false, success: function(mid){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/evals/modules/comments&request=getList&id="+pid, success: function(data){																																																																				
				$("#evals3 ul[rel=comments]").html(data.html);
				$('#evals_comments_items').html(data.items);
				var moduleidx = $("#evals3 ul").index($("#evals3 ul[rel=comments]"));
				var liindex = $("#evals3 ul[rel=comments] .module-click").index($("#evals3 ul[rel=comments] .module-click[rel='"+mid+"']"));
				module.getDetails(moduleidx,liindex);
				$("#evals3 ul[rel=comments] .module-click:eq("+liindex+")").addClass('active-link');
				}
			});
			}
		});
	}
	
	
	this.actionBin = function() {
		var module = this;
		var cid = $('#evals input[name="id"]').val()
		module.checkIn(cid);
		var txt = ALERT_DELETE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					var id = $("#evals").data("third");
					var pid = $("#evals").data("second");
					$.ajax({ type: "GET", url: "/", data: "path=apps/evals/modules/comments&request=binComment&id=" + id, cache: false, success: function(data){
							if(data == "true") {
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/evals/modules/comments&request=getList&id="+pid, success: function(data){
									$("#evals3 ul[rel=comments]").html(data.html);
									$('#evals_comments_items').html(data.items);
									if(data.html == "<li></li>") {
										evalsActions(3);
									} else {
										evalsActions(0);
									}
									var moduleidx = $("#evals3 ul").index($("#evals3 ul[rel=comments]"));
									var liindex = 0;
									module.getDetails(moduleidx,liindex);
									$("#evals3 ul[rel=comments] .module-click:eq("+liindex+")").addClass('active-link');
								}
								});
							}
						}
					});
				} 
			}
		});
	}


	this.checkIn = function(id) {
		$.ajax({ type: "GET", url: "/", async: false, data: 'path=apps/evals/modules/comments&request=checkinComment&id='+id, success: function(data){
			if(!data) {
				prompt("something wrong");
			}
			}
		});
	}
	
	
	this.actionRefresh = function() {
		var id = $("#evals").data("third");
		var pid = $("#evals").data("second");
		$("#evals3 ul[rel=comments] .active-link").trigger("click");
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/evals/modules/comments&request=getList&id="+pid, success: function(data){																																																																				
			$("#evals3 ul[rel=comments]").html(data.html);
			$('#evals_comments_items').html(data.items);
			var liindex = $("#evals3 ul[rel=comments] .module-click").index($("#evals3 ul[rel=comments] .module-click[rel='"+id+"']"));
			$("#evals3 ul[rel=comments] .module-click:eq("+liindex+")").addClass('active-link');
			}
		});
	}


	this.actionPrint = function() {
		var id = $("#evals").data("third");
		var url ='/?path=apps/evals/modules/comments&request=printDetails&id='+id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.actionSend = function() {
		var id = $("#evals").data("third");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/evals/modules/comments&request=getSend&id="+id, success: function(data){
			$("#modalDialogForward").html(data.html).dialog('open');
			if(data.error == 1) {
				$.prompt('<div style="text-align: center">' + ALERT_REMOVE_RECIPIENT + data.error_message + '<br /></div>');
				return false;
			}
			}
		});
	}


	this.actionSendtoResponse = function() {
		var id = $("#evals").data("third");
		$.ajax({ type: "GET", url: "/", data: "path=apps/evals/modules/comments&request=getSendtoDetails&id="+id, success: function(html){
			$("#evalscomment_sendto").html(html);
			//$("#modalDialogForward").dialog('close');
			}
		});
	}
	
	
	this.sortclick = function (obj,sortcur,sortnew) {
		var module = this;
		var cid = $('#evals input[name="id"]').val()
		module.checkIn(cid);
		
		var fid = $("#evals2 .module-click:visible").attr("rel");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/evals/modules/comments&request=getList&id="+fid+"&sort="+sortnew, success: function(data){
			$("#evals3 ul[rel=comments]").html(data.html);
			$('#evals_comments_items').html(data.items);
			obj.attr("rel",sortnew);
			obj.removeClass("sort"+sortcur).addClass("sort"+sortnew);
			var id = $("#evals3 ul[rel=comments] .module-click:eq(0)").attr("rel");
			$('#evals').data('third',id);
			if(id == undefined) {
				return false;
			}
			var moduleidx = $("#evals3 ul").index($("#evals3 ul[rel=comments]"));
			module.getDetails(moduleidx,0);
			$("#evals3 ul[rel=comments] .module-click:eq(0)").addClass('active-link');
		}
		});
	}


	this.sortdrag = function (order) {
		var fid = $("#evals").data("second");
		$.ajax({ type: "GET", url: "/", data: "path=apps/evals/modules/comments&request=setOrder&"+order+"&id="+fid, success: function(html){
			$("#evals3 .sort:visible").attr("rel", "3");
			$("#evals3 .sort:visible").removeClass("sort1").removeClass("sort2").addClass("sort3");
			}
		});
	}


	this.actionDialog = function(offset,request,field,append,title,sql) {
		switch(request) {
			case "getAccessDialog":
				$.ajax({ type: "GET", url: "/", data: 'path=apps/evals&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					//$("#modalDialog").dialog('option', 'height', 50);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			case "getCommentStatusDialog":
				$.ajax({ type: "GET", url: "/", data: 'path=apps/evals/modules/comments&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			case "getDocumentsDialog":
				var id = $("#evals").data("second");
				$.ajax({ type: "GET", url: "/", data: 'path=apps/evals/modules/documents&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql+'&id=' + id, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			default:
			$.ajax({ type: "GET", url: "/", data: 'path=apps/evals&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
				$("#modalDialog").html(html);
				$("#modalDialog").dialog('option', 'position', offset);
				$("#modalDialog").dialog('option', 'title', title);
				$("#modalDialog").dialog('open');
				if($("#" + field + "_ct .ct-content").length > 0) {
					var ct = $("#" + field + "_ct .ct-content").html();
					ct = ct.replace(CUSTOM_NOTE + " ","");
					$("#custom-text").val(ct);
				}
				}
			});
		}
	}


	this.insertStatus = function(rel,text) {
		var module = this;
		var html = '<div class="listmember" field="evalscomment_status" uid="'+rel+'" style="float: left">' + text + '</div>';
		$("#evalscomment_status").html(html);
		$("#modalDialog").dialog("close");
		$("#evalscomment_status").next().val("");
		$('#evals .coform').ajaxSubmit(module.poformOptions);
	}
	
	
	this.actionHelp = function() {
		var url = "/?path=apps/evals/modules/comments&request=getHelp";
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}
	
	
	// Recycle Bin
	this.binDelete = function(id) {
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: "path=apps/evals/modules/comments&request=deleteComment&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#comment_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}
	
	
	this.binRestore = function(id) {
		var txt = ALERT_RESTORE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: "path=apps/evals/modules/comments&request=restoreComment&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#comment_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


}


var evals_comments = new evalsComments('evals_comments');