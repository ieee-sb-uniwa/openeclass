@extends('layouts.default')

@section('content')

<div class="col-12 main-section">
<div class='{{ $container }} @if($course_code) module-container py-lg-0 @else main-container @endif'>
        <div class="@if($course_code) course-wrapper d-lg-flex align-items-lg-strech w-100 @else row m-auto @endif">

                @if($course_code)
                <div id="background-cheat-leftnav" class="col_sidebar_active d-flex justify-content-start align-items-strech ps-lg-0 pe-lg-0"> 
                    <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block ContentLeftNav">
                        @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                    </div>
                </div>
                @endif

                @if($course_code)
                <div class="col_maincontent_active">
                @else
                <div class="col-12">
                @endif
                    
                    <div class="row">
                        
                        @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                        @if($course_code)
                        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="collapseTools">
                            <div class="offcanvas-header">
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                            </div>
                        </div>
                        @endif

                        @include('layouts.partials.legend_view')
                    

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
                                
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        @endif


                        {!! $backButton !!}
                        
                        @if ($can_upload == 1)

                        <div class='@if(isset($module_id) and $module_id) d-lg-flex gap-4 @else row m-auto @endif mt-4'>
                            <div class='@if(isset($module_id) and $module_id) flex-grow-1 @else col-lg-6 col-12 px-0 @endif'>
                           
                            
                                <div class='form-wrapper form-edit mt-2 border-0 px-0'>
                                
                                    <form class='form-horizontal' role='form' action='{{ $upload_target_url }}' method='post'>
                                        <input type='hidden' name='{{ $pathName }}' value='{{ $pathValue }}'>
                                        {!! $group_hidden_input !!}
                                        @if ($back)
                                            <input type='hidden' name='back' value='{{ $back }}'>
                                        @endif
                                        @if ($sections)
                                            <div class='form-group mb-4'>
                                                <label for='section' class='col-sm-12 control-label-notes'>{{ trans('langSection') }}</label>
                                                <div class='col-sm-12'>
                                                    {!! selection($sections, 'section_id', $section_id) !!}
                                                </div>
                                            </div>
                                        @endif

                                        

                                        @if ($filename)
                                            <div class='form-group mb-4'>
                                                <label for='file_name' class='col-sm-12 control-label-notes'>{{ trans('langFileName') }}</label>
                                                <div class='col-sm-12'>
                                                    <p class='form-control-static'>{{ $filename }}</p>
                                                </div>
                                            </div>
                                        @endif

                                     

                                        <div class="form-group{{ Session::getError('file_title') ? ' has-error' : '' }}">
                                            <label for='file_title' class='col-sm-12 control-label-notes'>{{ trans('langTitle') }}</label>
                                            <div class='col-sm-12'>
                                                <input type='text' class='form-control' placeholder="{{ trans('langTitle') }}..." id='file_title' name='file_title' value='{{ $title }}'>
                                                <span class='help-block Accent-200-cl'>{{ Session::getError('file_title') }}</span>
                                            </div>
                                        </div>

                                        

                                        <div class='form-group mt-4'>
                                            <label for='file_title' class='col-sm-12 control-label-notes'>{{ trans('langContent') }}</label>
                                            <div class='col-sm-12'>
                                                {!! $rich_text_editor !!}
                                            </div>
                                        </div>

                                       

                                        <div class='form-group mt-5'>

                                            <div class='col-12 d-flex justify-content-end align-items-center gap-2'>
                                                <button class='btn submitAdminBtn' type='submit'>{{ trans('langSave') }}</button>
                                                <a class='btn cancelAdminBtn' href='{{ $backUrl }}'>{{ trans('langCancel') }}</a>
                                                {!! generate_csrf_token_form_field() !!}
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class='@if(isset($module_id) and $module_id) form-content-modules @else col-lg-6 col-12 @endif d-none d-lg-block'>
                                <img class='form-image-modules' src='{{ $urlAppend }}template/modern/img/form-image.png' alt='form-image'>
                            </div>
                        </div>




                        @else
                        <div class='col-12'>
                            <div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation fa-lg'></i><span>{{ trans('langNotAllowed') }}</span></div>
                        </div>
                        @endif
                    </div>
            </div>
        </div>
    
</div>
</div>
@endsection
