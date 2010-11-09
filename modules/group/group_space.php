<?php
/*========================================================================
*   Open eClass 2.3
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2010  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/
/*
 * Groups Component
 *
 * @author Evelthon Prodromou <eprodromou@upnet.gr>
 * @version $Id$
 *
 * @abstract This module is responsible for the user groups of each lesson
 *
 */

$require_login = true;
$require_current_course = true;
$require_help = true;
$helpTopic = 'Group';

include '../../include/baseTheme.php';
$nameTools = $langGroupSpace;
$navigation[] = array('url' => 'group.php', 'name' => $langGroups);

include 'group_functions.php';
mysql_select_db($mysqlMainDb);
initialize_group_id();
initialize_group_info($group_id);

if (isset($_GET['selfReg'])) {
	if (isset($uid) and !$is_member and $statut != 10 and $member_count < $max_members) {
                $sqlReg = mysql_query("INSERT INTO group_members SET user_id = $uid, group_id = $group_id");
                $message = "<font color=red>$langGroupNowMember</font>: ";
                $regDone = $is_member = true;
	} else { 
		$tool_content .= $langForbidden;
		draw($tool_content, 2);
		exit;
	}
}
if (!$is_member and !$is_adminOfCourse and (!$self_reg or $member_count >= $max_members)) {
        $tool_content .= $langForbidden;
        draw($tool_content, 2);
        exit;
}

$tool_content .= "<div id='operations_container'><ul id='opslist'>\n";
if ($is_adminOfCourse or $is_tutor) {
                $tool_content .= "<li><a href='group_edit.php?group_id=$group_id'>$langEditGroup</a></li>\n";
} elseif ($self_reg and isset($uid) and !$is_member and $member_count < $max_members) { 
                $tool_content .=  "<li><a href='$_SERVER[PHP_SELF]?registration=1&amp;group_id=$group_id'>$langRegIntoGroup</a></li>\n";
} elseif (isset($regDone)) {
        $tool_content .= "$message&nbsp;";
}

$tool_content .= loadGroupTools();
$tool_content .=  "<br /><table width='99%' class='FormData'>
	<thead><tr>
	<th width='220'>&nbsp;</th>
	<td><b>$langGroupInfo</b></td>
	</tr>
	<tr>
	<th class='left'>$langGroupName :</th>
	<td>" . q($group_name) . "</td>
	</tr>";

$tutors = array();
$members = array();
$q = db_query("SELECT user.user_id, nom, prenom, email, am, is_tutor, description
                      FROM group_members, user
                      WHERE group_id = $group_id AND
                            group_members.user_id = user.user_id
                      ORDER BY nom, prenom");
while ($user = mysql_fetch_array($q)) {
        if ($user['is_tutor']) {
                $tutors[] = display_user($user, true);
        } else {
                $members[] = $user;
        }
}

if ($tutors) {
        $tool_content_tutor = implode(', ', $tutors);
} else {
        $tool_content_tutor =  $langGroupNoTutor;
}

$tool_content .= "<tr><th class='left'>$langGroupTutor :</th>
<td>$tool_content_tutor</td></tr>";

$group_description = trim($group_description);
if (empty($group_description)) {
        $tool_content_description = $langGroupNone;
} else {
        $tool_content_description = q($group_description);
}

$tool_content .= "<tr><th class='left'>$langDescription :</th>
<td>$tool_content_description</td></tr>";

// members
$tool_content .= "<tr><th class='left' valign='top'>$langGroupMembers :</th>
<td><table width='99%' align='center' class='GroupSum'>
<thead>
<tr>
<td><b>$langNameSurname</b></td>
<td width='100'><div align='center'><b>$langAm</b></div></td>
<td><div align='center'><b>$langEmail</b></div></td>
</tr>
</thead>
<tbody>";

if ($members) {
	foreach ($members as $member){
		$user_group_description = $member['description'];
		$tool_content .= "<tr><td>" . display_user($member);  
		if ($user_group_description) {
			$tool_content .= "<br />".q($user_group_description);
		}
                $tool_content .= "</td><td class='center'>";
		if (!empty($member['am'])) {
			$tool_content .=  q($member['am']);
		} else {
			$tool_content .= '-';
		}
                $tool_content .= "</td><td class='center'>";
                $email = q(trim($member['email']));
                if (!empty($email)) {
                        $tool_content .= "<a href='mailto:$email'>$email</a>";
                } else {
                        $tool_content .= '-';
                }
                $tool_content .= "</td></tr>\n";
	}
} else {
	$tool_content .= "<tr><td colspan='3'>$langGroupNoneMasc</td></tr>";
}

$tool_content .=  "</tbody></table>";
$tool_content .= "</td></tr></thead></table>";
draw($tool_content, 2);


function loadGroupTools(){
        global $self_reg, $has_forum, $forum_id, $documents, $secret_directory, $langForums,
               $group_id, $langDoc, $is_adminOfCourse, $is_tutor, $group_id, $langEmailGroup,
               $langUsage;

	$group_tools = '';
        if (!$self_reg) {
        }
        // Drive members into their own forum
        if ($has_forum and $forum_id <> 0) {
                $group_tools .= "<li><a href='../phpbb/viewforum.php?forum=$forum_id'>$langForums</a></li>";
        }
        // Drive members into their own File Manager
        if ($documents) {
                 $group_tools .=  "<li><a href='document.php?gid=$group_id'>$langDoc</a></li>";
        }
        if ($is_adminOfCourse or $is_tutor) {
                $group_tools .=  "<li><a href='group_email.php?group_id=$group_id'>$langEmailGroup</a></li>
                <li><a href='group_usage.php?group_id=$group_id'>$langUsage</a></li>";
        }
	$group_tools .= "</ul></div>";
	return $group_tools;
}
