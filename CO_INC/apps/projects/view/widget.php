<?php
$message = 1;
if(is_array($reminders)) {
	$message = 0;
	foreach ($reminders as $reminder) { ?>
		<div class="widgetItemOuter projectsLink" rel="phases,<?php echo $reminder->folder . ',' . $reminder->pid . ',' . $reminder->phaseid;?>"><div class="widgetItemTitle"><div class="widgetIconReminder"></div>
    <?php
		if($reminder->cat == 0) {
			echo $lang["PROJECT_WIDGET_TITLE_TASK"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_REMINDER_TASK"], $reminder->text, $reminder->projectitle);
		} else {
			echo $lang["PROJECT_WIDGET_TITLE_MILESTONE"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_REMINDER_MILESTONE"], $reminder->text, $reminder->projectitle);
		}
		?>
    	</div></div>
    <?php
	}
}

if(is_array($kickoffs)) {
	$message = 0;
	foreach ($kickoffs as $kickoff) { ?>
    	<div class="widgetItemOuter projectsLink" rel="projects,<?php echo $kickoff->folder . ',' . $kickoff->pid . ',0';?>"><div class="widgetItemTitle"><div class="widgetIconKickoff"></div><?php echo $lang["PROJECT_WIDGET_TITLE_KICKOFF"] ;?></div><div class="widgetItemContent">
			<?php echo sprintf($lang["PROJECT_WIDGET_REMINDER_KICKOFF"], $kickoff->title); ?> 
            </div></div>
    <?php
	}
}

if(is_array($alerts)) {
	$message = 0;
	foreach ($alerts as $alert) { ?>
		<div class="widgetItemOuter projectsLink" rel="phases,<?php echo $alert->folder . ',' . $alert->pid . ',' . $alert->phaseid;?>"><div class="widgetItemTitle"><div class="widgetIconAlert"></div>
    <?php
		if($alert->cat == 0) {
			echo $lang["PROJECT_WIDGET_TITLE_TASK"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_ALERT_TASK"], $alert->text, $alert->projectitle);
		} else {
			echo $lang["PROJECT_WIDGET_TITLE_MILESTONE"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_ALERT_MILESTONE"], $alert->text, $alert->projectitle);
		} ?>
    	</div></div>
    <?php
	}
}

if(is_array($notices)) {
	$message = 0;
	foreach ($notices as $notice) { ?>
		<div><div class="widgetItemOuter Read projectsLinkMarkRead" rel="projects,<?php echo $notice->folder . ',' . $notice->pid . ',0';?>">
        <div class="widgetItemTitle"><div class="widgetIconNotice"></div>
    <?php
		if($notice->perm == 0) {
			echo $lang["PROJECT_WIDGET_TITLE_PROJECT"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_INVITATION_ADMIN"], $notice->projectitle);
		} else {
			echo $lang["PROJECT_WIDGET_TITLE_PROJECT"] . '</div><div class="widgetItemContent">';
			echo sprintf($lang["PROJECT_WIDGET_INVITATION_GUEST"], $notice->projectitle);
		} ?>
    	</div></div>
        <div class="widgetItemRead"><span class="projectsInlineMarkRead text11 yellow co-link" rel="projects,<?php echo $notice->folder . ',' . $notice->pid . ',0';?>"><?php echo $lang["WIDGET_REMOVE_NOTICE"];?></span></div></div>
    <?php
	}
}


if(is_array($projectlinks)) {
	$message = 0;
	foreach ($projectlinks as $projectlink) { ?>
    <?php
	if($projectlink->perm < 5) { ?>
		<div><div class="widgetItemOuter Read projectsLinkDelete" link="<?php echo $projectlink->noticeid;?>" rel="phases,<?php echo $projectlink->relfolder . ',' . $projectlink->relid . ',' . $projectlink->phid . ',projects';?>">
        <div class="widgetItemTitle">
        <div class="widgetIconAlert"></div><?php echo $lang["PROJECT_WIDGET_PROJECTLINK_TITLE"];?></div><div class="widgetItemContent">
	<?php } else { ?>
		<div><div class="widgetItemOuter Read projectsLinkDelete" link="<?php echo $projectlink->noticeid;?>" rel="projects,<?php echo $projectlink->folder . ',' . $projectlink->pid . ',0,projects';?>">
        <div class="widgetItemTitle">
        <div class="widgetIconAlert"></div><?php echo $lang["PROJECT_WIDGET_PROJECTLINK_NOTICE_TITLE"];?></div><div class="widgetItemContent">
	<?php }
		switch($projectlink->perm) {
			case 2:
				echo sprintf($lang["PROJECT_WIDGET_PROJECTLINK_STARTEND"], $projectlink->projectitle);
			break;
			case 3:
				echo sprintf($lang["PROJECT_WIDGET_PROJECTLINK_START"], $projectlink->projectitle);
			break;
			case 4:
				echo sprintf($lang["PROJECT_WIDGET_PROJECTLINK_END"], $projectlink->projectitle);
			break;
			case 5:
				echo sprintf($lang["PROJECT_WIDGET_PROJECTLINK_NOTICE"], $projectlink->projectitle, $projectlink->reltitle);
			break;
		}
	?>
    	</div></div>
        <?php 
        if($projectlink->perm < 5) { ?>
            <div class="widgetItemRead"><span class="projectsLinkInlineDelete text11 yellow co-link" link="<?php echo $projectlink->noticeid;?>" rel="phases,<?php echo $projectlink->relfolder . ',' . $projectlink->relid . ',' . $projectlink->phid . ',projects';?>"><?php echo $lang["WIDGET_REMOVE_NOTICE"];?></span></div></div>
        <?php } else { ?>
            <div class="widgetItemRead"><span class="projectsLinkInlineDelete text11 yellow co-link" link="<?php echo $projectlink->noticeid;?>" rel="projects,<?php echo $projectlink->folder . ',' . $projectlink->pid . ',0,projects';?>"><?php echo $lang["WIDGET_REMOVE_NOTICE"];?></span></div></div>
		<?php }
	}
}


if($message == 1) {
	echo $lang["PROJECT_WIDGET_NO_ACTIVITY"];
}
?>