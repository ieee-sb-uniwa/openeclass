<?php
/* ========================================================================
 * Open eClass 2.4
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2011  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */


/*
 * Index
 *
 * @author Evelthon Prodromou <eprodromou@upnet.gr>
 * @version $Id$
 *
 * @abstract Password change component (for platform administrator)
 *
 */
$require_login = true;
$require_admin = TRUE;
$helpTopic = 'Profile';
$require_valid_uid = TRUE;

require_once '../../include/phpass/PasswordHash.php';
include '../../include/baseTheme.php';

$nameTools = $langChangePass;
$navigation[] = array('url' => 'index.php', 'name' => $langAdmin);
$navigation[] = array ('url' => 'edituser.php', 'name'=> $langEditUser);

check_uid();
$tool_content = "";

if (!isset($urlSecure)) {
	$passurl = $urlServer.'modules/admin/password.php';
} else {
	$passurl = $urlSecure.'modules/admin/password.php';
}

if (!isset($_POST['changePass'])) {
        if (!isset($_GET['userid'])) {
                header("Location: {$urlServer}modules/admin/");
                exit;
        }
	$tool_content .= "
<form method='post' action='$passurl'>
<fieldset>
  <legend>$lang_remind_pass</legend>
  <input type='hidden' name='userid' value='$_GET[userid]' />
  <table class='tbl' width='100%'>
  <tr>
    <th class='left' width='160'>$langNewPass1:</th>
    <td><input type='password' size='40' name='password_form' value='' /></td>
  </tr>
  <tr>
    <th class='left'>$langNewPass2:</th>
    <td><input type='password' size='40' name='password_form1' value='' /></td>
  </tr>
  <tr>
    <th class='left'>&nbsp;</th>
    <td class='right'><input type='submit' name='changePass' value='$langModify' /></td>
  </tr>
  </table>
</fieldset>
</form>";
} else {
	$userid = intval($_POST['userid']);
	if (empty($_POST['password_form']) || empty($_POST['password_form1'])) {
		$tool_content .= mes($langFieldsMissing, '', 'caution');
		draw($tool_content, 3);
		exit();
	}
	if ($_POST['password_form1'] !== $_POST['password_form']) {
		$tool_content .= mes($langPassTwo, '', 'caution_small');
		draw($tool_content, 3);
		exit();
	}
	// All checks ok. Change password!
	$hasher = new PasswordHash(8, false);
	$new_pass = $hasher->HashPassword($_POST['password_form']);
	$sql = "UPDATE `user` SET `password` = '$new_pass' WHERE `user_id` = $userid";
	db_query($sql, $mysqlMainDb);
	$tool_content .= mes($langPassChanged, $langHome, 'success');
	draw($tool_content, 3);
	exit();
}

draw($tool_content, 3);

// display message
function mes($message, $urlText, $type) {
	global $urlServer, $langBack, $userid;

 	$str = "<p class='$type'>$message</p><br /><p><a href='$_SERVER[SCRIPT_NAME]?userid=$userid'>$langBack</a></p>";
	return $str;
}
