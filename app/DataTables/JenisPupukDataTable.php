<?php

namespace App\DataTables;

use App\Models\JenisPupuk;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class JenisPupukDataTable extends DataTable
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
            ->addColumn('action', 'jenis-pupuk.action')
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = JenisPupuk::query();
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
            ->setTableId('jenis-pupuk-datatable') // Unique ID for the table
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('jenis-pupuk.index'), // Route to fetch data
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
            ['data' => 'id', 'name' => 'id', 'title' => 'id'],
            ['data' => 'kode_pupuk', 'name' => 'kode_pupuk', 'title' => 'Kode Pupuk', 'orderable' => false],
            ['data' => 'nama_pupuk', 'name' => 'nama_pupuk', 'title' => 'Nama Pupuk'],
            ['data' => 'jenis_pupuk', 'name' => 'jenis_pupuk', 'title' => 'Jenis Pupuk'],
            ['data' => 'harga', 'name' => 'harga', 'title' => 'Harga'],
            ['data' => 'stok', 'name' => 'stok', 'title' => 'Stok'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
