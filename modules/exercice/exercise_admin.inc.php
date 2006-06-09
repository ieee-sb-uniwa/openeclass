<?php // $Id$
/*=============================================================================
       	GUnet e-Class 2.0 
        E-learning and Course Management Program  
================================================================================
       	Copyright(c) 2003-2006  Greek Universities Network - GUnet
        � full copyright notice can be read in "/info/copyright.txt".
        
       	Authors:    Costas Tsibanis <k.tsibanis@noc.uoa.gr>
        	    Yannis Exidaridis <jexi@noc.uoa.gr> 
      		    Alexandros Diamantidis <adia@noc.uoa.gr> 

        For a full list of contributors, see "credits.txt".  
     
        This program is a free software under the terms of the GNU 
        (General Public License) as published by the Free Software 
        Foundation. See the GNU License for more details. 
        The full license can be read in "license.txt".
     
       	Contact address: GUnet Asynchronous Teleteaching Group, 
        Network Operations Center, University of Athens, 
        Panepistimiopolis Ilissia, 15784, Athens, Greece
        eMail: eclassadmin@gunet.gr
==============================================================================*/

/*===========================================================================
	work.php
	@last update: 17-4-2006 by Costas Tsibanis
	@authors list: Dionysios G. Synodinos <synodinos@gmail.com>
==============================================================================        
        @Description: Main script for the work tool

 	This is a tool plugin that allows course administrators - or others with the
 	same rights

 	The user can : - navigate through files and directories.
                       - upload a file
                       - delete, copy a file or a directory
                       - edit properties & content (name, comments, 
			 html content)

 	@Comments: The script is organised in four sections.

 	1) Execute the command called by the user
           Note (March 2004) some editing functions (renaming, commenting)
           are moved to a separate page, edit_document.php. This is also
           where xml and other stuff should be added.
   	2) Define the directory to display
  	3) Read files and directories from the directory defined in part 2
  	4) Display all of that on an HTML page
 
  	@TODO: eliminate code duplication between document/document.php, scormdocument.php
==============================================================================
*/

// ALLOWED_TO_INCLUDE is defined in admin.php
if(!defined('ALLOWED_TO_INCLUDE'))
{
	exit();
}

// the exercise form has been submitted
if(isset($submitExercise))
{
	$exerciseTitle=trim($exerciseTitle);
	$exerciseDescription=trim($exerciseDescription);
	@$randomQuestions=$randomQuestions?$questionDrawn:0;

	// no title given
	if(empty($exerciseTitle))
	{
		$msgErr=$langGiveExerciseName;
	}
	else
	{
		$objExercise->updateTitle($exerciseTitle);
		$objExercise->updateDescription($exerciseDescription);
		$objExercise->updateType($exerciseType);
		
		$objExercise->updateStartDate($exerciseStartDate);
		$objExercise->updateEndDate($exerciseEndDate);
		$objExercise->updateTimeConstrain($exerciseTimeConstrain);
		$objExercise->updateAttemptsAllowed($exerciseAttemptsAllowed);
		
		$objExercise->setRandom($randomQuestions);
		$objExercise->save();

		// reads the exercise ID (only usefull for a new exercise)
		$exerciseId=$objExercise->selectId();

		unset($modifyExercise);
	}
}
else
{
	$exerciseTitle=$objExercise->selectTitle();
	$exerciseDescription=$objExercise->selectDescription();
	$exerciseType=$objExercise->selectType();
	$exerciseStartDate=$objExercise->selectStartDate();
	$exerciseEndDate=$objExercise->selectEndDate();
	$exerciseTimeConstrain=$objExercise->selectTimeConstrain();
	$exerciseAttemptsAllowed=$objExercise->selectAttemptsAllowed();
	$randomQuestions=$objExercise->isRandom();
}

