function initProcsContentScrollbar() {
	procsInnerLayout.initContent('center');
}

/* procs Object */
function procsApplication(name) {
	this.name = name;
	var module = this;
	this.isRefresh = false;
	var self = this;
	this.coNewOptions = '';
	
	this.init = function() {
		this.$app = $('#procs');
		this.$appContent = $('#procs-right');
		this.$first = $('#procs1');
		this.$second = $('#procs2');
		this.$third = $('#procs3');
		this.$thirdDiv = $('#procs3 div.thirdLevel');
		this.$layoutWest = $('#procs div.ui-layout-west');
		this.coPopupEditClass = 'popup-full';
		this.coPopupEdit = '<div class="head">Bearbeiten</div><div class="content"><div class="fieldset"><label>Titel</label><input type="text" class="title" maxlength="40" value="" /></div><div class="saveItemShape"><span rel="1"><span class="shape1"></span></span><span rel="2"><span class="shape2"></span></span><span rel="3"><span class="shape3"></span></span><span rel="4"><span class="shape4"></span></span><span rel="5"><span class="shape5"></span></span></div><div class="saveItemColor"><span rel="1"><span class="color1"></span></span><span rel="2"><span class="color2"></span></span><span rel="3"><span class="color3"></span></span><span rel="4"><span class="color4"></span></span><span rel="5"><span class="color5"></span></span></div><div class="fieldset"><label>Beschreibung</label><textarea class="text"></textarea></div><div class="resetIndexOuter"><span class="resetZindex">Anordnen in den Hintergrund</span></div><ul class="popupButtons"><li><a href="#" class="binItem alert" rel="">'+DATEPICKER_CLEAR+'</a></li></ul></div><span class="arrow"></span>';
		this.coPopupEditClassArrow = 'popup-arrows';
		this.coPopupEditArrow = '<div class="head">Bearbeiten</div><div class="content"><div class="saveItemArrow"><span rel="1"><span class="arrow1"></span></span><!--<span rel="2"><span class="arrow2"></span></span>--><span rel="3"><span class="arrow3"></span></span><!--<span rel="4"><span class="arrow4"></span></span>--><span rel="5"><span class="arrow5"></span></span><!--<span rel="6"><span  class="arrow6"></span></span>--><span rel="7"><span class="arrow7"></span></span><!--<span rel="8"><span class="arrow8"></span></span>--><div class="dimensionsOuter"><span class="arrowWidthMore"></span><span class="arrowWidthLess"></span><span class="arrowHeightMore"></span><span class="arrowHeightLess"></span></div></div><div class="resetIndexOuter"><span class="resetZindex">Anordnen in den Hintergrund</span></div><ul class="popupButtons"><li><a href="#" class="binItem alert" rel="">'+DATEPICKER_CLEAR+'</a></li></ul></div><span class="arrow"></span>';
		if(self.coNewOptions == '') {
			$.ajax({ type: "GET", url: "/", data: "path=apps/procs/&request=getNewOptions", success: function(html){
				self.coNewOptions = html;
			}});
		}
		this.coPopupEditArrowWin2 = '<div class="head">Bearbeiten</div><div class="content"><div class="saveItemArrow"><span rel="18"><span class="arrow18"></span></span><span rel="17"><span class="arrow17"></span></span><span rel="19"><span class="arrow19"></span></span><span rel="20"><span class="arrow20"></span></span><span rel="21"><span class="arrow21"></span></span><span rel="22"><span  class="arrow22"></span></span><span rel="23"><span class="arrow23"></span></span><span rel="24"><span class="arrow24"></span></span><div class="dimensionsOuter"><span class="arrowWidthMore"></span><span class="arrowWidthLess"></span><span class="arrowHeightMore"></span><span class="arrowHeightLess"></span></div></div><div class="resetIndexOuter"><span class="resetZindex">Anordnen in den Hintergrund</span></div><ul class="popupButtons"><li><a href="#" class="binItem alert" rel="">'+DATEPICKER_CLEAR+'</a></li></ul></div><span class="arrow"></span>';
		if(self.coNewOptions == '') {
			$.ajax({ type: "GET", url: "/", data: "path=apps/procs/&request=getNewOptions", success: function(html){
				self.coNewOptions = html;
			}});
		}
		this.coPopupEditArrowWin3 = '<div class="head">Bearbeiten</div><div class="content"><div class="saveItemArrow"><span rel="9"><span class="arrow9"></span></span><span rel="10"><span class="arrow10"></span></span><span rel="11"><span class="arrow11"></span></span><span rel="12"><span class="arrow12"></span></span><span rel="13"><span class="arrow13"></span></span><span rel="14"><span  class="arrow14"></span></span><span rel="15"><span class="arrow15"></span></span><span rel="16"><span class="arrow16"></span></span><div class="dimensionsOuter"><span class="arrowWidthMore"></span><span class="arrowWidthLess"></span><span class="arrowHeightMore"></span><span class="arrowHeightLess"></span></div></div><div class="resetIndexOuter"><span class="resetZindex">Anordnen in den Hintergrund</span></div><ul class="popupButtons"><li><a href="#" class="binItem alert" rel="">'+DATEPICKER_CLEAR+'</a></li></ul></div><span class="arrow"></span>';
		if(self.coNewOptions == '') {
			$.ajax({ type: "GET", url: "/", data: "path=apps/procs/&request=getNewOptions", success: function(html){
				self.coNewOptions = html;
			}});
		}
		this.coPopupEditClass = 'popup-full';
	}
	
	this.formProcess = function(formData, form, poformOptions) {
		var title = $("#procs input.title").fieldValue();
		if(title == "") {
			setTimeout(function() {
				title = $("#procs input.title").fieldValue();
				if(title == "") {
					$.prompt(ALERT_NO_TITLE, {submit: setTitleFocus});
				}
			}, 5000)
			return false;
		} else {
			formData[formData.length] = { "name": "title", "value": title };
		}
		formData[formData.length] = processListApps('folder');
	}

	
	this.formResponse = function(data) {
		switch(data.action) {
			case "edit":
				$("#procs2 span[rel='"+data.id+"'] .text").html($("#procs .title").val());
			break;
			case "reload":
				var fid = $("#procs").data("first");
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+data.id+"&fid="+fid, success: function(text){
					$("#procs-right").html(text.html);
						initProcsContentScrollbar();
					}
				});
			break;
		}
	}


	this.poformOptions = { beforeSubmit: this.formProcess, dataType: 'json', success: this.formResponse };


	this.actionClose = function() {
		procsLayout.toggle('west');
	}


	this.getNavModulesNumItems = function(id) {
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/procs&request=getNavModulesNumItems&id=' + id, success: function(data){
				$.each( data, function(k, v){
   					$('#'+k).html(v);
 				});
			}
		});
	}
	
	
	/*this.actionNew = function() {
		var module = this;
		var cid = $('#procs input[name="id"]').val()
		module.checkIn(cid);
		var id = $('#procs').data('first');
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/procs&request=newProc&id=' + id, cache: false, success: function(data){
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+id, success: function(list){
				$("#procs2 ul").html(list.html);
				var index = $("#procs2 .module-click").index($("#procs2 .module-click[rel='"+data.id+"']"));
				setModuleActive($("#procs2"),index);
				$('#procs').data({ "second" : data.id });				
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+data.id, success: function(text){
					$("#procs-right").html(text.html);
					initProcsContentScrollbar();
					$('#procs-right .focusTitle').trigger('click');
					module.getNavModulesNumItems(data.id);
					}
				});
				procsActions(0);
				}
			});
			}
		});
	}*/
	
	this.actionNewOption = function(option) {
		$('#co-splitActions').css('left',-1000);
		switch(option) {
			case '1':
				var module = this;
				var cid = $('#procs input[name="id"]').val()
				module.checkIn(cid);
				var id = $('#procs').data('first');
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/procs&request=newProc&id=' + id, cache: false, success: function(data){
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+id, success: function(list){
						$("#procs2 ul").html(list.html);
						var index = $("#procs2 .module-click").index($("#procs2 .module-click[rel='"+data.id+"']"));
						setModuleActive($("#procs2"),index);
						$('#procs').data({ "second" : data.id });				
						$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+data.id+"&fid="+id, success: function(text){
							$("#procs-right").html(text.html);
							initProcsContentScrollbar();
							$('#procs-right .focusTitle').trigger('click');
							module.getNavModulesNumItems(data.id);
							}
						});
						procsActions(0);
						}
					});
					}
				});
			break;
			case '2':
				this.actionDialog('my: "left top", at: "left+15 top+15", of: "#procs-right"','getProcsLinkDialog','status',1,'test');
				/*var id = $("#patients").data("third");
				var url ='/?path=apps/patients/modules/invoices&request=printDetails&option=reminder&id='+id;
				$("#documentloader").attr('src', url);*/
			break;
		}
	}
	
	this.addParentLink = function(pid) {
		//alert(id+' to link to');
		/*var module = this;
		var pid = $('#'+ module.app).data("second");
		var phid = $('#'+ module.app).data("third");
		$("#modalDialog").dialog("close");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'/modules/phases&request=addProjectLink&id=' + id + '&pid=' + pid + '&phid=' + phid, success: function(html){
				$('#'+ module.app +'phasetasks').append(html);
				var idx = parseInt($('#'+ module.app +'-right .cbx').size() -1);
				$('#'+ module.app +'-right div.phaseouter:eq('+idx+')').slideDown(function() {
					$(this).find(":text:eq(0)").focus();
					if(idx == 6) {
					$('#'+ module.app +'-right .addTaskTable').clone().insertAfter('#'+ module.app +'phasetasks');
					}
					window['init'+ module.objectnameCaps +'ContentScrollbar']();							   
				});
				}
			});
		$.ajax({ type: "GET", url: "/", data: 'path=apps/'+ module.app +'&request=saveLastUsedProjects&id='+id});
		*/
				var module = this;
				var cid = $('#procs input[name="id"]').val()
				module.checkIn(cid);
				var id = $('#procs').data('first');
				$("#modalDialog").dialog("close");
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: 'path=apps/procs&request=newProcLink&id=' + pid+'&fid='+id, cache: false, success: function(data){
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+id, success: function(list){
						$("#procs2 ul").html(list.html);
						var index = $("#procs2 .module-click").index($("#procs2 .module-click[rel='"+data.id+"']"));
						setModuleActive($("#procs2"),index);
						$('#procs').data({ "second" : pid });				
						$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+pid+"&fid="+id, success: function(text){
							$("#procs-right").html(text.html);
							initProcsContentScrollbar();
							$('#procs-right .focusTitle').trigger('click');
							module.getNavModulesNumItems(pid);
							}
						});
						procsActions(20);
						}
					});
					}
				});
	}
	
	this.actionNew = function() {
		var copopup = $('#co-splitActions');
		var pclass = this.coPopupEditClass;
		copopup.html(this.coNewOptions);
		copopup
			.removeClass(function (index, css) {
				   return (css.match (/\bpopup-\w+/g) || []).join(' ');
			   })
			.addClass(pclass)
			.position({
				  my: "center center",
				  at: "right+123 center",
				  of: '#procsActions .listNew',
				  collision: 'flip fit',
				  within: '#procs-right',
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
						copopup.hide().animate({width:'toggle'}, function() { 
							//copopup.find('.arrow').offset({ top: ui.target.top+25 });
							var arrowtop = Math.round(ui.target.top - ui.element.top)+20;
							copopup.find('.arrow').css('top', arrowtop); 
						})
				}
			});
	}


	this.actionDuplicate = function() {
		var module = this;
		var cid = $('#procs input[name="id"]').val()
		module.checkIn(cid);
		var pid = $("#procs").data("second");
		var oid = $("#procs").data("first");
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=createDuplicate&id=' + pid, cache: false, success: function(id){
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+oid, success: function(data){
				$("#procs2 ul").html(data.html);
					procsActions(0);
					var idx = $("#procs2 .module-click").index($("#procs2 .module-click[rel='"+id+"']"));
					setModuleActive($("#procs2"),idx)
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+id+"&fid="+oid, success: function(text){
							$("#procs").data("second",id);
							$("#"+procs.name+"-right").html(text.html);
							initProcsContentScrollbar();
							module.getNavModulesNumItems(id);
						}
					});
				}
			});
			}
		});
	}


	this.actionBin = function() {
		var module = this;
		if($('#ProcLink').length == 0) {
			var cid = $('#procs input[name="id"]').val()
		} else {
			var cid = parseInt($('#procs2 .active-link').parent().attr('id').replace(/procItem_/, ""));
		}
		module.checkIn(cid);
		var txt = ALERT_DELETE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					//var id = $("#procs").data("second");
					var fid = $("#procs").data("first");
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=binProc&id=" + cid, cache: false, success: function(data){
						if(data == "true") {
							$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+fid, success: function(list){
								$("#procs2 ul").html(list.html);
								if(list.html == "<li></li>") {
									procsActions(3);
								} else {
									procsActions(0);
									setModuleActive($("#procs2"),0);
								}
								var id = $("#procs2 .module-click:eq(0)").attr("rel");
								if(typeof id == 'undefined') {
									$("#procs").data("second", 0);
								} else {
									$("#procs").data("second", id);
								}
								$("#procs2 .module-click:eq(0)").addClass('active-link');
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&fid="+fid+"&id="+id, success: function(text){
									$("#procs-right").html(text.html);
									initProcsContentScrollbar();
									module.getNavModulesNumItems(id);
									}
								});
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
		if($('#ProcLink').length == 0) {
			$.ajax({ type: "GET", url: "/", async: false, data: 'path=apps/procs&request=checkinProc&id='+id, success: function(data){
					if(!data) {
						prompt("something wrong");
					}
				}
			});
		}
	}


	this.actionRefresh = function() {
		var oid = $('#procs').data('first');
		var pid = $('#procs').data('second');
		$("#procs2 .active-link").trigger("click");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+oid, success: function(data){
			$("#procs2 ul").html(data.html);
			var idx = $("#procs2 .module-click").index($("#procs2 .module-click[rel='"+pid+"']"));
			$("#procs2 .module-click:eq("+idx+")").addClass('active-link');
			}
		});
	}


	this.actionPrint = function() {
		var id = $("#procs").data("second");
		var url ='/?path=apps/procs&request=printProcDetails&id='+id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.actionSend = function() {
		var id = $("#procs").data("second");
		$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=getProcSend&id="+id, success: function(html){
			$("#modalDialogForward").html(html).dialog('open');
			}
		});
	}


	this.actionSendtoResponse = function() {
		var id = $("#procs").data("second");
	}


	this.sortclick = function (obj,sortcur,sortnew) {
		var module = this;
		var cid = $('#procs input[name="id"]').val()
		module.checkIn(cid);
		var fid = $("#procs .module-click:visible").attr("rel");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+fid+"&sort="+sortnew, success: function(data){
			$("#procs2 ul").html(data.html);
			obj.attr("rel",sortnew);
			obj.removeClass("sort"+sortcur).addClass("sort"+sortnew);
			var id = $("#procs2 .module-click:eq(0)").attr("rel");
			$('#procs').data('second',id);
			if(id == undefined) {
				return false;
			}
			setModuleActive($("#procs2"),'0');
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&id="+id+"&fid="+fid, success: function(text){
				$("#"+procs.name+"-right").html(text.html);
				initProcsContentScrollbar()
				}
			});
			}
		});
	}


	this.sortdrag = function (order) {
		var fid = $("#procs .module-click:visible").attr("rel");
		$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=setProcOrder&"+order+"&id="+fid, success: function(html){
			$("#procs2 .sort").attr("rel", "3");
			$("#procs2 .sort").removeClass("sort1").removeClass("sort2").addClass("sort3");
			}
		});
	}
	
	
	this.actionDialog = function(offset,request,field,append,title,sql) {
		switch(request) {
			case "getProcsLinkDialog":
				$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
					$("#modalDialog").html(html);
					$("#modalDialog").dialog('option', 'position', { my: "left top", at: "left+15 top+50", of: "#procs-right" });
					$("#modalDialog").dialog('option', 'title', 'window');
					$("#modalDialog").dialog('open');
					}
				});
			break;
			default:
			$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
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
	
	
	this.coPopup = function(el,request) {
		switch(request) {
			case 'note':
				var elepos = el.position();
				var id = parseInt(el.attr('id').replace(/note-/, ""));
				currentProcEditedNote = id;
				var title = $('#note-title-'+id).text();
				var text = $('#note-text-'+id).text();
				var regshape = /shape([0-9])+/.exec(el.attr('class'));
				var shape = regshape[1]-1;
				var regcolor = /color([0-9])+/.exec(el.attr('class'));
				var color = regcolor[1]-1;
				var html = this.coPopupEdit;
				var pclass = this.coPopupEditClass;
				var copopup = $('#co-popup');
				copopup.html(html);
				copopup.find('.title').val(title);
				copopup.find('.text').val(text);
				copopup.find('.saveItemShape>span:eq('+shape+')').addClass('procs-shape-active');
				copopup.find('.saveItemColor>span:eq('+color+')').addClass('procs-shape-active');
				//copopup.find('.saveItem')
				$('#co-popup a.binItem').attr('rel',id);
				copopup
					.removeClass(function (index, css) {
						   return (css.match (/\bpopup-\w+/g) || []).join(' ');
					   })
					.addClass(pclass)
					.position({
						  my: "center center",
						  at: "right+170 center",
						  of: el,
						  collision: 'flip fit',
						  within: '#notesOuter',
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
								//copopup.find('.arrow').offset({ top: ui.target.top+25 });
								var arrowtop = Math.round(ui.target.top - ui.element.top)+40;
								copopup.find('.arrow').css('top', arrowtop); 
				  		}
					})
			break;
			case 'text':
				var elepos = el.position();
				var id = parseInt(el.attr('id').replace(/note-/, ""));
				currentProcEditedNote = id;
				var title = $('#note-title-'+id).text();
				var text = $('#note-text-'+id).text();
				//var regshape = /shape([0-9])+/.exec(el.attr('class'));
				//var shape = regshape[1]-1;
				//var regcolor = /color([0-9])+/.exec(el.attr('class'));
				//var color = regcolor[1]-1;
				var html = this.coPopupEdit;
				var pclass = this.coPopupEditClass;
				var copopup = $('#co-popup');
				copopup.html(html);
				copopup.find('.title').val(title);
				copopup.find('.text').val(text);
				//copopup.find('.saveItemShape>span:eq('+shape+')').addClass('procs-shape-active');
				//copopup.find('.saveItemColor>span:eq('+color+')').addClass('procs-shape-active');
				copopup.find('.saveItemShape').hide();
				copopup.find('.saveItemColor').hide();
				//copopup.find('.saveItem')
				$('#co-popup a.binItem').attr('rel',id);
				copopup
					.removeClass(function (index, css) {
						   return (css.match (/\bpopup-\w+/g) || []).join(' ');
					   })
					.addClass(pclass)
					.position({
						  my: "center center",
						  at: "right+170 center",
						  of: el,
						  collision: 'flip fit',
						  within: '#notesOuter',
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
								//copopup.find('.arrow').offset({ top: ui.target.top+25 });
								var arrowtop = Math.round(ui.target.top - ui.element.top)+40;
								copopup.find('.arrow').css('top', arrowtop); 
				  		}
					});
			break;
			case 'arrow':
				var elepos = el.position();
				var id = parseInt(el.attr('id').replace(/note-/, ""));
				currentProcEditedNote = id;
				var html = procs.coPopupEditArrow;
				var pclass = procs.coPopupEditClassArrow;
				//var regshape = /arrow([0-9])+/.exec(el.attr('class'));
				//var shape = regshape[1]-1;
				var regshape = /arrow([0-9])+/.exec(el.attr('class'));
				var shape = parseInt(regshape[0].replace(/arrow/, ""));
				var copopup = $('#co-popup');
				copopup.html(html);
				$('#co-popup a.binItem').attr('rel',id);
				//copopup.find('.saveItemArrow>span:eq('+shape+')').addClass('procs-shape-active');
				copopup.find('.saveItemArrow>span[rel="'+shape+'"]').addClass('procs-shape-active');
				copopup
					.removeClass(function (index, css) {
						return (css.match (/\bpopup-\w+/g) || []).join(' ');
					})
					.addClass(pclass)
					.position({
						  my: "center center",
						  at: "right+170 center+1",
						  of: el,
						  collision: 'flip fit',
						  within: '#notesOuter',
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
								//copopup.find('.arrow').offset({ top: ui.target.top-6 });
								var arrowtop = Math.round(ui.target.top - ui.element.top)+11;
								if(arrowtop < 40) { arrowtop = 40; }
								copopup.find('.arrow').css('top', arrowtop); 
				  		}
					})
			break;
			case 'arrowWin2':
				var elepos = el.position();
				var id = parseInt(el.attr('id').replace(/note-/, ""));
				currentProcEditedNote = id;
				var html = procs.coPopupEditArrowWin2;
				var pclass = procs.coPopupEditClassArrow;
				var regshape = /arrow([0-9])+/.exec(el.attr('class'));
				var shape = parseInt(regshape[0].replace(/arrow/, ""));
				var copopup = $('#co-popup');
				copopup.html(html);
				$('#co-popup a.binItem').attr('rel',id);
				copopup.find('.saveItemArrow>span[rel="'+shape+'"]').addClass('procs-shape-active');
				copopup
					.removeClass(function (index, css) {
						return (css.match (/\bpopup-\w+/g) || []).join(' ');
					})
					.addClass(pclass)
					.position({
						  my: "center center",
						  at: "right+170 center+1",
						  of: el,
						  collision: 'flip fit',
						  within: '#notesOuter',
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
								//copopup.find('.arrow').offset({ top: ui.target.top-6 });
								var arrowtop = Math.round(ui.target.top - ui.element.top)+11;
								if(arrowtop < 40) { arrowtop = 40; }
								copopup.find('.arrow').css('top', arrowtop); 
				  		}
					})
			break;
			case 'arrowWin3':
				var elepos = el.position();
				var id = parseInt(el.attr('id').replace(/note-/, ""));
				currentProcEditedNote = id;
				var html = procs.coPopupEditArrowWin3;
				var pclass = procs.coPopupEditClassArrow;
				var regshape = /arrow([0-9])+/.exec(el.attr('class'));
				var shape = parseInt(regshape[0].replace(/arrow/, ""));
				var copopup = $('#co-popup');
				copopup.html(html);
				$('#co-popup a.binItem').attr('rel',id);
				copopup.find('.saveItemArrow>span[rel="'+shape+'"]').addClass('procs-shape-active');
				copopup
					.removeClass(function (index, css) {
						return (css.match (/\bpopup-\w+/g) || []).join(' ');
					})
					.addClass(pclass)
					.position({
						  my: "center center",
						  at: "right+170 center+1",
						  of: el,
						  collision: 'flip fit',
						  within: '#notesOuter',
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
								//copopup.find('.arrow').offset({ top: ui.target.top-6 });
								var arrowtop = Math.round(ui.target.top - ui.element.top)+11;
								if(arrowtop < 40) { arrowtop = 40; }
								copopup.find('.arrow').css('top', arrowtop); 
				  		}
					})
			break;
		}
	}


	this.insertStatusDate = function(rel,text) {
		var html = '<div class="listmember" field="procsstatus" uid="'+rel+'" style="float: left">' + text + '</div>';
		$("#procsstatus").html(html);
		$("#modalDialog").dialog("close");
		$("#procsstatus").nextAll('img').trigger('click');
	}
	
	
	// notes
	this.newItemOption = function(ele,what) {
		var oid = $('#procs').data('first');
		var id = $('#procs').data('second');
		var outer = $("#notesOuter");
		//var x = outer.scrollLeft() + 20;
		var x = Math.ceil((outer.scrollLeft()+1)/10)*10 + 20;
		//var y = outer.scrollTop() + 60;
		var y = Math.ceil((outer.scrollTop()+1)/10)*10 + 60;
		var zMax = 0;
		if($('#procs-right div.note').length > 0) {
			zMax = Math.max.apply(null,$.map($('#procs-right div.note'), function(e,n){
						return parseInt($(e).css('z-index'))||1 ;
					}));
		}
		var z = zMax + 1;
		procszIndex = z;
		$.ajax({ type: "GET", url: "/", dataType: 'json', data: "path=apps/procs&request=newProcNote&id="+id+"&x="+x+"&y="+y+"&z="+z+"&what="+what, success: function(data){
				var line1 = '<div id="note-'+data.id+'" class="note shape1 color1 showCoPopup" request="note" style="left: '+x+'px; top: '+y+'px; z-index: '+z+';"><div class="firstArrowStyle"></div><div class="secondArrowStyle"></div><div class="arrowStyle"><span id="note-more-'+data.id+'" class="note-readmore coTooltip" style="display: none;"><div style="display: none" class="coTooltipHtml"></div></span><div id="note-title-'+data.id+'">'+data.title+'</div>';
				if(what == 'text') {
					var line1 = '<div id="note-'+data.id+'" class="note shape34 color1 showCoPopup" request="text" style="left: '+x+'px; top: '+y+'px; z-index: '+z+';"><div class="firstArrowStyle"></div><div class="secondArrowStyle"></div><div class="arrowStyle"><span id="note-more-'+data.id+'" class="note-readmore coTooltip" style="display: none;"><div style="display: none" class="coTooltipHtml"></div></span><div id="note-title-'+data.id+'">'+data.title+'</div>';
				}
				if(what == 'arrow') {
					var line1 = '<div id="note-'+data.id+'" class="note shape10 arrow1 showCoPopup" request="arrow" style="left: '+x+'px; top: '+y+'px; z-index: '+z+';"><div class="firstArrowStyle"></div><div class="secondArrowStyle"></div><div class="arrowStyle"><span id="note-more-'+data.id+'" class="note-readmore coTooltip" style="display: none;"><div style="display: none" class="coTooltipHtml"></div></span><div id="note-title-'+data.id+'"></div>';
				}
				if(what == 'arrow2') {
					var line1 = '<div id="note-'+data.id+'" class="note shape10 arrow18 showCoPopup" request="arrowWin2" style="left: '+x+'px; top: '+y+'px; z-index: '+z+';"><div class="firstArrowStyle"></div><div class="secondArrowStyle"></div><div class="arrowStyle"><span id="note-more-'+data.id+'" class="note-readmore coTooltip" style="display: none;"><div style="display: none" class="coTooltipHtml"></div></span><div id="note-title-'+data.id+'"></div>';
				}
				if(what == 'arrow3') {
					var line1 = '<div id="note-'+data.id+'" class="note shape10 arrow9 showCoPopup" request="arrowWin3" style="left: '+x+'px; top: '+y+'px; z-index: '+z+';"><div class="firstArrowStyle"></div><div class="secondArrowStyle"></div><div class="arrowStyle"><span id="note-more-'+data.id+'" class="note-readmore coTooltip" style="display: none;"><div style="display: none" class="coTooltipHtml"></div></span><div id="note-title-'+data.id+'"></div>';
				}
				var html = line1 +
        '<div id="note-text-'+data.id+'" style="display: none;"></div></div></div>';
				$("#notesOuter").append(html);
				initProcsContentScrollbar();
			}
		});
	}
	
	this.saveItem = function() {
		var id = currentProcEditedNote;
		var title = $('#co-popup input.title').val();
		$('#note-title-'+id).text(title);
		var text = $('#co-popup textarea.text').val();
		$('#note-text-'+id).text(text);
		//$('#note-more-'+id+' .coTooltipHtml').text(text);
		// shape & color
		var shape = $('#co-popup .saveItemShape span.procs-shape-active').html();
		$('#note-'+id).addClass('shape'+shape);
		var proc_id = $('#procs').data('second');

		$.ajax({ type: "POST", url: "/", data: { path: 'apps/procs', request: 'saveProcNote', proc_id : proc_id, id: id, title: title, text: text }, success: function(data){
				if(text == "") {
					$('#note-more-'+id).hide()
				} else {
					$('#note-more-'+id).show()
				}
			}
		});
	}
	
	this.saveItemShape = function(ele) {
		var id = currentProcEditedNote;
		$('#co-popup .saveItemShape>span').removeClass('procs-shape-active');
		ele.addClass('procs-shape-active');
		var shape = ele.attr('rel');
		var color = $('#co-popup .saveItemColor span.procs-shape-active').attr('rel');
		$('#note-'+id).removeClass(function (index, css) {
			return (css.match (/\bshape\w+/g) || []).join(' ');
		})
		$('#note-'+id).removeClass(function (index, css) {
			return (css.match (/\bcolor\w+/g) || []).join(' ');
		})
		$('#note-'+id).addClass('shape'+shape+' color'+color);
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=saveItemStyle&id='+id+'&shape='+shape+'&color='+color, success: function(html){
			}
		});
	}


	this.saveItemColor = function(ele) {
		var id = currentProcEditedNote;
		$('#co-popup .saveItemColor>span').removeClass('procs-shape-active');
		ele.addClass('procs-shape-active');
		var color = ele.attr('rel');
		var shape= $('#co-popup .saveItemShape span.procs-shape-active').attr('rel');
		$('#note-'+id).removeClass(function (index, css) {
			return (css.match (/\bshape\w+/g) || []).join(' ');
		})
		$('#note-'+id).removeClass(function (index, css) {
			return (css.match (/\bcolor\w+/g) || []).join(' ');
		})
		$('#note-'+id).addClass('shape'+shape+' color'+color);
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=saveItemStyle&id='+id+'&shape='+shape+'&color='+color, success: function(html){
			}
		});
	}
	
	
	this.saveItemArrow = function(ele) {
		var id = currentProcEditedNote;
		$('#co-popup .saveItemArrow>span').removeClass('procs-shape-active');
		ele.addClass('procs-shape-active');
		var shape = ele.attr('rel');
		$('#note-'+id).removeClass(function (index, css) {
			return (css.match (/\barrow\w+/g) || []).join(' ');
		})
		$('#note-'+id).css('width','');
		$('#note-'+id).css('height','');
		$('#note-'+id+' .arrowStyle').css('width','');
		$('#note-'+id+' .arrowStyle').css('height','');
		$('#note-'+id+' .firstArrowStyle').css('width','');
		$('#note-'+id+' .firstArrowStyle').css('height','');
		$('#note-'+id+' .secondArrowStyle').css('width','');
		$('#note-'+id+' .secondArrowStyle').css('height','');
		$('#note-'+id).addClass('arrow'+shape);
		shape = parseInt(shape)+9;
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=saveItemStyle&id='+id+'&shape='+shape+'&color=0', success: function(html){
			}
		});
	}
	
	this.saveItemArrowWidth = function(action) {
		var id = currentProcEditedNote;
		var regshape = /arrow([0-9])+/.exec($('#note-'+id).attr('class'));
		var shape = parseInt(regshape[0].replace(/arrow/, ""));
		var widthitem = $('#note-'+id+' .arrowStyle');
		var dofirstArrowStyle = false;
		//console.log(shape);
		switch(shape) {
			case 3: case 7: case 9: case 10: case 11: case 12: case 21: case 22: case 23: case 24:
				widthitem = $('#note-'+id+' .secondArrowStyle');
			break;
			case 13: case 14: case 15: case 16:
				dofirstArrowStyle = true;
			break;
		}
		
		var outerwidth = $('#note-'+id).width();
		var arrowwidth = widthitem.width();
		if(action == 'more') {
			var outerwidthnew = outerwidth+20;
			var arrowwidthnew = arrowwidth+20;
		} else {
			var outerwidthnew = outerwidth-20;
			var arrowwidthnew = arrowwidth-20;
		}
		if(outerwidthnew < 40) {
			outerwidthnew = outerwidth;
			arrowwidthnew = arrowwidth;
		}
		$('#note-'+id).width(outerwidthnew);
		widthitem.width(arrowwidthnew);
		if(dofirstArrowStyle) {
			$('#note-'+id+' .firstArrowStyle').width(outerwidthnew);
		}
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=saveItemWidth&id='+id+'&width='+outerwidthnew, success: function(html){
			}
		});
	}
	
	this.saveItemArrowHeight = function(action) {
		var id = currentProcEditedNote;
		var regshape = /arrow([0-9])+/.exec($('#note-'+id).attr('class'));
		var shape = parseInt(regshape[0].replace(/arrow/, ""));
		var heightitem = $('#note-'+id+' .arrowStyle');
		var dofirstArrowStyle = false;
		//console.log(shape);
		switch(shape) {
			case 1: case 5: case 18: case 17: case 19: case 20: case 13: case 14: case 15: case 16:
				heightitem = $('#note-'+id+' .secondArrowStyle');
			break;
			case 9: case 10: case 11: case 12:
				dofirstArrowStyle = true;
			break;
		}
		
		var outerheight = $('#note-'+id).height();
		var arrowheight = heightitem.height();
		if(action == 'more') {
			var outerheightnew = outerheight+20;
			var arrowheightnew = arrowheight+20;
		} else {
			var outerheightnew = outerheight-20;
			var arrowheightnew = arrowheight-20;
		}
		if(outerheightnew < 40) {
			outerheightnew = outerheight;
			arrowheightnew = arrowheight;
		}
		$('#note-'+id).height(outerheightnew);
		heightitem.height(arrowheightnew);
		if(dofirstArrowStyle) {
			$('#note-'+id+' .firstArrowStyle').height(outerheightnew);
		}
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request=saveItemHeight&id='+id+'&height='+outerheightnew, success: function(html){
			}
		});
	}
	
	this.resetItemZindex = function() {
		var id = currentProcEditedNote;
		var note = $('#note-'+id);
		note.css('z-index',0);
		var x = parseInt(note.css('left'));
		var y = parseInt(note.css('top'));
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=updateNotePosition&id="+id+"&x="+x+"&y="+y+"&z=0", success: function(data){
				}
			});
	}
	
	this.binItem = function(id) {
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					var proc_id = $('#procs').data('second');
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=deleteProcNote&id="+id+"&proc_id="+proc_id, success: function(data){
						if(data){
							$("#note-"+id).fadeOut(function(){ 
								$(this).remove();
								currentProcEditedNote = 0;
							});
						} 
						}
					});
				} 
			}
		});	
	}
	
	this.binItems = function() {
		var pid = $('#procs').data('second');
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=binItems&id="+pid, success: function(data){
						if(data){
							module.actionRefresh();
						} 
						}
					});
				} 
			}
		});	
	}
	
	
	this.actionArchive = function() {
		var module = this;
		var cid = $('#procs input[name="id"]').val()
		module.checkIn(cid);
		var txt = ALERT_ARCHIVE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					var id = $("#procs").data("second");
					var fid = $("#procs").data("first");
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=movetoArchive&id=" + id + "&fid=" + fid, cache: false, success: function(data){
						if(data == "true") {
							$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcList&id="+fid, success: function(list){
								$("#procs2 ul").html(list.html);
								if(list.html == "<li></li>") {
									procsActions(3);
								} else {
									procsActions(0);
									setModuleActive($("#procs2"),0);
								}
								var id = $("#procs2 .module-click:eq(0)").attr("rel");
								if(typeof id == 'undefined') {
									$("#procs").data("second", 0);
								} else {
									$("#procs").data("second", id);
								}
								$("#procs2 .module-click:eq(0)").addClass('active-link');
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getProcDetails&fid="+fid+"&id="+id, success: function(text){
									$("#procs-right").html(text.html);
									initProcsContentScrollbar();
									module.getNavModulesNumItems(id);
									}
								});
							}
							});
						}
					}
					});
				} 
			}
		});
	}
	
	
	
	this.actionHelp = function() {
		var url = "/?path=apps/procs&request=getProcsHelp";
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
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=deleteProc&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#proc_'+id).slideUp();
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
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=restoreProc&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#proc_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}
	
	
	this.binDeleteItem = function(id) {
		var txt = ALERT_DELETE_REALLY;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=deleteItem&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#proc_task_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


	this.binRestoreItem = function(id) {
		var txt = ALERT_RESTORE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=restoreItem&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#proc_task_'+id).slideUp();
						}
					}
					});
				} 
			}
		});
	}


	this.markNoticeRead = function(pid) {
		$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=markNoticeRead&pid=" + pid, cache: false});
	}


	this.datepickerOnClose = function(dp) {
		var obj = getCurrentModule();
		if(obj.name != 'procs_rosters' || obj.name != 'procs_grids') {
			$('#'+getCurrentApp()+' .coform').ajaxSubmit(obj.poformOptions);
		}
	}

}

