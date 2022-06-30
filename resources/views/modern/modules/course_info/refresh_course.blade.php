@extends('layouts.default')

@push('head_styles')
<link href="{{ $urlAppend }}js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" type='text/css' rel='stylesheet'>
@endpush

@push('head_scripts')
<script type='text/javascript' src='{{ $urlAppend }}js/tools.js'></script>
<script type='text/javascript' src='{{ $urlAppend }}js/bootstrap-datepicker/js/bootstrap-datepicker.min.js'></script>
<script type='text/javascript' src='{{ $urlAppend }}js/bootstrap-datepicker/locales/bootstrap-datepicker.{{ $language }}.min.js'></script>

<script type='text/javascript'>    
$(function() {
    $('#reg_date').datepicker({
            format: 'dd-mm-yyyy',
            language: '{{ $language }}',
            autoclose: true
        });
});
</script>

@endpush

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
                            <?php $size_breadcrumb = count($breadcrumbs); $count=0; ?>
                            <?php for($i=0; $i<$size_breadcrumb; $i++){ ?>
                                <li class="breadcrumb-item"><a href="{!! $breadcrumbs[$i]['bread_href'] !!}">{!! $breadcrumbs[$i]['bread_text'] !!}</a></li>
                            <?php } ?> 
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


                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="row p-2"></div><div class="row p-2"></div>
                        <legend class="float-none w-auto py-2 px-4 notes-legend"><span class="pos_TitleCourse"><i class="fas fa-list" aria-hidden="true"></i> {{$toolName}} {{trans('langsOfCourse')}} <<strong>{{$currentCourseName}} <small>({{$course_code}})</small></strong>></span>
                            <div class="manage-course-tools"style="float:right">
                                @if($is_editor)
                                    
                                        @include('layouts.partials.manageCourse',[$urlAppend => $urlAppend,'coursePrivateCode' => $course_code])
                                    
                                @endif
                            </div>
                        </legend>
                    </div>

                    <div class="row p-2"></div><div class="row p-2"></div>
                    <span class="control-label-notes ms-1">{{trans('langTeacher')}}: <small>{{course_id_to_prof($course_id)}}</small></span>
                    <div class="row p-2"></div><div class="row p-2"></div>

                    {!! $action_bar !!}

                    <div class='form-wrapper'>
                        @if (!isset($_GET['from_user']))
                            <div class='alert alert-info'>
                                {{ trans('langRefreshInfo') }} {{ trans('langRefreshInfo_A') }}
                            </div>
                            <form class='form-horizontal' role='form' action='{{ $form_url }}' method='post'>
                        @else
                            <form class='form-horizontal' role='form' action='{{ $form_url_from_user }}' method='post'>
                        @endif
                    <fieldset>

                    <div class='row p-2'></div>

                        <div class='form-group'>
                            <label for='delusers' class='col-sm-6 control-label-notes'>{{ trans('langUsers') }}</label>
                            <div class='col-sm-4 checkbox'>
                                <label><input type='checkbox' name='delusers'> {{ trans('langUserDelCourse') }}:</label>
                            </div>
                            <div class='col-sm-3'>
                                {!! $selection_date !!}
                            </div>
                            <div class='col-sm-3'>
                                <input type='text' name='reg_date' id='reg_date' value='{{ $date_format }}'>
                            </div>                
                        </div>
                    @if (!isset($_GET['from_user']))

                    <div class='row p-2'></div>

                        <div class='form-group'>
                            <label for='delannounces' class='col-sm-6 control-label-notes'>{{ trans('langAnnouncements') }}</label>
                            <div class='col-sm-12 checkbox'>
                                <label><input type='checkbox' name='delannounces'> {{ trans('langAnnouncesDel') }}</label>
                            </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='delagenda' class='col-sm-6 control-label-notes'>{{ trans('langAgenda') }}</label>
                        <div class='col-sm-12 checkbox'>
                            <label><input type='checkbox' name='delagenda'> {{ trans('langAgendaDel') }}</label>
                        </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='hideworks' class='col-sm-6 control-label-notes'>{{ trans('langWorks') }}</label>
                            <div class='col-sm-12 checkbox'>
                                <label><input type='checkbox' name='hideworks'> {{ trans('langHideWork') }}</label>
                            </div>
                            <div class='col-sm-offset-2 col-sm-10 checkbox'>
                                <label><input type='checkbox' name='delworkssubs'> {{ trans('langDelAllWorkSubs') }}</label>
                            </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='purgeexercises' class='col-sm-6 control-label-notes'>{{ trans('langExercises') }}</label>
                        <div class='col-sm-12 checkbox'>
                            <label><input type='checkbox' name='purgeexercises'> {{ trans('langPurgeExercisesResults') }}</label>
                        </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='clearstats' class='col-sm-6 control-label-notes'>{{ trans('langUsage') }}</label>
                        <div class='col-sm-12 checkbox'>
                            <label><input type='checkbox' name='clearstats'> {{ trans('langClearStats') }}</label>
                        </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='delblogposts' class='col-sm-6 control-label-notes'>{{ trans('langBlog') }}</label>
                        <div class='col-sm-12 checkbox'>
                            <label><input type='checkbox' name='delblogposts'> {{ trans('langDelBlogPosts') }}</label>
                        </div>
                        </div>

                        <div class='row p-2'></div>

                        <div class='form-group'>
                        <label for='delwallposts' class='col-sm-6 control-label-notes'>{{ trans('langWall') }}</label>
                        <div class='col-sm-12 checkbox'>
                            <label><input type='checkbox' name='delwallposts'> {{ trans('langDelWallPosts') }}</label>
                        </div>
                        </div>
                    @endif

                    <div class='row p-2'></div>

                        {{ showSecondFactorChallenge() }}

                        <div class='row p-2'></div>

                    <div class='col-sm-offset-2 col-sm-10'>
                        <input class='btn btn-primary' type='submit' value='{{ trans('langSubmitActions') }}' name='submit'>
                    </div>
                    </fieldset>
                    {!! generate_csrf_token_form_field() !!}
                    </form>
                    </div>    
                </div>
            </div>

        </div>
    </div>
</div>

@endsection