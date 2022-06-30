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
                        <small class='text-secondary'>{!! $breadcrumbs[count($breadcrumbs)-1]['bread_text'] !!}</small>
                    </div>
                    <div class='row p-2'></div>
                    @endif

                    {!! isset($action_bar) ?  $action_bar : '' !!}

                    <div class='col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12'>
                        <div class='form-wrapper shadow-lg p-3 mb-5 bg-body rounded bg-primary'>
                        <form role='form' class='form-horizontal' method='post' action='autoenroll.php'>
                            <input type='hidden' name='add' value='{{ $type }}'>
                            @if (isset($_GET['edit']))
                                <input type='hidden' name='id' value='{{ $_GET['edit'] }}'>
                            @endif           
                            <fieldset>
                                <div class='form-group'>
                                    <label class='col-sm-6 control-label-notes'>{{ trans('langStatus') }}:</label>   
                                    <div class='col-sm-12'>
                                        <p class='form-control-static'>{{ $type == USER_STUDENT ? trans('langStudents') : trans('langTeachers') }}</p>
                                    </div>
                                </div>
                                <div class='row p-2'></div>
                                <div class='form-group'>
                                    <label for='title' class='col-sm-6 control-label-notes'>{{ trans('langFaculty') }}:</label>   
                                    <div class='col-sm-12 form-control-static'>
                                        {!! $htmlTree !!}
                                    </div>
                                </div>
                                <div class='row p-2'></div>
                                <div class='form-group'>
                                    <label for='title' class='col-sm-6 control-label-notes'>{{ trans('langAutoEnrollCourse') }}:</label>   
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='hidden' id='courses' name='courses' value=''>
                                    </div>
                                </div>
                                <div class='row p-2'></div>
                                <div class='form-group'>
                                    <label for='title' class='col-sm-6 control-label-notes'>{{ trans('langAutoEnrollDepartment') }}:</label>   
                                    <div class='col-sm-12 form-control-static'>                  
                                        <div id='nodCnt2'>
                                        @foreach ($deps as $key => $dep)
                                            <p id='nc_{{ $key }}'>
                                                <input type='hidden' name='rule_deps[]' value='{{ $dep }}'>
                                                {{ $tree->getFullPath(getDirectReference($dep)) }}
                                                &nbsp;
                                                <a href='#nodCnt2'>
                                                    <span class='fa fa-times' data-bs-toggle='tooltip' data-original-title='{{ trans('langNodeDel') }}' data-bs-placement='top' title='{{ trans('langNodeDel') }}'></span>
                                                </a>
                                            </p>
                                        @endforeach
                                        </div>
                                        <div>
                                            <p>
                                                <a id='ndAdd2' href='#add'>
                                                    <span class='fa fa-plus' data-bs-toggle='tooltip' data-bs-placement='top' title='{{ trans('langNodeAdd') }}'></span>
                                                </a>
                                            </p>
                                        </div>
                                        <div class='modal fade' id='treeCourseModal' tabindex='-1' role='dialog' aria-labelledby='treeModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <button type='button' class='close treeCourseModalClose'>
                                                            <span aria-hidden='true'>&times;</span>
                                                            <span class='sr-only'>{{ trans('langCancel') }}</span>
                                                        </button>
                                                        <h4 class='modal-title' id='treeCourseModalLabel'>{{ trans('langNodeAdd') }}</h4>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <div id='js-tree-course'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary treeCourseModalClose'>{{ trans('langCancel') }}</button>
                                                        <button type='button' class='btn btn-primary' id='treeCourseModalSelect'>{{ trans('langSelect') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                    
                                    </div>
                                </div>
                                <div class='row p-2'></div>
                                <div class='form-group'>
                                    <div class='col-sm-12 checkbox'>
                                        <label>
                                            <input type='checkbox' name='apply' id='apply' value='1' checked='1'>
                                            {{ trans('langApplyRule') }}
                                        </label>
                                    </div>
                                </div>
                                <div class='row p-2'></div>
                                {!! showSecondFactorChallenge() !!}
                                <div class='form-group'>
                                    <div class='col-sm-10 col-sm-offset-2'>
                                        <input class='btn btn-primary' type='submit' name='submit' value='{{ trans('langSubmit') }}'>
                                        <a href='autoenroll.php' class='btn btn-secondary'>{{ trans('langCancel') }}</a>    
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