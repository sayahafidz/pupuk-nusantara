<?php

namespace App\DataTables;

use App\Models\Pemupukan; // Use the Pemupukan model
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PemupukanDataTable extends DataTable
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
            ->addColumn('action', 'pemupukan.action') // Adjust the action view to match Pemupukan
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Pemupukan $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Pemupukan::query();

        if ($regional = request('regional')) {
            $query->where('regional', $regional);
        }

        if ($kebun = request('kebun')) {
            $query->where('kebun', $kebun);
        }

        if ($afdeling = request('afdeling')) {
            $query->where('afdeling', $afdeling);
        }

        if ($tahunTanam = request('tahun_tanam')) {
            $query->where('tahun_tanam', $tahunTanam);
        }

        if ($jenisPupuk = request('jenis_pupuk')) {
            $query->where('jenis_pupuk', $jenisPupuk);
        }

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
            ->minifiedAjax(route('rencana-pemupukan.index'))
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
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'regional', 'name' => 'regional', 'title' => 'Regional'],
            ['data' => 'kebun', 'name' => 'kebun', 'title' => 'Kebun'],
            ['data' => 'afdeling', 'name' => 'afdeling', 'title' => 'Afdeling'],
            ['data' => 'blok', 'name' => 'blok', 'title' => 'Blok'],
            ['data' => 'tahun_tanam', 'name' => 'tahun_tanam', 'title' => 'Tahun Tanam'],
            ['data' => 'jenis_pupuk', 'name' => 'jenis_pupuk', 'title' => 'Jenis Pupuk'],
            ['data' => 'jumlah_pupuk', 'name' => 'jumlah_pupuk', 'title' => 'Jumlah Pupuk'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
