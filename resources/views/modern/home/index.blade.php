@extends('layouts.default')

@section('content')

<div class="pb-lg-3 pt-lg-3 pb-0 pt-0">

    <div class="container-fluid main-container">

        <div class="row rowMedium">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 justify-content-center col_maincontent_active_Homepage">

                <div class="row">

                    @if(!(get_config('upgrade_begin') || get_config('dont_display_login_form')))
                    <div class='col-12 card jumbotron jumbotron-login BordersTop'>
                        <div class='row'>
                            @if($warning)<div class='col-12 mt-4 mb-0'>{!! $warning !!}</div>@endif
                            <div class='col-xl-6 col-lg-7 col-md-8 col-12'>
                                <div class='card-body mt-md-5 mb-md-5 me-md-5 ms-md-5 mt-5 mb-5 ms-3 me-3 Borders cardLogin'>
                                    <div class='card-header bg-transparent border-0'>
                                        <div class='control-label-notes fs-5 text-center'><img src="template/modern/img/user2.png" class='user-icon'> {{ trans('langUserLogin') }}</div>
                                    </div>
                                    <form action="{{ $urlAppend }}" method="post">
                                        <div class="login-form-spacing mt-2">
                                            <input class="login-input bg-body border border-secondary w-100" placeholder="{{ trans('langUsername') }} &#xf007;" type="text" id="uname" name="uname" >
                                            <input class="login-input bg-body border border-secondary w-100 mt-4" placeholder="{{ trans('langPassword') }} &#xf023;" type="password" id="pass" name="pass">
                                            <input class="btn text-white w-100 login-form-submit mt-md-4 mb-md-0 mt-4 mb-4" type="submit" name="submit" value="{{ trans('langLogin') }}">
                                        </div>
                                    </form>
                                    <div class='col-sm-12 d-flex justify-content-center'>
                                        <a class="text-primary fw-bold btnlostpass mb-2" href="{{$urlAppend}}modules/auth/lostpass.php">{{ trans('lang_forgot_pass') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class='col-xl-6 col-lg-5 col-md-4 col-0'></div>
                        </div>
                    </div>
                    @else
                    <div class='d-none d-md-none d-lg-block'>
                        <div class='row'>
                            <div class='card h-400px jumbotron jumbotron-login BordersTop'></div>
                        </div>
                    </div>
                    <div class='d-block d-md-block d-lg-none'>
                        <div class='row'>
                            <div class='card h-400px jumbotron jumbotron-login NoBorders'></div>
                        </div>
                    </div>
                    @endif


                    @if(get_config('homepage_title') or get_config('homepage_intro'))
                    <div class='d-none d-sm-none d-md-block'>
                        <div class='row rowMedium'>
                            <div class='col-12 ps-md-5 pe-md-5 pt-md-5 pb-md-5'>
                                <div class="panel panel-default homepageIntroPanel w-100">
                                    @if(get_config('homepage_title'))
                                    <div class="panel-heading text-center p-3">
                                        <span class='fs-5 control-label-notes'>
                                            {!! get_config('homepage_title') !!}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="panel-body bg-body @if(get_config('homepage_title')) NoBorderTop @else Borders @endif">
                                        {!! get_config('homepage_intro') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(get_config('homepage_title') or get_config('homepage_intro'))
                    <div class='d-block d-md-none'>
                        <div class='row rowMedium'>
                            <div class='col-12 pt-5'>
                                <div class="panel panel-default w-100">
                                    <div class='panel-body Borders'>
                                        {!! get_config('homepage_intro') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                    @if (get_config('enable_mobileapi'))
                        @if(!get_config('homepage_title') and !get_config('homepage_intro'))
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-5 pt-5 mb-5'>
                        @else
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-0 pt-5 mb-5'>
                        @endif
                            <div class='row rowMedium'>
                                <div class='col-md-6 col-12'>
                                    <div class='panel panel-default'>
                                        <div class='panel-body Borders'>
                                            <a href="http://www.openeclass.org/" target="_blank">
                                                <img class="img-responsive center-block m-auto d-block" src="/template/modern/img/open_eclass_banner.png" alt="Open eClass Banner">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-md-6 col-12 mt-md-0 pt-md-0 pt-5'>
                                    <div class='panel panel-default'>
                                        <div class='panel-body Borders'>
                                            <div class='col-12'>
                                                <div class='row'>
                                                    <div class='col-6'>
                                                        <a href='https://itunes.apple.com/us/app/open-eclass-mobile/id1398319489' target=_blank>
                                                            <img src='template/modern/images/appstore.png' class='img-responsive center-block m-auto d-block' alt='Available on the App Store'>
                                                        </a>
                                                    </div>
                                                    <div class='col-6'>
                                                        <a href='https://play.google.com/store/apps/details?id=gr.gunet.eclass' target=_blank>
                                                            <img src='template/modern/images/playstore.png' class='img-responsive center-block m-auto d-block' alt='Available on the Play Store'>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @else
                        @if(!get_config('homepage_title') and !get_config('homepage_intro'))
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-5 pt-5 mb-5'>
                        @else
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-0 pt-5 mb-5'>
                        @endif
                            <div class='row rowMedium'>
                                <div class='col-12'>
                                    <div class='panel panel-default'>
                                        <div class='panel-body Borders'>
                                            <a href="http://www.openeclass.org/" target="_blank">
                                                <img class="img-responsive center-block m-auto d-block" src="/template/modern/img/open_eclass_banner.png" alt="Open eClass Banner">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    @if (!isset($openCoursesExtraHTML))
                        @php $openCoursesExtraHTML = ''; @endphp
                        {!! setOpenCoursesExtraHTML() !!}
                    @endif
                    @if (get_config('opencourses_enable'))
                        @if ($openCoursesExtraHTML)
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-0 mb-5'>
                            <div class='row rowMedium'>
                                <div class='col-12'>
                                    <div class='panel panel-default'>
                                        <div class='panel-body Borders'>
                                            {!! $openCoursesExtraHTML !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class='col-12 ps-md-5 pe-md-5 pt-md-0 mb-5'>
                            <div class='row rowMedium'>
                                <div class='col-12'>
                                    <div class='panel panel-default bg-dark text-center p-2'>
                                        <a class='text-white' href='http://opencourses.gr' target='_blank'>
                                            {{ trans('langNationalOpenCourses') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif




                    {{--<div class="col-lg-12 d-flex justify-content-end ms-1 mb-3 mt-4">
                        <button class="btnMoreHomePage" data-bs-toggle="collapse" data-bs-target="#collapse_main_section" aria-expanded="false" aria-controls="collapse_main_section">
                            <span class='fa fa-arrow-down'></span>
                        </button>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>

</div>


<!-- collapse menu -->

{{--<div class="collapse" id="collapse_main_section">--}}
    @if ($announcements)
    <div class="container-fluid main-section">
        <div class="row rowMedium">
            <div class="col-lg-12 border-15px Announcements-Homepage">
                <div class="news">
                    <h2 class="block-title">{{ trans('langAnnouncements') }}
                        <a href='{{ $urlServer }}rss.php' style='padding-left:5px;'>
                            <span class='fa fa-rss-square'></span>
                        </a>
                    </h2>
                    <div class="row news-list m-auto">
                        @php $counterAn = 0; @endphp
                        @foreach ($announcements as $announcement)
                            @if($counterAn < 6)
                            <div class="col-sm-12 news-list-item">
                                <div class="date">
                                    {{ format_locale_date(strtotime($announcement->date)) }}
                                </div>
                                <div class="title"><a class="announcement-title-a" href='modules/announcements/main_ann.php?aid={{ $announcement->id }}'>{{$announcement->title}}</a></div>
                            </div>
                            @endif
                        @php $counterAn++; @endphp
                        @endforeach
                    </div>
                    <div class="more-link"><a class="all_announcements mt-3 float-end" href="{{ $urlServer }}main/system_announcements.php">ΟΛΕΣ ΟΙ ΑΝΑΚΟΙΝΩΣΕΙΣ <span class='fa fa-arrow-right'></span></a></div>
                </div>
            </div>
        </div>
    </div>
    @endif


    <div class="container-fluid statistics mt-lg-3 mt-0">
        <div class='row rowMedium'>
            <div class="statistics-wrapper">
                <h2 class="text-center pt-lg-0 pt-4">
                    {{trans('langViewStatics')}}
                </h2>
                <div class="row row_pad_courses">
                    <div class="col-sm-4 text-center">
                            <i class="fas fa-book"></i>
                            @php $course_inactive = Database::get()->querySingle("SELECT COUNT(*) as count FROM course WHERE visible != ?d", COURSE_INACTIVE)->count; @endphp
                            <div class="num">{{ $course_inactive }}</div>
                            <div class="num-text">{{trans('langsCourses')}}</div>
                    </div>
                    <div class="col-sm-4 text-center">
                            <i class="fas fa-mouse-pointer"></i>
                            <div class="num">10<span>K+</span></div>
                            <div class="num-text">{{trans('langUserLogins')}}/{{trans('langWeek')}}</div>
                    </div>
                    <div class="col-sm-4 text-center">
                            <i class="fas fa-user"></i>
                            <div class="num">{{ getOnlineUsers() }}
                                <!-- <span>K+</span> -->
                            </div>
                            <div class="num-text">{{trans('langOnlineUsers')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-lg-3 mb-lg-3">
        <div class="container-fluid testimonials mt-lg-0 mb-lg-0 mt-2 mb-2 bg-light">
            <div class="testimonial">
                <div class="testimonial-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                <div class="testimonial-person">- Λυδία Καλομοίρη -</div>
            </div>
            <div class="testimonial">
                <div class="testimonial-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                <div class="testimonial-person">- Γιάννης Ιωάννου -</div>
            </div>
            <div class="testimonial">
                <div class="testimonial-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                <div class="testimonial-person">- Νίκος Παπαδάκης -</div>
            </div>
        </div>
    </div>

{{--</div>--}}

<script>
    $('.testimonials').slick({
		autoplay:true,
		autoplaySpeed:1500,
		centerMode: true,
		centerPadding: '25vw',
		slidesToShow: 1,
		responsive: [{
			breakpoint: 4000,
			settings: { centerPadding: '15vw', }
		}]
	});
    // var moreCounter = 0;
    // $('.btnMoreHomePage').on('click',function(){
    //     moreCounter++;
    //     if(moreCounter % 2 == 1){
    //         $('.btnMoreHomePage .fa').css('transform','rotate(180deg)');
    //     }else{
    //         $('.btnMoreHomePage .fa').css('transform','rotate(0deg)');
    //     }

    // });
</script>

@endsection