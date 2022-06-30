<?php print_r($deleteUrl)?>

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
                            <li class="breadcrumb-item"><a href="{{ $urlAppend }}main/portfolio.php">Χαρτοφυλάκιο</a></li>
                            <li class="breadcrumb-item"><a href="{{ $urlAppend }}main/my_courses.php">Τα μαθήματά μου</a></li>
                            <li class="breadcrumb-item"><a href="{{$urlServer}}courses/{{$course_code}}/index.php">{{$currentCourseName}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$toolName}}</li>
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
                        <legend class="float-none w-auto py-2 px-4 notes-legend"><span class="pos_TitleCourse"><i class="fas fa-folder-open" aria-hidden="true"></i> {{$toolName}} του μαθήματος <strong>{{$currentCourseName}} <small>({{$course_code}})</small></strong></span>
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

                    
                    <div class="table">
                        <table id='request_table_{{ $course_id }}' class='table announcements_table'>
                            <thead>
                                <tr class='notes_thead'>
                                    <th class='text-white'>{{ trans('langRequest') }}</th>
                                    <th class='text-white'>{{ trans('langNewBBBSessionStatus') }}</th>
                                    <th class='text-white'>{{ trans('langOpenedOn') }}</th>
                                    <th class='text-white'>{{ trans('langUpdatedOn') }}</th>
                                    <th class='text-white text-center'><span class='fa fa-cogs'></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan='5'>
                                        <div class='form-inline'>
                                            <label>{{ trans('langShowClosedRequests') }}:
                                                <input type='checkbox' class='form-control' id='closedRequests'>
                                            </label>
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                        
                </div>
            </div>

        </div>
    </div>
</div>



<script>
    $(function() {
        var oTable = $('#request_table_{{ $course_id }}').DataTable({
            ajax: {
                url: '{{ $listUrl }}',
                data: function (data) {
                    data.show_closed = $('#closedRequests').prop('checked');
                }
            },
            order: [[0, 'desc']],
            lengthMenu: [
                [10, 25, 100 , -1],
                [10, 25, 100, '{{ trans('langAllOfThem') }}']
            ],
            columns: [
                { searchable: true },
                { searchable: true, orderable: true },
                { searchable: false, orderable: true },
                { searchable: false, orderable: true },
                { searchable: false, orderable: false }
            ],
            stateSave: true,
            processing: true,
            serverSide: true,
            scrollX: true,
            drawCallback: function(settings) {
                tooltip_init();
                popover_init();
            },
            paginationType: 'full_numbers',
            language: {
                lengthMenu: '{{ trans('langDisplay') . ' _MENU_ ' . trans('langResults2') }}',
                zeroRecords: '{{ trans('langNoResult') }}',
                info: '{{ trans('langDisplayed') . ' _START_ ' . trans('langTill') .
                            ' _END_ ' . trans('langFrom2') . ' _TOTAL_ ' . trans('langTotalResults') }}',
                infoEmpty: '{{ trans('langDisplayed') . ' 0 ' . trans('langTill') . ' 0 ' .
                                trans('langFrom2') . ' 0 ' . trans('langResults2') }}',
                infoFiltered: '',
                infoPostFix: '',
                search: '{{ trans('langSearch') . ': ' }}',
                searchPlaceholder: '{{ trans('langTitle') . ', ' . trans('langUser') }}',
                paginate: {
                    first:    '&laquo;',
                    previous: '&lsaquo;',
                    next:     '&rsaquo;',
                    last:     '&raquo;'
                }
            }
        });
        $('.dataTables_filter input').attr({
            style: 'width: 200px',
            class:'form-control input-sm'
        });
        $(document).on('change', '#closedRequests', function (e) {
            oTable.ajax.reload();
        });
        $(document).on( 'click','.delete_btn', function (e) {
            e.preventDefault();
            var row_id = $(this).data('id');
            console.log(row_id);
            bootbox.confirm('{{ js_escape(trans('langConfirmDelete')) }}', function(result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ $deleteUrl }}',
                        datatype: 'json',
                        data: {
                            id: row_id
                        },
                        success: function(data){
                            var info = oTable.page.info();
                            var page_number = info.page;
                            oTable.draw(false);
                        },
                        error: function(xhr, textStatus, error){
                            console.log(xhr.statusText);
                            console.log(textStatus);
                            console.log(error);
                        }
                    });
                }
            });
        });
    });
</script>
@endsection