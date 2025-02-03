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
                            <select class="form-control" id="regionalSelect" required name="regional">
                                <option selected disabled value="">All</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-2">
                            <label for="kebunSelect">Kebun</label>
                            <select id="kebunSelect" class="form-control">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="afdelingSelect">Afdeling</label>
                            <select id="afdelingSelect" class="form-control">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tahunTanamSelect">Tahun Tanam</label>
                            <select id="tahunTanamSelect" class="form-control">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jenisPupuk">Jenis Pupuk</label>
                            <select class="form-control" id="jenisPupukSelect" required name="jenisPupuk">
                                <option selected disabled value="">All</option>
                                @foreach ($jenisPupuks as $jenisPupuk)
                                    <option value="{{ $jenisPupuk }}">{{ $jenisPupuk }}</option>
                                @endforeach
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



                            document.getElementById('regionalSelect').addEventListener('change', function() {
                                const regional = this.value;
                                fetch(`/api/kebun/${regional}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let kebunOptions = '<option >All</option>';
                                        data.forEach(kebun => {
                                            kebunOptions += `<option value="${kebun}">${kebun}</option>`;
                                        });
                                        document.getElementById('kebunSelect').innerHTML = kebunOptions;
                                    });
                            });


                            document.getElementById('kebunSelect').addEventListener('change', function() {
                                const regional = document.getElementById('regionalSelect').value;
                                const kebun = this.value;
                                fetch(`/api/afdeling/${regional}/${kebun}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let afdelingOptions =
                                            '<option selected disabled value="">All</option>';
                                        data.forEach(afdeling => {
                                            afdelingOptions +=
                                                `<option value="${afdeling}">${afdeling}</option>`;
                                        });
                                        document.getElementById('afdelingSelect').innerHTML = afdelingOptions;
                                    });
                            });

                            document.getElementById('afdelingSelect').addEventListener('change', function() {
                                const regional = document.getElementById('regionalSelect').value;
                                const kebun = document.getElementById('kebunSelect').value;
                                const afdeling = this.value;
                                fetch(`/api/tahuntanam/${regional}/${kebun}/${afdeling}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let tahuntanamOption = '<option selected disabled value="">All</option>';
                                        data.forEach(tahuntaman => {
                                            tahuntanamOption +=
                                                `<option value="${tahuntaman}">${tahuntaman}</option>`;
                                        });
                                        document.getElementById('tahunTanamSelect').innerHTML = tahuntanamOption;
                                    });
                            });



                            // Initialize the column chart
                            const columnChart = Highcharts.chart('pupukChart', {
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: 'Rekap Pemupukan'
                                },
                                xAxis: {
                                    categories: []
                                },
                                yAxis: {
                                    title: {
                                        text: 'Jumlah Pemupukan'
                                    }
                                },
                                series: [{
                                    name: 'Jumlah Pokok',
                                    data: []
                                }, {
                                    name: 'Luas Blok (Ha)',
                                    data: []
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
                                    categories: []
                                },
                                yAxis: {
                                    title: {
                                        text: 'Trend Data'
                                    }
                                },
                                series: [{
                                    name: 'Jumlah Pokok',
                                    data: []
                                }, {
                                    name: 'Luas Blok (Ha)',
                                    data: []
                                }],
                                credits: {
                                    enabled: false
                                }
                            });

                            // Initialize the pie chart
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
                                    data: []
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
                                    data: []
                                }],
                                credits: {
                                    enabled: false
                                }
                            });



                            function updateCharts() {
                                const regional = document.getElementById('regionalSelect').value;
                                const kebun = document.getElementById('kebunSelect').value;
                                const afdeling = document.getElementById('afdelingSelect').value;
                                const tahunTanam = document.getElementById('tahunTanamSelect').value;
                                const jenisPupuk = document.getElementById('jenisPupukSelect').value;
                                const fromDate = document.getElementById('fromDate').value;
                                const toDate = document.getElementById('toDate').value;

                                // Build the URL with only the parameters that have values other than "All"
                                let url = `/pemupukan/comparison/${regional}`;

                                // Add filters if they are not empty or "All"
                                if (kebun && kebun !== 'All') url += `/${kebun}`;
                                if (afdeling && afdeling !== 'All') url += `/${afdeling}`;
                                if (tahunTanam && tahunTanam !== 'All') url += `/${tahunTanam}`;
                                if (jenisPupuk && jenisPupuk !== 'All') url += `/${jenisPupuk}`;
                                if (fromDate) url += `/${fromDate}`;
                                if (toDate) url += `/${toDate}`;

                                // Fetch data with the dynamically built URL
                                fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        // Extract data for the charts
                                        const categories = [];
                                        const jumlahPokok = [];
                                        const luasBlok = [];
                                        const lineCategories = [];
                                        const lineJumlahPokok = [];
                                        const lineLuasBlok = [];
                                        const pieRegionalData = [];
                                        const pieKebunData = [];

                                        // Loop through the fetched data to populate the chart data
                                        for (const key in data) {
                                            const item = data[key];
                                            if (item.pemupukan) {
                                                categories.push(`${item.regional} - ${item.kebun}`);
                                                jumlahPokok.push(item.pemupukan.jumlah_pupuk);
                                                luasBlok.push(item.pemupukan.luas_blok);

                                                // Prepare line chart data
                                                lineCategories.push(`${item.regional} - ${item.kebun}`);
                                                lineJumlahPokok.push(item.pemupukan.jumlah_pupuk);
                                                lineLuasBlok.push(item.pemupukan.luas_blok);

                                                // Prepare pie chart data for regional
                                                pieRegionalData.push({
                                                    name: `${item.regional}`,
                                                    y: item.pemupukan.jumlah_pupuk,
                                                });

                                                // Prepare pie chart data for kebun
                                                pieKebunData.push({
                                                    name: `${item.kebun}`,
                                                    y: item.pemupukan.jumlah_pupuk,
                                                });
                                            }
                                        }

                                        // Update column chart
                                        columnChart.xAxis[0].setCategories(categories);
                                        columnChart.series[0].setData(jumlahPokok);
                                        columnChart.series[1].setData(luasBlok);

                                        // Update line chart
                                        lineChart.xAxis[0].setCategories(lineCategories);
                                        lineChart.series[0].setData(lineJumlahPokok);
                                        lineChart.series[1].setData(lineLuasBlok);

                                        // Update pie chart 1
                                        pieChart1.series[0].setData(pieRegionalData);

                                        // Update pie chart 2
                                        pieChart2.series[0].setData(pieKebunData);
                                    });
                            }

                            // Call updateCharts when any of the dropdowns or date inputs change
                            document.getElementById('regionalSelect').addEventListener('change', updateCharts);
                            document.getElementById('kebunSelect').addEventListener('change', updateCharts);
                            document.getElementById('afdelingSelect').addEventListener('change', updateCharts);
                            document.getElementById('tahunTanamSelect').addEventListener('change', updateCharts);
                            document.getElementById('jenisPupukSelect').addEventListener('change', updateCharts);
                            document.getElementById('fromDate').addEventListener('change', updateCharts);
                            document.getElementById('toDate').addEventListener('change', updateCharts);

                            // Initialize charts on page load
                            updateCharts();

                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
