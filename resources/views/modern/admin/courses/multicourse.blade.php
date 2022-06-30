@extends('layouts.default')

@section('content')

<div class="pb-3 pt-3">

    <div class="container-fluid main-container">

        <div class="row">

            <div class="col-xl-2 col-lg-2 col-md-0 col-sm-0 col-0 justify-content-center col_sidebar_active"> 
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block">
                    @include('layouts.partials.sidebarAdmin')
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
                        @include('layouts.partials.sidebarAdmin')
                        </div>
                    </div>

                    @if($breadcrumbs && count($breadcrumbs)>2)
                    <div class='row p-2'></div>
                    <div class="float-start">
                        <p class='control-label-notes'>{!! $breadcrumbs[1]['bread_text'] !!}</p>
                        <small class='text-secondary'>{!! $breadcrumbs[2]['bread_text'] !!}</small>
                    </div>
                    <div class='row p-2'></div>
                    @endif


                    {!! isset($action_bar) ?  $action_bar : '' !!}

                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                        <div class='alert alert-info'>{{ trans('langMultiCourseInfo') }}</div>
                    </div>

                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                        <div class='form-wrapper shadow-lg p-3 mb-5 bg-body rounded bg-primary'>
                            <form role='form' class='form-horizontal' method='post' action='{{ $_SERVER['SCRIPT_NAME'] }}' onsubmit="return validateNodePickerForm();">
                                <fieldset>
                                    <div class='form-group mt-3'>
                                        <label for='title' class='col-sm-6 control-label-notes'>{{ trans('langMultiCourseTitles') }}:</label>
                                        <div class='col-sm-12'>{!! text_area('courses', 20, 80, '') !!}</div>
                                    </div>
                                    <div class='form-group mt-3'>
                                        <label for='title' class='col-sm-6 control-label-notes'>{{ trans('langFaculty') }}:</label>	  
                                        <div class='col-sm-12'>
                                            {!! $html !!}
                                        </div>
                                    </div>                
                                    <div class='form-group mt-3'>
                                        <label class='col-sm-offset-4 col-sm-8 control-label-notes'>{{ trans('langConfidentiality') }}</label></div>
                                        <div class='form-group'>
                                            <label for='password' class='col-sm-3 text-secondary'>{{ trans('langOptPassword') }}</label>
                                            <div class='col-sm-12'>
                                                <input id='coursepassword' class='form-control' type='text' name='password' id='password' autocomplete='off'>
                                            </div>
                                        </div>
                                    <div class='form-group mt-3'>
                                        <label for='Public' class='col-sm-6 control-label-notes'>{{ trans('langOpenCourse') }}</label>
                                        <div class='col-sm-12 radio'>
                                            <label>
                                                <input id='courseopen' type='radio' name='formvisible' value='2' checked> {{ trans('langPublic') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class='form-group mt-3'>
                                        <label for='PrivateOpen' class='col-sm-6 control-label-notes'>{{ trans('langRegCourse') }}</label>	
                                        <div class='col-sm-12 radio'>
                                            <label>
                                                <input id='coursewithregistration' type='radio' name='formvisible' value='1'> 
                                                    {{ trans('langPrivOpen') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class='form-group mt-3'>
                                        <label for='PrivateClosed' class='col-sm-6 control-label-notes'>{{ trans('langClosedCourse') }}</label>
                                        <div class='col-sm-12 radio'>
                                            <label>
                                                <input id='courseclose' type='radio' name='formvisible' value='0'> 
                                                    {{ trans('langClosedCourseShort') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class='form-group mt-3'>
                                        <label for='Inactive' class='col-sm-6 control-label-notes'>{{ trans('langInactiveCourse') }}</label>
                                        <div class='col-sm-12 radio'>
                                            <label>
                                                <input id='courseinactive' type='radio' name='formvisible' value='3'> {{ trans('langCourseInactiveShort') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class='form-group mt-3'>
                                        <label for='language' class='col-sm-6 control-label-notes'>{{ trans('langLanguage') }}:</label>	  
                                        <div class='col-sm-12'>{!! lang_select_options('lang') !!}</div>
                                    </div>
                                    {!! showSecondFactorChallenge() !!}
                                    <div class='form-group mt-3'>
                                        <div class='col-sm-10 col-sm-offset-2'>
                                            <input class='btn btn-primary' type='submit' name='submit' value='{{ trans('langSubmit') }}'>
                                            <a href='index.php' class='btn btn-secondary'>{{ trans('langCancel') }}</a>    
                                        </div>
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
</div>
@endsection