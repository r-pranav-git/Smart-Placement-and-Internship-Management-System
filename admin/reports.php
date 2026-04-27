<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");
include("../includes/admin_navbar.php");

require_role('admin');

// 1. Application Status Distribution
$status_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM applications GROUP BY status");
$status_data = [];
while($row = mysqli_fetch_assoc($status_query)){
    $status_data[] = $row;
}

// 2. Department-wise Placements (Selected status)
$dept_query = mysqli_query($conn, "
    SELECT s.department, COUNT(*) as count 
    FROM applications a
    JOIN students s ON a.student_id = s.user_id
    WHERE a.status = 'selected'
    GROUP BY s.department
");
$dept_data = [];
while($row = mysqli_fetch_assoc($dept_query)){
    $dept_data[] = $row;
}

// 3. Average Package per Department
$salary_query = mysqli_query($conn, "
    SELECT department, AVG(COALESCE(salary,0)) as avg_salary
    FROM jobs
    GROUP BY department
");
$salary_data = [];
while($row = mysqli_fetch_assoc($salary_query)){
    $salary_data[] = $row;
}
?>

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>Placement Analytics</h2>
        <p>Visual insights into recruitment cycles and student performance.</p>
    </div>
</div>

<div class="row g-4">
        <!-- Application Status Chart -->
        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <h6 class="fw-bold mb-4">Application Lifecycle Distribution</h6>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Dept Wise Placement Chart -->
        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <h6 class="fw-bold mb-4">Department-wise Selections</h6>
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        <!-- Salary Trends -->
        <div class="col-12">
            <div class="dashboard-card">
                <h6 class="fw-bold mb-4">Average Salary Package by Department (LPA)</h6>
                <div style="height: 300px;">
                    <canvas id="salaryChart"></canvas>
                </div>
            </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data from PHP
const statusData = <?php echo json_encode($status_data); ?>;
const deptData = <?php echo json_encode($dept_data); ?>;
const salaryData = <?php echo json_encode($salary_data); ?>;

// 1. Status Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
        datasets: [{
            data: statusData.map(d => d.count),
            backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
        },
        cutout: '70%'
    }
});

// 2. Dept Chart
new Chart(document.getElementById('deptChart'), {
    type: 'pie',
    data: {
        labels: deptData.map(d => d.department),
        datasets: [{
            data: deptData.map(d => d.count),
            backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316'],
            borderWidth: 0
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
        }
    }
});

// 3. Salary Chart
new Chart(document.getElementById('salaryChart'), {
    type: 'bar',
    data: {
        labels: salaryData.map(d => d.department),
        datasets: [{
            label: 'Avg Package (LPA)',
            data: salaryData.map(d => d.avg_salary),
            backgroundColor: '#2563eb',
            borderRadius: 8,
            maxBarThickness: 50
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<?php include("../includes/admin_footer.php"); ?>