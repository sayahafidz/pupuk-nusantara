<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
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
            ->editColumn('status', function ($query) {
                $status = 'warning';
                switch ($query->status) {
                    case 'active':
                        $status = 'primary';
                        break;
                    case 'inactive':
                        $status = 'danger';
                        break;
                    case 'banned':
                        $status = 'dark';
                        break;
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $query->status . '</span>';
            })
            ->addColumn('role_title', function ($query) {
                // Get the first role's title or a default value if no roles exist
                $role = $query->roles->first();
                return $role ? $role->title : 'N/A';
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $sql = "CONCAT(users.first_name,' ',users.last_name) like ?";
                return $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('action', 'users.action')
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = User::query()->with(['userProfile', 'roles']); // Eager load roles
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
            ->setTableId('users-datatable') // Unique table ID
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('users.index'), // Route to fetch data
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
            ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Full Name', 'orderable' => false],
            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Phone Number'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'role_title', 'name' => 'role_title', 'title' => 'User Role'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