var procs = new procsApplication('procs');
//procs.resetModuleHeights = procsresetModuleHeights;
procs.modules_height = procs_num_modules*module_title_height;
procs.GuestHiddenModules = new Array("access");

// register folder object
function procsFolders(name) {
	this.name = name;
	
	
	this.formProcess = function(formData, form, poformOptions) {
		var title = $("#procs input.title").fieldValue();
		if(title == "") {
			setTimeout(function() {
				title = $("#procs input.title").fieldValue();
				if(title == "") {
					$.prompt(ALERT_NO_TITLE, {submit: setTitleFocus});
				}
			}, 5000)
			return false;
		} else {
			formData[formData.length] = { "name": "title", "value": title };
		}
	}
	
	
	this.formResponse = function(data) {
		switch(data.action) {
			case "edit":
				$("#procs1 span[rel='"+data.id+"'] .text").html($("#procs .title").val());
			break;
		}
	}


	this.poformOptions = { beforeSubmit: this.formProcess, dataType: 'json', success: this.formResponse };

	
	this.actionNew = function() {
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=newFolder", cache: false, success: function(data){
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderList", success: function(list){
				$("#procs1 ul").html(list.html);
				$("#procs1 li").show();
				var index = $("#procs1 .module-click").index($("#procs1 .module-click[rel='"+data.id+"']"));
				setModuleActive($("#procs1"),index);
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderDetails&id="+data.id, success: function(text){
					$("#procs").data("first",data.id);
					$("#"+procs.name+"-right").html(text.html);
					initProcsContentScrollbar();
					$('#procs-right .focusTitle').trigger('click');
					}
				});
				procsActions(9);
				}
			});
			}
		});
	}
	
	
	this.actionBin = function() {
		var txt = ALERT_DELETE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					var id = $("#procs").data("first");
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=binFolder&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderList", success: function(data){
								$("#procs1 ul").html(data.html);
								if(data.html == "<li></li>") {
									procsActions(3);
								} else {
									procsActions(9);
								}
								var id = $("#procs1 .module-click:eq(0)").attr("rel");
								if(typeof id == 'undefined') {
									$("#procs").data("first",0);
								} else {
									$("#procs").data("first",id);
								}
								$("#procs1 .module-click:eq(0)").addClass('active-link');
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderDetails&id="+id, success: function(text){
									$("#"+procs.name+"-right").html(text.html);
									initProcsContentScrollbar();
								}
								});
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
		var id = $("#procs").data("first");
		$("#procs1 .active-link").trigger("click");
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderList", success: function(data){
			$("#procs1 ul").html(data.html);
			if(data.html == "<li></li>") {
				procsActions(3);
			} else {
				procsActions(9);
			}
			var idx = $("#procs1 .module-click").index($("#procs1 .module-click[rel='"+id+"']"));
			$("#procs1 .module-click:eq("+idx+")").addClass('active-link');
			}
		});
	}
	

	this.actionPrint = function() {
		var id = $("#procs").data("first");
		var url ='/?path=apps/procs&request=printFolderDetails&id='+id;
		if(!iOS()) {
			$("#documentloader").attr('src', url);
		} else {
			window.open(url);
		}
	}


	this.actionSend = function() {
		var id = $("#procs").data("first");
		$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=getFolderSend&id="+id, success: function(html){
			$("#modalDialogForward").html(html).dialog('open');
			}
		});
	}


	this.actionSendtoResponse = function() {
			//$("#modalDialogForward").dialog('close');
	}



	this.sortclick = function (obj,sortcur,sortnew) {
		$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderList&sort="+sortnew, success: function(data){
			$("#procs1 ul").html(data.html);
			obj.attr("rel",sortnew);
		  	obj.removeClass("sort"+sortcur).addClass("sort"+sortnew);
			var id = $("#procs1 .module-click:eq(0)").attr("rel");
			$('#procs').data('first',id);
			$("#procs1 .module-click:eq(0)").addClass('active-link');
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=getFolderDetails&id="+id, success: function(text){
				$("#procs-right").html(text.html);
				initProcsContentScrollbar()
				}
			});
			}
		});
	}


	this.sortdrag = function (order) {
		$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=setFolderOrder&"+order, success: function(html){
			$("#procs1 .sort").attr("rel", "3");
			$("#procs1 .sort").removeClass("sort1").removeClass("sort2").addClass("sort3");
			}
		});
	}
	
	
	this.actionDialog = function(offset,request,field,append,title,sql) {
		$.ajax({ type: "GET", url: "/", data: 'path=apps/procs&request='+request+'&field='+field+'&append='+append+'&title='+title+'&sql='+sql, success: function(html){
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
	
	this.actionArchive = function() {
		var module = this;
		/*var cid = $('#projects input[name="id"]').val()*/
		//var id = $("#projects").data("first");
		//module.checkIn(cid);
		var txt = ALERT_ARCHIVE;
		var langbuttons = {};
		langbuttons[ALERT_YES] = true;
		langbuttons[ALERT_NO] = false;
		$.prompt(txt,{ 
			buttons:langbuttons,
			submit: function(e,v,m,f){		
				if(v){
					//var id = $("#projects").data("second");
					var fid = $("#procs").data("first");
					//alert(fid);
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=moveFolderToArchive&&fid=" + fid, cache: false, success: function(data){
						if(data == "true") {
									//$("#projects-right").html(text.html);
									//initProjectsContentScrollbar();
									//module.getNavModulesNumItems(id);
									module.actionRefresh();
									}
									}
								});
					
					
					/*$.ajax({ type: "GET", url: "/", data: "path=apps/projects&request=moveFoldertoArchive&&fid=" + fid, cache: false, success: function(data){
						if(data == "true") {
							$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/projects&request=getFolderDetails&id="+fid, success: function(list){
								$("#projects2 ul").html(list.html);
								if(list.html == "<li></li>") {
									projectsActions(3);
								} else {
									projectsActions(0);
									setModuleActive($("#projects2"),0);
								}
								var id = $("#projects2 .module-click:eq(0)").attr("rel");
								if(typeof id == 'undefined') {
									$("#projects").data("second", 0);
								} else {
									$("#projects").data("second", id);
								}
								$("#projects2 .module-click:eq(0)").addClass('active-link');
								$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/projects&request=getProjectDetails&fid="+fid+"&id="+id, success: function(text){
									$("#projects-right").html(text.html);
									initProjectsContentScrollbar();
									module.getNavModulesNumItems(id);
									}
								});
							}
							});
						}
					}
					});*/
				} 
			}
		});
	}
	

	this.actionHelp = function() {
		var url = "/?path=apps/procs&request=getProcsFoldersHelp";
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
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=deleteFolder&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#folder_'+id).slideUp();
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
					$.ajax({ type: "GET", url: "/", data: "path=apps/procs&request=restoreFolder&id=" + id, cache: false, success: function(data){
						if(data == "true") {
							$('#folder_'+id).slideUp();
						}
						}
					});
				} 
			}
		});
	}

	
}

