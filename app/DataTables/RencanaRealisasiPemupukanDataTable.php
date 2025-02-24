<?php

namespace App\DataTables;

use App\Models\RencanaRealisasiPemupukan;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RencanaRealisasiPemupukanDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('rencana_semester_1', function ($row) {
                return number_format($row->rencana_semester_1, 0, ',', '.') . ' Kg';
            })
            ->addColumn('realisasi_semester_1', function ($row) {
                return number_format($row->realisasi_semester_1, 0, ',', '.') . ' Kg';
            })
            ->addColumn('percentage_semester_1', function ($row) {
                return $row->rencana_semester_1 > 0
                ? number_format(($row->realisasi_semester_1 / $row->rencana_semester_1) * 100, 2, ',', '.') . '%'
                : '0%';
            })
            ->addColumn('rencana_semester_2', function ($row) {
                return number_format($row->rencana_semester_2, 0, ',', '.') . ' Kg';
            })
            ->addColumn('realisasi_semester_2', function ($row) {
                return number_format($row->realisasi_semester_2, 0, ',', '.') . ' Kg';
            })
            ->addColumn('percentage_semester_2', function ($row) {
                return $row->rencana_semester_2 > 0
                ? number_format(($row->realisasi_semester_2 / $row->rencana_semester_2) * 100, 2, ',', '.') . '%'
                : '0%';
            })
            ->addColumn('rencana_total', function ($row) {
                return number_format($row->rencana_total, 0, ',', '.') . ' Kg';
            })
            ->addColumn('realisasi_total', function ($row) {
                return number_format($row->realisasi_total, 0, ',', '.') . ' Kg';
            })
            ->addColumn('percentage_total', function ($row) {
                return $row->rencana_total > 0
                ? number_format(($row->realisasi_total / $row->rencana_total) * 100, 2, ',', '.') . '%'
                : '0%';
            });
    }

    public function query()
    {
        // Group by 'regional' and aggregate the numeric columns
        $query = RencanaRealisasiPemupukan::query()
            ->selectRaw('
                regional,
                SUM(rencana_semester_1) as rencana_semester_1,
                SUM(realisasi_semester_1) as realisasi_semester_1,
                SUM(rencana_semester_2) as rencana_semester_2,
                SUM(realisasi_semester_2) as realisasi_semester_2,
                SUM(rencana_total) as rencana_total,
                SUM(realisasi_total) as realisasi_total
            ')
            ->groupBy('regional');

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax() // Empty URL for client-side processing
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            ->parameters([
                'processing' => true, // Client-side processing
                'serverSide' => true, // Client-side processing
                'autoWidth' => false,
                'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print'],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('regional')->title('Regional'),
            Column::make('rencana_semester_1')->title('Rencana Semester 1'),
            Column::make('realisasi_semester_1')->title('Realisasi Semester 1'),
            Column::make('percentage_semester_1')->title('Percentage Semester 1'),
            Column::make('rencana_semester_2')->title('Rencana Semester 2'),
            Column::make('realisasi_semester_2')->title('Realisasi Semester 2'),
            Column::make('percentage_semester_2')->title('Percentage Semester 2'),
            Column::make('rencana_total')->title('Rencana Total'),
            Column::make('realisasi_total')->title('Realisasi Total'),
            Column::make('percentage_total')->title('Percentage Total'),
        ];
    }
}
