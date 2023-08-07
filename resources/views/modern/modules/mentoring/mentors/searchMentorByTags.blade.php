
<div class='panel panel-admin border-0 bg-white p-0 rounded-0 d-block d-lg-none'>
    <div class='panel-body p-0 rounded-0'>
        <a id='closeSidebarSpecializations' class='btn float-end'><span class='fa fa-times fs-3 text-danger'></span></a>
    </div>
</div>

<div class='panel panel-admin border-top-1 border-start-1 border-end-1 border-bottom-0 bg-white py-md-3 px-md-3 py-3 px-3 rounded-0 panelSpecialization'>
    <div class='panel-body p-0 rounded-0'>
        <p class='blackBlueText TextBold fs-6 mb-2'>{{ trans('langAvailability') }}</p>
        <div class='col-12 mb-3 ps-2 pe-2'>
            <div class='d-flex justify-content-start align-items-center mt-1'>
                <input style='width:15px; height:15px;' class='checkAvailable' id='availableId' type='checkbox' value='1' checked><span class='TextRegular small-text'>{{ trans('langAvailabilityMentor') }}</span>
            </div>
            <div class='d-flex justify-content-start align-items-center mt-2'>
                <input style='width:15px; height:15px;' class='checkAvailable' id='unavailableId' type='checkbox' value='0'><span class='TextRegular small-text'>{{ trans('langUnAnavailability') }}</span>
            </div>
        </div>
    </div>
</div>


<div class='panel panel-admin border-top-0 border-start-1 border-end-1 border-bottom-1 bg-white py-md-3 px-md-3 py-3 px-3 rounded-0 panelSpecialization'>
    <div class='panel-body p-0 rounded-0'>
        <select id='allTagsSelect' class='d-none' multiple>
            @foreach($all_specializations as $tag)
                @php 
                    $skills = Database::get()->queryArray("SELECT *FROM mentoring_skills 
                                                                        WHERE id IN (SELECT skill_id FROM mentoring_specializations_skills 
                                                                                    WHERE specialization_id = ?d)",$tag->id);
                @endphp
                @if(count($skills) > 0)
                    <ul class='p-0' style='list-style-type: none;'>
                        @foreach($skills as $sk)
                            <option value='{{ $sk->id }}' selected></option>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </select>
        
        @foreach($all_specializations as $tag)
            
            <p class='blackBlueText TextBold fs-6 mb-2'>
                
                @php 
                    $checkTranslationSpecialization = Database::get()->querySingle("SELECT *FROM mentoring_specializations_translations
                                                                                    WHERE specialization_id = ?d AND lang = ?s",$tag->id, $language);
                @endphp

                @if($checkTranslationSpecialization)
                    {{ $checkTranslationSpecialization->name }}
                @else
                    {{ $tag->name }}
                @endif
            </p>
            @php 
                $skills = Database::get()->queryArray("SELECT *FROM mentoring_skills 
                                                        WHERE id IN (SELECT skill_id FROM mentoring_specializations_skills 
                                                                    WHERE specialization_id = ?d)",$tag->id);
            @endphp
            @if(count($skills) > 0)
                <div class='col-12 mb-5 ps-2 pe-2'>
                    <ul class='p-0' style='list-style-type: none;'>
                       
                        @foreach($skills as $sk)
                            <li class='d-flex justify-content-start align-items-start mb-2'>
                                <input id='TheSkill{{ $sk->id }}{{ $tag->id }}' class='tagClick' type='checkbox' value='{{ $sk->id }},{{ $tag->id }}' style='width:15px; height:15px;'>
                                <span class='TextRegular small-text'>
                                    @php 
                                        $checkTranslationSkill = Database::get()->querySingle("SELECT *FROM mentoring_skills_translations
                                                                                                        WHERE skill_id = ?d AND lang = ?s",$sk->id, $language);
                                    @endphp

                                    @if($checkTranslationSkill)
                                        {{ $checkTranslationSkill->name }}
                                    @else
                                        {{ $sk->name }}
                                    @endif
                                </span>

                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif    
        @endforeach
    </div>
    <div class='panel-footer rounded-0 d-flex justify-content-center align-items-center mt-0 p-0'>
        <a id='SearchMentors' href='#' type='button' class='btn btn-sm small-text shadow-sm search_clear_filter w-50 solidPanel'>
            <span class='fa fa-search fs-5 normalBlueText'></span>
        </a>
        <a class='uncheckBtn btn btn-sm small-text shadow-sm search_clear_filter ms-2 w-50 solidPanel'>
            <span class='fa fa-trash fs-5 text-danger'></span>
        </a>
    </div>
</div>