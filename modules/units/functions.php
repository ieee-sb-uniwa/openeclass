<?php
/**
 * @file functions.php
 * @brief Units utility functions
 */

require_once 'include/lib/mediaresource.factory.php';
require_once 'include/lib/multimediahelper.class.php';

/**
 * @brief  Process resource actions
 * @global type $tool_content
 * @global type $id
 * @global type $langResourceCourseUnitDeleted
 * @global type $langResourceUnitModified
 * @global type $course_id
 * @global type $course_code
 * @return string
 */
function process_actions() {
    global $tool_content, $id, $langResourceCourseUnitDeleted,
        $langResourceUnitModified, $course_id, $course_code, $webDir,
        $head_content, $langBack, $urlAppend, $navigation, $pageName,
        $langEditChange, $langViMod;

    // update index and refresh course metadata
    require_once 'modules/search/indexer.class.php';
    require_once 'modules/course_metadata/CourseXML.php';
    if (isset($_REQUEST['edit'])) {
        $res_id = intval($_GET['edit']);
        if (check_admin_unit_resource($res_id)) {
            $q = Database::get()->querySingle("SELECT title FROM course_units
                WHERE id = ?d AND course_id = ?d", $id, $course_id);
            $navigation[] = array('url' => "index.php?course=$course_code&amp;id=$id", 'name' => $q->title);
            $pageName = $langEditChange;
            $tool_content .= action_bar(array(
                array('title' => $langBack,
                      'url' => "{$urlAppend}modules/units/index.php?course=$course_code&amp;id=$id",
                      'icon' => 'fa-reply',
                      'level' => 'primary-label')));
            $tool_content .= edit_res($res_id);
            draw($tool_content, 2, null, $head_content);
            exit;
        }
    } elseif (isset($_REQUEST['edit_res_submit'])) { // edit resource
        $res_id = intval($_REQUEST['resource_id']);
        if (check_admin_unit_resource($res_id)) {
            if (!isset($_REQUEST['restitle'])) {
                $restitle = '';
            } else {
                $restitle = $_REQUEST['restitle'];
            }
            $rescomments = purify($_REQUEST['rescomments']);
            Database::get()->query("UPDATE unit_resources SET
                                        title = ?s,
                                        comments = ?s
                                        WHERE unit_id = ?d AND id = ?d", $restitle, $rescomments, $id, $res_id);
            Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_UNITRESOURCE, $res_id);
            Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_COURSE, $course_id);
            CourseXMLElement::refreshCourse($course_id, $course_code);
        }
       // Session::Messages($langResourceUnitModified, 'alert-success');
        Session::flash('message',$langResourceUnitModified);
        Session::flash('alert-class', 'alert-success');
        redirect_to_home_page('modules/units/index.php?course=' . $course_code . '&id=' . $id);
    } elseif (isset($_REQUEST['del'])) { // delete resource from course unit
        $res_id = intval($_GET['del']);
        if (check_admin_unit_resource($res_id)) {
            Database::get()->query("DELETE FROM unit_resources WHERE id = ?d", $res_id);
            Database::get()->query("DELETE FROM course_units_activities WHERE id = ?d", $res_id);
            Indexer::queueAsync(Indexer::REQUEST_REMOVE, Indexer::RESOURCE_UNITRESOURCE, $res_id);
            Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_COURSE, $course_id);
            CourseXMLElement::refreshCourse($course_id, $course_code);
           // Session::Messages($langResourceCourseUnitDeleted, 'alert-success');
            Session::flash('message',$langResourceCourseUnitDeleted);
            Session::flash('alert-class', 'alert-success');
            redirect_to_home_page('modules/units/index.php?course=' . $course_code . '&id=' . $id);
        }
    } elseif (isset($_REQUEST['del_act'])) { // delete resource from course unit
        $res_id = intval($_GET['del_act']);
        $act_id = $_GET['actid'];
        Database::get()->query("DELETE FROM course_units_activities WHERE id = ?d", $res_id);
        Database::get()->query("DELETE FROM unit_resources WHERE activity_id = ?s", $act_id);
        //Indexer::queueAsync(Indexer::REQUEST_REMOVE, Indexer::RESOURCE_UNITRESOURCE, $res_id);
        //Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_COURSE, $course_id);
        //CourseXMLElement::refreshCourse($course_id, $course_code);
        Session::flash('message', $langResourceCourseUnitDeleted);
        Session::flash('alert-class', 'alert-success');
        redirect_to_home_page('modules/units/index.php?course=' . $course_code . '&id=' . $id);

    } elseif (isset($_REQUEST['vis'])) { // modify visibility in text resources only
        $res_id = intval($_REQUEST['vis']);
        if (check_admin_unit_resource($res_id)) {
            $vis = Database::get()->querySingle("SELECT `visible` FROM unit_resources WHERE id = ?d", $res_id)->visible;
            $newvis = ($vis == 1) ? 0 : 1;
            Database::get()->query("UPDATE unit_resources SET visible = '$newvis' WHERE id = ?d", $res_id);
            //Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_UNITRESOURCE, $res_id);
            //Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_COURSE, $course_id);
            //CourseXMLElement::refreshCourse($course_id, $course_code);
            Session::flash('message',$langViMod);
            Session::flash('alert-class', 'alert-success');
            redirect_to_home_page('modules/units/index.php?course=' . $course_code . '&id=' . $id);
        }
    } elseif (isset($_REQUEST['vis_act'])) { // modify visibility in text resources only

        $res_id = intval($_REQUEST['vis_act']);

        $vis = Database::get()->querySingle("SELECT `visible` FROM course_units_activities WHERE id = ?d", $res_id)->visible;
        $newvis = ($vis == 1) ? 0 : 1;
        Database::get()->query("UPDATE course_units_activities SET visible = '$newvis' WHERE id = ?d", $res_id);
        //Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_ACTIVITYRESOURCE, $res_id);
        //Indexer::queueAsync(Indexer::REQUEST_STORE, Indexer::RESOURCE_COURSE, $course_id);
        // CourseXMLElement::refreshCourse($course_id, $course_code);
        Session::flash('message', $langViMod);
        Session::flash('alert-class', 'alert-success');
        redirect_to_home_page('modules/units/index.php?course=' . $course_code . '&id=' . $id);

    } elseif (isset($_REQUEST['down'])) { // change order down
        $res_id = intval($_REQUEST['down']);
        if (check_admin_unit_resource($res_id)) {
            move_order('unit_resources', 'id', $res_id, 'order', 'down', "unit_id=$id");
        }
    } elseif (isset($_REQUEST['up'])) { // change order up
        $res_id = intval($_REQUEST['up']);
        if (check_admin_unit_resource($res_id)) {
            move_order('unit_resources', 'id', $res_id, 'order', 'up', "unit_id=$id");
        }
    }
    return '';
}

/**
 * @brief Check that a specified resource id belongs to a resource in the
          current course, and that the user is an admin in this course.
          Return the id of the unit or false if user is not an admin
 * @global type $course_id
 * @global type $is_editor
 * @param type $resource_id
 * @return boolean
 */
