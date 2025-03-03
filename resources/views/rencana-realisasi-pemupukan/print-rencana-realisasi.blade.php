<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            /* Matches original body margin */
            font-size: 12px;
            /* Matches original body font size */
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            /* Matches original h1 margin */
            font-size: 24px;
            /* Matches original h1 font size */
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            /* Matches original table margin */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            /* Matches original padding */
            text-align: center;
        }

        th {
            background-color: #d3d3d3;
            /* Matches original grey header */
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }

            @page {
                size: landscape;
                /* Matches original landscape layout */
            }
        }
    </style>
</head>

<body onload="window.print()">
    <h1>{{ $pageTitle }}</h1>
    <table>
        <thead>
            <tr>
                <th>Regional</th>
                <th>Rencana S1</th>
                <th>Realisasi S1</th>
                <th>% S1</th>
                <th>Rencana S2</th>
                <th>Realisasi S2</th>
                <th>% S2</th>
                <th>Rencana Total</th>
                <th>Realisasi Total</th>
                <th>% Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->regional }}</td>
                    <td>{{ number_format($row->rencana_semester_1, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_semester_1, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_semester_1 > 0 ? number_format(($row->realisasi_semester_1 / $row->rencana_semester_1) * 100, 2, ',', '.') . '%' : '0%' }}
                    </td>
                    <td>{{ number_format($row->rencana_semester_2, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_semester_2, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_semester_2 > 0 ? number_format(($row->realisasi_semester_2 / $row->rencana_semester_2) * 100, 2, ',', '.') . '%' : '0%' }}
                    </td>
                    <td>{{ number_format($row->rencana_total, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_total, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_total > 0 ? number_format(($row->realisasi_total / $row->rencana_total) * 100, 2, ',', '.') . '%' : '0%' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button class="no-print" onclick="window.close()">Close</button>
</body>

</html>
