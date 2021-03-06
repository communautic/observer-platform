<div class="table-title-outer">
<div id="procs_folder-action-new" style="display: none"><?php echo $lang["PROC_FOLDER_ACTION_NEW"];?></div>
<table border="0" cellpadding="0" cellspacing="0" class="table-title">
	<tr>
		<td class="tcell-left text11"><span class="<?php if($folder->canedit) { ?>content-nav focusTitle<?php } ?>"><span><?php echo $lang["PROC_FOLDER"];?></span></span></td>
		<td><?php if($folder->canedit) { ?><input name="title" type="text" class="title textarea-title" value="<?php echo($folder->title);?>" maxlength="100" /><?php } else { ?><div class="textarea-title"><?php echo($folder->title);?></div><?php } ?></td>
	</tr>
</table>
</div>
<div class="ui-layout-content"><div class="scroll-pane">
<?php if($folder->access == "sysadmin") { ?>
<form action="/" method="post" name="coform" class="<?php if($folder->canedit) { ?>coform <?php } ?>">
<input type="hidden" id="path" name="path" value="<?php echo $this->form_url;?>">
<input type="hidden" id="poformaction" name="request" value="setFolderDetails">
<input type="hidden" name="id" value="<?php echo($folder->id);?>">
<input name="procstatus" type="hidden" value="0" />
</form>
<div class="content-spacer"></div>
<?php } ?>
<table border="0" cellpadding="0" cellspacing="0" class="table-content">
	<tr>
		<td class="tcell-left-inactive text11"><?php echo $lang["PROC_PROCS"];?></td>
    <td class="tcell-right">&nbsp;</td>
    </tr>
</table>
<?php
if(is_array($procs)) {
	foreach ($procs as $proc) { 
	?>
    
    <div class="loadProc listOuter"  rel="<?php echo($proc->id);?>">
    <div class="bold co-link listTitle"><?php echo($proc->title);?></div>
    <div class="text11 listText"><div><?php echo $lang["PROC_FOLDER_CREATED_ON"];?> <?php echo($proc->created_date);?> &nbsp; | &nbsp; </div><div><?php echo $lang["PROC_FOLDER_INITIATOR"];?> <?php echo($proc->created_user);?> &nbsp; </div></div>
    </div>
    <?php 
	}
}
?>
</div>
</div>
<div>
<table border="0" cellspacing="0" cellpadding="0" class="table-footer">
  <tr>
    <td class="left"><?php echo($lang["GLOBAL_FOOTER_STATUS"] . " " . $folder->today);?></td>
    <td class="middle"></td>
    <td class="right"></td>
  </tr>
</table>
</div>