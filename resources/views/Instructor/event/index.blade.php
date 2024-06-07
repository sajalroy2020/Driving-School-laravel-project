@extends('layouts.app')
@push('title')
    {{$pageTitle}}
@endpush

@section('content')
    <div data-aos="fade-up" data-aos-duration="1000" class="p-sm-30 p-15">
        <div class="row rg-20">
            <div class="col-12">
                <div class="bd-ra-15 bg-white p-sm-30 p-15 mb-30">
                    <div class="bd-one bd-c-stroke bd-ra-20 bg-white overflow-hidden">
                        <div id="ctFullCalendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="event-list-route" value="{{route('instructor.event.index')}}">
@endsection

@push('script')
    <script src="{{asset('assets/custom/instructor/js/event.js')}}"></script>
@endpush