// shows the form to modify the exercise
if(isset($modifyExercise))
{

$tool_content .= <<<cData
	<form method="post" action="${PHP_SELF}?modifyExercise=${modifyExercise}">
	<table border="0" cellpadding="5">
cData;

	if(!empty($msgErr))
	{

		$tool_content .= <<<cData
			<tr>
			  <td colspan="2">
				<table border="0" cellpadding="3" align="center" width="400" bgcolor="#FFCC00">
				<tr>
				  <td>$msgErr</td>
				</tr>
				</table>
			  </td>
			</tr>
cData;
	}
/////////////////////////////////////////////////////////////////////////////////
//$tool_content .= "<tr><td>".$langExerciseName." :</td><td><input type=\"text\" name=\"exerciseTitle\" ".
//	"size=\"50\" maxlength=\"200\" value=\"".htmlspecialchars($exerciseTitle)." style=\"width:400px;\"></td></tr>";

$tool_content .= "<tr><td>".$langExerciseName." :</td><td><input type=\"text\" name=\"exerciseTitle\" ".
	"size=\"50\" maxlength=\"200\" value=\"".htmlspecialchars($exerciseTitle)."\" style=\"width:400px;\"></td></tr>";

$tool_content .= "<tr><td valign=\"top\">".$langExerciseDescription." :</td><td><textarea wrap=\"virtual\" ".
	"name=\"exerciseDescription\" cols=\"50\" rows=\"4\" style=\"width:400px;\">".
	htmlspecialchars($exerciseDescription)."</textarea></td></tr>";
	
$tool_content .= "<tr><td valign=\"top\">".$langExerciseType." :</td><td>".
	"<input type=\"radio\" name=\"exerciseType\" value=\"1\" ";
	
if($exerciseType <= 1) 
	$tool_content .= " checked=\"checked\"";
$tool_content .= ">".$langSimpleExercise."<br><input type=\"radio\" name=\"exerciseType\" value=\"2\" ";

if($exerciseType >= 2) 
	$tool_content .= 'checked="checked"';
$tool_content .= "> ".$langSequentialExercise."</td></tr>";

$tool_content .= "<td valign=\"top\">".$langExerciseStart." :</td><td><input type=\"text\" name=\"exerciseStartDate\" ".
	"value=\"".htmlspecialchars($exerciseStartDate)."\" size=\"22\" maxlength=\"19\"> ".
  "(".$langExerciseEg." 1977-06-29 12:00:00)</td></tr>";
  
$tool_content .= "<tr><td valign=\"top\">".$langExerciseEnd." :</td>".
	"<td><input type=\"text\" name=\"exerciseEndDate\" value=\"".htmlspecialchars($exerciseEndDate)."\" ". 
 	"size=\"22\" maxlength=\"19\">". 
  "(".$langExerciseEg." 1977-06-29 12:00:00)</td></tr>";
  
$tool_content .= "<tr><td valign=\"top\">".$langExerciseConstrain." :</td>".
	"<td><input type=\"text\" name=\"exerciseTimeConstrain\" size=\"3\" maxlength=\"3\" ". 
  "value=\"".htmlspecialchars($exerciseTimeConstrain)."\">". 
  $langExerciseConstrainUnit." (".$langExerciseConstrainExplanation.")</td></tr>";
  
$tool_content .= "<tr><td valign=\"top\">".$langExerciseAttemptsAllowed." :</td>".
	"<td><input type=\"text\" name=\"exerciseAttemptsAllowed\" size=\"2\" maxlength=\"2\"". 
  "value=\"".htmlspecialchars($exerciseAttemptsAllowed)."\">". 
  $langExerciseAttemptsAllowedUnit." (".$langExerciseAttemptsAllowedExplanation.")</td></tr>";
/////////////////////////////////////////////////////////////////////////////////
	if($exerciseId && $nbrQuestions)
	{

		$tool_content .= "<tr><td valign=\"top\">".$langRandomQuestions." :</td>".
  		"<td><input type=\"checkbox\" name=\"randomQuestions\" value=\"1\" "; 
  	
  	if($randomQuestions) 
  		$tool_content .= "checked=\"checked\"";  
  	$tool_content .= ">".$langYes.", $langTake";
  	
    $tool_content .= "<select name=\"questionDrawn\">";

		for($i=1;$i <= $nbrQuestions;$i++)
		{

			$tool_content .= "<option value=\"".$i." ";
			
			if((isset($formSent) && $questionDrawn == $i) || (!isset($formSent) && ($randomQuestions == $i || ($randomQuestions <= 0 && $i == $nbrQuestions)))) 
				$tool_content .= 'selected="selected"'; 
			
			$tool_content .=">".$i."</option>";
		}

		$tool_content .= "</select> ".strtolower($langQuestions)." ".$langAmong." ".$nbrQuestions." </td></tr>";

	}

	$tool_content .= <<<cData
		<tr>
		  <td colspan="2" align="center">
			<input type="submit" name="submitExercise" value="${langOk}">
			&nbsp;&nbsp;<input type="submit" name="cancelExercise" value="${langCancel}">
		  </td>
		</tr>
		</table>
		</form>
cData;

}
else
{

$tool_content .= "<h3>".$exerciseTitle."</h3>";

$tool_content .= <<<cData

<blockquote>
	<table border="0">
		<tr>
		  <td valign="top">$langExerciseDescription :</td>
		  <td>
cData;

$tool_content .= nl2br($exerciseDescription);

$tool_content .= <<<cData
		  </td>
		</tr>
		<tr>
		  <td valign="top">${langExerciseStart} :</td>
			<td>${exerciseStartDate}</td>
		</tr>
		<tr>
		  <td valign="top">${langExerciseEnd} :</td>
			<td>${exerciseEndDate}</td>
		</tr>
		<tr>
		  <td valign="top">${langExerciseConstrain} :</td>
			<td>${exerciseTimeConstrain}</td>
		</tr>
		<tr>
		  <td valign="top">${langExerciseAttemptsAllowed} :</td>
		  <td>${exerciseAttemptsAllowed}</td>
		</tr>
	</table>
</blockquote>
cData;

$tool_content .= "<a href=\"".$PHP_SELF."?modifyExercise=yes\"><img src=\"../../images/edit.gif\" ".
	"border=\"0\" align=\"absmiddle\" alt=\"".$langModify."\"></a>";

}
?>
