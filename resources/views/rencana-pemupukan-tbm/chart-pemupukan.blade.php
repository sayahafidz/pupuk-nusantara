<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Rekap Pemupukan Chart</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Dropdown filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="regionalSelect">Regional</label>
                            <select id="regionalSelect" class="form-control">
                                <option value="">All</option>
                                <option value="regional1">Regional 1</option>
                                <option value="regional2">Regional 2</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="kebunSelect">Kebun</label>
                            <select id="kebunSelect" class="form-control" disabled>
                                <option value="">All</option>
                                <option value="kebun1">Kebun 1</option>
                                <option value="kebun2">Kebun 2</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="afdelingSelect">Afdeling</label>
                            <select id="afdelingSelect" class="form-control" disabled>
                                <option value="">All</option>
                                <option value="afdeling1">Afdeling 1</option>
                                <option value="afdeling2">Afdeling 2</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tahunTanamSelect">Tahun Tanam</label>
                            <select id="tahunTanamSelect" class="form-control">
                                <option value="">All</option>
                                <option value="2005">2005</option>
                                <option value="2006">2006</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jenisPupuk">Jenis Pupuk</label>
                            <select id="jenisPupuk" class="form-control">
                                <option value="">All</option>
                                <option value="NPK">NPK</option>
                                <option value="DOLOMIT">DOLOMIT</option>
                            </select>
                        </div>
                    </div>


                    <!-- Date range inputs -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fromDate">From</label>
                            <input type="date" id="fromDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="toDate">To</label>
                            <input type="date" id="toDate" class="form-control">
                        </div>
                    </div>


                    <!-- Highcharts containers -->
                    <div class="row">
                        <div class="col-md-6">
                            <div id="pupukChart" style="width:100%; height:400px;"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="lineChart" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="pieChart1" style="width:100%; height:400px; margin-top: 30px;"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="pieChart2" style="width:100%; height:400px; margin-top: 30px;"></div>
                        </div>
                    </div>

                    <!-- Highcharts script -->
                    <script src="https://code.highcharts.com/highcharts.js"></script>
                    <script type="text/javascript">
                        document.addEventListener('DOMContentLoaded', function() {
                            // Initialize the column chart
                            const columnChart = Highcharts.chart('pupukChart', {
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: 'Rekap Pemupukan'
                                },
                                xAxis: {
                                    categories: ['Tiger Nixon', 'System Architect', 'Edinburgh', 'Afdeling 61']
                                },
                                yAxis: {
                                    title: {
                                        text: 'Jumlah Pemupukan'
                                    }
                                },
                                series: [{
                                    name: 'Jumlah Pokok',
                                    data: [320800, 230400, 210700, 180200]
                                }, {
                                    name: 'Luas Blok (Ha)',
                                    data: [24, 30, 40, 15]
                                }],
                                credits: {
                                    enabled: false
                                }
                            });

                            // Initialize the line chart
                            const lineChart = Highcharts.chart('lineChart', {
                                chart: {
                                    type: 'line'
                                },
                                title: {
                                    text: 'Trend Pemupukan Over Time'
                                },
                                xAxis: {
                                    categories: ['January', 'February', 'March', 'April']
                                },
                                yAxis: {
                                    title: {
                                        text: 'Trend Data'
                                    }
                                },
                                series: [{
                                    name: 'Jumlah Pokok',
                                    data: [120000, 150000, 130000, 110000]
                                }, {
                                    name: 'Luas Blok (Ha)',
                                    data: [22, 25, 30, 20]
                                }],
                                credits: {
                                    enabled: false
                                }
                            });

                            // Initialize the first pie chart
                            const pieChart1 = Highcharts.chart('pieChart1', {
                                chart: {
                                    type: 'pie'
                                },
                                title: {
                                    text: 'Percentage of Regional Pemupukan'
                                },
                                series: [{
                                    name: 'Pemupukan',
                                    colorByPoint: true,
                                    data: [{
                                            name: 'Regional 1',
                                            y: 45
                                        },
                                        {
                                            name: 'Regional 2',
                                            y: 30
                                        },
                                        {
                                            name: 'Regional 3',
                                            y: 25
                                        }
                                    ]
                                }],
                                credits: {
                                    enabled: false
                                }
                            });

                            // Initialize the second pie chart
                            const pieChart2 = Highcharts.chart('pieChart2', {
                                chart: {
                                    type: 'pie'
                                },
                                title: {
                                    text: 'Percentage of Kebun Pemupukan'
                                },
                                series: [{
                                    name: 'Pemupukan',
                                    colorByPoint: true,
                                    data: [{
                                            name: 'Kebun 1',
                                            y: 40
                                        },
                                        {
                                            name: 'Kebun 2',
                                            y: 35
                                        },
                                        {
                                            name: 'Kebun 3',
                                            y: 25
                                        }
                                    ]
                                }],
                                credits: {
                                    enabled: false
                                }
                            });

                            // Enable/disable dropdowns and reset values
                            document.getElementById('regionalSelect').addEventListener('change', function() {
                                const kebunSelect = document.getElementById('kebunSelect');
                                const afdelingSelect = document.getElementById('afdelingSelect');

                                kebunSelect.disabled = !this.value;
                                afdelingSelect.disabled = true;

                                kebunSelect.value = '';
                                afdelingSelect.value = '';

                                updateCharts();
                            });

                            document.getElementById('kebunSelect').addEventListener('change', function() {
                                const afdelingSelect = document.getElementById('afdelingSelect');
                                afdelingSelect.disabled = !this.value;
                                afdelingSelect.value = '';

                                updateCharts();
                            });

                            document.getElementById('afdelingSelect').addEventListener('change', updateCharts);

                            // Function to update all charts based on dropdown values
                            function updateCharts() {
                                const regional = document.getElementById('regionalSelect').value;
                                const kebun = document.getElementById('kebunSelect').value;
                                const afdeling = document.getElementById('afdelingSelect').value;

                                // Fetch or filter data based on regional, kebun, and afdeling
                                // const updatedColumnData = [ /* Adjust column chart data based on filters */ ];
                                // const updatedLineData = [ /* Adjust line chart data based on filters */ ];
                                // const updatedPieData1 = [ /* Adjust pie chart 1 data */ ];
                                // const updatedPieData2 = [ /* Adjust pie chart 2 data */ ];
                                // Generate random data for demonstration purposes
                                const generateRandomData = (length, min, max) => {
                                    return Array.from({
                                        length
                                    }, () => Math.floor(Math.random() * (max - min + 1)) + min);
                                };

                                const updatedColumnData = generateRandomData(4, 100000, 500000);
                                const updatedLineData = generateRandomData(4, 100000, 200000);
                                const updatedPieData1 = [{
                                        name: 'Regional 1',
                                        y: Math.random() * 100
                                    },
                                    {
                                        name: 'Regional 2',
                                        y: Math.random() * 100
                                    },
                                    {
                                        name: 'Regional 3',
                                        y: Math.random() * 100
                                    }
                                ];
                                const updatedPieData2 = [{
                                        name: 'Kebun 1',
                                        y: Math.random() * 100
                                    },
                                    {
                                        name: 'Kebun 2',
                                        y: Math.random() * 100
                                    },
                                    {
                                        name: 'Kebun 3',
                                        y: Math.random() * 100
                                    }
                                ];

                                // Example data update - update actual logic as needed
                                columnChart.series[0].setData(updatedColumnData);
                                lineChart.series[0].setData(updatedLineData);
                                pieChart1.series[0].setData(updatedPieData1);
                                pieChart2.series[0].setData(updatedPieData2);
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
