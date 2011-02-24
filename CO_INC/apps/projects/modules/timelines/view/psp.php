<div>
<table border="0" cellspacing="0" cellpadding="0" class="table-title grey">
  <tr>
    <td class="tcell-left text11"><span class="content-nav">Projektstrukturplan (PSP)</span></td>
    <td>
    <table border="0" cellspacing="0" cellpadding="0" class="timeline-legend">
    <tr>
        <td class="barchart_color_planned"><span><?php echo TIMELINE_STATUS_PLANED;?></span></td>
        <td width="15"></td>
        <td class="barchart_color_inprogress"><span><?php echo TIMELINE_STATUS_INPROGRESS;?></span></td>
         <td width="15"></td>
        <td class="barchart_color_finished"><span><?php echo TIMELINE_STATUS_FINISHED;?></span></td>
         <td width="15"></td>
        <td class="barchart_color_overdue"><span><?php echo TIMELINE_STATUS_NOT_FINISHED;?></span></td>
    </tr>
</table></td>
  </tr>
</table>
</div>
<div class="ui-layout-content barchart-scroll">
<table border="0" cellpadding="0" cellspacing="0" class="table-content no-margin">
	<tr>
		<td class="tcell-left text11"><span class="content-nav">Projekt</span></td>
		<td class="tcell-right">
			<div class="psp-item <?php echo($project["status"]);?>"><a href="#" class="loadProject" rel="<?php echo($project["id"]);?>"><?php echo($project["title"]);?></a><br />
				<div class="psp-item-startdate"><?php echo($project["startdate"]);?></div><div class="psp-item-enddate"><?php echo($project["enddate"]);?></div>
                <div class="psp-connector-project-vert"></div>
			</div>
		</td>
	</tr>
</table>
<?php 
$numPhases = sizeof($project["phases"]);
if($numPhases > 0) { 
$width = $numPhases * 170;
?>
<div class="text11 tbl-inactive" style="position: absolute; padding-left: 15px; height: 58px;" >Phasen</div>
	<div style="width: <?php echo($width+150);?>px">
    <div style="width: 150px; float: left;">
      <div style="height: 58px; margin-bottom: 18px;"></div>
      <div class="text11" style="padding-left: 15px;">Aktivit&auml;ten / Meilensteine</div>
    </div>
	<?php
    
	//echo('<div style="width: ' . $width . 'px">');
	$countPhases = 1;
	foreach($project["phases"] as $key => &$value){ 
		$leftline = ' class="td_border_left"';
		if($countPhases == $numPhases) {
			$leftline='';
		}
		$numTasks = sizeof($project["phases"][$key]["tasks"]);
		$taskline='class="td_border_top_right"';
		if($numTasks == 0) {
			$taskline='';
		}
	?>
    <div style="width: 170px; float: left;">
        <div class="psp-item <?php echo($project["phases"][$key]["status"]);?>">
			<div class="psp-connector-phase-vert"></div>
            <?php if($countPhases > 1) { echo '<div class="psp-connector-phase-hori"></div>'; } ?>
			<a href="#" class="loadPhase" rel="<?php echo($project["phases"][$key]["id"]);?>"><?php echo($countPhases . ". " .$project["phases"][$key]["title"]);?></a>
            <div class="psp-item-startdate"><?php echo($project["phases"][$key]["startdate"]);?></div><div class="psp-item-enddate"><?php echo($project["phases"][$key]["enddate"]);?></div>
        </div>
			<?php
		foreach($project["phases"][$key]["tasks"] as $tkey => &$tvalue){ ?>
             <div class="psp-item <?php echo($project["phases"][$key]["tasks"][$tkey]["status"]);?>">
             <div class="psp-connector-vert"></div>
            <a href="#" class="loadPhase" rel="<?php echo($project["phases"][$key]["id"]);?>"><?php echo($project["phases"][$key]["tasks"][$tkey]["text"]);?></a>
            <?php if($project["phases"][$key]["tasks"][$tkey]["cat"] == 0) { ?>
				<div class="psp-item-startdate"><?php echo($project["phases"][$key]["tasks"][$tkey]["startdate"]);?></div><div class="psp-item-enddate"><?php echo($project["phases"][$key]["tasks"][$tkey]["enddate"]);?></div>
			<?php } else { ?>
				<div class="psp-item-startdate"><span class="icon-milestone"></span></div><div class="psp-item-enddate"><?php echo($project["phases"][$key]["tasks"][$tkey]["enddate"]);?></div>
			<?php }?>
        </div>
		<?php } ?>
</div>
    <?php 
    $countPhases++;
    }
}
?>
</div>
</div>
<div>
<table border="0" cellspacing="0" cellpadding="0" class="table-footer">
  <tr>
    <td class="left">Stand <?php echo($project["datetime"]);?></td>
    <td class="middle"></td>
    <td class="right"></td>
  </tr>
</table>
</div>