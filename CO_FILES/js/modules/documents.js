/* documents Object */
function Documents(app) {
	this.name = app +'_documents';
	this.app = app;
	this.object = window[app];
	this.objectFirst = this.app.substr(0, 1);
	this.objectnameCaps = this.objectFirst.toUpperCase() + this.app.substr(1);
	
	
	this.createUploader = function(ele){            
		var module = this;
		var did = $('#'+ module.app).data("third");
		var num = 0;
		var numdocs = 0;
		var uploader = new qq.FileUploader({
			element: ele[0],
			template: '<table cellspacing="0" cellpadding="0" border="0" class="table-content"><tr><td class="tcell-left text11"><div class="qq-uploader">' + 
					'<div class="qq-upload-button">' + FILE_BROWSE + '</div></td><td class="tcell-right"></td></tr></table>' +
					'<div style="position: relative;">' +
					'<div class="qq-upload-drop-area"><span>' + FILE_DROP_AREA + '</span></div>' +
					'<div class="qq-upload-list" id="documents"></div></div>' + 
				 '</div>',
			fileTemplate: '<span class="doclist-outer">' +
					'<span class="qq-upload-file docitem" style="line-height: 15px;"></span><br />' +
					'<span class="qq-upload-spinner"></span>' +
					'<span class="qq-upload-size"></span>' +
					'<a class="qq-upload-cancel" href="#" style="line-height: 15px;">' + UPLOAD_CANCEL + '</a>' +
					'<span class="qq-upload-failed-text">Failed</span>' +
				'</span>',
			action: '/',
			sizeLimit: 50*1024*1024, // max size
			params: {
				path: 'classes/file_uploader',
				request: 'createNew',
				did: did,
				module: this.name
			},
			onSubmit: function(id, fileName){},
			onProgress: function(id, fileName, loaded, total){},
			onComplete: function(id, fileName, data){
				
				numdocs = $(".doclist-outer").size();
				num = num+1;
				if(num == numdocs) {
					$('#'+ module.app +'3 ul[rel=documents] .active-link').trigger("click");
				}
			},
			onCancel: function(id, fileName){
				},
			debug: false
		});    
	}


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
		formData[formData.length] = processListApps('document_access');
	 }
 

	 this.formResponse = function(data) {
		 var app = getCurrentApp();
		 switch(data.action) {
			case "edit":
				$("#"+ app +"3 ul[rel=documents] span[rel="+data.id+"] .text").html($("#"+ app +" .title").val());
				var moduleidx = $("#"+ app +"3 ul").index($("#"+ app +"3 ul[rel=documents]"));
				var liindex = $("#"+ app +"3 ul[rel=documents] .module-click").index($("#"+ app +"3 ul[rel=documents] .module-click[rel='"+data.id+"']"));
				switch(data.access) {
					case "0":
						$("#"+ app +"3 ul[rel=documents] span[rel="+data.id+"] .module-access-status").removeClass("module-access-active");
					break;
					case "1":
						$("#"+ app +"3 ul[rel=documents] span[rel="+data.id+"] .module-access-status").addClass("module-access-active");
					break;
				}
			break;
		}
	}


 	this.formSerialize = function(formData, form, poformOptions) {
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
	}


	this.poformOptions = { beforeSerialize: this.formSerialize, beforeSubmit: this.formProcess, dataType:  'json', success: this.formResponse };


	this.getDetails = function(moduleidx,liindex,list) {
		var module = this;
		var id = $("#"+ module.app +"3 ul:eq("+moduleidx+") .module-click:eq("+liindex+")").attr("rel");
		$('#'+ module.app).data({ "third" : id});
		var fid = $('#'+ module.app).data('first');
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getDetails&id='+id+"&fid="+fid, success: function(data){
			$('#'+ module.app +'-right').html(data.html);
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
		var id = $('#'+ module.app).data('second');
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/documents&request=createNew&id=' + id, cache: false, success: function(data){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getList&id='+id, success: function(ldata){						
				$('#'+ module.app +'3 ul[rel=documents]').html(ldata.html);
				$('#'+ module.app +'_documents_items').html(ldata.items);
				var liindex = $('#'+ module.app +'3 ul[rel=documents] .module-click').index($("#"+ module.app +"3 ul[rel=documents] .module-click[rel='"+data.id+"']"));
				$("#"+ module.app +"3 ul[rel=documents] .module-click:eq("+liindex+")").addClass('active-link');
				var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=documents]'));
				module.getDetails(moduleidx,liindex);
				window[module.app +'Actions'](0);
				setTimeout(function() { $('#'+ module.app +'-right .focusTitle').trigger('click'); }, 800);		
				}
			});
			}
		});
	}


	this.actionDuplicate = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		var pid = $('#'+ module.app).data("second");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=createDuplicate&id=' + id, cache: false, success: function(did){
			$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getList&id='+pid, success: function(data){																																																																				
				$('#'+ module.app +'3 ul[rel=documents]').html(data.html);
				$('#'+ module.app +'_documents_items').html(data.items);
				var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=documents]'));
				var liindex = $('#'+ module.app +'3 ul[rel=documents] .module-click').index($("#"+ module.app +"3 ul[rel=documents] .module-click[rel='"+did+"']"));
				module.getDetails(moduleidx,liindex);
				$("#"+ module.app +"3 ul[rel=documents] .module-click:eq("+liindex+")").addClass('active-link');
				window[module.app +'Actions'](0);
				}
			});
			}
		});
	}
	
	
	this.actionBin = function() {
		var module = this;
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=binDocument&id=' + id, cache: false, success: function(data){
							if(data == "true") {
								$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getList&id='+pid, success: function(data){
								$('#'+ module.app +'3 ul[rel=documents]').html(data.html);
								$('#'+ module.app +'_documents_items').html(data.items);
								var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=documents]'));
								var liindex = 0;
								module.getDetails(moduleidx,liindex);
								$("#"+ module.app +"3 ul[rel=documents] .module-click:eq("+liindex+")").addClass('active-link');
								window[module.app +'Actions'](0);
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
		return true;
	}


	this.actionRefresh = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		var pid = $('#'+ module.app).data("second");
		var fid = $('#'+ module.app).data("first");
		$('#'+ module.app +'3 ul[rel=documents] .active-link').trigger("click");
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getList&id='+pid+"&fid="+fid, success: function(data){																																																																				
			$('#'+ module.app +'3 ul[rel=documents]').html(data.html);
			$('#'+ module.app +'_documents_items').html(data.items);
			var liindex = $('#'+ module.app +'3 ul[rel=documents] .module-click').index($("#"+ module.app +"3 ul[rel=documents] .module-click[rel='"+id+"']"));
			$("#"+ module.app +"3 ul[rel=documents] .module-click:eq("+liindex+")").addClass('active-link');
			}
		});
	}
	
	
	this.actionPrint = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		var url ='/?path=apps/'+ module.app +'/modules/documents&request=printDetails&id='+id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.actionSend = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=getSend&id='+id, success: function(html){
			$("#modalDialogForward").html(html).dialog('open');
			}
		});
	}


	this.actionSendtoResponse = function() {
		var module = this;
		var id = $('#'+ module.app).data("third");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=getSendtoDetails&id='+id, success: function(html){
			$('#'+ module.app +'document_sendto').html(html);
			}
		});
	}


	this.sortclick = function (obj,sortcur,sortnew) {
		var module = this;
		var folderid = $('#'+ module.app).data("first");
		var fid = $('#'+ module.app +'2 .module-click:visible').attr("rel");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/'+ module.app +'/modules/documents&request=getList&id='+fid+'&sort='+sortnew+"&fid="+folderid, success: function(data){
			$('#'+ module.app +'3 ul[rel=documents]').html(data.html);
			$('#'+ module.app +'_documents_items').html(data.items);
			obj.attr("rel",sortnew);
			obj.removeClass("sort"+sortcur).addClass("sort"+sortnew);
			var id = $('#'+ module.app +'3 ul[rel=documents] .module-click:eq(0)').attr("rel");
			$('#'+ module.app).data('third',id);
			if(id == undefined) {
				return false;
			}
			var moduleidx = $('#'+ module.app +'3 ul').index($('#'+ module.app +'3 ul[rel=documents]'));
			module.getDetails(moduleidx,0);
			$('#'+ module.app +'3 ul[rel=documents] .module-click:eq(0)').addClass('active-link');
			}
		});
	}
	
	
	this.sortdrag = function (order) {
		var module = this;
		var fid = $('#'+ module.app).data("second");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=setOrder&'+order+'&id='+fid, success: function(html){
			$('#'+ module.app +'3 .sort:visible').attr("rel", "3");
			$('#'+ module.app +'3 .sort:visible').removeClass("sort1").removeClass("sort2").addClass("sort3");
			}
		});
	}


	this.actionDialog = function(offset,request,field,append,title,sql) {
		var module = this;
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
			$("#modalDialog").html(html);
			$("#modalDialog").dialog('option', 'position', offset);
			$("#modalDialog").dialog('option', 'title', title);
			$("#modalDialog").dialog('open');
			}
		});	
	}


	this.showItemContext = function(ele,uid,field) {
		var module = this;
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=getDocContext&id='+uid+'&field='+field, success: function(html){
			ele.parent().append(html);
			ele.next().slideDown();
			}
		});
	}
	
	
	this.downloadDocument = function(id) {
		var module = this;
		var url = '/?path=apps/'+ module.app +'/modules/documents&request=downloadDocument&id=' + id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.insertItem = function(field,append,id,text) {
		var module = this;
		var html = '<span class="docitems-outer"><a href="'+ module.app +'_documents" class="showItemContext" uid="' + id + '" field="' + field + '">' + text + '</a></span>';
		if(append == 0) {
			$("#"+field).html(html);
			$("#modalDialog").dialog('close');
		} else {
			if($("#"+field).html() != "") {
				$("#"+field+" .showItemContext:visible:last").append(", ");
				$("#"+field).append(html);
			} else {
				$("#"+field).append(html);
			}
		}
		var obj = getCurrentModule();
		$('#'+ module.app +' .coform').ajaxSubmit(obj.poformOptions);
	}


	this.removeItem = function(clicked,field) {
		var module = this;
		clicked.parent().fadeOut();
		//clicked.parent().prev().toggleClass('deletefromlist');
		clicked.parent().parent().prev().toggleClass('deletefromlist');
		clicked.parents(".docitems-outer").hide();
		if($("#"+field+" .docitems-outer:visible").length > 0) {
		var text = $("#"+field+" .docitems-outer:visible:last .showItemContext").html();
		var textnew = text.split(", ");
		$("#"+field+" .docitems-outer:visible:last .showItemContext").html(textnew[0]);
		}
		var obj = getCurrentModule();
		$('#'+ module.app +' .coform').ajaxSubmit(obj.poformOptions);
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=binDocItem&id=' + id, success: function(data){
						if(data){
							$("#doc_"+id).slideUp(function(){ 
								$(this).remove();
							});
						} 
						}
					});
				} 
			}
		});
	}
	
	
	this.actionHelp = function() {
		var module = this;
		var url = '/?path=apps/'+ module.app +'/modules/documents&request=getHelp';
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=deleteDocument&id=' + id, cache: false, success: function(data){
						if(data == "true") {
							$('#document_'+id).slideUp();
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=restoreDocument&id=' + id, cache: false, success: function(data){
						if(data == "true") {
							$('#document_'+id).slideUp();
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=deleteFile&id=' + id, cache: false, success: function(data){
						if(data == "true") {
							$('#file_'+id).slideUp();
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
					$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/documents&request=restoreFile&id=' + id, cache: false, success: function(data){
						if(data == "true") {
							$('#file_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}

}