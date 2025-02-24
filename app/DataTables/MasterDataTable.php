<?php

namespace App\DataTables;

use App\Models\MasterData;
use Yajra\DataTables\Html\Button;
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
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')

            ->parameters([
                "processing" => true,
                "autoWidth" => false,
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
            ['data' => 'kondisi', 'name' => 'kondisi', 'title' => 'Kondisi', 'orderable' => false],
            ['data' => 'status_umur', 'name' => 'status_umur', 'title' => 'Status Umur'],
            ['data' => 'rpc', 'name' => 'rpc', 'title' => 'RPC'],
            ['data' => 'plant', 'name' => 'plant', 'title' => 'PLANT'],
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
