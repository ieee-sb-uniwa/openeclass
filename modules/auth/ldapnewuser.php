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


/*===========================================================================
	ldapnewuser.php
* @version $Id$
	@authors list: Karatzidis Stratos <kstratos@uom.gr>
		       Vagelis Pitsioygas <vagpits@uom.gr>
==============================================================================
  @Description: Introductory file that displays a form, requesting
  from the user/prof to enter the account settings and authenticate
  him/her against the predefined method of the platform


==============================================================================
*/

include '../../include/baseTheme.php';
include 'auth.inc.php';

$navigation[]= array ("url"=>"registration.php", "name"=> "$langNewUser");

if (isset($_REQUEST['auth'])) {
	$auth = intval($_REQUEST['auth']);
	$_SESSION['u_tmp'] = $auth;
}
if(!isset($_REQUEST['auth'])) {
	$auth = 0;
	$auth = $_SESSION['u_tmp'];
}

$authmethods = get_auth_active_methods();

$msg = get_auth_info($auth);
$settings = get_auth_settings($auth);
if(!empty($msg)) $nameTools = "$langConfirmUser ($msg)";

if (isset($_GET['p']) and ($_GET['p'] == true)) {
	$tool_content .= "<form method='post' action='ldapsearch_prof.php'>";
} else {
	$tool_content .= "<form method='post' action='ldapsearch.php'>";
}
$tool_content .= "<fieldset><legend>".$settings['auth_instructions']."</legend>
<table class='tbl' width='100%'>";

if (($auth != 7) and ($auth != 6)) {
	@$tool_content .= "
	<tr>
	<th width='180'>$langAuthUserName</th>
	<td><input type='text' name='ldap_email' value='$ldap_email'></td>
	</tr>
	<tr>
	<th>$langAuthPassword</th>
	<td><input type='password' name='ldap_passwd' value='$ldap_passwd'></td>
	</tr>";
}

$tool_content .= "<tr>
     <td>&nbsp;</td>
     <td class='right'>
<input type='hidden' name='auth' value='".$auth."'>";

if (($auth != 7) and ($auth != 6)) {
	$tool_content .= "<input type='submit' name='is_submit' value='$langSubmit'>";
} else {
	$tool_content .= "<input type='submit' name='is_submit' value='$langCheck'>";
}

$tool_content .= "</td></tr></table></fieldset></form>";

draw($tool_content, 0);
