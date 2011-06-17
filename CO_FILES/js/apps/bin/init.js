/* bin Object */
var bin = new Application('bin');
bin.path = 'apps/bin/';
bin.resetModuleHeights = binresetModuleHeights;
bin.usesLayout = true;
bin.displayname = "Bin";
bin.actionRefresh = refreshBin;
bin.actionBin = binBin;
bin.checkIn = checkInBin;
bin.modules_height = bin_num_modules*module_title_height;

function refreshBin() {
	$("#bin1 .active-link").trigger("click");
}

function binBin() {
	var id = $("#bin1 .active-link").attr("rel");
	//alert("work in progress - delete all in " + id);
	var txt = ALERT_DELETE_BIN;
	var langbuttons = {};
	langbuttons[ALERT_YES] = true;
	langbuttons[ALERT_NO] = false;
	$.prompt(txt,{ 
		buttons:langbuttons,
		callback: function(v,m,f){		
			if(v){
				$.ajax({ type: "GET", url: "/", data: "path=apps/"+ id +"&request=emptyBin", success: function(html){
					$("#bin-right").html(html);
					binInnerLayout.initContent('center');
					}
				});
			} 
		}
	});
}

function binActions(status) {
	/*	0= refresh	1= delete	*/
	switch(status) {
		case 0: actions = ['0','1']; break;
		//case 0: actions = []; break;
		default: 	actions = [];  	// none
	}
	$('#binActions > li span').each( function(index) {
		if(index in oc(actions)) {
			$(this).removeClass('noactive');
		} else {
			$(this).addClass('noactive');
		}
	})
}


function binloadModuleStart() {
	$("#bin-current").val("bin");
	binActions(0);
	var id = $("#bin1 .module-click:eq(0)").attr("rel");
	$("#bin1 .module-click:eq(0)").addClass('active-link');
	$.ajax({ type: "GET", url: "/", data: "path=apps/"+ id +"&request=getBin", success: function(html){
		$("#bin-right").html(html);
		binInnerLayout.initContent('center');
		$('#bin1 input.filter').quicksearch('#bin1 li');
					
		}
	});
}


function binresetModuleHeights() {
	
	var h = $("#bin .ui-layout-west").height();
	if($("#bin1").height() != module_title_height) {
		$("#bin1").css("height", h-46);
		$("#bin1 .module-inner").css("height", h-46);
	}
	//initScrollbar( '#bin .scrolling-content' );
}

function checkInBin() {
	return true;
}



var binLayout, binInnerLayout;

$(document).ready(function() { 

	binLayout = $('#bin').layout({
			west__onresize:				function() { binresetModuleHeights() }
		,	resizeWhileDragging:		true
		,	spacing_open:				0
		,	closable: 				false
		,	resizable: 				false
		,	slidable:				false
		, 	west__size:				325
		,	west__closable: 		true
		,	west__resizable: 		true
		, 	south__size:			10
		,	center__onresize: "binInnerLayout.resizeAll"
		
	});
	
	binInnerLayout = $('#bin div.ui-layout-center').layout({
			center__onresize:				function() { }
		,	resizeWhileDragging:		false
		,	spacing_open:				0			// cosmetic spacing
		,	closable: 				false
		,	resizable: 				false
		,	slidable:				false
		,	north__paneSelector:	".center-north"
		,	center__paneSelector:	".center-center"
		,	west__paneSelector:	".center-west"
		, 	north__size:			80
		, 	west__size:			50
		 

	});
	
	binloadModuleStart();


	$("#bin1-outer > h3").click(function() {
		if(confirmNavigation()) {
			formChanged = false;
			var obj = getCurrentModule();
			$('#'+getCurrentApp()+' .coform').ajaxSubmit(obj.poformOptions);
		}
		$("#bin1 .module-click").removeClass('active-link');
		var id = $("#bin1 .module-click:eq(0)").attr("rel");
		$("#bin1 .module-click:eq(0)").addClass('active-link');
		$.ajax({ type: "GET", url: "/", data: "path=apps/"+ id +"&request=getBin", success: function(html){
			$("#bin-right").html(html);
			binInnerLayout.initContent('center');
			}
		 });
		return false;
	});


	$("#bin1 .module-click").live('click',function(e) {
		if($(this).hasClass("deactivated")) {
			return false;
		}
		var id = $(this).attr("rel");
		var index = $("#bin .module-click").index(this);
		$("#bin .module-click").removeClass("active-link");
		$(this).addClass("active-link");
			
		$.ajax({ type: "GET", url: "/", data: "path=apps/"+ id +"&request=getBin", success: function(html){
			$("#bin-right").html(html);
			binInnerLayout.initContent('center');
			}
		});
		binActions(0);
		return false;
	});


});