var procs_folder = new procsFolders('procs_folder');


function procsActions(status) {
	/*	0= new	1= print	2= send		3= duplicate	4= roster		5=refresh 	6 = delete*/
	var obj = getCurrentModule();
	switch(status) {
		//case 0: actions = ['0','1','2','3','5','6']; break;
		case 0: 
			if(obj.name == 'procs') {
				actions = ['0','1','2','3','6','7','8','9'];
			} else {
				actions = ['0','1','2','3','6','8','9'];
			}
		break;
		case 1: actions = ['0','6','8','9']; break;
		case 3: 	actions = ['0','6','8']; break;   					// just new
		//case 4: 	actions = ['0','1','2','4','5']; break;   		// new, print, send, handbook, refresh
		case 4: 	actions = ['0','1','2','5','6','8']; break;
		//case 5: 	actions = ['1','2','5']; break;   			// print, send, refresh
		case 5: 	actions = ['1','2','6','8']; break;
		case 6: 	actions = ['6','7']; break;   			// handbook refresh
		//case 7: 	actions = ['0','1','2','5']; break;   			// new, print, send, refresh
		case 7: 	actions = ['0','1','2','6','8']; break;
		//case 8: 	actions = ['1','2','4','5']; break;   			// print, send, handbook, refresh
		case 8: 	actions = ['1','2','5','6','8']; break;
		//case 9: actions = ['0','1','2','3','4','5','6']; break;
		case 9: actions = ['0','1','2','6','7','8','9']; break;
		
		// vdocs
		// 0 == 10
		case 10: actions = ['0','1','2','3','4','6','8','9']; break;
		// 5 == 11
		case 11: 	actions = ['1','2','4','6','8']; break;   			// print, send, refresh
		
		// rosters
		case 12: actions = ['0','1','2','3','5','6','8','9']; break;
		//procs link
		case 20: actions = ['0','1','2','6','8','9']; break;
		
		
		default: 	actions = ['6','8'];  								// none
	}
	$('#procsActions > li span').each( function(index) {
		if(index in oc(actions)) {
			$(this).removeClass('noactive');
		} else {
			$(this).addClass('noactive');
		}
	})
}