function check_admin_unit_resource($resource_id) {
    global $course_id, $is_editor;

    if ($is_editor) {
        $q = Database::get()->querySingle("SELECT course_units.id AS cuid FROM course_units,unit_resources WHERE
            course_units.course_id = ?d AND course_units.id = unit_resources.unit_id
            AND unit_resources.id = ?d", $course_id, $resource_id);
        if ($q) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


/**
 * @brief Display resources for unit with id=$id
 * @global type $tool_content
 * @global type $max_resource_id
 * @param type $unit_id
 */
function show_resources($unit_id)
{
    global $max_resource_id,
           $head_content, $langDownload, $langPrint, $langCancel,
           $langFullScreen, $langNewTab, $langActInHome, $langActInClass, $langActAfterClass, $course_code;

    $html = '';

    $q = Database::get()->querySingle("SELECT flipped_flag FROM course WHERE code = ?s", $course_code);

    if ($q->flipped_flag == 2) {
        $req_in_home = Database::get()->queryArray("SELECT * FROM unit_resources WHERE unit_id = ?d AND `order` >= 0 AND fc_type =?d ORDER BY `order`", $unit_id, 0);
        $req_in_class = Database::get()->queryArray("SELECT * FROM unit_resources WHERE unit_id = ?d AND `order` >= 0 AND fc_type =?d ORDER BY `order`", $unit_id, 1);
        $req_after_class = Database::get()->queryArray("SELECT * FROM unit_resources WHERE unit_id = ?d AND `order` >= 0 AND fc_type =?d ORDER BY `order`", $unit_id, 2);

        if (count($req_in_home) > 0 || count($req_in_class) > 0 || count($req_after_class)) {

            load_js('screenfull/screenfull.min.js');
            $head_content .= "<script>
            $(document).ready(function(){
                var count_1 = $('#unitResources_1').length;
                var count_2 = $('#unitResources_2').length;
                var count_3 = $('#unitResources_3').length;

                if(count_1>0){
                    Sortable.create(unitResources_1,{
                        handle: '.fa-arrows',
                        animation: 150,
                        onEnd: function (evt) {

                        var itemEl = $(evt.item);

                        var idReorder = itemEl.attr('data-id');
                        var prevIdReorder = itemEl.prev().attr('data-id');

                        $.ajax({
                        type: 'post',
                        dataType: 'text',
                        data: {
                                toReorder: idReorder,
                                prevReorder: prevIdReorder,
                                }
                            });
                        }
                    });
                }

                if(count_2>0){
                    Sortable.create(unitResources_2,{
                        handle: '.fa-arrows',
                        animation: 150,
                        onEnd: function (evt) {

                        var itemEl = $(evt.item);

                        var idReorder = itemEl.attr('data-id');
                        var prevIdReorder = itemEl.prev().attr('data-id');

                        $.ajax({
                        type: 'post',
                        dataType: 'text',
                        data: {
                                toReorder: idReorder,
                                prevReorder: prevIdReorder,
                                }
                            });
                        }
                    });
                }

                if(count_3>0){
                    Sortable.create(unitResources_3,{
                        handle: '.fa-arrows',
                        animation: 150,
                        onEnd: function (evt) {

                        var itemEl = $(evt.item);

                        var idReorder = itemEl.attr('data-id');
                        var prevIdReorder = itemEl.prev().attr('data-id');

                        $.ajax({
                        type: 'post',
                        dataType: 'text',
                        data: {
                                toReorder: idReorder,
                                prevReorder: prevIdReorder,
                                }
                            });
                        }
                    });
                }
            });
            $(function(){
                $('.fileModal').click(function (e)
                {
                    e.preventDefault();
                    var fileURL = $(this).attr('href');
                    var downloadURL = $(this).prev('input').val();
                    var fileTitle = $(this).attr('title');
                    var buttons = {};
                    if (downloadURL) {
                        buttons.download = {
                                label: '<i class=\"fa fa-download\"></i> $langDownload',
                                className: 'btn-success',
                                callback: function (d) {
                                    window.location = downloadURL;
                                }
                        };
                    }
                    buttons.print = {
                                label: '<i class=\"fa fa-print\"></i> $langPrint',
                                className: 'btn-primary',
                                callback: function (d) {
                                    var iframe = document.getElementById('fileFrame');
                                    iframe.contentWindow.print();
                                }
                            };
                    if (screenfull.enabled) {
                        buttons.fullscreen = {
                            label: '<i class=\"fa fa-arrows-alt\"></i> $langFullScreen',
                            className: 'btn-primary',
                            callback: function() {
                                screenfull.request(document.getElementById('fileFrame'));
                                return false;
                            }
                        };
                    }
                    buttons.newtab = {
                        label: '<i class=\"fa fa-plus\"></i> $langNewTab',
                        className: 'btn-primary',
                        callback: function() {
                            window.open(fileURL);
                            return false;
                        }
                    };
                    buttons.cancel = {
                                label: '$langCancel',
                                className: 'btn-default'
                            };
                    bootbox.dialog({
                        size: 'large',
                        title: fileTitle,
                        message: '<div class=\"row\">'+
                                    '<div class=\"col-sm-12\">'+
                                        '<div class=\"iframe-container\"><iframe id=\"fileFrame\" src=\"'+fileURL+'\"></iframe></div>'+
                                    '</div>'+
                                '</div>',
                        buttons: buttons
                    });
                });
            });

            </script>";
        }


        if (count($req_in_home) > 0) {

            $max_resource_id = Database::get()->querySingle("SELECT id FROM unit_resources
                                    WHERE unit_id = ?d ORDER BY `order` DESC LIMIT 1", $unit_id)->id;
            $html .= "<div class='form-group'>
                                <label class='col-2 control-label'>$langActInHome</label>
                            </div>
            ";
            $html .= "<div class='table-responsive'>";
            $html .= "<table class='table table-striped table-hover'><tbody id='unitResources_1'>";


            foreach ($req_in_home as $info_home) {
                if (!is_null($info_home->comments)) {
                    $info_home->comments = standard_text_escape($info_home->comments);
                }
                $html .= show_resource($info_home);
            }

            $html .= "</tbody></table>";
            $html .= "</div>";

        }

        if (count($req_in_class) > 0) {
            $max_resource_id = Database::get()->querySingle("SELECT id FROM unit_resources
            WHERE unit_id = ?d ORDER BY `order` DESC LIMIT 1", $unit_id)->id;

            $html .= "<div class='form-group'>
            <label class='col-2 control-label'>$langActInClass</label>
            </div>
            ";
            $html .= "<div class='table-responsive'>";
            $html .= "<table class='table table-striped table-hover'><tbody id='unitResources_2'>";
            foreach ($req_in_class as $info_class) {
                if (!is_null($info_class->comments)) {
                    $info_class->comments = standard_text_escape($info_class->comments);
                }
                $html .= show_resource($info_class);
            }
            $html .= "</tbody></table>";
            $html .= "</div>";
        }

        if (count($req_after_class) > 0) {
            $max_resource_id = Database::get()->querySingle("SELECT id FROM unit_resources
            WHERE unit_id = ?d ORDER BY `order` DESC LIMIT 1", $unit_id)->id;

            $html .= "<div class='form-group'>
            <label class='col-2 control-label'>$langActAfterClass</label>
            </div>
            ";
            $html .= "<div class='table-responsive'>";
            $html .= "<table class='table table-striped table-hover'><tbody id='unitResources_3'>";
            foreach ($req_after_class as $info_after_class) {
                if (!is_null($info_after_class->comments)) {
                    $info_after_class->comments = standard_text_escape($info_after_class->comments);
                }
                $html .= show_resource($info_after_class);
            }
            $html .= "</tbody></table>";
            $html .= "</div>";
        }
    } else {
        $req = Database::get()->queryArray("SELECT * FROM unit_resources WHERE unit_id = ?d AND `order` >= 0 ORDER BY `order`", $unit_id);


        if (count($req) > 0) {
            load_js('screenfull/screenfull.min.js');
            $head_content .= "<script>
            $(document).ready(function(){
                Sortable.create(unitResources,{
                    handle: '.fa-arrows',
                    animation: 150,
                    onEnd: function (evt) {

                    var itemEl = $(evt.item);

                    var idReorder = itemEl.attr('data-id');
                    var prevIdReorder = itemEl.prev().attr('data-id');

                    $.ajax({
                    type: 'post',
                    dataType: 'text',
                    data: {
                            toReorder: idReorder,
                            prevReorder: prevIdReorder,
                            }
                        });
                    }
                });
            });
            $(function(){
                $('.fileModal').click(function (e)
                {
                    e.preventDefault();
                    var fileURL = $(this).attr('href');
                    var downloadURL = $(this).prev('input').val();
                    var fileTitle = $(this).attr('title');
                    var buttons = {};
                    if (downloadURL) {
                        buttons.download = {
                                label: '<i class=\"fa fa-download\"></i> $langDownload',
                                className: 'btn-success',
                                callback: function (d) {
                                    window.location = downloadURL;
                                }
                        };
                    }
                    buttons.print = {
                                label: '<i class=\"fa fa-print\"></i> $langPrint',
                                className: 'btn-primary',
                                callback: function (d) {
                                    var iframe = document.getElementById('fileFrame');
                                    iframe.contentWindow.print();
                                }
                            };
                    if (screenfull.enabled) {
                        buttons.fullscreen = {
                            label: '<i class=\"fa fa-arrows-alt\"></i> $langFullScreen',
                            className: 'btn-primary',
                            callback: function() {
                                screenfull.request(document.getElementById('fileFrame'));
                                return false;
                            }
                        };
                    }
                    buttons.newtab = {
                        label: '<i class=\"fa fa-plus\"></i> $langNewTab',
                        className: 'btn-primary',
                        callback: function() {
                            window.open(fileURL);
                            return false;
                        }
                    };
                    buttons.cancel = {
                                label: '$langCancel',
                                className: 'btn-default'
                            };
                    bootbox.dialog({
                        size: 'large',
                        title: fileTitle,
                        message: '<div class=\"row\">'+
                                    '<div class=\"col-sm-12\">'+
                                        '<div class=\"iframe-container\" style=\"height:500px;\"><iframe id=\"fileFrame\" src=\"'+fileURL+'\" style=\"width:100%; height:500px;\"></iframe></div>'+
                                    '</div>'+
                                '</div>',
                        buttons: buttons
                    });
                });
            });

        </script>";
            $max_resource_id = Database::get()->querySingle("SELECT id FROM unit_resources
                                WHERE unit_id = ?d ORDER BY `order` DESC LIMIT 1", $unit_id)->id;
            $html .= "<div class='table-responsive'>";
            $html .= "<table class='table table-striped table-hover'><tbody id='unitResources'>";
            foreach ($req as $info) {
                if (!is_null($info->comments)) {
                    $info->comments = standard_text_escape($info->comments);
                }
                $html .= show_resource($info);
            }
            $html .= "</tbody></table>";
            $html .= "</div>";
        }
       
    } 

    return $html;
}

/**
 * @brief display unit resources
 * @param type $info
 */
function show_resource($info) {
    global $langUnknownResType, $is_editor;

    $html = '';
    if ($info->visible == 0 and $info->type != 'doc' and ! $is_editor) { // special case handling for old unit resources with type 'doc' .
        return;
    }
    switch ($info->type) {
        case 'doc':
            $html .= show_doc($info->title, $info->comments, $info->id, $info->res_id, $info->activity_title);
            break;
        case 'text':
            $html .= show_text($info->comments, $info->id, $info->visible, $info->activity_title);
            break;
        case 'description': // deprecated module. only for compatibility !
            $html .= show_description($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'lp':
            $html .= show_lp($info->title, $info->comments, $info->id, $info->res_id, $info->activity_title);
            break;
        case 'video':
        case 'videolink':
            $html .= show_video($info->type, $info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'videolinkcategory':
            $html .= show_videocat($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'exercise':
            $html .= show_exercise($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'work':
            $html .= show_work($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'topic':
        case 'forum':
            $html .= show_forum($info->type, $info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'wiki':
            $html .= show_wiki($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'poll':
            $html .= show_poll($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'link':
            $html .= show_link($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'linkcategory':
            $html .= show_linkcat($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'ebook':
            $html .= show_ebook($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'section':
            $html .= show_ebook_section($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'subsection':
            $html .= show_ebook_subsection($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'chat':
            $html .= show_chat($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'blog':
            $html .= show_blog($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'h5p':
            $html .= show_h5p($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        case 'h5p':
            $html .= show_h5p($info->title, $info->comments, $info->id, $info->res_id, $info->visible);
            break;
        case 'tc':
            $html .= show_tc($info->title, $info->comments, $info->id, $info->res_id, $info->visible, $info->activity_title);
            break;
        default:
            $html .= $langUnknownResType;
    }
    return $html;
}

/**
 * @brief display resource documents
 * @return string
 */
function show_doc($title, $comments, $resource_id, $file_id, $act_name) {
    global $can_upload, $course_id, $langWasDeleted, $urlServer,
           $id, $course_code;

    $file = Database::get()->querySingle("SELECT * FROM document WHERE course_id = ?d AND id = ?d", $course_id, $file_id);

    if (!$file) {
        $download_hidden_link = '';
        if (!$can_upload) {
            return '';
        }
        $status = 'del';
        $image = 'fa-times';
        $link = "<span class='not_visible'>" . q($title) . " ($langWasDeleted)</span>";
    } else {
        $status = $file->visible;
        if (!$can_upload and (!resource_access($file->visible, $file->public))) {
            return '';
        }
        if ($file->format == '.dir') {
            $image = 'fa-folder-o';
            $download_hidden_link = '';
            $link = "<a href='{$urlServer}modules/document/index.php?course=$course_code&amp;openDir=$file->path&amp;unit=$id'>" .
                q($title) . "</a>";
        } else {
            $file->title = $title;
            $image = choose_image('.' . $file->format);
            $download_url = "{$urlServer}modules/document/index.php?course=$course_code&amp;download=$file->path";
            $download_hidden_link = ($can_upload || visible_module(MODULE_ID_DOCS))?
                "<input type='hidden' value='$download_url'>" : '';
            $file_obj = MediaResourceFactory::initFromDocument($file);
            $file_obj->setAccessURL(file_url($file->path, $file->filename));
            $file_obj->setPlayURL(file_playurl($file->path, $file->filename));
            $link = MultimediaHelper::chooseMediaAhref($file_obj);
        }
    }
    $class_vis = ($status == '0' or $status == 'del') ? ' class="not_visible"' : '';
    if (!empty($comments)) {
        $comment = '<br />' . $comments;
    } else {
        $comment = '';
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>" . icon($image, '') . "</td>
          <td class='text-left' width='1'>$act_name</td>
          <td class='text-left'>$download_hidden_link$link$comment</td>" .
            actions('doc', $resource_id, $status) .
            "</tr>";
}

/**
 * @brief display resource text
 * @return string
 */
function show_text($comments, $resource_id, $visibility, $act_name) {

    $content = '';
    $class_vis = ($visibility == 0) ? ' class="not_visible"' : ' ';
    $comments = mathfilter($comments, 12, "../../courses/mathimg/");
    $content .= "
        <tr$class_vis data-id='$resource_id'>
        <td class='text-left' width='1'>$act_name</td>
          <td colspan='2'>$comments</td>" .
            actions('text', $resource_id, $visibility) .
            "</tr>";

    return $content;
}

/**
 * @brief display course description resource
 * @return string
 */
function show_description($title, $comments, $id, $res_id, $visibility, $act_name) {

    $content = '';
    $comments = mathfilter($comments, 12, "../../courses/mathimg/");
    $content .= "
        <tr>
        <td class='text-left' width='1'>$act_name</td>
          <td colspan='2'>
            <div class='title'>" . q($title) . "</div>
            <div class='content'>$comments</div>
          </td>" . actions('description', $id, $visibility, $res_id) . "</tr>";

    return $content;
}

/**
 * @brief display resource learning path
 * @return string
 */
function show_lp($title, $comments, $resource_id, $lp_id, $act_name): string
{

    global $id, $urlAppend, $course_id, $is_editor, $langWasDeleted, $course_code;

    $title = q($title);
    $lp = Database::get()->querySingle("SELECT * FROM lp_learnPath WHERE course_id = ?d AND learnPath_id = ?d", $course_id, $lp_id);
    if (!$lp) { // check if lp was deleted
        if (!$is_editor) {
            return '';
        } else {
            $status = 'del';
            $imagelink = icon('fa-times');
            $link = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $status = $lp->visible;
        $module_id = Database::get()->querySingle("SELECT module_id FROM lp_rel_learnPath_module WHERE learnPath_id = ?d ORDER BY `rank` LIMIT 1", $lp_id)->module_id;
        $link = "<a href='${urlAppend}modules/units/view.php?course=$course_code&amp;res_type=lp&amp;path_id=$lp_id&amp;module_id=$module_id&amp;unit=$id'> $title";
        $imagelink = icon('fa-ellipsis-h');
    }

    if (!empty($comments)) {
        $comment_box = "<br>$comments";
    } else {
        $comment_box = '';
    }
    return "
        <tr data-id='$resource_id'>
          <td width='1'>$imagelink</a></td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$link</a>$comment_box</td>" .
            actions('lp', $resource_id, $status) . "</tr>";
}

/**
 * @brief display resource video
 * @global type $is_editor
 * @global type $course_id
 * @global string $tool_content
 * @param type $table
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $video_id
 * @param string $visibility
 * @return string
 */
function show_video($table, $title, $comments, $resource_id, $video_id, $visibility, $act_name) {
    global $is_editor, $course_id, $tool_content, $urlServer, $course_code, $id;


    $row = Database::get()->querySingle("SELECT * FROM `$table` WHERE course_id = ?d AND id = ?d", $course_id, $video_id);
    if ($row) {
        $row->title = $title;
        $status = $row->public;
        if ($table == 'video') {
            $videoplayurl = "${urlServer}modules/units/view.php?course=$course_code&amp;res_type=video&amp;id=$video_id&amp;unit=$id";
            $vObj = MediaResourceFactory::initFromVideo($row);
            $vObj->setPlayURL($videoplayurl);
            $videolink = MultimediaHelper::chooseMediaAhref($vObj);
        } else {
            $videoplayurl = "${urlServer}modules/units/view.php?course=$course_code&amp;res_type=videolink&amp;id=$video_id&amp;unit=$id";
            $vObj = MediaResourceFactory::initFromVideoLink($row);
            $vObj->setPlayURL($videoplayurl);
            $videolink = MultimediaHelper::chooseMedialinkAhref($vObj);
        }
        $imagelink = "fa-film";
    } else { // resource was deleted
        if (!$is_editor) {
            return;
        }
        $videolink = $title;
        $imagelink = "fa-times";
        $visibility = 'del';
    }

    if (!empty($comments)) {
        $comment_box = "<p>$comments";
    } else {
        $comment_box = "";
    }
    $class_vis = ($visibility == 0 or $status == 'del') ? ' class="not_visible"' : ' ';
    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>".icon($imagelink)."</td>
          <td class='text-left' width='1'>$act_name</td>
          <td> $videolink $comment_box</td>" . actions('video', $resource_id, $visibility) . "
        </tr>";
}

/**
 * @brief display resource video category
 * @global type $is_editor
 * @global type $course_id
 * @global type $langInactiveModule
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $videolinkcat_id
 * @param type $visibility
 * @return string
 */
function show_videocat($title, $comments, $resource_id, $videolinkcat_id, $visibility, $act_name)
{
    global $is_editor, $course_id, $langInactiveModule;

    $module_visible = visible_module(MODULE_ID_VIDEO); // checks module visibility

    if (!$module_visible and !$is_editor) {
        return '';
    }
    $linkcontent = $imagelink = $link = '';
    $class_vis = (!$module_visible)?
                 ' class="not_visible"': ' ';
    $vlcat = Database::get()->querySingle("SELECT * FROM video_category WHERE id = ?d AND course_id = ?d", $videolinkcat_id, $course_id);
    $content = "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>".icon('fa-folder-o')."</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>" . q($title);

    if (!empty($comments)) {
        $content .= "<br>$comments";
    } elseif (!empty($vlcat->description)) {
        $content .= '<p>' . q($vlcat->description) . '</p>';
    }
    foreach (array('video', 'videolink') as $table) {
        $sql2 = Database::get()->queryArray("SELECT * FROM $table WHERE category = ?d AND course_id = ?d", $vlcat->id, $course_id);
        foreach ($sql2 as $row) {
            if (!$is_editor) {
                if (!resource_access(1, $row->public) or $row->visible == 0) {
                    continue;
                }
            }

            if ($table == 'video') {
                $vObj = MediaResourceFactory::initFromVideo($row);
                $videolink = MultimediaHelper::chooseMediaAhref($vObj);
            } else {
                $vObj = MediaResourceFactory::initFromVideoLink($row);
                $videolink = MultimediaHelper::chooseMedialinkAhref($vObj);
            }
            if (!$module_visible) {
                $videolink .= " <i>($langInactiveModule)</i>";
            }

            $class_vis = ($row->visible == 0 or !$module_visible)? ' class="not_visible"': ' ';
            $linkcontent .= "<div $class_vis>" . icon('fa-film') . "&nbsp;&nbsp;$videolink</div>";
        }
    }

    return $content . $linkcontent .'
           </td>'. actions('videolinkcategory', $resource_id, $visibility) .
        '</tr>';
}


/**
 * @brief display resource assignment (aka work)
 * @global type $id
 * @global type $urlServer
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $course_id
 * @global type $course_code
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $work_id
 * @param type $visibility
 * @return string
 */
function show_work($title, $comments, $resource_id, $work_id, $visibility, $act_name) {
    global $id, $urlServer, $is_editor,
    $langWasDeleted, $course_id, $course_code, $langPassCode;

    $title = q($title);
    $work = Database::get()->querySingle("SELECT * FROM assignment WHERE course_id = ?d AND id = ?d", $course_id, $work_id);
    if (!$work) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $exlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        if ($work->password_lock) {
            $lock_description = "<ul>";
            $lock_description .= "<li>$langPassCode</li>";
            enable_password_bootbox();
            $class = 'class="password_protected"';
            $lock_description .= "</ul>";
            $exclamation_icon = "&nbsp;&nbsp;<span class='fa fa-exclamation-triangle space-after-icon' data-toggle='tooltip' data-placement='right' data-html='true' data-title='$lock_description'></span>";
        } else {
            $class = $exclamation_icon = '';
        }

        $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=assignment&amp;id=$work_id&amp;unit=$id' $class>";
        $exlink = $link . "$title</a> $exclamation_icon";
        $imagelink = $link . "</a>".icon('fa-flask')."";
    }

    if (!empty($comments)) {
        $comment_box = "<br>$comments";
    } else {
        $comment_box = '';
    }
    return "
        <tr data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$exlink $comment_box</td>" .
            actions('lp', $resource_id, $visibility) . '
        </tr>';
}

/**
 * @brief display resource exercise
 * @global type $id
 * @global type $urlServer
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $course_id
 * @global type $course_code
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $exercise_id
 * @param type $visibility
 * @return string
 */
function show_exercise($title, $comments, $resource_id, $exercise_id, $visibility, $act_name) {
    global $id, $urlServer, $is_editor, $langWasDeleted, $course_id, $course_code, $langPassCode, $uid,
        $langAttemptActive, $langAttemptPausedS;

    $title = q($title);
    $exercise = Database::get()->querySingle("SELECT * FROM exercise WHERE course_id = ?d AND id = ?d", $course_id, $exercise_id);
    if (!$exercise) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $status = 'del';
            $imagelink = icon('fa-times');
            $exlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $status = $exercise->active;
        if (!$is_editor and (!resource_access($exercise->active, $exercise->public))) {
            return '';
        }
        $link_class = $exclamation_icon = '';
        if ($exercise->password_lock) {
            enable_password_bootbox();
            $link_class = 'password_protected';
            $exclamation_icon = "&nbsp;&nbsp;<span class='fa fa-exclamation-triangle space-after-icon' data-toggle='tooltip' data-placement='right' data-html='true' data-title='$langPassCode'></span>";
        }

        // check if exercise is in "paused" or "running" state
        $pending_label = $pending_class = '';
        if ($uid) {
            $paused_attempt = Database::get()->querySingle("SELECT eurid, attempt
                FROM exercise_user_record
                WHERE eid = ?d AND uid = ?d AND
                attempt_status = ?d",
                $exercise_id, $uid, ATTEMPT_PAUSED);
            if ($paused_attempt) {
                $eurid = $paused_attempt->eurid;
                $pending_class = 'paused_exercise';
                $pending_label = "<span class='not_visible'>($langAttemptPausedS)</span>";
            } elseif ($exercise->continue_time_limit) {
                $incomplete_attempt = Database::get()->querySingle("SELECT eurid, attempt
                    FROM exercise_user_record
                    WHERE eid = ?d AND uid = ?d AND
                    attempt_status = ?d AND
                    TIME_TO_SEC(TIMEDIFF(NOW(), record_end_date)) < ?d
                    ORDER BY eurid DESC LIMIT 1",
                    $exercise_id, $uid, ATTEMPT_ACTIVE, 60 * $exercise->continue_time_limit);
                if ($incomplete_attempt) {
                    $eurid = $incomplete_attempt->eurid;
                    $pending_class = 'active_exercise';
                    $pending_label = "<span class='not_visible'>($langAttemptActive)</span>";
                }
            }
        }
        if ($pending_class) {
            enable_password_bootbox();
            $link = "<a class='ex_settings $pending_class $link_class' href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=exercise&amp;exerciseId=$exercise_id&amp;eurId=$eurid&amp;unit=$id'>";
        } else {
            $link = "<a class='ex_settings $link_class' href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=exercise&amp;exerciseId=$exercise_id&amp;unit=$id'>";
        }
        $exlink = $link . "$title</a> $exclamation_icon $pending_label";
        $imagelink = $link . "</a>" . icon('fa-pencil-square-o'). "";
    }
    $class_vis = ($status == '0' or $status == 'del') ? ' class="not_visible"' : ' ';

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    } else {
        $comment_box = "";
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='3'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$exlink $comment_box</td>" . actions('lp', $resource_id, $visibility) . "
        </tr>";
}

/**
 * @brief display resource forum
 * @global type $id
 * @global type $urlServer
 * @global type $is_editor
 * @global type $course_id
 * @global type $course_code
 * @global type $langWasDeleted
 * @param type $type
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $ft_id
 * @param type $visibility
 * @return string
 */
function show_forum($type, $title, $comments, $resource_id, $ft_id, $visibility, $act_name) {
    global $id, $urlServer, $is_editor, $course_code, $langWasDeleted;

    $class_vis = ($visibility == 0) ? ' class="not_visible"' : ' ';
    $title = q($title);
    if ($type == 'forum') {
        $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=forum&amp;forum=$ft_id&amp;unit=$id'>";
        $forumlink = $link . "$title</a>";
        $imagelink = icon('fa-comments');
    } else {
        $r = Database::get()->querySingle("SELECT forum_id FROM forum_topic WHERE id = ?d", $ft_id);
        if (!$r) { // check if it was deleted
            if (!$is_editor) {
                return '';
            } else {
                $imagelink = icon('fa-times');
                $forumlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
            }
        } else {
            $forum_id = $r->forum_id;
            $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=forum_topic&amp;topic=$ft_id&amp;forum=$forum_id&amp;unit=$id'>";
            $forumlink = $link . "$title</a>";
            $imagelink = icon('fa-comments'). "";
        }
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    } else {
        $comment_box = '';
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$forumlink $comment_box</td>" .
            actions('forum', $resource_id, $visibility) . '
        </tr>';
}


/**
 * @brief display resource poll
 * @param type $type
 * @param type $title
 * @param type $resource_id
 * @param type $poll_id
 * @param type $visibility
 * @return string
 */
function show_poll($title, $comments, $resource_id, $poll_id, $visibility, $act_name) {

    global $course_id, $course_code, $is_editor, $urlServer, $id, $langWasDeleted;

    $class_vis = ($visibility == 0 ) ? ' class="not_visible"' : ' ';
    $title = q($title);
    $poll = Database::get()->querySingle("SELECT * FROM poll WHERE course_id = ?d AND pid = ?d", $course_id, $poll_id);
    if (!$poll) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $polllink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=questionnaire&amp;pid=$poll_id&amp;UseCase=1&amp;unit_id=$id'>";
        $polllink = $link . $title . '</a>';
        $imagelink = $link . "</a>" . icon('fa-question-circle') . "";
    }

    if (!empty($comments)) {
        $comment_box = "<br>$comments";
    } else {
        $comment_box = '';
    }
    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$polllink $comment_box</td>" .
            actions('poll', $resource_id, $visibility) . '
        </tr>';


}
/**
 * @brief display resource wiki
 * @global type $id
 * @global type $mysqlMainDb
 * @global type $urlServer
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $langInactiveModule
 * @global type $course_id
 * @global type $course_code
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $wiki_id
 * @param type $visibility
 * @return string
 */
function show_wiki($title, $comments, $resource_id, $wiki_id, $visibility, $act_name) {
    global $id, $urlServer, $is_editor,
    $langWasDeleted, $langInactiveModule, $course_id, $course_code;

    $module_visible = visible_module(MODULE_ID_WIKI); // checks module visibility

    if (!$module_visible and ! $is_editor) {
        return '';
    }

    $class_vis = ($visibility == 0 or ! $module_visible) ?
            ' class="not_visible"' : ' ';
    $title = q($title);
    $wiki = Database::get()->querySingle("SELECT * FROM wiki_properties WHERE course_id = ?d AND id = ?d", $course_id, $wiki_id);
    if (!$wiki) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $wikilink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $link = "<a href='${urlServer}modules/wiki/page.php?course=$course_code&amp;wikiId=$wiki_id&amp;action=show&amp;unit=$id'>";
        $wikilink = $link . "$title</a>";
        if (!$module_visible) {
            $wikilink .= " <i>($langInactiveModule)</i>";
        }
        $imagelink = $link . "</a>" .icon('fa-wikipedia-w') . "";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    } else {
        $comment_box = '';
    }
    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$wikilink $comment_box</td>" .
            actions('wiki', $resource_id, $visibility) . '
        </tr>';
}

/**
 * @brief display resource link
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $course_id
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $link_id
 * @param type $visibility
 * @return string
 */
function show_link($title, $comments, $resource_id, $link_id, $visibility, $act_name) {

    global $is_editor, $langWasDeleted, $course_id;

    $class_vis = ($visibility == 0) ? ' class="not_visible"' : ' ';
    $l = Database::get()->querySingle("SELECT * FROM link WHERE course_id = ?d AND id = ?d", $course_id, $link_id);
    if (!$l) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $exlink = "<span class='not_visible'>" . q($title) . " ($langWasDeleted)</span>";
        }
    } else {
        if ($title == '') {
            $title = q($l->url);
        } else {
            $title = q($title);
        }
        $link = "<a href='" . q($l->url) . "' target='_blank'>";
        $exlink = $link . "$title</a>";
        $imagelink = icon('fa-link');
    }

    if (!empty($comments)) {
        $comment_box = '<br />' . standard_text_escape($comments);
    } else {
        $comment_box = '';
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$exlink $comment_box</td>" . actions('link', $resource_id, $visibility) . "
        </tr>";
}

/**
 * @brief display resource link category
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $linkcat_id
 * @param type $visibility
 * @return string
 */
function show_linkcat($title, $comments, $resource_id, $linkcat_id, $visibility, $act_name) {

    global $is_editor, $langWasDeleted, $course_id;

    $content = $linkcontent = $comment_box = '';
    $class_vis = ($visibility == 0) ? ' class="not_visible"' : ' ';
    $sql = Database::get()->queryArray("SELECT * FROM link_category WHERE course_id = ?d AND id = ?d", $course_id, $linkcat_id);
    if (!$sql) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $content = "<tr class='not_visible' data-id='$resource_id'>
                        <td width='1'>" . icon('fa-folder-o') . "</td>
                        <td class='text-left' width='1'>$act_name</td>
                        <td>" . q($title) . " ($langWasDeleted)";

        }
    } else {
        foreach ($sql as $lcat) {
            $content .= "
                        <tr$class_vis data-id='$resource_id'>
                          <td width='1'>".icon('fa-folder-o')."</td>
                          <td class='text-left' width='1'>$act_name</td>
                          <td>" . q($lcat->name);
            if (!empty($lcat->description)) {
                $comment_box = "<br><small>$lcat->description</small>";
            }

            $sql2 = Database::get()->queryArray("SELECT * FROM link WHERE course_id = ?d AND category = $lcat->id", $course_id);
            foreach ($sql2 as $l) {
                $imagelink = icon('fa-link');
                $ltitle = q(($l->title == '') ? $l->url : $l->title);
                $linkcontent .= "<br>$imagelink&nbsp;&nbsp;<a href='" . q($l->url) ."' target='_blank'>$ltitle</a>";
            }
        }
        if (!empty($comments)) {
            $comment_box = '<br />' . standard_text_escape($comments);
        } else {
            $comment_box = '';
        }
    }
    return $content . $comment_box . $linkcontent . '
           </td>' . actions('linkcategory', $resource_id, $visibility) .
            '</tr>';
}

/**
 * @brief display resource ebook
 * @global type $id
 * @global type $urlServer
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $course_code
 * @global type $langInactiveModule
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $ebook_id
 * @param type $visibility
 * @return string
 */
function show_ebook($title, $comments, $resource_id, $ebook_id, $visibility, $act_name) {
    global $id, $urlServer, $is_editor,
    $langWasDeleted, $course_code, $langInactiveModule;

    $module_visible = visible_module(MODULE_ID_EBOOK); // checks module visibility

    if (!$module_visible and ! $is_editor) {
        return '';
    }

    $class_vis = ($visibility == 0 or ! $module_visible) ?
            ' class="not_visible"' : ' ';
    $title = q($title);
    $r = Database::get()->querySingle("SELECT * FROM ebook WHERE id = ?d", $ebook_id);
    if (!$r) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $exlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $link = "<a href='${urlServer}modules/ebook/show.php?$course_code/$ebook_id/unit=$id'>";
        $exlink = $link . "$title</a>";
        if (!$module_visible) {
            $exlink .= " <i>($langInactiveModule)</i>";
        }
        $imagelink = $link . "</a>" .icon('fa-book') . "";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    } else {
        $comment_box = "";
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='3'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$exlink $comment_box</td>" . actions('ebook', $resource_id, $visibility) . "
        </tr>";
}

/**
 * @brief display ebook section
 * @global type $course_id
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $section_id
 * @param type $visibility
 * @return type
 */
function show_ebook_section($title, $comments, $resource_id, $section_id, $visibility, $act_name) {
    global $course_id;

    $data = Database::get()->querySingle("SELECT ebook.id AS ebook_id, ebook_subsection.id AS ssid
                FROM ebook, ebook_section, ebook_subsection
                WHERE ebook.course_id = ?d AND
                    ebook_section.ebook_id = ebook.id AND
                    ebook_section.id = ebook_subsection.section_id AND
                    ebook_section.id = ?d
                ORDER BY CONVERT(ebook_subsection.public_id, UNSIGNED), ebook_subsection.public_id
                LIMIT 1", $course_id, $section_id);
    if (!$data) { // check if it was deleted
        $deleted = true;
        $display_id = $ebook_id = false;
    } else {
        $deleted = false;
        $ebook_id = $data->ebook_id;
        $display_id = $section_id . ',' . $data->ssid;
    }
    return show_ebook_resource($title, $comments, $resource_id, $ebook_id, $display_id, $visibility, $deleted, $act_name);
}

/**
 * @brief display ebook subsection
 * @global type $course_id
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $subsection_id
 * @param type $visibility
 * @return type
 */
function show_ebook_subsection($title, $comments, $resource_id, $subsection_id, $visibility, $act_name) {
    global $course_id;

    $data = Database::get()->querySingle("SELECT ebook.id AS ebook_id, ebook_section.id AS sid
                FROM ebook, ebook_section, ebook_subsection
                WHERE ebook.course_id = ?d AND
                    ebook_section.ebook_id = ebook.id AND
                    ebook_section.id = ebook_subsection.section_id AND
                    ebook_subsection.id = ?d
                LIMIT 1", $course_id, $subsection_id);
    if (!$data) { // check if it was deleted
        $deleted = true;
        $display_id = $ebook_id = false;
    } else {
        $deleted = false;
        $ebook_id = $data->ebook_id;
        $display_id = $data->sid . ',' . $subsection_id;
    }
    return show_ebook_resource($title, $comments, $resource_id, $ebook_id, $display_id, $visibility, $deleted, $act_name);
}

/**
 * @brief display resource ebook subsection
 * @global type $id
 * @global type $urlServer
 * @global type $is_editor
 * @global type $langWasDeleted
 * @global type $course_code
 * @global type $langInactiveModule
 * @param type $title
 * @param type $comments
 * @param type $resource_id
 * @param type $ebook_id
 * @param type $display_id
 * @param type $visibility
 * @param type $deleted
 * @return string
 */
function show_ebook_resource($title, $comments, $resource_id, $ebook_id, $display_id, $visibility, $deleted, $act_name) {
    global $id, $urlServer, $is_editor,
    $langWasDeleted, $course_code, $langInactiveModule;

    $module_visible = visible_module(MODULE_ID_EBOOK); // checks module visibility

    if (!$module_visible and ! $is_editor) {
        return '';
    }

    $class_vis = ($visibility == 0 or ! $module_visible) ?
            ' class="not_visible"' : ' ';
    if ($deleted) {
        if (!$is_editor) {
            return '';
        } else {
            $status = 'del';
            $imagelink = icon('fa-times');
            $exlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $link = "<a href='${urlServer}modules/ebook/show.php?$course_code/$ebook_id/$display_id/unit=$id'>";
        $exlink = $link . q($title) . '</a>';
        if (!$module_visible) {
            $exlink .= " <i>($langInactiveModule)</i>";
        }
        $imagelink = $link . "</a>" .icon('fa-book'). "";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    } else {
        $comment_box = "";
    }

    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='3'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$exlink $comment_box</td>" . actions('section', $resource_id, $visibility) . "
        </tr>";
}

/**
 * @brief display chat resources
 * @param $title
 * @param $comments
 * @param $resource_id
 * @param $chat_id
 * @param $visibility
 * @return string
 */
function show_chat($title, $comments, $resource_id, $chat_id, $visibility, $act_name) {
    global $urlServer, $is_editor, $langWasDeleted, $course_id, $course_code, $id;

    $comment_box = '';
    $title = q($title);
    $chat = Database::get()->querySingle("SELECT * FROM conference WHERE course_id = ?d AND conf_id = ?d", $course_id, $chat_id);
    if (!$chat) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $chatlink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        if (!$is_editor and $chat->status == 'inactive') {
            return '';
        }
        $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=chat&amp;conference_id=$chat_id&amp;unit=$id'>";
        $chatlink = $link . "$title</a>";
        $imagelink = $link . "</a>" .icon('fa-exchange') . "";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    }
    $class_vis = ($chat->status == 'inactive') ?
        ' class="not_visible"' : ' ';
    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$chatlink $comment_box</td>" .
        actions('chat', $resource_id, $visibility) . '
        </tr>';
}

/**
 * @brief display chat resources
 * @param $title
 * @param $comments
 * @param $resource_id
 * @param $blog_id
 * @param $visibility
 */
function show_blog($title, $comments, $resource_id, $blog_id, $visibility, $act_name) {

    global $urlServer, $is_editor, $langWasDeleted, $course_id, $course_code, $id;

    $comment_box = '';
    $title = q($title);
    $blog = Database::get()->querySingle("SELECT * FROM blog_post WHERE course_id = ?d AND id = ?d", $course_id, $blog_id);
    if (!$blog) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $bloglink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        //$link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=chat&amp;conference_id=$chat_id&amp;unit=$id'>";
        $link = "<a href='${urlServer}modules/blog/index.php?course=$course_code&amp;action=showPost&amp;pId=$blog_id'>";
        $bloglink = $link . "$title</a>";
        $imagelink = $link . "</a>" .icon('fa-columns') . "";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    }

    return "
        <tr data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$bloglink $comment_box</td>" .
        actions('blog', $resource_id, $visibility) . '
        </tr>';
}

/**
 * @brief display h5p resources
 * @param $title
 * @param $comments
 * @param $resource_id
 * @param $h5p_id
 * @param $visibility
 */
function show_h5p($title, $comments, $resource_id, $h5p_id, $visibility, $act_name) {
    global $urlServer, $is_editor, $langWasDeleted, $course_id, $course_code, $id, $webDir, $urlAppend;

    $comment_box = '';
    $title = q($title);
    $h5p = Database::get()->querySingle("SELECT * FROM h5p_content WHERE course_id = ?d AND id = ?d", $course_id, $h5p_id);
    if (!$h5p) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $h5plink = "<span class='not_visible'>$title ($langWasDeleted)</span>";
        }
    } else {
        $q = Database::get()->querySingle("SELECT machine_name, title, major_version, minor_version
                                            FROM h5p_library WHERE id = ?s", $h5p->main_library_id);
        $h5p_content_type_title = $q->title;
        $typeFolder = $q->machine_name . "-" . $q->major_version . "." . $q->minor_version;
        $typeIconPath = $webDir . "/courses/h5p/libraries/" . $typeFolder . "/icon.svg";
        $typeIcon = (file_exists($typeIconPath))
            ? $urlAppend . "courses/h5p/libraries/" . $typeFolder . "/icon.svg"  // expected icon
            : $urlAppend . "js/h5p-core/images/h5p_library.svg"; // fallback icon
        $link = "<a href='${urlServer}modules/units/view.php?course=$course_code&amp;res_type=h5p&amp;id=$h5p_id&amp;unit=$id'>";
        $h5plink = $link . "$title</a>";
        $imagelink = $link . "</a><img src='$typeIcon' width='30px' height='30px' title='$h5p_content_type_title' alt='$h5p_content_type_title'>";
    }

    if (!empty($comments)) {
        $comment_box = "<br />$comments";
    }

    return "
        <tr data-id='$resource_id'>
          <td>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$h5plink $comment_box</td>" .
        actions('h5p', $resource_id, $visibility) . '
        </tr>';

}


/**
 * @brief display tc resources
 * @param $title
 * @param $comments
 * @param $resource_id
 * @param $tc_id
 * @param $visibility
 * @return string
 */
function show_tc($title, $comments, $resource_id, $tc_id, $visibility, $act_name) {
    global  $is_editor, $langWasDeleted, $langInactiveModule, $course_id;

    $module_visible = visible_module(MODULE_ID_TC); // checks module visibility

    if (!$module_visible and !$is_editor) {
        return '';
    }

    $tc = Database::get()->querySingle("SELECT * FROM tc_session WHERE course_id = ?d AND id = ?d", $course_id, $tc_id);
    if (!$tc) { // check if it was deleted
        if (!$is_editor) {
            return '';
        } else {
            $imagelink = icon('fa-times');
            $tclink = "<span class='not_visible'>" .q($title) ." ($langWasDeleted)</span>";
        }
    } else {
        if (!$is_editor and !$tc->active) {
            return '';
        }
        $tclink = q($title);
        if (!$module_visible) {
            $tclink .= " <i>($langInactiveModule)</i>";
        }
        $imagelink = icon('fa-exchange');
    }

    if (!empty($comments)) {
        $comment_box = "<br>$comments";
    } else {
        $comment_box = '';
    }
    $class_vis = (!$tc->active or !$module_visible) ?
        ' class="not_visible"' : ' ';
    return "
        <tr$class_vis data-id='$resource_id'>
          <td width='1'>$imagelink</td>
          <td class='text-left' width='1'>$act_name</td>
          <td>$tclink $comment_box</td>" .
        actions('tc', $resource_id, $visibility) . '
        </tr>';
}

/**
 * @brief resource actions
 * @param type $res_type
 * @param type $resource_id
 * @param type $status
 * @param type $res_id
 * @return string
 */
function actions($res_type, $resource_id, $status, $res_id = false) {
    global $is_editor, $langEditChange, $langDelete,
    $langAddToCourseHome, $langConfirmDelete, $course_code,
    $langViewHide, $langViewShow, $langReorder, $langAlreadyBrowsed, $langNeverBrowsed;

    if (!$is_editor) {
        if (prereq_unit_has_completion_enabled($_GET['id'])) {
            $activity_result = unit_resource_completion($_GET['id'], $resource_id);
            switch ($activity_result) {
                case 1: $content = "<td class='style='padding: 10px 0; width: 85px;'>
                                    <span class='fa fa-check-circle text-center' data-bs-toggle='tooltip' data-bs-placement='bottom' data-bs-original-title='$langAlreadyBrowsed'></span>
                                    </td>";
                    break;
                case 0:
                    $content = "<td class='style='padding: 10px 0; width: 85px;'>
                                <span class='fa fa-hourglass-2 text-center' data-bs-toggle='tooltip' data-bs-placement='bottom' data-bs-original-title='$langNeverBrowsed'></span>
                                </td>";
                    break;
                case 2:
                    $content = '';
                    break;
            }
            return $content;
        } else {
            return '';
        }
    }

    if ($res_type == 'description') {
        $icon_vis = ($status == 1) ? 'fa-send' : 'fa-send-o';
        $edit_link = "edit.php?course=$course_code&amp;id=$_GET[id]&amp;numBloc=$res_id";
    } else {
        $showorhide = ($status == 1) ? $langViewHide : $langViewShow;
        $icon_vis = ($status == 1) ? 'fa-eye-slash' : 'fa-eye';
        $edit_link = "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;id=$_GET[id]&amp;edit=$resource_id";
    }

    $q = Database::get()->querySingle("SELECT flipped_flag FROM course WHERE code = ?s", $course_code);
    $content = "<td style='padding: 10px 0; width: 85px;'>
                    <div class='reorder-btn d-flex justify-content-center' style='padding:5px 10px 0; font-size: 16px; cursor: pointer; vertical-align: bottom;'>
                        <span class='fa fa-arrows' data-bs-toggle='tooltip' data-bs-placement='top' title='$langReorder'></span>
                    </div>
                <div class='d-flex justify-content-center mt-3'>";
    $content .= action_button(array(
                array('title' => $langEditChange,
                      'url' => $edit_link,
                      'icon' => 'fa-edit',
                      'show' => $status != 'del'),
                array('title' => $showorhide,
                      'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;id=$_GET[id]&amp;vis=$resource_id",
                      'icon' => $icon_vis,
                      'show' => $status != 'del' and (in_array($res_type, array('text', 'video', 'forum', 'topic')) or $q->flipped_flag==2)),
                array('title' => $langAddToCourseHome,
                      'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;id=$_GET[id]&amp;vis=$resource_id",
                      'icon' => $icon_vis,
                      'show' => $status != 'del' and in_array($res_type, array('description'))),
                array('title' => $langDelete,
                      'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;id=$_GET[id]&amp;del=$resource_id",
                      'icon' => 'fa-times',
                      'confirm' => $langConfirmDelete,
                      'class' => 'delete')
            ));

    $content .= "</div></td>";

    $first = false;
    return $content;
}

/**
 * @brief edit resource
 * @param type $resource_id
 * @return string
 */
function edit_res($resource_id) {
    global $id, $urlServer, $langTitle, $langDescription, $langContents, $langModify, $course_code;

    $ru = Database::get()->querySingle("SELECT id, title, comments, type FROM unit_resources WHERE id = ?d", $resource_id);
    $restitle = " value='" . htmlspecialchars($ru->title, ENT_QUOTES) . "'";
    $rescomments = $ru->comments;
    $resource_id = $ru->id;
    $resource_type = $ru->type;
    $content = "<div class='col-12'><div class='form-wrapper form-edit rounded'>";
    $content .= "<form class='form-horizontal' role='form' method='post' action='${urlServer}modules/units/index.php?course=$course_code'>" .
            "<input type='hidden' name='id' value='$id'>" .
            "<input type='hidden' name='resource_id' value='$resource_id'>";
    if ($resource_type != 'text') {
        $content .= "<div class='form-group'>
                <label class='col-sm-6 control-label-notes'>$langTitle</label>
                <div class='col-sm-12'><input class='form-control' type='text' name='restitle' size='50' maxlength='255' $restitle></div>
                </div>";
        $message = $langDescription;
    } else {
        $message = $langContents;
    }
    $content .= "
                <div class='form-group mt-4'>
                    <label class='col-sm-6 control-label-notes'>$message</label>
                    <div class='col-sm-12'>" . rich_text_editor('rescomments', 4, 20, $rescomments) . "</div>
                </div>
                <div class='col-12 mt-5 d-flex justify-content-center'>
                    <input class='btn submitAdminBtn' type='submit' name='edit_res_submit' value='$langModify'>
                </div>                
            </form></div>
        </div>";
    return $content;
}

/**
 * @return string
 */
function localhostUrl() {
    return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME']
    );
}

/**
 * @param int $unit_id
 * @param int $prereq_unit_id
 */
function insert_prerequisite_unit ($unit_id, $prereq_unit_id) {

    global $is_editor,
           $course_id, $course_code,
           $langResultsFailed, $langUnitHasNotCompletionEnabled, $langNewUnitPrerequisiteFailInvalid,
           $langNewUnitPrerequisiteSuccess, $langNewUnitPrerequisiteFailAlreadyIn;

    if ($is_editor) { // Auth check
        if ($prereq_unit_id < 0) {
            Session::Messages($langNewUnitPrerequisiteFailInvalid);
            redirect_to_home_page('modules/units/manage.php?course=' . $course_code . '&manage=1&unit_id=' . $unit_id);
        }

        $prereqHasCompletion = prereq_unit_has_completion_enabled($prereq_unit_id);

        if ( !$prereqHasCompletion ) {
            Session::Messages($langUnitHasNotCompletionEnabled);
            redirect_to_home_page('modules/units/manage.php?course=' . $course_code . '&manage=1&unit_id=' . $unit_id);
        }

        // check already exists
        $result = Database::get()->queryArray("SELECT up.id
                                 FROM unit_prerequisite up
                                 WHERE up.course_id = ?d
                                 AND up.unit_id = ?d
                                 AND up.prerequisite_unit = ?d", $course_id, $unit_id, $prereq_unit_id);

        if (count($result) > 0) {
            Session::Messages($langNewUnitPrerequisiteFailAlreadyIn, 'alert-danger');
            redirect_to_home_page('modules/units/manage.php?course=' . $course_code . '&manage=1&unit_id=' . $unit_id);
        }

        Session::Messages($langNewUnitPrerequisiteSuccess, 'alert-success');
        Database::get()->query("INSERT INTO unit_prerequisite (course_id, unit_id, prerequisite_unit)
                                                VALUES (?d, ?d, ?d)", $course_id, $unit_id, $prereq_unit_id);
    } else {
        Session::Messages($langResultsFailed);
        redirect_to_home_page('modules/units/manage.php?course=' . $course_code . '&manage=1&unit_id=' . $unit_id);
    }
}

/**
 * @param int $prereq_unit_id
 * @return bool
 */
function prereq_unit_has_completion_enabled($prereq_unit_id) {
    $query = "SELECT bc.id FROM badge_criterion bc WHERE bc.badge IN (SELECT b.id FROM badge b WHERE b.unit_id = ?d)";
    $exists = Database::get()->querySingle($query, $prereq_unit_id);
    if ($exists) {
        return true;
    }
    return false;
}

/**
 * @param int $unit_id
 */
function delete_unit_prerequisite($unit_id) {
    $query = "DELETE FROM unit_prerequisite WHERE unit_id = ?d";
    Database::get()->query($query, $unit_id);
}


/**
 * @brief check if unit resource has completed
 * @param $unit_id
 * @param $unit_resource_id
 * @return integer
 */
function unit_resource_completion($unit_id, $unit_resource_id) {

    global $uid, $course_id;

    $sql_res = Database::get()->querySingle("SELECT res_id FROM unit_resources WHERE unit_id = ?d AND id = ?d", $unit_id, $unit_resource_id);
    if ($sql_res) {
        $res_id = $sql_res->res_id;
        $sql = Database::get()->querySingle("SELECT id FROM badge WHERE course_id = ?d AND unit_id = ?d", $course_id, $unit_id);
        if ($sql) {
            $badge_id = $sql->id;
            $q = Database::get()->querySingle("SELECT completed FROM user_badge JOIN badge_criterion
                                                        ON user_badge.badge = badge_criterion.badge
                                                        AND user = ?d
                                                        AND user_badge.badge = ?d
                                                        AND resource = ?d", $uid, $badge_id, $res_id);
            if ($q) {
                if ($q->completed) {
                    return 1; // activity has been completed
                } else {
                    return 0; // activity has not been completed
                }
            } else {
                return 2; // there is no activity
            }
        }
    }
}
