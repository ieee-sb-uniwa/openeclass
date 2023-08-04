@extends('layouts.default')

@push('head_scripts')
    <script type='text/javascript'>
        $(document).on('click', '#email_notification', function(e) {
            e.preventDefault();
            var info_message = '';
            var action_message = '';
            var url = $(this).attr('href');
            var varUrl = url.split('?'); /* split url parameters */

            for (i = 0; i < varUrl.length; i++) {
                varUrlName = varUrl[i].split('=');
            }

            var valueMessage = varUrlName[2]; /* value of url parameter 'email_un' */

            if (valueMessage == 1) {
                info_message = "{{ js_escape(trans('langUserEmailNotification')) }}" + "<br><br>" + "{{ js_escape(trans('langConfDisableMailNotification')) }}"
                action_message = " {{ js_escape(trans('langDeactivate')) }} ";
            } else {
                info_message = "{{ js_escape(trans('langNoUserEmailNotification')) }}" + "<br><br>" + "{{ js_escape(trans('langConfEnableMailNotification')) }}";
                action_message = " {{ js_escape(trans('langActivate')) }} ";
            }
            bootbox.confirm({
                title: "{{ js_escape(trans('langEmailUnsubscribe')) }}",
                message: info_message,
                buttons: {
                    confirm: {
                        label: action_message,
                        className: 'deleteAdminBtn'
                    },
                    cancel: {
                        label: "{{ js_escape(trans('langCancel')) }}",
                        className: 'cancelAdminBtn'
                    }
                },
                callback: function(result) {
                    if (result) {
                        window.location.href = url;
                    }
                }
            });
        });
    </script>
@endpush

