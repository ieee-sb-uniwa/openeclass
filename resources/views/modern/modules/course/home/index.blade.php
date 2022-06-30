
@extends('layouts.default')

@section('content')

<div class="pb-3 pt-3">
    <div class="container-fluid main-container">
        <div class="row">

            <div class="col-xl-2 col-lg-2 col-md-0 col-sm-0 col-0 justify-content-center col_sidebar_active"> 
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block">
                    @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                </div>
            </div>

            <div class="col-xl-10 col-lg-10 col-md-12 col-sm-12 col-12 justify-content-center col_maincontent_active">
                <div class="row p-5">
                    <nav class="navbar navbar-expand-lg navrbar_menu_btn">
                        <button type="button" id="menu-btn" class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block btn btn-primary menu_btn_button">
                            <i class="fas fa-align-left"></i>
                            <span></span>
                        </button>
                        <a class="btn btn-primary d-lg-none mr-auto" type="button" data-bs-toggle="offcanvas" href="#collapseTools" role="button" aria-controls="collapseTools" style="margin-top:-10px;">
                            <i class="fas fa-tools"></i>
                        </a>
                    </nav>

                    <nav class="navbar_breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ $urlAppend }}main/portfolio.php">{{trans('langPortfolio')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ $urlAppend }}main/my_courses.php">{{trans('mycourses')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$title_course}}</li>
                        </ol>
                    </nav>


                    <div class="offcanvas offcanvas-start d-lg-none mr-auto" tabindex="-1" id="collapseTools" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                        </div>
                    </div>


                    <div class="col-xxl-12 col-lx-12 col-lg-12 col-md-10 col-sm-6">
                        <div class="row p-2"></div><div class="row p-2"></div>
                        <legend class="float-none w-auto py-2 px-4 notes-legend"><span class="pos_TitleCourse"><i class="fas fa-university" aria-hidden="true"></i> <strong>{{$title_course}} <small>({{$course_code_title}})</small></strong></span>
                            <div class="manage-course-tools float-end">
                                @if ($is_course_admin)
                                    @include('layouts.partials.manageCourse',[$urlAppend => $urlAppend,'coursePrivateCode' => $course_code_title])
                                @endif
                            </div>
                        </legend>
                    </div>
                    

                    <p class='mt-5'>
                        <span class='control-label-notes text-start ps-1'>
                            {{trans('langTeacher')}}: 
                            <small>{{course_id_to_prof($course_id)}}</small>
                        </span>

                        <a class="btn btn-secondary rounded-circle float-end" data-bs-toggle="collapse" href="#InfoCourse" role="button" aria-expanded="false" aria-controls="InfoCourse">
                            <i class='fa fa-arrow-down'></i>
                        </a>
                    </p>

                    <div class="collapse mt-2" id="InfoCourse">
                        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="tools-courses-new ps-2 pe-2 shadow-lg p-3 mb-5 bg-body rounded bg-primary">
                                <div class='row p-2'>
                                    <div class='col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6'>
                                        <a data-bs-modal='citation' data-bs-toggle='modal' data-bs-target='#citation' href='javascript:void(0);'><span class='fa fa-paperclip fa-fw' data-bs-toggle='tooltip' data-bs-placement='top' title='{{ trans('langCitation') }}'></span> {{ trans('langCitation') }}</a>
                                    </div>
                                    <div class='col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6'>
                                        @if($uid)
                                            
                                            @if ($is_course_admin)
                                                <a class="ps-2 pe-2 float-end" href="{{ $urlAppend }}modules/user/index.php?course={{$course_code}}"><span class="fa fa-users fa-fw" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $numUsers }}&nbsp;{{ trans('langRegistered') }}"></span> {{ $numUsers }}&nbsp;{{ trans('langRegistered') }}</a>
                                            @else
                                                @if ($visible == COURSE_CLOSED)
                                                    <a class="ps-2 pe-2 float-end" href="{{ $urlAppend }}modules/user/userslist.php?course={{ $course_code }}"><span class="fa fa-users fa-fw" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $numUsers }}&nbsp;{{ trans('langRegistered') }}"></span> {{ $numUsers }}&nbsp;{{ trans('langRegistered') }}</a>
                                                @endif
                                            @endif
                                            
                                        @endif
                                    </div>
                                </div>

                               
                                    
                                    @if(isset($rating_content) || isset($social_content) || isset($comment_content))
                                        <div class='row p-2'>
                                            <div class='col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6'>        
                                                @if(isset($rating_content))
                                                    <li>
                                                        {!! $rating_content !!}
                                                    </li>
                                                @endif
                                            </div>
                                        

                                            <div class='col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6'> 
                                                @if(isset($social_content) || isset($comment_content))
                                                    <span class='float-end ps-2 pe-2'>
                                                        @if(isset($comment_content))
                                                            {!! $comment_content !!}
                                                        @endif
                                                        @if(isset($social_content) && isset($comment_content))
                                                            &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;
                                                        @endif
                                                        @if(isset($social_content))
                                                            {!! $social_content !!}
                                                        @endif
                                                    </span>
                                                    
                                                @endif
                                            </div>
                                        </div>
                
                                    @endif

                                @if ($offline_course)
                                    <li>
                                        <a href="{{ $urlAppend }}modules/offline/index.php?course={{ $course_code }}">
                                            <span class="fa fa-download fa-fw" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('langDownloadCourse') }}"></span>
                                        </a>
                                    </li>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class='panel panel-default mt-3'>
                        <div class='panel-body'>
                            @if ($course_info->home_layout == 1)
                                <!-- <div class='banner-image-wrapper col-md-5 col-sm-5 col-xs-12'>
                                    <div>
                                        <img class='banner-image img-responsive' src='{{ isset($course_info->course_image) ? "$urlAppendcourses/$course_code/image/" . rawurlencode($course_info->course_image) : "$themeimg/ph1.jpg" }}' alt='Course Banner'/>
                                    </div>

                                </div> -->
                                @if(!empty($course_info->course_image))
                                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <figure>
                                            <picture>
                                                <img class="uploadImageCourse" src='{{$urlAppend}}courses/{{$course_code}}/image/{{$course_info->course_image}}'>
                                            </picture>
                                        </figure>
                                    </div>
                                @else
                                    <div class="row p-2"></div>
                                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 uploadImageCourseCol">
                                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 NouploadImageCourseCol"></div>
                                    </div>
                                @endif
                            @endif
                            <div class='col-12{{ $course_info->home_layout == 1 ? ' col-sm-7' : ''}}'>
                                <div class='course_info'>
                                    @if ($course_info->description)
                                            <div class="control-label-notes">{{ trans('langDescription') }}</div>
                                            {!! $course_info->description !!}
                                    @else
                                        <p class='not_visible'> - {{ trans('langThisCourseDescriptionIsEmpty') }} - </p>
                                    @endif
                                </div>
                            </div>

                            <div class="row p-2"></div>

                            @if ((!$is_editor) and (!$courseDescriptionVisible))
                                @if ($course_info->course_license)
                                    <span class='pull-right' style="margin-top: 15px;">{!! copyright_info($course_id) !!}</span>
                                @endif
                            @else
                                <div class='col-12 course-below-wrapper'>
                                    <div class='row text-muted course-below-info'>
                                        <div class="row">
                                            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-6 col-6">
                                                <a role='button' id='btn-syllabus' data-bs-toggle='collapse' href='#collapseDescription' aria-expanded='false' aria-controls='collapseDescription'>
                                                    <span class='fa fa-chevron-right fa-fw'></span>
                                                    <span style='padding-left: 5px;'>{{ trans('langCourseDescription') }}</span>
                                                </a>
                                            </div>
                                            @if($is_editor)
                                                <div class="col-xl-7 col-lg-7 col-md-6 col-sm-6 col-6">
                                                    @if($courseDescriptionVisible>0)
                                                        <span class='float-end'>{{trans('langCourseDescription')}}</span>
                                                    @else
                                                    <span class='float-end'>{{trans('langAdd')}}</span>
                                                    @endif 
                                                    <span class='float-end pe-2'>{!! $edit_course_desc_link !!}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($course_info->course_license)
                                            <span class="pull-right">{!! copyright_info($course_id) !!}</span>
                                        @endif
                                        <div class='collapse shadow-lg p-3 mb-5 bg-body rounded bg-primary' id='collapseDescription'>
                                            <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                                                @foreach ($course_descriptions as $row)
                                                    <div style='margin-top: 1px;'><strong>{{$row->title}}</strong></div>
                                                    <div>{!! standard_text_escape($row->comments) !!}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                        
                    </div>
                </div> <!-- end row p-5 -->

                <div class="row pb-3 pt-1 pe-5 ps-5">
                    <div class="col-xl-8 col-lg-6 col-md-12 mt-3 px-md-5 col_maincontent_active col_maincontent_active_unit">
                        <div class="row p-2 bg-white"></div>
                        <div class="row p-2 bg-white"></div>
                        <div class="pb-5 bg-white">
                            @if (!$alter_layout)
                                <div class='col-md-12 course-units'>
                                    <div class='row'>
                                        <div class='col-md-12 mt-5px'>
                                            <div class='content-title pull-left h5'>
                                                {{ trans('langCourseUnits') }}
                                            </div>
                                            <div class='row p-2'></div><div class='row p-2'></div>
                                                <a style="float:right" class='pull-left add-unit-btn' id='help-btn' href='{{ $urlAppend }}modules/help/help.php?language={{$language}}&topic=course_units' data-toggle='tooltip' data-placement='top' title='{{ trans('langHelp') }}'>
                                                {{ trans('langHelp') }} <span class='fa fa-question-circle'></span>
                                                </a>
                                            @if ($is_editor and $course_info->view_type == 'units')
                                                <a href='{{ $urlServer }}modules/units/info.php?course={{ $course_code }}' class='pull-left add-unit-btn' data-toggle='tooltip' data-placement='top' title='{{ trans('langAddUnit') }}'>
                                                {{ trans('langAddUnit') }} <span class='fa fa-plus-circle'></span>

                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <!-- <div class='row boxlist no-list' id='boxlistSort'> -->
                                    <div class='' id='boxlistSort'>
                                        @if ($course_units)
                                            <?php $count_index = 0;?>
                                            @foreach ($course_units as $key => $course_unit)
                                                <?php
                                                    $not_shown = false;
                                                    if (!(is_null($course_unit->start_week)) and (date('Y-m-d') < $course_unit->start_week)) {
                                                        $not_shown = true;
                                                    }
                                                ?>
                                                @if ($course_unit->visible == 1)
                                                <?php $count_index++; ?>
                                                @endif
                                                @if (!$is_editor and $not_shown)
                                                    @continue;
                                                @else
                                                    <div id='unit_{{ getIndirectReference($course_unit->id) }}' class='col-xs-12' data-id='{{ $course_unit->id }}'>
                                                        <div class='panel clearfix'>
                                                            <div class='col-12'>
                                                                <div class='item-content'>
                                                                    <div class='item-header clearfix'>
                                                                        <div class='item-title h4'>
                                                                            <a {!! (!$course_unit->visible or $not_shown)? " class='not_visible'" : "" !!} href='{{ $urlServer }}modules/units/index.php?course={{ $course_code }}&amp;id={{ $course_unit->id }}'>
                                                                                {{ $course_unit->title }}
                                                                            </a>
                                                                            <small>
                                                                                <span class='help-block'>
                                                                                    <?php
                                                                                        if (!(is_null($course_unit->start_week))) {
                                                                                            echo $GLOBALS['langFrom2'];
                                                                                            echo "&nbsp;";
                                                                                            echo nice_format($course_unit->start_week);
                                                                                        }
                                                                                    ?>
                                                                                    <?php
                                                                                        if (!(is_null($course_unit->finish_week))) {
                                                                                            echo $GLOBALS['langTill'];
                                                                                            echo "&nbsp;";
                                                                                            echo nice_format($course_unit->finish_week);
                                                                                        }
                                                                                    ?>
                                                                                </span>
                                                                            </small>
                                                                        </div>
                                                @endif
                                                                    <div class='item-body'>
                                                                        {!! $course_unit->comments == ' ' ? '' : standard_text_escape($course_unit->comments) !!}
                                                                    </div>
                                                                    @if ($is_editor)
                                                                        <div class="dropdown float-end">
                                                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton{{$course_unit->id}}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="fas fa-cogs"></i>
                                                                            </button>
                                                                            <ul class="row p-4 dropdown-menu myuls" aria-labelledby="dropdownMenuButton{{$course_unit->id}}">
                                                                                <li><a href="{{$urlAppend}}modules/units/info.php?course={{$course_code}}&amp;edit={{$course_unit->id}}"><i class="fas fa-edit"></i> {{ trans('langEditChange') }}</a></li>
                                                                                <li><a href="{{ $_SERVER['REQUEST_URI'] }}?vis={{$course_unit->id}}"><i class="fas fa-eye"></i>@if($course_unit->visible == 1){{ trans('langViewHide') }}@else{{ trans('langViewShow') }}@endif</a></li>
                                                                                <li><a href="{{ $_SERVER['REQUEST_URI'] }}?access={{$course_unit->id}}"><i class="fas fa-lock"></i>@if($course_unit->public == 1) {{ trans('langResourceAccessLock') }} @else {{ trans('langResourceAccessUnlock') }} @endif</a></li>
                                                                                <li><a href="" data-bs-toggle="modal" data-bs-target="#unit{{$course_unit->id}}"><i class="fas fa-trash"></i> {{trans('langDelete')}}</a></li>

                                                                            </ul>

                                                                        </div>
                                                                        <!-- Modal -->
                                                                        <div class="modal fade" id="unit{{$course_unit->id}}" tabindex="-1" aria-labelledby="unitLabel{{$course_unit->id}}" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="unitLabel{{$course_unit->id}}">{{$course_unit->title}}</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        Είστε σίγουρος/η ότι θέλετε να προχωρήσετε στην συγκεκριμένη διαγραφή?
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ακύρωση</button>
                                                                                        <a type="button" href="{{ $_SERVER['REQUEST_URI'] }}?del={{$course_unit->id}}" class="btn btn-danger">Διαγραφή</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                        @else
                                            <div class='col-sm-12'>
                                                <div class='panel'>
                                                    <div class='panel-body not_visible'> - {{ trans('langNoUnits') }} - </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {!! $course_home_main_area_widgets !!}
                                </div>
                            @else
                                <div class='col-sm-12'>
                                    {{ trans('langCourseUnits') }}<hr>
                                    <div class='panel'>
                                        <div class='panel-body not_visible'> - {{ trans('langNoUnits') }} - </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div><!-- end col units -->
                    <div class="col-xl-4 col-lg-6 col-md-12 mt-3 float-end ">

                        <div class="container-fluid container_fluid_calendar col_maincontent_active_calendar">
                            {!! $user_personal_calendar !!}
                            <div class='row p-2'></div>
                            <div class='row p-2'></div>
                            <div class='panel-footer'>
                                <div class='row'>
                                    <div class='col-sm-6 event-legend'>
                                        <div>
                                            <span class='event event-important'></span>
                                            <span>{{ trans('langAgendaDueDay') }}</span>
                                        </div>
                                        <div>
                                            <span class='event event-info'></span>
                                            <span>{{ trans('langAgendaCourseEvent') }}</span>
                                        </div>
                                    </div>
                                    <div class='col-sm-6 event-legend'>
                                        <div>
                                            <span class='event event-success'></span>
                                            <span>{{ trans('langAgendaSystemEvent') }}</span>
                                        </div>
                                        <div>
                                            <span class='event event-special'></span>
                                            <span>{{ trans('langAgendaPersonalEvent') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row p-2'></div>
                            <div class='row p-2'></div>
                        </div>

                        <div class="col-md-12 px-5 mt-3 announcement-course-page" style="margin-left:11px;">

                            <div class="pb-5 bg-white">
                                <div class='row p-2'></div>
                                <h5 class='content-title'>{{ trans('langAnnouncements') }}</h5>
                                <hr>
                                <div class='panel'>
                                    <div class='panel-body'>
                                        <ul class='tablelist' style='margin-left:-20px; margin-top:-20px;'>
                                            {!! course_announcements() !!}
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class='panel-footer'>
                                        <div class='pull-right'>
                                            <a href='{{ $urlAppend }}modules/announcements/index.php?course={{ $course_code}}'>
                                                <small>{{ trans('langMore') }}&hellip;</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                {!! $course_home_sidebar_widgets !!}

                            </div>
                        </div>

                        <div class="col-md-12 px-5 mt-3 col_maincontent_active_CourseComplete" style="margin-left:11px;">
                            <div class="row p-2 bg-white"></div>
                            <div class="row p-2 bg-white"></div>
                            <div class="pb-5 bg-white">
                            <h5 class='content-title'>{{ trans('langCourseCompletion') }}</h5>
                            <hr>
                                @if(isset($course_completion_id) and $course_completion_id > 0)
                                    <div class='col-md-12'>
                                        <div class='panel'>
                                            <div class='panel-body'>
                                                <div class='text-center'>
                                                    <div class='col-sm-12'>
                                                        <div class="center-block" style="display:inline-block;">
                                                            <a style="text-decoration:none" href='{{ $urlServer }}modules/progress/index.php?course={{ $course_code }}&badge_id={{ $course_completion_id}}&u={{ $uid }}'>
                                                        @if ($percentage == '100%')
                                                            <i class='fa fa-check-circle fa-5x state_success'></i>
                                                        @else
                                                            <div class='course_completion_panel_percentage'>
                                                                {{ $percentage }}
                                                            </div>
                                                        @endif
                                                        </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($level) && !empty($level))
                                    <div class='col-md-12'>
                                        <h5 class='content-title'>{{ trans('langOpenCourseShort') }}</h5>
                                        <div class='panel'>
                                            <div class='panel-body'>
                                                {!! $opencourses_level !!}
                                            </div>
                                            <div class='panel-footer'>
                                                {!! $opencourses_level_footer !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>



                    </div><!-- end col calendar -->

                </div><!-- end row pb3 pt1 -->


            </div><!-- end col-10 maincontent active-->


        </div>
    </div>
</div>





<div class='modal fade' id='citation' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
                <div class='modal-title h4' id='myModalLabel'>{{ trans('langCitation') }}</div>
            </div>
            <div class='modal-body'>
                {{ $course_info->prof_names }}&nbsp;
                <span>{{ $currentCourseName }}</span>&nbsp;
                {{ trans('langAccessed') }} {{ claro_format_locale_date(trans('dateFormatLong'), strtotime('now')) }}&nbsp;
                {{ trans('langFrom2') }} {{ $urlServer }}courses/{{$course_code}}/
            </div>
        </div>
    </div>
</div>

@if(!$registered)
    <script type='text/javascript'>
        $(function() {
            $('#passwordModal').on('click', function(e){
                var registerUrl = this.href;
                e.preventDefault();
                @if ($course_info->password !== '')
                    bootbox.dialog({
                        title: '{{ js_escape(trans('langLessonCode')) }}',
                        message: '<form class="form-horizontal" role="form" action="' + registerUrl + '" method="POST" id="password_form">' +
                                    '<div class="form-group">' +
                                        '<div class="col-sm-12">' +
                                            '<input type="text" class="form-control" id="password" name="password">' +
                                            '<input type="hidden" id="register" name="register" value="from-home">' +
                                            "{!! generate_csrf_token_form_field() !!}" +
                                        '</div>' +
                                    '</div>' +
                                '</form>',
                        buttons: {
                            cancel: {
                                label: '{{ js_escape(trans('langCancel')) }}',
                                className: 'btn-default'
                            },
                            success: {
                                label: '{{ js_escape(trans('langSubmit')) }}',
                                className: 'btn-success',
                                callback: function (d) {
                                    var password = $('#password').val();
                                    if(password != '') {
                                        $('#password_form').submit();
                                    } else {
                                        $('#password').closest('.form-group').addClass('has-error');
                                        $('#password').after('<span class="help-block">{{ js_escape(trans('langTheFieldIsRequired')) }}</span>');
                                        return false;
                                    }
                                }
                            }
                        }
                    });
                @else
                    $('<form method="POST" action="' + registerUrl + '">' +
                        '<input type="hidden" name="register" value="from-home">' +
                        "{!! generate_csrf_token_form_field() !!}" +
                    '</form>').appendTo('body').submit();
                @endif
            });
        });
    </script>

    <!-- Remove localStorage for sidebarCourse And sidebarAdmin -->
    <script>
        localStorage.removeItem('getIdSideBarTexts');
    </script>

@endif

@endsection