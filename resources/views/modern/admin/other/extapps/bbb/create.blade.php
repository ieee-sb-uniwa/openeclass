@extends('layouts.default')

@section('content')

<div class="pb-3 pt-3">

    <div class="container-fluid main-container">

        <div class="row">

            <div id="background-cheat-leftnav" class="col-xl-2 col-lg-2 col-md-0 col-sm-0 col-0 justify-content-center col_sidebar_active"> 
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block">
                    @include('layouts.partials.sidebarAdmin')
                </div>
            </div>

            <div class="col-xl-10 col-lg-10 col-md-12 col-sm-12 col-12 justify-content-center col_maincontent_active">
                    
                <div class="row p-lg-5 p-md-5 ps-1 pe-1 pt-5 pb-5">

                    <nav class="navbar navbar-expand-lg navrbar_menu_btn">
                        <button type="button" id="menu-btn" class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block btn btn-primary menu_btn_button">
                            <i class="fas fa-align-left"></i>
                            <span></span>
                        </button>
                        
                    
                        <a class="btn btn-primary d-lg-none mr-auto" type="button" data-bs-toggle="offcanvas" href="#collapseTools" role="button" aria-controls="collapseTools" style="margin-top:-10px;">
                            <i class="fas fa-tools"></i>
                        </a>
                    </nav>

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                    <div class="offcanvas offcanvas-start d-lg-none mr-auto" tabindex="-1" id="collapseTools" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                        @include('layouts.partials.sidebarAdmin')
                        </div>
                    </div>

                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])

                    {!! isset($action_bar) ?  $action_bar : '' !!}

                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                        <div class='form-wrapper shadow-lg p-3 mb-5 bg-body rounded bg-primary'>
                            <form class='form-horizontal' role='form' name='serverForm' action='{{ $_SERVER['SCRIPT_NAME'] }}' method='post'>
                            <fieldset>
                                <div class='form-group mt-3'>
                                    <label for='api_url_form' class='col-sm-6 control-label-notes'>API URL:</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' id='api_url_form' name='api_url_form' value='{{ isset($server) ? $server->api_url : "" }}'>
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label for='key_form' class='col-sm-6 control-label-notes'>{{ trans('langPresharedKey') }}:</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' name='key_form' value='{{ isset($server) ? $server->server_key : "" }}'>
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label for='max_rooms_form' class='col-sm-6 control-label-notes'>{{ trans('langMaxRooms') }}:</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' id='max_rooms_for' name='max_rooms_form' value='{{ isset($server) ? $server->max_rooms : "" }}'>
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label for='max_rooms_form' class='col-sm-6 control-label-notes'>{{ trans('langMaxUsers') }}:</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' id='max_users_form' name='max_users_form' value='{{ isset($server) ? $server->max_users : "" }}'>
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label class='col-sm-6 control-label-notes'>{{ trans('langBBBEnableRecordings') }}:</label>
                                    <div class="col-sm-12">
                                        <div class='radio'>
                                            <label>
                                                <input  type='radio' id='recordings_on' name='enable_recordings' value='true'{{ $enabled_recordings ? ' checked' : '' }}>
                                                {{ trans('langYes') }}
                                            </label>
                                        </div>                
                                        <div class='radio'>
                                            <label>
                                                <input  type='radio' id='recordings_off' name='enable_recordings' value='false'{{ $enabled_recordings ? '' : ' checked' }}>
                                                {{ trans('langNo') }}
                                            </label>
                                        </div>                    
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label class='col-sm-6 control-label-notes'>{{ trans('langActivate') }}:</label>
                                    <div class="col-sm-12">
                                        <div class='radio'>
                                            <label>
                                                <input  type='radio' id='enabled_true' name='enabled' value='true'{{ $enabled ? ' checked' : '' }}>
                                                {{ trans('langYes') }}
                                            </label>
                                        </div>                
                                        <div class='radio'>
                                            <label>
                                                <input  type='radio' id='enabled_false' name='enabled' value='false'{{ $enabled ? '' : ' checked' }}>
                                                {{ trans('langNo') }}
                                            </label>
                                        </div>                      
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label class='col-sm-6 control-label-notes'>{{ trans('langBBBServerOrder') }}:</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' name='weight' value='{{ isset($server) ? $server->weight : "" }}'>
                                    </div>
                                </div>
                                <div class='form-group mt-3'>
                                    <label class='col-sm-6 control-label-notes'>{{ trans('langUseOfTc') }}:</label>
                                    <div class="col-sm-12">
                                        <select class='form-control' name='tc_courses[]' multiple class='form-control' id='select-courses'>                        
                                            {!! $listcourses !!}
                                        </select>            
                                        <a href='#' id='selectAll'>{{ trans('langJQCheckAll') }}</a> | <a href='#' id='removeAll'>{{ trans('langJQUncheckAll') }}</a>
                                    </div>
                                </div>
                                @if (isset($server))
                                    <input class='form-control' type = 'hidden' name = 'id_form' value='{{ getIndirectReference($bbb_server) }}'>
                                @endif
                                <div class='form-group mt-3'>
                                    <div class='col-sm-offset-3 col-sm-9'>
                                        <input class='btn btn-primary' type='submit' name='submit' value='{{ trans('langAddModify') }}'>
                                    </div>
                                </div>
                            </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script language="javaScript" type="text/javascript">
    var chkValidator  = new Validator("serverForm");
    chkValidator.addValidation("key_form","req","{{ trans('langBBBServerAlertKey') }}");
    chkValidator.addValidation("api_url_form","req","{{ trans('langBBBServerAlertAPIUrl') }}");
    chkValidator.addValidation("max_rooms_form","req","{{ trans('langBBBServerAlertMaxRooms') }}");
    chkValidator.addValidation("max_rooms_form","numeric","{{ trans('langBBBServerAlertMaxRooms') }}");
    chkValidator.addValidation("max_users_form","req","{{ trans('langBBBServerAlertMaxUsers') }}");
    chkValidator.addValidation("max_users_form","numeric","{{ trans('langBBBServerAlertMaxUsers') }}");
    chkValidator.addValidation("weight","req","{{ trans('langBBBServerAlertOrder') }}");
    chkValidator.addValidation("weight","numeric","{{ trans('langBBBServerAlertOrder') }}");
</script>  
@endsection