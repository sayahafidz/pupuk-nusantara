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
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #d3d3d3; /* Grey color for header */
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
                size: landscape; /* Default print layout to landscape */
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
                <th>Kebun</th>
                <th>Afdeling</th>
                <th>Rencana Semester 1</th>
                <th>Realisasi Semester 1</th>
                <th>Percentage Semester 1</th>
                <th>Rencana Semester 2</th>
                <th>Realisasi Semester 2</th>
                <th>Percentage Semester 2</th>
                <th>Rencana Total</th>
                <th>Realisasi Total</th>
                <th>Percentage Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->regional }}</td>
                    <td>{{ $row->kebun }}</td>
                    <td>{{ $row->afdeling }}</td>
                    <td>{{ number_format($row->rencana_semester_1, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_semester_1, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_semester_1 > 0 ? number_format(($row->realisasi_semester_1 / $row->rencana_semester_1) * 100, 2, ',', '.') . '%' : '0%' }}</td>
                    <td>{{ number_format($row->rencana_semester_2, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_semester_2, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_semester_2 > 0 ? number_format(($row->realisasi_semester_2 / $row->rencana_semester_2) * 100, 2, ',', '.') . '%' : '0%' }}</td>
                    <td>{{ number_format($row->rencana_total, 0, ',', '.') }} Kg</td>
                    <td>{{ number_format($row->realisasi_total, 0, ',', '.') }} Kg</td>
                    <td>{{ $row->rencana_total > 0 ? number_format(($row->realisasi_total / $row->rencana_total) * 100, 2, ',', '.') . '%' : '0%' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button class="no-print" onclick="window.close()">Close</button>
</body>
</html>