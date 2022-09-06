@extends('layouts.default')

@section('content')

<div class="pb-lg-3 pt-lg-3 pb-0 pt-0">
    <div class="container-fluid main-container">
        <div class="row rowMedium">

            <div id="background-cheat-leftnav" class="col-xl-2 col-lg-3 col-md-0 col-sm-0 col-0 justify-content-center col_sidebar_active">
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block">
                    @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                </div>
            </div>

            <div class="col-xl-10 col-lg-9 col-md-12 col-sm-12 col-12 justify-content-center col_maincontent_active">

                <div class="row p-lg-5 p-md-5 ps-1 pe-2 pt-5 pb-5">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                    <div class="offcanvas offcanvas-start d-lg-none mr-auto" tabindex="-1" id="collapseTools" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                        </div>
                    </div>

                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])

                    {!! isset($action_bar) ?  $action_bar : '' !!}
                    <div class='row margin-top-thin margin-bottom-fat'>
                        <div class='col-md-12'>
                            <div class='panel panel-default border border-secondary-4 shadow-sm Borders'>

                                <div class='panel-body Borders'>
                                    <div id='course-title-wrapper' class='course-info-title clearfix'>
                                        <div class='text-start h4'>
                                            {{ trans('langDescription') }}
                                        </div>
                                        <ul class='course-title-actions clearfix float-end list-inline'>
                                            <li class='access float-end'>
                                                <a href='javascript:void(0);' style='color: #23527C;'>
                                                    <span id='lalou' class='fa fa-info-circle fa-fw' data-container='#course-title-wrapper' data-toggle='popover' data-placement='bottom' data-html='true' data-content='{{ $course_info_popover }}'></span>
                                                    <span class='hidden'>.</span>
                                                </a>
                                            </li>
                                            <li class='access float-end'>
                                                <a href='javascript:void(0);'>{!! $lessonStatus !!}</a>
                                            </li>
                                            <li class='access float-end'>
                                                <a data-modal='citation' data-bs-toggle='modal' data-bs-target='#citation' href='javascript:void(0);'>
                                                    <span class='fa fa-paperclip fa-fw' data-toggle='tooltip' data-placement='top' title='{{ trans('langCitation') }}'></span>
                                                    <span class='hidden'>.</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @if ($course_info->home_layout == 1)
                                        <div class='banner-image-wrapper col-md-5 col-sm-5 col-12'>
                                            <div>
                                                <img class='banner-image img-responsive' src='{{ isset($course_info->course_image) ? "{$urlAppend}courses/$course_code/image/" . rawurlencode($course_info->course_image) : "$themeimg/ph1.jpg" }}' alt='Course Banner'/>
                                            </div>
                                        </div>
                                    @endif
                                    <div class='col-12'>
                                        <div class='course_info'>
                                            @if ($course_info->description)
                                                <!--Hidden html text to store the full description text & the truncated desctiption text so as to be accessed by javascript-->
                                                <div id='not_truncated' class='hidden'>
                                                    {!! $full_description !!}
                                                </div>
                                                <div id='truncated' class='hidden'>
                                                    {!! $truncated_text !!}
                                                </div>
                                                <!--Show the description text-->
                                                <div id='descr_content' class='is_less'>
                                                    {!! $truncated_text !!}
                                                </div>
                                            @else
                                                <p class='not_visible'> - {{ trans('langThisCourseDescriptionIsEmpty') }} - </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class='col-12 course-below-wrapper'>
                                        <div class='row text-muted course-below-info'>
                                            <div class='col-6'>
                                                <strong>{{ trans('langCode') }}: </strong> {{ $course_info->public_code }}<br>
                                                <strong>{{ trans('langFaculty') }}: </strong>
                                                {!! $departments !!}
                                            </div>
                                            <div class='col-6'>
                                                @if ($course_info->course_license)
                                                    <div class ='text-center'>
                                                        <strong>{{ trans('langLicense') }}:</strong><br>
                                                        <span>{!! copyright_info($course_id, 0) !!}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12 course-units border border-secondary-4 shadow-sm Borders mt-3'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <div class='content-title text-start h3'>
                                        {{ $course_info->view_type == 'weekly' ? trans('langCourseWeeklyFormat') : trans('langCourseUnits') }}
                                    </div>
                                        <a class='text-start add-unit-btn' id='help-btn' href='{{ $urlAppend }}modules/help/help.php?language={{ $language}}&topic=course_units' data-toggle='tooltip' data-placement='top' title='{{ trans('langHelp') }}'>
                                            <span class='fa fa-question-circle'></span>
                                        </a>
                                </div>
                            </div>
                            <div class='row boxlist no-list'>
                                @if ($course_units)
                                    <?php $count_index = 0;?>
                                    @foreach ($course_units as $key => $course_unit)
                                        <?php $count_index++; ?>
                                        <div class='col-12'>
                                            <div class='panel clearfix'>
                                                <div class='col-12'>
                                                    <div class='item-content'>
                                                        <div class='item-header clearfix'>
                                                            <div class='item-title h4'>
                                                                @if ($course_info->view_type == 'weekly')
                                                                    <a href="{{ $urlAppend }}modules/weeks/index.php?course={{ $course_code }}&amp;id={{ $course_unit->id }}&amp;cnt={{ $count_index }}">
                                                                        @if(!empty($course_unit->title))
                                                                            {{ $course_unit->title }}
                                                                        @else
                                                                            {{ $count_index.trans('langor') }} {{ trans('langsWeek') }}
                                                                        @endif
                                                                        ({{ trans('langFrom2') }} {{ format_locale_date(strtotime($course_unit->start_week), 'short', false) }} {{ trans('langTill') }} {{ format_locale_date(strtotime($course_unit->finish_week), 'short', false) }})
                                                                    </a>
                                                                @else
                                                                    <a href="modules/unit/{{ $course_unit->id }}.html">
                                                                        {{ $course_unit->title }}
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class='item-body'>
                                                            {!! $course_unit->comments == ' ' ? '' : standard_text_escape($course_unit->comments) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                    {{ trans('langAccessed') }} {{ format_locale_date(strtotime('now')) }}&nbsp;
                                    {{ trans('langFrom2') }} {{ $urlServer }}courses/{{$course_code}}/
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! $course_descriptions_modals !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection