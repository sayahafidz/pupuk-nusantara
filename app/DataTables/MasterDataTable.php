<?php

namespace App\DataTables;

use App\Models\MasterData;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MasterDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', 'master-data.action')
            ->rawColumns(['action']); // Removed 'status' since it’s not in columns
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = MasterData::query();
        return $this->applyScopes($model);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('master-data-datatable') // Unique table ID
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('master-data.index'), // Route to fetch data
                'type' => 'GET', // HTTP method
            ])
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'autoWidth' => false,
                'buttons' => [
                    ['extend' => 'excel', 'className' => 'btn btn-success btn-sm', 'text' => 'Export Excel'],
                    ['extend' => 'pdf', 'className' => 'btn btn-danger btn-sm', 'text' => 'Export PDF'],
                ],
                'order' => [[0, 'asc']], // Default ordering
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json',
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'kondisi', 'name' => 'kondisi', 'title' => 'Kondisi', 'orderable' => false],
            ['data' => 'status_umur', 'name' => 'status_umur', 'title' => 'Status Umur'],
            ['data' => 'rpc', 'name' => 'rpc', 'title' => 'RPC'],
            ['data' => 'plant', 'name' => 'plant', 'title' => 'Plant'],
            ['data' => 'kode_kebun', 'name' => 'kode_kebun', 'title' => 'Kode Kebun'],
            ['data' => 'nama_kebun', 'name' => 'nama_kebun', 'title' => 'Nama Kebun'],
            ['data' => 'kkl_kebun', 'name' => 'kkl_kebun', 'title' => 'KKL Kebun'],
            ['data' => 'afdeling', 'name' => 'afdeling', 'title' => 'Afdeling'],
            ['data' => 'tahun_tanam', 'name' => 'tahun_tanam', 'title' => 'Tahun Tanam'],
            ['data' => 'no_blok', 'name' => 'no_blok', 'title' => 'No Blok'],
            ['data' => 'luas', 'name' => 'luas', 'title' => 'Luas (Ha)'],
            ['data' => 'jlh_pokok', 'name' => 'jlh_pokok', 'title' => 'Jumlah Pokok'],
            ['data' => 'pkk_ha', 'name' => 'pkk_ha', 'title' => 'Pokok/Ha'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
