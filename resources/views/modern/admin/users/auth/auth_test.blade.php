@extends('layouts.default')

@section('content')

<div class="pb-lg-3 pt-lg-3 pb-0 pt-0">

    <div class="container-fluid main-container">

        <div class="row rowMedium">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 justify-content-center col_maincontent_active_Homepage">
                    
                <div class="row p-lg-5 p-md-5 ps-1 pe-1 pt-5 pb-5">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])

                    @if(Session::has('message'))
                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5'>
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                            @if(is_array(Session::get('message')))
                                @php $messageArray = array(); $messageArray = Session::get('message'); @endphp
                                @foreach($messageArray as $message)
                                    {!! $message !!}
                                @endforeach
                            @else
                                {!! Session::get('message') !!}
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </p>
                    </div>
                    @endif

                    {!! isset($action_bar) ?  $action_bar : '' !!}
                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                        <div class='form-wrapper shadow-sm p-3 mt-5 rounded'>
                        
                    <form class='form-horizontal' name='authmenu' method='post' action='{{ $_SERVER['SCRIPT_NAME'] }}'>
                        <input type='hidden' name='auth' value='{{ $auth }}'>
                        <fieldset>  
                            <div class='alert alert-info'>{{ trans('langTestAccount') }} ({{ $auth_ids[$auth] }})</div>
                            
                            <div class='form-group mt-3'>
                                <label for='test_username' class='col-sm-6 control-label-notes'>{{ trans('langUsername') }}:</label>
                                <div class='col-sm-12'>
                                    <input class='form-control' type='text' name='test_username' id='test_username' value='{{ canonicalize_whitespace($test_username) }}' autocomplete='off'>
                                </div>
                            </div>

                          

                            <div class='form-group mt-3'>
                                <label for='test_password' class='col-sm-6 control-label-notes'>{{ trans('langPass') }}:</label>
                                <div class='col-sm-12'>
                                    <input class='form-control' type='password' name='test_password' id='test_password' value='{{ $test_password }}' autocomplete='off'>
                                </div>
                            </div>

                            

                            <div class='form-group mt-3'>
                                <div class='col-sm-10 col-sm-offset-2'>
                                    <input class='btn btn-primary' type='submit' name='submit' value='{{ trans('langConnTest') }}'>
                                    <a class='btn btn-secondary' href='auth.php'>{{ trans('langCancel') }}</a>
                                </div>
                            </div>
                        </fieldset>
                        {!! generate_csrf_token_form_field() !!}
                        </form>
                    </div></div>
                </div>
            </div>
        </div>  
    </div>
</div>     
@endsection