@if($course_info->view_type == 'units' and $countUnits > 0)
  @push('head_scripts')
    <script src="{{ $urlServer }}/js/sortable/Sortable.min.js"></script>
    <script type='text/javascript'>
        $(document).ready(function(){
            Sortable.create(boxlistSort, {
                    animation: 350,
                    handle: '.fa-arrows',
                    animation: 150,
                    onUpdate: function (evt) {
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
    </script>
  @endpush
@endif

@push('head_scripts')
    <script>
        $(function() {
            $('body').keydown(function(e) {
                if(e.keyCode == 37 || e.keyCode == 39) {
                    if ($('.modal.in').length) {
                        var visible_modal_id = $('.modal.in').attr('id').match(/\d+/);
                        if (e.keyCode == 37) {
                            var new_modal_id = parseInt(visible_modal_id) - 1;
                        } else {
                            var new_modal_id = parseInt(visible_modal_id) + 1;
                        }
                        var new_modal = $('#hidden_'+new_modal_id);
                        if (new_modal.length) {
                            hideVisibleModal();
                            new_modal.modal('show');
                        }
                    }
                }
            });
        });
        function hideVisibleModal(){
            var visible_modal = $('.modal.in');
            if (visible_modal) { // modal is active
                visible_modal.modal('hide'); // close modal
            }
        };
    </script>
@endpush

@section('content')

<style>
    #progress_circle {
        display: flex;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: conic-gradient(red var(--progress), #E8EDF8 0deg);
        font-size: 0;
    }
    #progress_circle::after {
        content: attr(data-progress) '%';
        display: flex;
        justify-content: center;
        flex-direction: column;
        width: 100%;
        margin: 10px;
        border-radius: 50%;
        background: white;
        font-size: 2rem;
        text-align: center;
    }
</style>

<div class="col-12 main-section">
    <div class='{{ $container }} py-lg-0'>
        <div class="course-wrapper d-lg-flex align-items-lg-strech w-100">

            <aside id="background-cheat-leftnav" class="col_sidebar_active d-flex justify-content-start align-items-strech ps-lg-0 pe-lg-0">
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block ContentLeftNav">
                    @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                </div>
            </aside>

            <div class="col_maincontent_active">

                <div class="row">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="collapseTools" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                        </div>
                    </div>

                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])


                    @if(Session::has('message'))
                        <div class='col-12 all-alerts'>
                            <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                                @php 
                                    $alert_type = '';
                                    if(Session::get('alert-class', 'alert-info') == 'alert-success'){
                                        $alert_type = "<i class='fa-solid fa-circle-check fa-lg'></i>";
                                    }elseif(Session::get('alert-class', 'alert-info') == 'alert-info'){
                                        $alert_type = "<i class='fa-solid fa-circle-info fa-lg'></i>";
                                    }elseif(Session::get('alert-class', 'alert-info') == 'alert-warning'){
                                        $alert_type = "<i class='fa-solid fa-triangle-exclamation fa-lg'></i>";
                                    }else{
                                        $alert_type = "<i class='fa-solid fa-circle-xmark fa-lg'></i>";
                                    }
                                @endphp
                                
                                @if(is_array(Session::get('message')))
                                    @php $messageArray = array(); $messageArray = Session::get('message'); @endphp
                                    {!! $alert_type !!}<span>
                                    @foreach($messageArray as $message)
                                        {!! $message !!}
                                    @endforeach</span>
                                @else
                                    {!! $alert_type !!}<span>{!! Session::get('message') !!}</span>
                                @endif
                                
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif

                </div>

                <div class='row g-lg-5 mt-4'>

                    <div class='col-xl-7 col-12 mt-0'>
                        <div class='card panelCard border-0'>
                            <div class='card-header px-0 py-0 border-0 bg-white d-md-flex justify-content-md-between align-items-md-center'>

                                <div class='d-flex'>
                                    <h3 class='mb-0'>{{ trans('langCourseProgram') }}</h3>
                                    @if ($is_editor)
                                        @php
                                            warnCourseInvalidDepartment(true);
                                        @endphp
                                        <a class='mt-2 ms-2' href='{{ $urlAppend }}modules/course_home/editdesc.php?course={{ $course_code }}'>
                                            <span class='fa fa-pencil' data-bs-toggle='tooltip' data-bs-placement='top' title='{{ trans('langEditMeta') }}'></span>
                                        </a>
                                    @endif
                                </div>

                                <div class='mt-md-0 mt-3'>
                                    {!! $email_notify_icon !!}
                                    <a href='javascript:void(0);' data-bs-modal='citation' data-bs-toggle='modal' data-bs-target='#citation' class='float-end mt-2'>
                                        <span class='fa fa-paperclip fa-fw' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langCitation') }}"></span>
                                    </a>
                                    @if($uid)
                                        @if ($is_course_admin)
                                            <a href="{{ $urlAppend }}modules/user/index.php?course={{$course_code}}" class='float-end me-2 mt-2'>
                                                <span class="fa fa-users fa-fw" data-bs-toggle="tooltip" data-bs-placement="bottom" title data-bs-original-title="{{ $numUsers }}&nbsp;{{ trans('langRegistered') }}"></span>
                                            </a>
                                        @else
                                            @if (setting_get(SETTING_USERS_LIST_ACCESS, $course_id) == 1)
                                                <a href="{{ $urlAppend }}modules/user/userslist.php?course={{ $course_code }}" class='float-end me-2 mt-2'>
                                                    <span class="fa fa-users fa-fw" data-bs-toggle="tooltip" data-bs-placement="bottom" title data-bs-original-title="{{ $numUsers }}&nbsp;{{ trans('langRegistered') }}"></span>
                                                </a>
                                            @endif
                                        @endif
                                        @if ($is_editor)
                                                <a href = "{{ $urlAppend  }}modules/usage/index.php?course={{ $course_code }}" class='float-end me-2 mt-2'>
                                                <span class="fa fa-area-chart fa-fw" data-bs-toggle="tooltip" data-bs-placement="bottom" title data-bs-original-title="{{ trans('langUsage') }}"></span>
                                            </a>
                                        @else
                                            <a href = "{{ $urlAppend }}modules/usage/userduration.php?course={{ $course_code }}&u={{ $uid }}" class='float-end me-2 mt-2'>
                                                <span class="fa fa-area-chart fa-fw" data-bs-toggle="tooltip" data-bs-placement="bottom" title data-bs-original-title="{{ trans('langCourseParticipation') }}"></span>
                                            </a>
                                        @endif
                                    @endif
                                    @if ($offline_course)
                                        <a href="{{ $urlAppend }}modules/offline/index.php?course={{ $course_code }}" class='float-end me-2 mt-2'>
                                            <span class="fa fa-download fa-fw" data-bs-toggle="tooltip" data-bs-placement="bottom" title data-bs-original-title="{{ trans('langDownloadCourse') }}"></span>
                                        </a>
                                    @endif
                                </div>

                            </div>
                            <div class='card-body'>
                                <div class='row'>
                                    @if($course_info->home_layout == 1)
                                        <div class='col-12 px-0'>
                                            <figure>
                                                <picture>
                                                    @if($course_info->course_image)
                                                        <img class='uploadImageCourse' src='{{$urlAppend}}courses/{{$course_code}}/image/{{$course_info->course_image}}' alt='Course Banner'/>
                                                    @else
                                                        <img class='uploadImageCourse' src='{{$urlAppend}}template/modern/img/ph1.jpg'/>
                                                    @endif
                                                </picture>
                                            </figure>
                                        </div>
                                        <div class='col-12 mt-3 mb-3 px-0'>
                                            <div class='course_info'>
                                                @if ($course_info->description)
                                                        {!! $course_info->description !!}
                                                @else
                                                    <p class='not_visible text-center'> - {{ trans('langThisCourseDescriptionIsEmpty') }} - </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class='col-12 px-0'>
                                            <div class='course_info'>
                                                @if ($course_info->description)
                                                        {!! $course_info->description !!}
                                                @else
                                                    <p class='not_visible text-center'> - {{ trans('langThisCourseDescriptionIsEmpty') }} - </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                @if ((!$is_editor) and (!$courseDescriptionVisible))
                                    @if ($course_info->course_license)
                                        <div class='row'>
                                            <div class='col-12 d-flex justify-content-end px-0'>{!! copyright_info($course_id) !!}</div>
                                        </div>
                                    @endif
                                @else
                                    <div class='row'>
                                        <div class='col-12 course-below-wrapper mt-2 px-0'>
                                            <div class='row text-muted course-below-info'>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 d-flex justify-content-md-start justify-content-center">
                                                    <a role='button' id='btn-syllabus' data-bs-toggle='collapse' href='#collapseDescription' aria-expanded='false' aria-controls='collapseDescription'>
                                                        <span class='fa-solid fa-chevron-right fa-fw'></span>
                                                        <span class='TextBold ps-1'>{{ trans('langCourseDescription') }}</span>
                                                    </a>
                                                    @if($is_editor)
                                                        <span class='ps-2'>{!! $edit_course_desc_link !!}</span>
                                                    @endif
                                                </div>



                                                @if ($course_info->course_license)
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mt-md-0 mt-2 d-flex justify-content-end">{!! copyright_info($course_id) !!}</div>
                                                @endif
                                                <div class='col-12'>
                                                    <div class='collapse p-0' id='collapseDescription'>
                                                        <div class='col-12 p-0 bg-white'>
                                                            @foreach ($course_descriptions as $row)
                                                                <div class='row mb-3'>
                                                                    <div class='col-12'>
                                                                        <p class='form-label text-start'>{{$row->title}}</p>
                                                                    </div>
                                                                    <div class='col-12 desCourse'>
                                                                        {!! standard_text_escape($row->comments) !!}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if(isset($rating_content) || isset($social_content) || isset($comment_content))
                                <div class='card-footer d-md-flex justify-content-md-between align-items-md-start bg-white border-0 mt-0 mb-0 p-0'>
                                    @if(isset($rating_content))
                                        <div class='d-flex justify-content-md-start justify-content-center align-items-center'>
                                            <div class='ps-md-0 pt-2 pb-2 pe-2'>{!! $rating_content !!}</div>
                                        </div>
                                    @endif
                                    @if(isset($comment_content))
                                        <div class='d-flex justify-content-md-start justify-content-center align-items-center'>
                                            <div class='ps-3 pt-2 pb-2 pe-2'>{!! $comment_content !!}</div>
                                        </div>
                                    @endif
                                    @if(isset($social_content))
                                        <div class='d-flex justify-content-md-start justify-content-center align-items-center'>
                                            <div class='ps-3 pt-2 pb-2 pe-md-0'>{!! $social_content !!}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>

                        @if($course_info->view_type == 'units')
                            <div class='card panelCard px-lg-4 py-lg-3 mt-5'>

                                <div class='card-header border-0 bg-white d-flex justify-content-between align-items-center'>
                                    <h3>
                                        {{ trans('langCourseUnits') }}
                                    </h3>
                                    <div class='d-flex'>
                                        @if ($is_editor)
                                            <a href='{{ $urlServer }}modules/units/info.php?course={{ $course_code }}' class='add-unit-btn mt-0 float-end pe-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langAddUnit') }}">
                                                <span class='fa fa-plus-circle'></span>
                                            </a>
                                            @if($course_info->flipped_flag == 2)
                                                <a href='{{ $urlServer }}modules/create_course/edit_flipped_classroom.php?course={{ $course_code }}&fromFlipped=1' class='add-unit-btn mt-0 float-end pe-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langFlippedEdit') }}">
                                                    <span class='fa fa-pencil'></span>
                                                </a>
                                            @endif
                                        @endif
                                        @if($total_cunits > 0 and $is_editor)
                                            <a href='{{ $urlServer }}modules/course_home/course_home.php?course={{ $course_code }}&viewUnit=0' class='add-unit-btn mt-0 float-end pe-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langShowUnitCarousel') }}">
                                                <span class='fa fa-columns'></span>
                                            </a>
                                            <a href='{{ $urlServer }}modules/course_home/course_home.php?course={{ $course_code }}&viewUnit=1' class='add-unit-btn mt-0 float-end pe-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langShowUnitRow') }}">
                                                <span class='fa fa-list mb-0'></span>
                                            </a>
                                        @endif
                                        <a id='help-btn' href='{{ $urlServer }}modules/help/help.php?language={{$language}}&topic=course_units' class='add-unit-btn mt-0 float-end' data-bs-toggle='tooltip' data-bs-placement='bottom' title data-bs-original-title="{{ trans('langHelp') }}">
                                            <span class='fa fa-question-circle'></span>
                                        </a>
                                    </div>
                                </div>

                                <div class='card-body' id='boxlistSort'>
                                    {!! $cunits_content !!}
                                </div>

                            </div>
                        @endif


                        @if($course_info->view_type == 'activity')
                            @if($is_editor)
                                <div class='col-12 d-flex justify-content-start mb-3 mt-5'>
                                    <a class='btn submitAdminBtnDefault mt-0 mb-2 gap-2' href="{{ $urlServer }}modules/course_info/activity_edit.php?course{{$course_code}}">
                                        <i class='fa-solid fa-edit'></i>
                                        {{trans('langActivityEdit')}}
                                    </a>
                                </div>
                            @endif
                            <div class='col-12'>
                                @php
                                    $qVisible = ($is_editor? '': 'AND visible = 1');
                                    $items = Database::get()->queryArray("SELECT activity_content.id, heading, content
                                        FROM activity_heading
                                            LEFT JOIN activity_content
                                                ON activity_heading.id = activity_content.heading_id AND
                                                course_id = ?d
                                        ORDER BY `order`", $course_id);
                                @endphp

                                @if(count($items) > 0)

                                    <div class='panel'>
                                        <div class='panel-group group-section' id='accordion' role='tablist' aria-multiselectable='true'>
                                            <ul class="list-group list-group-flush @if($is_editor) mt-3 @else mt-5 @endif">
                                                @foreach ($items as $item)
                                                    @if (trim($item->content))
                                                        <li class="list-group-item px-0 mb-4 bg-transparent">
                                                            <a class='accordion-btn d-flex justify-content-start align-items-start' role='button' data-bs-toggle='collapse' href='#item-{{ $item->id }}' aria-expanded='false' aria-controls='#{{ $faq->id }}'>
                                                                <span class='fa-solid fa-chevron-down'></span>
                                                                {!! q(getSerializedMessage($item->heading)) !!}
                                                                
                                                            </a>

                                                            <div id='item-{{ $item->id }}' class='panel-collapse accordion-collapse collapse border-0 rounded-0' role='tabpanel' aria-labelledby='heading{{ $faq->id }}' data-bs-parent='#accordion'>
                                                                <div class='panel-body bg-transparent Neutral-900-cl px-4'>
                                                                    {!! standard_text_escape($item->content) !!}

                                                                    @php
                                                                        $resources = Database::get()->queryArray("SELECT * FROM unit_resources
                                                                            WHERE unit_id = ?d AND `order` >= 0 $qVisible ORDER BY `order`", $item->id);
                                                                    @endphp

                                                                    @if (count($resources))
                                                                        <div class='table-responsive'>
                                                                            <table class='table-default table-striped table-hover'>
                                                                                <tbody>
                                                                                    @foreach ($resources as $info)
                                                                                        @php $info->comments = standard_text_escape($info->comments); @endphp
                                                                                        {!! show_resourceWeek($info) !!}
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>


                                @endif
                            </div>
                        @endif


                        @if($course_info->view_type == 'wall')
                            @include('layouts.partials.course_wall_functions',['is_editor' => $is_editor])
                        @endif


                    </div>


                    <div class='col-xl-5 col-12 mt-xl-0 mt-5'>
                        <div class="panel panel-admin panel-admin-calendar p-0 border-0">
                            {!! $user_personal_calendar !!}
                            <div class='panel-footer d-flex justify-content-start align-items-center flex-wrap px-0 py-3'>
                                <div class='d-flex align-items-center px-2 py-1'>
                                    <span class='event event-important'></span>
                                    <span class='small-text'>{{ trans('langAgendaDueDay') }}</span>
                                </div>
                                <div class='d-flex align-items-center px-2 py-1'>
                                    <span class='event event-info'></span>
                                    <span class='small-text'>{{ trans('langAgendaCourseEvent') }}</span>
                                </div>
                                <div class='d-flex align-items-center px-2 py-1'>
                                    <span class='event event-success'></span>
                                    <span class='small-text'>{{ trans('langAgendaSystemEvent') }}</span>
                                </div>
                                <div class='d-flex align-items-center px-2 py-1'>
                                    <span class='event event-special'></span>
                                    <span class='small-text'>{{ trans('langAgendaPersonalEvent') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card panelCard border-0 mt-5">
                            <div class='card-header px-0 py-0 border-0 bg-white d-flex justify-content-between align-items-center'>
                                <h3 class='mb-0'>{{ trans('langAnnouncements') }}</h3>
                                <div class='text-end'>
                                    <a class='TextRegular text-decoration-underline vsmall-text' href="{{ $urlAppend }}modules/announcements/index.php?course={{ $course_code }}">{{ trans('langAllAnnouncements') }}...</a>
                                </div>
                            </div>
                            <div class='card-body px-0 py-0'>
                                <ul class='list-group list-group-flush mt-3'>
                                    {!! course_announcements() !!}
                                </ul>
                            </div>
                        </div>


                        @if ($uid)
                            @if(isset($course_completion_id) and $course_completion_id > 0)
                                <div class="card panelCard border-0 mt-5">
                                    <div class='card-header px-0 py-0 border-0 bg-white d-flex justify-content-between align-items-center'>
                                        <h3 class='mb-0'>{{ trans('langCourseCompletion') }}</h3>
                                        @if ($is_editor)
                                            <a class='TextRegular text-decoration-underline vsmall-text' href='{{ $urlServer }}modules/progress/index.php?course={{ $course_code }}&badge_id={{ $course_completion_id }}&progressall=true'>
                                                {{ $certified_users}}/{{ $studentUsers }} {{ trans('langUsersS') }}...
                                            </a>
                                        @else
                                            <a class='TextRegular text-decoration-underline vsmall-text' href='{{ $urlServer }}modules/progress/index.php?course={{ $course_code }}&badge_id={{ $course_completion_id }}&u={{ $uid }}'>
                                                {{ trans('langDetail') }}...
                                            </a>
                                        @endif
                                    </div>
                                    <div class='card-body'>
                                        <div class='text-center'>
                                            <div class='col-12 h-100'>
                                                @if ($is_editor)
                                                    <i class="fa-solid fa-award" style='transform: scale(10,10); margin-top:80px;'></i>
                        
                                                @else
                                                    <div class="center-block d-inline-block">
                                                        <a href='{{ $urlServer }}modules/progress/index.php?course={{ $course_code }}&badge_id={{ $course_completion_id}}&u={{ $uid }}'>
                                                            @if ($percentage == '100')
                                                                <i class='fa fa-check-circle fa-5x state_success'></i>
                                                            @else
                                                                <div id="progress_circle" data-progress="{{ $percentage }}" style="--progress: {{ $percentage }}deg;">{{ $percentage }}%</div>
                                                            @endif
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if (isset($level) && !empty($level))
                            <div class='card panelCard border-0' style='margin-top:100px;'>
                                <div class='card-header px-0 py-0 border-0 bg-white d-flex justify-content-between align-items-center'>
                                    <h3>{{ trans('langOpenCourseShort') }}</h3>
                                </div>
                                <div class='card-body px-0 py-0'>
                                    {!! $opencourses_level !!}
                                    <div class='mt-3 text-center'>
                                        {!! $opencourses_level_footer !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($course_home_main_area_widgets)
                            <div class='card panelCard border-0' style='margin-top:100px;'>
                                <div class='card-header px-0 py-0 border-0 bg-white d-flex justify-content-between align-items-center'>
                                    <h3>{{ trans('langWidgets') }}</h3>
                                </div>
                                <div class='card-body px-0 py-0'>
                                    {!! $course_home_main_area_widgets !!}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div> <!-- end row -->

        </div>

    </div>
</div>

<div class='modal fade' id='citation' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <div class='modal-title' id='myModalLabel'>{{ trans('langCitation') }}</div>
                <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
                    <span class='fa-solid fa-xmark fa-lg Neutral-700-cl' aria-hidden='true'></span>
                </button>
                
            </div>
            <div class='modal-body'>
                {{ $course_info->prof_names }}&nbsp;
                <span>{{ $currentCourseName }}</span>&nbsp;
                {{ trans('langAccessed') }} {{ format_locale_date(strtotime('now')) }}&nbsp;
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
                                className: 'cancelAdminBtn'
                            },
                            success: {
                                label: '{{ js_escape(trans('langSubmit')) }}',
                                className: 'submitAmdinBtn',
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

@endif

@endsection
