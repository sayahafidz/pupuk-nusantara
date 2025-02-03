<?php

namespace App\DataTables;

use App\Models\Pemupukan; // Change the model to Pemupukan
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RencanaRealisasiPemupukanDataTable extends DataTable
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
            ->addColumn('percentage_semester_1', function ($row) {
                return $row->rencana_semester_1 > 0
                    ? number_format(($row->realisasi_semester_1 / $row->rencana_semester_1) * 100, 2) . '%'
                    : '0%';
            })
            ->addColumn('percentage_semester_2', function ($row) {
                return $row->rencana_semester_2 > 0
                    ? number_format(($row->realisasi_semester_2 / $row->rencana_semester_2) * 100, 2) . '%'
                    : '0%';
            })
            ->addColumn('percentage_total', function ($row) {
                return $row->rencana_total > 0
                    ? number_format(($row->realisasi_total / $row->rencana_total) * 100, 2) . '%'
                    : '0%';
            });
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Pemupukan $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Pemupukan::query()
            ->join('rencana_pemupukan', 'rencana_pemupukan.id', '=', 'pemupukan.id')
            ->selectRaw('
                rencana_pemupukan.regional,
                SUM(CASE WHEN rencana_pemupukan.semester_pemupukan = 1 THEN rencana_pemupukan.jumlah_pupuk ELSE 0 END) as rencana_semester_1,
                SUM(CASE WHEN rencana_pemupukan.semester_pemupukan = 1 THEN pemupukan.jumlah_pupuk ELSE 0 END) as realisasi_semester_1,
                SUM(CASE WHEN rencana_pemupukan.semester_pemupukan = 2 THEN rencana_pemupukan.jumlah_pupuk ELSE 0 END) as rencana_semester_2,
                SUM(CASE WHEN rencana_pemupukan.semester_pemupukan = 2 THEN pemupukan.jumlah_pupuk ELSE 0 END) as realisasi_semester_2,
                SUM(rencana_pemupukan.jumlah_pupuk) as rencana_total,
                SUM(pemupukan.jumlah_pupuk) as realisasi_total
            ')
            ->groupBy('rencana_pemupukan.regional');

        return $this->applyScopes($query);
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
            ->minifiedAjax(route('rencana-realisasi-pemupukan.index')) // Changed to the 'pemupukan' route
            ->ajax([
                'data' => 'function(data) {
                    data.regional = $("#filter-regional").val();
                    data.kebun = $("#filter-kebun").val();
                    data.afdeling = $("#filter-afdeling").val();
                    data.tahun_tanam = $("#filter-tahun_tanam").val();
                    data.jenis_pupuk = $("#filter-jenis_pupuk").val();
                }',
            ])
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            ->parameters([
                "processing" => true,
                "serverSide" => true,
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
            ['data' => 'regional', 'name' => 'regional', 'title' => 'Regional'],
            ['data' => 'rencana_semester_1', 'name' => 'rencana_semester_1', 'title' => 'Rencana Semester 1'],
            ['data' => 'realisasi_semester_1', 'name' => 'realisasi_semester_1', 'title' => 'Realisasi Semester 1'],
            ['data' => 'percentage_semester_1', 'name' => 'percentage_semester_1', 'title' => 'Percentage Semester 1'],
            ['data' => 'rencana_semester_2', 'name' => 'rencana_semester_2', 'title' => 'Rencana Semester 2'],
            ['data' => 'realisasi_semester_2', 'name' => 'realisasi_semester_2', 'title' => 'Realisasi Semester 2'],
            ['data' => 'percentage_semester_2', 'name' => 'percentage_semester_2', 'title' => 'Percentage Semester 2'],
            ['data' => 'rencana_total', 'name' => 'rencana_total', 'title' => 'Rencana Total'],
            ['data' => 'realisasi_total', 'name' => 'realisasi_total', 'title' => 'Realisasi Total'],
            ['data' => 'percentage_total', 'name' => 'percentage_total', 'title' => 'Percentage Total'],
        ];
    }
}
