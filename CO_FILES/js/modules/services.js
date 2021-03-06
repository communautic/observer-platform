/* services Object */
function Services(app) {
	this.name = app +'_services';
	this.app = app;
	var self = this;
	this.object = window[app];
	this.objectFirst = this.app.substr(0, 1);
	this.objectnameCaps = this.objectFirst.toUpperCase() + this.app.substr(1);
	this.askStatus = true;
	this.coPopupEditClass = 'popup-200';
	this.coPopupEdit = '';

	this.formProcess = function(formData, form, poformOptions) {
		var app = getCurrentApp();
		var title = $('#'+ app +' input.title').fieldValue();
		if(title == "") {
			setTimeout(function() {
				var title = $('#'+ app +' input.title').fieldValue();
				if(title == "") {
					$.prompt(ALERT_NO_TITLE, {submit: setTitleFocus});
				}
			}, 5000)
			return false;
		} else {
			formData[formData.length] = { "name": "title", "value": title };
		}
		$('#'+ app +'servicetasks > div').each(function() {
			var id = $(this).attr('id');
			var reg = /[0-9]+/.exec(id);
			/*var yo = "task_text_"+reg;
			var namen = "task_text["+reg+"]";
			if($('#task_'+reg+' :input[name="task_text_'+reg+'"]').length > 0) {
				var text = $('#'+yo).val();
				for (var i=0; i < formData.length; i++) { 
					if (formData[i].name == yo) { 
						formData[i].name = namen;
						formData[i].value = text;
					} 
				}
			} else {
				var text = $('#'+yo).html();
				formData[formData.length] = { "name": name, "value": text };
			}*/
			var yo = "task_text2_"+reg;
			var namen = "task_text2["+reg+"]";
			if($('#task_'+reg+' :input[name="task_text2_'+reg+'"]').length > 0) {
				var text = $('#'+yo).val();
				for (var i=0; i < formData.length; i++) { 
					if (formData[i].name == yo) { 
						formData[i].name = namen;
						formData[i].value = text;
					} 
				}
			} else {
				var text = $('#'+yo).html();
				formData[formData.length] = { "name": name, "value": text };
			}
			var yo = "task_text3_"+reg;
			var namen = "task_text3["+reg+"]";
			if($('#task_'+reg+' :input[name="task_text3_'+reg+'"]').length > 0) {
				var text = $('#'+yo).val();
				for (var i=0; i < formData.length; i++) { 
					if (formData[i].name == yo) { 
						formData[i].name = namen;
						formData[i].value = text;
					} 
				}
			} else {
				var text = $('#'+yo).html();
				formData[formData.length] = { "name": name, "value": text };
			}
		});
		//formData[formData.length] = processListApps('participants');
		//formData[formData.length] = processCustomTextApps('participants_ct');
		//formData[formData.length] = processListApps('management');
		//formData[formData.length] = processCustomTextApps('management_ct');
		//formData[formData.length] = processListApps('location');
		//formData[formData.length] = processCustomTextApps('location_ct');
		//formData[formData.length] = processStringApps('servicestart');
		//formData[formData.length] = processStringApps('serviceend');
		//formData[formData.length] = processListApps('service_relates_to');
		formData[formData.length] = processDocListApps('documents');
		formData[formData.length] = processListApps('service_access');
	 }
	 
	 
	 this.formResponse = function(data) {
		 var app = getCurrentApp();
		 var module = getCurrentModule();
		$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .text").html($("#"+ app +" .title").val());
		$('#totalcosts').text(data.totalcosts);
		
		switch(data.access) {
			case "0":
				$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-access-status").removeClass("module-access-active");
			break;
			case "2":
				$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-access-status").addClass("module-access-active");
			break;
		}	
		var serviceid = data.id;
		if(data.changeServiceStatus != "0" && module.askStatus && $('.jqibox').length == 0) {
			switch(data.changeServiceStatus) {
				/*case "1":
					var txt = ALERT_STATUS_SERVICE_ACTIVATE;
					var button = 'inprogress';
				break;*/
				case "2":
					var txt = ALERT_STATUS_SERVICE_COMPLETE + '<br>' +ALERT_MESSAGE_SERVICE_FINISHED_GERERATE_INVOICE;
					var button = 'finished';
				break;
			}
			
			var checkForInvoice = $('#patients-right span.showCoPopup');
			if(! checkForInvoice.hasClass('activeInvoice')) {
				var langbuttons = {};
				langbuttons[ALERT_YES] = true;
				langbuttons[ALERT_NO] = false;
				$.prompt(txt,{ 
					buttons:langbuttons,
					submit: function(e,v,m,f){		
						if(v){
							var pid = $("#patients").data("second");
							$.ajax({ type: "GET", dataType:  'json', url: "/", data: 'path=apps/patients/modules/services&request=generateInvoice&pid='+pid+'&sid='+serviceid, success: function(html){
								$.prompt.close();
								checkForInvoice.addClass('activeInvoice');
								//self.coPopupOpen(el,request);
								var fid = $("#patients").data("second");
								patients.getNavModulesNumItems(fid);
								$('#patientsActions .actionBin').addClass('noactive');
								
								
									$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/patients/modules/services&request=updateStatus&id=' + serviceid + '&date=&status='+data.changeServiceStatus, cache: false, success: function(data){
										switch(data.status) {
											
											case "2":
										$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active").removeClass("module-item-active-stopped");
									break;
									/*case "2":
										$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active-stopped").removeClass("module-item-active");
									break;*/
									default:
										$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").removeClass("module-item-active").removeClass("module-item-active-stopped");
											
											
											
										}
										
										
												$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/patients/modules/invoices&request=getList&id="+pid, success: function(data){
													$('#patients_invoices_items').html(data.items);
													}
												});
										
										//module.askStatus = false;
										$('#patients span.statusButton').removeClass('active');
										$('#patients span.statusButton.'+button).addClass('active');
										var today = new Date();
										var statusdate = today.toString("dd.MM.yyyy");
										$('#patients-right input.statusdp').val(statusdate);
										//if(data.changePatientStatus != 0) { module.setPatientStatus(data.changePatientStatus); }
										}
									});
									
								}
							});
					} else {
						module.askStatus = false;
					}
				}
			});
		}
		
		}
	}


	this.poformOptions = { beforeSubmit: this.formProcess, dataType: 'json', success: this.formResponse };


	this.statusOnClose = function(dp) {
		var app = getCurrentApp();
		var id = $("#"+ app).data("third");
		var status = $("#"+ app +" .statusTabs li span.active").attr('rel');
		var date = $("#"+ app +" .statusTabs input").val();
		
		
		if(status == 2) {
			var checkForInvoice = $('#patients-right span.showCoPopup');
			if(! checkForInvoice.hasClass('activeInvoice')) {
				var langbuttons = {};
				langbuttons[ALERT_YES] = true;
				langbuttons[ALERT_NO] = false;
				$.prompt(ALERT_MESSAGE_SERVICE_FINISHED_GERERATE_INVOICE,{ 
					buttons:langbuttons,
					submit: function(e,v,m,f){		
						if(v){
							e.preventDefault();
							var pid = $("#patients").data("second");
							var id = $("#patients").data("third");
							
							$.ajax({ type: "GET", dataType:  'json', url: "/", data: 'path=apps/patients/modules/services&request=generateInvoice&pid='+pid+'&sid='+id, success: function(html){
								$.prompt.close();
								checkForInvoice.addClass('activeInvoice');
								//self.coPopupOpen(el,request);
								var fid = $("#patients").data("second");
								patients.getNavModulesNumItems(fid);
								$('#patientsActions .actionBin').addClass('noactive');
								
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/"+ app +"/modules/services&request=updateStatus&id="+ id +"&date="+ date +"&status="+ status, cache: false, success: function(data){

									switch(data.status) {
										case "2":
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active").removeClass("module-item-active-stopped");
										break;
										default:
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").removeClass("module-item-active").removeClass("module-item-active-stopped");
									}
									
									$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/patients/modules/invoices&request=getList&id="+pid, success: function(data){
										$('#patients_invoices_items').html(data.items);
										}
									});
									}
								});
								
								
								}
							});
							
							
						} else {
							var fid = $("#patients").data("second");
							patients.getNavModulesNumItems(fid);
							e.preventDefault();
							$.prompt.close();
							$("#patients .statusTabs li span:eq(1)").click();
							//return false;
							status = 1;
							
							
							$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/"+ app +"/modules/services&request=updateStatus&id="+ id +"&date="+ date +"&status="+ status, cache: false, success: function(data){

									switch(data.status) {
										case "2":
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active").removeClass("module-item-active-stopped");
										break;
										default:
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").removeClass("module-item-active").removeClass("module-item-active-stopped");
									}
									var pid = $("#patients").data("second");
									$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/patients/modules/invoices&request=getList&id="+pid, success: function(data){
										$('#patients_invoices_items').html(data.items);
										}
									});
									}
								});
							
							
							
						}
					}
				});
			} else {
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/"+ app +"/modules/services&request=updateStatus&id="+ id +"&date="+ date +"&status="+ status, cache: false, success: function(data){

									switch(data.status) {
										case "2":
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active").removeClass("module-item-active-stopped");
										break;
										default:
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").removeClass("module-item-active").removeClass("module-item-active-stopped");
									}
									var pid = $("#patients").data("second");
									$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/patients/modules/invoices&request=getList&id="+pid, success: function(data){
										$('#patients_invoices_items').html(data.items);
										}
									});
									}
								});
			}
		} else {
		
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/"+ app +"/modules/services&request=updateStatus&id="+ id +"&date="+ date +"&status="+ status, cache: false, success: function(data){

									switch(data.status) {
										case "2":
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").addClass("module-item-active").removeClass("module-item-active-stopped");
										break;
										default:
											$("#"+ app +"3 ul[rel=services] span[rel="+data.id+"] .module-item-status").removeClass("module-item-active").removeClass("module-item-active-stopped");
									}
									var pid = $("#patients").data("second");
									$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/patients/modules/invoices&request=getList&id="+pid, success: function(data){
										$('#patients_invoices_items').html(data.items);
										}
									});
									}
								});
			
		}
		
	}


	this.getDetails = function(moduleidx,liindex,list) {
		var module = this;
		module.askStatus = true;
		var id = $("#"+ module.app +"3 ul:eq("+moduleidx+") .module-click:eq("+liindex+")").attr("rel");
		$('#'+ module.app).data({ "third" : id});
		var fid = $('#'+ module.app).data('first');
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/"+ module.app +"/modules/services&request=getDetails&id="+id+"&fid="+fid, success: function(data){
			$("#"+ module.app +"-right").html(data.html);
			if($('#checkedOut').length > 0) {
					$("#"+ module.app +"3 ul[rel=services] .active-link .icon-checked-out").addClass('icon-checked-out-active');
				} else {
					$("#"+ module.app +"3 ul[rel=services] .active-link .icon-checked-out").removeClass('icon-checked-out-active');
				}
			if(list == 0) {
				switch (data.access) {
					case "sysadmin": case "admin":
						window[module.app +'Actions'](0);
					break;
					case "guest":
						window[module.app +'Actions'](5);
					break;
				}
			} else {
				switch (data.access) {
					case "sysadmin": case "admin" :
						if(list == "<li></li>") {
							window[module.app +'Actions'](3);
						} else {
							window[module.app +'Actions'](0);
						}
					break;
					case "guest":
						if(list == "<li></li>") {
							window[module.app +'Actions']();
						} else {
							window[module.app +'Actions'](5);
						}
					break;
				}
			}
			window['init'+ module.objectnameCaps +'ContentScrollbar']();
			}
		});	
	}


	this.actionNew = function() {
		var module = this;
		var cid = $('#'+ module.app +' input[name="id"]').val()
		module.checkIn(cid);
		var id = $('#'+ module.app).data('second');
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/services&request=createNew&id='+ id, cache: false, success: function(data){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/services&request=getList&id='+id, success: function(list){
				$('#'+ module.app +'3 ul[rel=services]').html(list.html);
				$('#'+ module.app +'_services_items').html(list.items);
				var liindex = $('#'+ module.app +'3 ul[rel=services] .module-click').index($("#"+ module.app +"3 ul[rel=services] .module-click[rel='"+data.id+"']"));
				$('#'+ module.app +'3 ul[rel=services] .module-click:eq('+liindex+')').addClass('active-link');
				var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=services]'));
				module.getDetails(moduleidx,liindex);
				setTimeout(function() { $('#'+ module.app +'-right .focusTitle').trigger('click'); }, 800);
				}
			});
			}
		});
	}


	this.actionDuplicate = function() {
		var module = this;
		var cid = $('#'+ module.app +' input[name="id"]').val()
		module.checkIn(cid);
		var id = $('#'+ module.app).data("third");
		var pid = $('#'+ module.app).data("second");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=createDuplicate&id='+ id, cache: false, success: function(mid){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/services&request=getList&id='+pid, success: function(data){																																																																				
				$('#'+ module.app +'3 ul[rel=services]').html(data.html);
				$('#'+ module.app +'_services_items').html(data.items);
				var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=services]'));
				var liindex = $('#'+ module.app +'3 ul[rel=services] .module-click').index($("#"+ module.app +"3 ul[rel=services] .module-click[rel='"+mid+"']"));
				module.getDetails(moduleidx,liindex);
				$('#'+ module.app +'3 ul[rel=services] .module-click:eq('+liindex+')').addClass('active-link');
				}
			});
			}
		});
	}
	
	
	this.actionBin = function() {
		var module = this;
		var cid = $('#'+ module.app +' input[name="id"]').val()
		module.checkIn(cid);
		var txt = ALERT_DELETE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					var id = $('#'+ module.app).data("third");
					var pid = $('#'+ module.app).data("second");
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=binService&id='+ id, cache: false, success: function(data){
							if(data == "true") {
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/services&request=getList&id='+pid, success: function(data){
									$('#'+ module.app +'3 ul[rel=services]').html(data.html);
									$('#'+ module.app +'_services_items').html(data.items);
									if(data.html == "<li></li>") {
										window[module.app +'Actions'](3);
									} else {
										window[module.app +'Actions'](0);
									}
									var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=services]'));
									var liindex = 0;
									module.getDetails(moduleidx,liindex);
									$('#'+ module.app +'3 ul[rel=services] .module-click:eq('+liindex+')').addClass('active-link');
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
		var module = this;
		$.ajax({ type: "GET", url: "/", async: false, data: 'path=apps/'+ module.app +'/modules/services&request=checkinService&id='+id, success: function(data){
			if(!data) {
				prompt("something wrong");
			}
			}
		});
	}


	this.actionRefresh = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		var pid = $('#'+ module.app).data("second");
		var fid = $('#'+ module.app).data("first");
		$('#'+ module.app +'3 ul[rel=services] .active-link').trigger("click");
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/services&request=getList&id='+pid+"&fid="+fid, success: function(data){																																																																				
			$('#'+ module.app +'3 ul[rel=services]').html(data.html);
			$('#'+ module.app +'_services_items').html(data.items);
			var liindex = $('#'+ module.app +'3 ul[rel=services] .module-click').index($("#"+ module.app +"3 ul[rel=services] .module-click[rel='"+id+"']"));
			$('#'+ module.app +'3 ul[rel=services] .module-click:eq('+liindex+')').addClass('active-link');
			}
		});
	}


	this.actionPrint = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		var url ='/?path=apps/'+ module.app +'/modules/services&request=printDetails&id='+id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.actionSend = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/services&request=getSend&id='+id, success: function(data){
			$("#modalDialogForward").html(data.html).dialog('open');
			if(data.error == 1) {
				$.prompt('<div style="text-align: center">'+ ALERT_REMOVE_RECIPIENT + data.error_message +'<br /></div>');
				return false;
			}
			}
		});
	}


	this.actionSendtoResponse = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=getSendtoDetails&id='+id, success: function(html){
			$('#'+ module.app +'service_sendto').html(html);
			}
		});
	}


	this.sortclick = function (obj,sortcur,sortnew) {
		var module = this;
		var cid = $('#'+ module.app +' input[name="id"]').val()
		module.checkIn(cid);
		var folderid = $('#'+ module.app).data("first");
		var fid = $('#'+ module.app +'2 .module-click:visible').attr("rel");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/services&request=getList&id='+fid+'&sort='+sortnew+"&fid="+folderid, success: function(data){
			$('#'+ module.app +'3 ul[rel=services]').html(data.html);
			$('#'+ module.app +'_services_items').html(data.items);
			obj.attr("rel",sortnew);
			obj.removeClass("sort"+sortcur).addClass("sort"+sortnew);
			var id = $('#'+ module.app +'3 ul[rel=services] .module-click:eq(0)').attr("rel");
			$('#'+ module.app).data('third',id);
			if(id == undefined) {
				return false;
			}
			var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=services]'));
			module.getDetails(moduleidx,0);
			$('#'+ module.app +'3 ul[rel=services] .module-click:eq(0)').addClass('active-link');
		}
		});
	}


	this.sortdrag = function (order) {
		var module = this;
		var fid = $('#'+ module.app).data("second");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=setOrder&'+order+'&id='+fid, success: function(html){
			$('#'+ module.app +'3 .sort:visible').attr("rel", "3");
			$('#'+ module.app +'3 .sort:visible').removeClass("sort1").removeClass("sort2").addClass("sort3");
			}
		});
	}


	this.actionDialog = function(offset,request,field,append,title,sql) {
		var module = this;
		switch(request) {
			case "getAccessDialog":
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			/*case "getServiceStatusDialog":
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;*/
			case "getDocumentsDialog":
				var id = $('#'+ module.app).data("second");
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql+'&id='+ id, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			case "get"+module.objectnameCaps+"Dialog":
			var id = $('#'+ module.app).data("second");
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', offset);
					$("#modalDialog").dialog('option', 'title', title);
					$("#modalDialog").dialog('open');
					}
				});
			break;
			default:
			$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
				$("#modalDialog").html(html);
				$("#modalDialog").dialog('option', 'position', offset);
				$("#modalDialog").dialog('option', 'title', title);
				$("#modalDialog").dialog('open');
				if($("#"+ field +"_ct .ct-content").length > 0) {
					var ct = $("#"+ field +"_ct .ct-content").html();
					ct = ct.replace(CUSTOM_NOTE +" ","");
					$("#custom-text").val(ct);
				}
				}
			});
		}
	}


	this.addParentLink = function(id) {
		var module = this;
		var pid = $('#'+ module.app).data("second");
		var phid = $('#'+ module.app).data("third");
		$("#modalDialog").dialog("close");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/services&request=copyService&id='+ id +'&pid='+ id +'&phid='+ phid, success: function(data){
			if($('#'+ module.app +'servicescopies').html() != "") {
				$('#'+ module.app +'servicescopies').append("<br />");
			}
			$('#'+ module.app +'servicescopies').append(data.titlelink);
			$.prompt(ALERT_SUCCESS_COPY_SERVICE +'"'+data.title+'"');
			}
		});
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'&request=saveLastUsed'+ module.objectnameCaps +'&id='+id});
	}


	/*this.insertStatus = function(rel,text) {
		var module = this;
		var html = '<div class="listmember" field="'+ module.app +'service_status" uid="'+rel+'" style="float: left">'+ text +'</div>';
		$('#'+ module.app +'service_status').html(html);
		$("#modalDialog").dialog("close");
		$('#'+ module.app +'service_status').next().val("");
		$('#'+ module.app +' .coform').ajaxSubmit(module.poformOptions);
	}


	this.insertStatusDate = function(rel,text) {
		var module = this;
		var html = '<div class="listmember" field="'+ module.app +'service_status" uid="'+rel+'" style="float: left">'+ text +'</div>';
		$('#'+ module.app +'service_status').html(html);
		$("#modalDialog").dialog("close");
		$('#'+ module.app +'service_status').nextAll('img').trigger('click');
	}*/


	this.newItem = function() {
		var module = this;
		var mid = $('#'+ module.app).data("third");
		var num = parseInt($('#'+ module.app +'-right .task_sort').size());
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=addTask&mid='+ mid +'&num='+ num +'&sort='+ num, success: function(html){
			$('#'+ module.app +'servicetasks').append(html);
			var idx = parseInt($('#'+ module.app +'servicetasks .cbx').size() -1);
			var element = $('#'+ module.app +'servicetasks .cbx:eq('+idx+')');
			$.jNice.CheckAddPO(element);
			$('#'+ module.app +'servicetasks .serviceouter:eq('+idx+')').slideDown(function() {
				$(this).find(":text:eq(0)").focus();
				/*if(idx == 6) {
				$('#'+ module.app +'-right .addTaskTable').clone().insertAfter('#'+ module.app +'servicetasks');
				}*/
				window['init'+module.objectnameCaps+'ContentScrollbar']();
				module.askStatus = true;
			});
			}
		});
	}


	this.actionSortItems = function(order) {
		var module = this;
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=sortItems&'+ order, cache: false, success: function(data){
			}
		});
	}


	this.togglePost = function(id,obj) {
		var module = this;
		var outer = $('#'+ module.app +'servicetask_'+id);
		outer.slideToggle();
		obj.find('span').toggleClass('active');
	}


	this.binItem = function(id) {
		var module = this;
		var txt = ALERT_DELETE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=deleteTask&id='+ id, success: function(data){
					if(data){
						$("#task_"+id).slideUp(function(){ $(this).remove(); });
					}
					}
				});
				} 
			}
		});
	}


	this.manageCheckpoint = function(action,date) {
		var module = this;
		var pid = $('#'+ module.app).data('third');
		switch(action) {
			case 'new':
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=newCheckpoint&id='+ pid +'&date='+ date, cache: false });
			break;
			case 'update':
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=updateCheckpoint&id='+ pid +'&date='+ date, cache: false });			
			break;
			case 'delete':
				$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=deleteCheckpoint&id='+ pid, cache: false });
			break;
		}
	}


	this.saveCheckpointText = function() {
		var module = this;
		var pid = $('#'+ module.app).data('third');
		var text = $('#'+ module.app +'_servicesCheckpoint textarea').val();
		$.ajax({ type: "POST", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=updateCheckpointText&id='+ pid +'&text='+ text, cache: false });
	}


	this.actionHelp = function() {
		var module = this;
		var url = '/?path=apps/'+ module.app +'/modules/services&request=getHelp';
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	// Recycle Bin
	this.binDelete = function(id) {
		var module = this;
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=deleteService&id='+ id, cache: false, success: function(data){
						if(data == "true") {
							$('#service_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


	this.binRestore = function(id) {
		var module = this;
		var txt = ALERT_RESTORE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=restoreService&id='+ id, cache: false, success: function(data){
						if(data == "true") {
							$('#service_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


	this.binDeleteItem = function(id) {
		var module = this;
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=deleteServiceTask&id='+ id, cache: false, success: function(data){
						if(data == "true") {
							$('#service_task_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


	this.binRestoreItem = function(id) {
		var module = this;
		var txt = ALERT_RESTORE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/services&request=restoreServiceTask&id='+ id, cache: false, success: function(data){
						if(data == "true") {
							$('#service_task_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}
	
	
	this.coPopup = function(el,request) {
		
		if(request == 'showAllTasks') {
			if(el.hasClass('activeInvoice')) {
				self.coPopupOpen(el,request);
			} else {
					var langbuttons = {};
					langbuttons[ALERT_YES] = true;
					langbuttons[ALERT_NO] = false;
					$.prompt(ALERT_MESSAGE_GERERATE_INVOICE,{ 
						buttons:langbuttons,
						submit: function(e,v,m,f){		
							if(v){
								e.preventDefault();
								var pid = $("#patients").data("second");
								var sid = $("#patients").data("third");
								$.ajax({ type: "GET", dataType:  'json', url: "/", data: 'path=apps/patients/modules/services&request=generateInvoice&pid='+pid+'&sid='+sid, success: function(html){
									$.prompt.close();
									el.addClass('activeInvoice');
									self.coPopupOpen(el,request);
									var fid = $("#patients").data("second");
									patients.getNavModulesNumItems(fid);
									$('#patientsActions .actionBin').addClass('noactive');
									}
								});
								
								
							} else {
								e.preventDefault();
								$.prompt.close();
							}
						}
					});
			}
		} else {
			self.coPopupOpen(el,request);
		}
	}
	
	
	this.coPopupOpen = function(el,request) {

				var elepos = el.position();
				
				if(request == 'showAllTasks') {
				
					var id = $("#patients").data("third");
					$.ajax({ type: "GET", url: "/", data: "path=apps/patients/modules/services&request=getCoPopup&id="+id, success: function(html){
						self.coPopupEdit = html;
					
					var html = self.coPopupEdit;
					var pclass = self.coPopupEditClass;
					var copopup = $('#co-popup');
					copopup.html(html);
					if(request == 'title' || request == 'stagegate') {
						copopup.find('.tohide').hide();
						var r = 211;
					} else {
						copopup.find('.tohide').show();
						var r = 170;
					}
					copopup
						.removeClass(function (index, css) {
								 return (css.match (/\bpopup-\w+/g) || []).join(' ');
							 })
						.addClass(pclass)
						.position({
								my: "right bottom",
								at: "right+300 top",
								of: el,
								collision: 'flip fit',
								within: '#patients-right .scroll-pane',
								using: function(coords, ui) {
									var $modal = $(this),
									t = coords.top,
									l = coords.left,
									className = 'switch-' + ui.horizontal;
									$modal.css({
										left: l + 'px',
										top: t + 'px'
									}).removeClass(function (index, css) {
											return (css.match (/\bswitch-\w+/g) || []).join(' ');
										})
									.addClass(className);
									//copopup.find('.arrow').offset({ top: ui.target.top });
									var arrowtop = Math.round(ui.target.top - ui.element.top)+14;
									copopup.find('.arrow').css('top', arrowtop); 
								}
						});
						}});
				} else {
					
					var belegId = el.attr('rel');
					
					var delButton = '';
					if(el.hasClass('showdelete')) {
						delButton = '<li style="display: inline-block"><a href="#" class="ServiceDeleteBarBeleg alert" rel="'+belegId+'" style="width: 80px; margin-left: 18px;">L&ouml;schen</a></li>';
					}
					
					var html = '<div class="head">Barzahlung</div><div class="BarzahlungenPopup content"><div class="inner" id="">' + $('#belegText-'+belegId).val() + '</div><ul class="popupButtons" style="margin-top: 5px;"><li style="display: inline-block"><a href="#" class="ServiceCopyText blue" rel="' + belegId + '" style="width: 80px;">Kopieren</a></li>' + delButton + '</ul>';
					var pclass = self.coPopupEditClass;
					var copopup = $('#co-popup');
					copopup.html(html);
					if(request == 'title' || request == 'stagegate') {
						copopup.find('.tohide').hide();
						var r = 211;
					} else {
						copopup.find('.tohide').show();
						var r = 170;
					}
					copopup
						.removeClass(function (index, css) {
								 return (css.match (/\bpopup-\w+/g) || []).join(' ');
							 })
						.addClass(pclass)
						.position({
								my: "center bottom",
								at: "center top",
								of: el,
								collision: 'flip fit',
								within: '#patients-right .scroll-pane',
								using: function(coords, ui) {
									var $modal = $(this),
									t = coords.top,
									l = coords.left,
									className = 'switch-' + ui.horizontal;
									$modal.css({
										left: l + 'px',
										top: t + 'px'
									}).removeClass(function (index, css) {
											return (css.match (/\bswitch-\w+/g) || []).join(' ');
										})
									.addClass(className);
									//copopup.find('.arrow').offset({ top: ui.target.top });
									var arrowtop = Math.round(ui.target.top - ui.element.top)+14;
									copopup.find('.arrow').css('top', arrowtop); 
								}
						});
					
				}
	}
	
	

}

$(document).ready(function () {
	
	$(document).on('click', 'span.toggleServiceTaskBarzahlung', function(e) {
		e.preventDefault();
		prevent_dblclick(e)
		
		var ele = $(this);
		
		if(ele.hasClass('active')) {
			ele.removeClass('active');
		} else {
			ele.addClass('active');
		}
		//$('#calculateRevenue').click();
	});
	
	$(document).on('click', 'span.toggleServiceAllTasksBarzahlung', function(e) {
		e.preventDefault();
		prevent_dblclick(e)

		$('div.BarzahlungenPopup .toggleServiceTaskBarzahlung').each(function(i) { 
			var ele = $(this);
		  ele.addClass('active');
		})
	});
	
	$(document).on('click', 'a.saveServiceTasksBarzahlung', function(e) {
		e.preventDefault();
		prevent_dblclick(e)
		
		var tasks = {};
		
		$('div.BarzahlungenPopup .toggleServiceTaskBarzahlung.active').each(function(i) { 
			tasks[i] = $( this ).attr('rel');	
		})
		//alert(JSON.stringify(tasks));
		if($.isEmptyObject(tasks)) {
			return false;
		} else {
			var tid = $('#patients').data('third');
			
			$.ajax({ type: "GET", url: "/", data: "path=apps/patients/modules/services&request=generateBarzahlung&tid=" + tid + "&tasks="+JSON.stringify(tasks), cache: false, success: function(html){
				$('#listBarPayments').html(html);
				for (i in tasks) {
					var toRemove = tasks[i];
						//$('#patients-right a.binItem[rel='+toRemove+']').remove();
						var t = $('#task_'+toRemove);
						
						t.find('a.binItem').hide();
						t.find('.jNiceCheckbox').addClass('noperm');
						$('#task_title_'+toRemove).attr('readonly','readonly');
						$('#task_text2_'+toRemove).attr('readonly','readonly');
						$('#task_text3_'+toRemove).attr('readonly','readonly');
						t.find('.selectMenge').removeClass('content-nav').removeClass('selectTextfield');
						t.find('.selectPreis').removeClass('content-nav').removeClass('selectTextfield');
						//t.find('.behandlungsart').removeClass('content-nav').removeClass('showDialog');
						//t.find('.showItemContext ').removeClass('co-link').attr('edit', 0);
				}
				$('#co-popup').css('left',-1000);
			}
		});
		}
	});
	
	$(document).on('click', 'a.ServiceCopyText', function(e) {
		e.preventDefault();
		prevent_dblclick(e)
		var belegId = $(this).attr('rel');
		$("#belegText-"+belegId  ).select();
		document.execCommand("copy");
		$('#co-popup').css('left',-1000);
	});
	
	$(document).on('click', 'a.ServiceDeleteBarBeleg', function(e) {
		e.preventDefault();
		prevent_dblclick(e)
		var belegId = $(this).attr('rel');
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/patients/modules/services&request=deleteBarBeleg&id=" + belegId , cache: false, success: function(data){
			$('#barbeleg-'+belegId).remove();
			//$('#patientsActions .actionExport').removeClass('noactive');
			for(var i in data) {
			var t = $('#task_'+data[i]);
						t.find('a.binItem').show();
						t.find('.jNiceCheckbox').removeClass('noperm');
						$('#task_title_'+data[i]).removeAttr('readonly');
						$('#task_text2_'+data[i]).removeAttr('readonly');
						$('#task_text3_'+data[i]).removeAttr('readonly');
						t.find('.selectMenge').addClass('content-nav').addClass('selectTextfield');
						t.find('.selectPreis').addClass('content-nav').addClass('selectTextfield');
			}
			$('#co-popup').css('left',-1000);
			
			}
		});
		//$("#belegText-"+belegId  ).select();
		//document.execCommand("copy");
	});
	
});