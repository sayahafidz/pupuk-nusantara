<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $pageTitle ?? 'List' }}</h4>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table id="dataTable" class="table text-center table-striped w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: "{{ route('rencana-realisasi-pemupukan.index') }}",
                    columns: [{
                            data: 'regional',
                            name: 'regional',
                            title: 'Regional'
                        },
                        {
                            data: 'rencana_semester_1',
                            name: 'rencana_semester_1',
                            title: 'Rencana Semester 1'
                        },
                        {
                            data: 'realisasi_semester_1',
                            name: 'realisasi_semester_1',
                            title: 'Realisasi Semester 1'
                        },
                        {
                            data: 'percentage_semester_1',
                            name: 'percentage_semester_1',
                            title: 'Percentage Semester 1'
                        },
                        {
                            data: 'rencana_semester_2',
                            name: 'rencana_semester_2',
                            title: 'Rencana Semester 2'
                        },
                        {
                            data: 'realisasi_semester_2',
                            name: 'realisasi_semester_2',
                            title: 'Realisasi Semester 2'
                        },
                        {
                            data: 'percentage_semester_2',
                            name: 'percentage_semester_2',
                            title: 'Percentage Semester 2'
                        },
                        {
                            data: 'rencana_total',
                            name: 'rencana_total',
                            title: 'Rencana Total'
                        },
                        {
                            data: 'realisasi_total',
                            name: 'realisasi_total',
                            title: 'Realisasi Total'
                        },
                        {
                            data: 'percentage_total',
                            name: 'percentage_total',
                            title: 'Percentage Total'
                        }
                    ],
                    dom: '<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>>' +
                        '<"table-responsive my-3" rt>' +
                        '<"row align-items-center" <"col-md-6" i><"col-md-6" p>>',
                    buttons: [{
                            extend: 'copy',
                            text: 'Copy',
                            title: 'Rencana Realisasi Pemupukan Data'
                        },
                        {
                            extend: 'csv',
                            text: 'CSV',
                            title: 'Rencana_Realisasi_Pemupukan_Data'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            title: 'Rencana_Realisasi_Pemupukan_Data'
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            title: 'Rencana Realisasi Pemupukan Data'
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            title: 'Rencana Realisasi Pemupukan Data'
                        }
                    ],
                    language: {
                        paginate: {
                            previous: 'Previous',
                            next: 'Next'
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