var procsLayout, procsInnerLayout;
var procszIndex = 0; // zindex notes for mindmap
var currentProcEditedNote = 0;

/*function setcEN(id) {
	currentProcEditedNote = id;
}*/

$(document).ready(function() {
	
	procs.init();
	
	if($('#procs').length > 0) {
		procsLayout = $('#procs').layout({
				west__onresize:				function() { resetModuleHeightsnavThree('procs'); }
			,	resizeWhileDragging:		true
			,	spacing_open:				0
			,	spacing_closed:				0
			,	closable: 					false
			,	resizable: 					false
			,	slidable:					false
			, 	west__size:					325
			,	west__closable: 			true
			,	center__onresize: "procsInnerLayout.resizeAll"
			
		});
		
		procsInnerLayout = $('#procs div.ui-layout-center').layout({
				center__onresize:				function() {  
					var obj = getCurrentModule();
					if(obj.name == 'procs') {
						var p = $('#procs-right').offset();
						$("#notesOuter div.note").livequery( function() {
							$(this).draggable('option','containment', [p.left,200]);
						})
					}
				}
			,	resizeWhileDragging:		true
			,	spacing_open:				0
			,	closable: 					false
			,	resizable: 					false
			,	slidable:					false
			,	north__paneSelector:		".center-north"
			,	center__paneSelector:		".center-center"
			,	west__paneSelector:			".center-west"
			, 	north__size:				68
			, 	west__size:					60
		});

		loadModuleStartnavThree('procs');
	}


	$("#procs1-outer").on('click', 'h3', function(e, passed_id) {
		e.preventDefault();
		navThreeTitleFirst('procs',$(this),passed_id)
		prevent_dblclick(e)
	}).disableSelection();


	$("#procs2-outer").on('click', 'h3', function(e, passed_id) {
		e.preventDefault();
		navThreeTitleSecond('procs',$(this),passed_id)
		prevent_dblclick(e)
	}).disableSelection();


	$("#procs3").on('click', 'h3', function(e, passed_id) {
		e.preventDefault();
		navThreeTitleThird('procs',$(this),passed_id)
		prevent_dblclick(e)
	}).disableSelection();


	$('#procs1').on('click', 'span.module-click', function(e) {
		e.preventDefault();
		navItemFirst('procs',$(this))
		prevent_dblclick(e)
	});


	$('#procs2').on('click', 'span.module-click', function(e) {
		e.preventDefault();
		navItemSecond('procs',$(this))
		prevent_dblclick(e)
	});


	$('#procs3').on('click', 'span.module-click', function(e) {
		e.preventDefault();
		navItemThird('procs',$(this))
		prevent_dblclick(e)
	});

	
	$(document).on('click', 'a.insertProcFolderfromDialog', function(e) {
		e.preventDefault();
		var field = $(this).attr("field");
		var gid = $(this).attr("gid");
		var title = $(this).attr("title");
		var html = '<a class="listmember" uid="' + gid + '" field="'+field+'">' + title + '</a>';
		$("#"+field).html(html);
		$("#modalDialog").dialog('close');
		var obj = getCurrentModule();
		$('#procs .coform').ajaxSubmit(obj.poformOptions);
	});
	
	$(document).on('click', 'a.insertProcFolderfromArchiveDialog', function(e) {
		e.preventDefault();
		var field = $(this).attr("field");
		var gid = $(this).attr("gid");
		var title = $(this).attr("title");
		var html = '<span uid="' + gid + '">' + title + '</span>';
		$("#"+field).html(html);
		$("#modalDialog").dialog('close');
	});
	
	
// INTERLINKS FROM Content
	
	// load a proc
	$(document).on('click', '.loadProc', function(e) {
		e.preventDefault();
		var obj = getCurrentModule();
		if(confirmNavigation()) {
			formChanged = false;
			$('#'+getCurrentApp()+' .coform').ajaxSubmit(obj.poformOptions);
		}
		var id = $(this).attr("rel");
		$("#procs2-outer > h3").trigger('click', [id]);
	});

	var tmp;
	// autocomplete procs search
	$('.procs-search').livequery(function() {
		var id = $("#procs").data("second");
		$(this).autocomplete({
			appendTo: '#tabs-1',
			source: "?path=apps/procs&request=getProcsSearch&exclude="+id,
			//minLength: 2,
			select: function(event, ui) {
				var obj = getCurrentModule();
				obj.addParentLink(ui.item.id);
			},
			close: function(event, ui) {
				$(this).val("");
			}
		});
	});
	
	$(document).on('click', '.addProcLink', function(e) {
		e.preventDefault();
		var id = $(this).attr("rel");
		var obj = getCurrentModule();
		obj.addParentLink(id);
	});
	
	
	$("#notesOuter div.note").livequery( function() {
		$(this).each(function(){
			tmp = $(this).css('z-index');
			if(!isNaN) {
			if(tmp>procszIndex) procszIndex = tmp;
			}
		}).draggable({
			containment:[405,200],
			grid:[10,10],
			start: function(e,ui){ 
				var zMax = 0;
				zMax = Math.max.apply(null,$.map($('#procs-right div.note'), function(e,n){
					return parseInt($(e).css('z-index'))||1 ;
				}));
				var z = zMax + 1;
				$(this).css('z-index',z); },
			stop: function(e,ui){
				var x = Math.round(ui.position.left);
				var y = Math.round(ui.position.top);
				var z = $(this).css('z-index');
				var id = $(this).attr("id").replace(/note-/, "");
				$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=updateNotePosition&id="+id+"&x="+x+"&y="+y+"&z="+z, success: function(data){
					}
				});
			}
		})
		.click(function(e) { 
			e.preventDefault();
			var zMax = 0;
			zMax = Math.max.apply(null,$.map($('#procs-right div.note'), function(e,n){
				return parseInt($(e).css('z-index'))||1 ;
			}));
			var z = zMax + 1;
			$(this).css('z-index',z);
			//var loc = $(this).position();
			var x = parseInt($(this).css('left'));
			var y = parseInt($(this).css('top'));
			var id = $(this).attr("id").replace(/note-/, "");
			$.ajax({ type: "GET", url: "/", dataType:  'json', data: "path=apps/procs&request=updateNotePosition&id="+id+"&x="+x+"&y="+y+"&z="+z, success: function(data){
				}
			});
		} )
	})
	
	
	$(document).on('click','#co-popup .saveItemShape>span',function(e) {
		e.preventDefault();
		var ele = $(this);
		var obj = getCurrentModule();
		obj.saveItemShape(ele);
   });
	
	$(document).on('click','#co-popup .saveItemColor>span',function(e) {
		e.preventDefault();
		var ele = $(this);
		var obj = getCurrentModule();
		obj.saveItemColor(ele);
   });
	
	$(document).on('click','#co-popup .saveItemArrow>span',function(e) {
		e.preventDefault();
		var ele = $(this);
		var obj = getCurrentModule();
		obj.saveItemArrow(ele);
   });
	
	$(document).on('click','#co-popup .arrowWidthMore',function(e) {
		e.preventDefault();
		var action = 'more';
		var obj = getCurrentModule();
		obj.saveItemArrowWidth(action);
   });
	$(document).on('click','#co-popup .arrowWidthLess',function(e) {
		e.preventDefault();
		var action = 'less';
		var obj = getCurrentModule();
		obj.saveItemArrowWidth(action);
   });
	$(document).on('click','#co-popup .arrowHeightMore',function(e) {
		e.preventDefault();
		var action = 'more';
		var obj = getCurrentModule();
		obj.saveItemArrowHeight(action);
   });
	$(document).on('click','#co-popup .arrowHeightLess',function(e) {
		e.preventDefault();
		var action = 'less';
		var obj = getCurrentModule();
		obj.saveItemArrowHeight(action);
   });
	$(document).on('click','#co-popup .resetZindex',function(e) {
		e.preventDefault();
		var obj = getCurrentModule();
		obj.resetItemZindex();
   });

});