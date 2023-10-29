<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('content') ?>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="d-inline-flex">
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start Date">
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date">
        </div>
    </div>
    <div style="max-height: 72vh">
        <canvas id="url-stats"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('url-stats');
        const absoluteStartDate = new Date('<?= $absoluteStartDate ?>')
        let chart;
        let visits = <?= json_encode($visits); ?>;
        visits = visits.map((visit) => {
            return {
                date: new Date(visit.created_at),
                ip: visit.ip
            }
        });
        let endDate = new Date();
        endDate.setHours(24, 0, 0, 0);
        document.getElementById('end_date').value = new Date(endDate).toISOString().split('T')[0];
        let startDate = endDate - 7 * 24 * 60 * 60 * 1000; // 7 days ago
        if (startDate < absoluteStartDate) {
            startDate = absoluteStartDate;
        }
        document.getElementById('start_date').value = new Date(startDate).toISOString().split('T')[0];

        MakeChart();

        function MakeChart() {
            console.log(startDate);
            console.log(endDate);
            if (chart) {
                chart.destroy();
            }
            let nOfDays = (endDate - startDate) / (24 * 60 * 60 * 1000);

            let dates = []
            let clicks = [];
            for (let i = 0; i < nOfDays; i++) {
                let newDate = new Date(startDate + i * 24 * 60 * 60 * 1000)
                dates.push(newDate);
                clicks.push(visits.filter((visit, i) => {
                    return visit.date.toLocaleDateString() == newDate.toLocaleDateString();
                }).length);
            };
            chart = new Chart( ctx, 
            {
                type: 'bar',
                data: {
                    labels : dates.map((date) => {
                        return date.toLocaleDateString();
                    }),
                    datasets : [{
                        label: 'Clicks',
                        data: clicks,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }],
                }
            })
            
        }

        document.getElementById('start_date').addEventListener('change', (e) => {
            let targetValue = e.target.value;
            startDate = new Date(targetValue);
            if (startDate < absoluteStartDate) {
                startDate = absoluteStartDate;
                e.target.value = new Date(startDate).toISOString().split('T')[0];
            }
            startDate = startDate.getTime();
            MakeChart();
        });

        document.getElementById('end_date').addEventListener('change', (e) => {
            let targetValue = e.target.value;
            if (targetValue > new Date().toISOString().split('T')[0]) {
                e.target.value = new Date().toISOString().split('T')[0];
            }
            else {
                endDate = new Date(targetValue);
            }
            MakeChart();
        });
    </script>
<?php $this->endSection() ?>