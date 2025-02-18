<!-- Backend Bundle JavaScript -->
<script src="{{ secure_asset('js/libs.min.js')}}"></script>
@if(in_array('data-table',$assets ?? []))
<script src="{{ secure_asset('vendor/datatables/buttons.server-side.js')}}"></script>
@endif
@if(in_array('chart',$assets ?? []))
    <!-- apexchart JavaScript -->
    <script src="{{secure_asset('js/charts/apexcharts.js') }}"></script>
    <!-- widgetchart JavaScript -->
    <script src="{{secure_asset('js/charts/widgetcharts.js') }}"></script>
    <script src="{{secure_asset('js/charts/dashboard.js') }}"></script>
@endif

<!-- mapchart JavaScript -->
<script src="{{secure_asset('vendor/Leaflet/leaflet.js') }} "></script>
<script src="{{secure_asset('js/charts/vectore-chart.js') }}"></script>


<!-- fslightbox JavaScript -->
<script src="{{secure_asset('js/plugins/fslightbox.js')}}"></script>
<script src="{{secure_asset('js/plugins/slider-tabs.js') }}"></script>
<script src="{{secure_asset('js/plugins/form-wizard.js')}}"></script>

<!-- settings JavaScript -->
<script src="{{secure_asset('js/plugins/setting.js')}}"></script>

<script src="{{secure_asset('js/plugins/circle-progress.js') }}"></script>
@if(in_array('animation',$assets ?? []))
<!--aos javascript-->
<script src="{{secure_asset('vendor/aos/dist/aos.js')}}"></script>
@endif

@if(in_array('calender',$assets ?? []))
<!-- Fullcalender Javascript -->
{{-- {{-- <script src="{{secure_asset('vendor/fullcalendar/core/main.js')}}"></script>
<script src="{{secure_asset('vendor/fullcalendar/daygrid/main.js')}}"></script>
<script src="{{secure_asset('vendor/fullcalendar/timegrid/main.js')}}"></script>
<script src="{{secure_asset('vendor/fullcalendar/list/main.js')}}"></script>
<script src="{{secure_asset('vendor/fullcalendar/interaction/main.js')}}"></script> --}}
<script src="{{secure_asset('vendor/moment.min.js')}}"></script>
<script src="{{secure_asset('js/plugins/calender.js')}}"></script>
@endif

<script src="{{ secure_asset('vendor/flatpickr/dist/flatpickr.min.js') }}"></script>
<script src="{{ secure_asset('js/plugins/flatpickr.js') }}" defer></script>
{{-- <script src="{{secure_asset('vendor/vanillajs-datepicker/dist/js/datepicker-full.js')}}"></script> --}}

@stack('scripts')

<script src="{{secure_asset('js/plugins/prism.mini.js')}}"></script>

<!-- Custom JavaScript -->
<script src="{{secure_asset('js/hope-ui.js') }}"></script>
<script src="{{secure_asset('js/modelview.js')}}"></script>
