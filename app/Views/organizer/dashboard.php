<?php
$page_title = 'Dashboard';
$page_subtitle = 'Overview of your tournaments and activities';
?>
<?= $this->extend('organizer_base'); ?>

<?= $this->section('pageCss'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
/* ── Dashboard Styles ───────────────────────────────────── */
.dashboard-stat-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    padding: 20px 24px;
    transition: all 0.2s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.dashboard-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.dashboard-stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
}

.dashboard-stat-card .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}

.dashboard-stat-card .stat-label {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
}

.dashboard-stat-card .stat-change {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 8px;
}

.stat-change.positive {
    background: #D1FAE5;
    color: #065F46;
}

.stat-change.negative {
    background: #FEE2E2;
    color: #991B1B;
}

.stat-change.neutral {
    background: #EFF6FF;
    color: #1E3A8A;
}

/* Color variants for stat icons */
.stat-icon.blue { background: #DBEAFE; color: #2563EB; }
.stat-icon.green { background: #D1FAE5; color: #059669; }
.stat-icon.purple { background: #EDE9FE; color: #7C3AED; }
.stat-icon.orange { background: #FEF3C7; color: #D97706; }
.stat-icon.red { background: #FEE2E2; color: #DC2626; }
.stat-icon.indigo { background: #E0E7FF; color: #4338CA; }

/* ── Chart Cards ─────────────────────────────────────────── */
.chart-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    padding: 20px 24px;
    height: 100%;
}

.chart-card .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.chart-card .chart-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.chart-card .chart-subtitle {
    font-size: 12px;
    color: #6B7280;
}

/* ── Recent Items ────────────────────────────────────────── */
.recent-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-list li {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #F3F4F6;
}

.recent-list li:last-child {
    border-bottom: none;
}

.recent-list .recent-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 12px;
    font-size: 14px;
}

.recent-list .recent-icon.tournament { background: #DBEAFE; color: #2563EB; }
.recent-list .recent-icon.booking { background: #D1FAE5; color: #059669; }
.recent-list .recent-icon.event { background: #EDE9FE; color: #7C3AED; }

.recent-list .recent-content {
    flex: 1;
}

.recent-list .recent-title {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
    margin: 0;
}

.recent-list .recent-meta {
    font-size: 12px;
    color: #6B7280;
    margin: 0;
}

.recent-list .recent-status {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
}

.recent-list .recent-status.active {
    background: #D1FAE5;
    color: #065F46;
}

.recent-list .recent-status.upcoming {
    background: #DBEAFE;
    color: #2563EB;
}

.recent-list .recent-status.completed {
    background: #F3F4F6;
    color: #6B7280;
}

.recent-list .recent-status.pending {
    background: #FEF3C7;
    color: #D97706;
}

/* ── Status Distribution ─────────────────────────────────── */
.status-distribution {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.status-item {
    flex: 1;
    min-width: 80px;
    text-align: center;
    padding: 12px;
    border-radius: 8px;
    background: #F9FAFB;
}

.status-item .status-count {
    font-size: 24px;
    font-weight: 700;
}

.status-item .status-name {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
}

.status-item.upcoming .status-count { color: #2563EB; }
.status-item.ongoing .status-count { color: #059669; }
.status-item.completed .status-count { color: #6B7280; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 768px) {
    .dashboard-stat-card .stat-number {
        font-size: 22px;
    }
    .status-distribution {
        flex-direction: column;
    }
    .status-item {
        min-width: unset;
    }
}

/* ── Quick Actions ──────────────────────────────────────── */
.quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.quick-actions .btn-quick {
    padding: 10px 18px;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    background: #fff;
    color: #374151;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.quick-actions .btn-quick:hover {
    background: #F3F4F6;
    border-color: #D1D5DB;
}

.quick-actions .btn-quick.primary {
    background: #2563EB;
    color: #fff;
    border-color: #2563EB;
}

.quick-actions .btn-quick.primary:hover {
    background: #1D4ED8;
    border-color: #1D4ED8;
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<!-- ── Quick Actions ────────────────────────────────────── -->
<div class="mb-4">
    <div class="quick-actions">
        <a href="<?= base_url('organizer/create_tournament') ?>" class="btn-quick primary">
            <i class="fa-solid fa-plus"></i> Create Tournament
        </a>
        <a href="<?= base_url('organizer/booking_history') ?>" class="btn-quick">
            <i class="fa-solid fa-list"></i> View Bookings
        </a>
        <a href="<?= base_url('organizer/umpire_list') ?>" class="btn-quick">
            <i class="fa-solid fa-users"></i> Manage Umpires
        </a>
    </div>
</div>

<!-- ── Statistics Cards ──────────────────────────────────── -->
<div class="row g-4 mb-4">
    <!-- 1. Total Tournaments -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-trophy"></i></div>
            <div class="stat-number"><?= number_format($total_tournaments ?? 0) ?></div>
            <div class="stat-label">Total Tournaments</div>
            <?php if (isset($status_distribution)): ?>
                <div class="stat-change neutral">
                    <i class="fa-regular fa-calendar"></i> 
                    <?= ($status_distribution['upcoming'] ?? 0) ?> Upcoming
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Active Tournaments -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon green"><i class="fa-regular fa-calendar-check"></i></div>
            <div class="stat-number"><?= number_format($active_tournaments ?? 0) ?></div>
            <div class="stat-label">Active Tournaments</div>
            <?php if (isset($status_distribution)): ?>
                <div class="stat-change positive">
                    <i class="fa-regular fa-clock"></i> Ongoing
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. Total Events -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon purple"><i class="fa-regular fa-calendar-plus"></i></div>
            <div class="stat-number"><?= number_format($total_events ?? 0) ?></div>
            <div class="stat-label">Total Events</div>
            <div class="stat-change neutral">
                <i class="fa-regular fa-star"></i> Across all tournaments
            </div>
        </div>
    </div>

    <!-- 4. Total Participants -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-users"></i></div>
            <div class="stat-number"><?= number_format($total_participants ?? 0) ?></div>
            <div class="stat-label">Total Participants</div>
            <?php if (isset($today_bookings) && $today_bookings > 0): ?>
                <div class="stat-change positive">
                    <i class="fa-regular fa-calendar"></i> +<?= $today_bookings ?> today
                </div>
            <?php else: ?>
                <div class="stat-change neutral">
                    <i class="fa-regular fa-clock"></i> No new today
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 5. Total Revenue -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon indigo"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            <div class="stat-number">₹<?= number_format($total_revenue ?? 0) ?></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-change neutral">
                <i class="fa-regular fa-credit-card"></i> All time
            </div>
        </div>
    </div>

    <!-- 6. Today's Bookings -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="dashboard-stat-card">
            <div class="stat-icon red"><i class="fa-regular fa-clock"></i></div>
            <div class="stat-number"><?= number_format($today_bookings ?? 0) ?></div>
            <div class="stat-label">Today's Bookings</div>
            <?php if (isset($today_bookings) && $today_bookings > 0): ?>
                <div class="stat-change positive">
                    <i class="fa-regular fa-circle-check"></i> New registrations
                </div>
            <?php else: ?>
                <div class="stat-change neutral">
                    <i class="fa-regular fa-circle"></i> No bookings today
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Charts Row ─────────────────────────────────────────── -->
<div class="row g-4 mb-4">
    <!-- Monthly Tournaments Chart -->
    <div class="col-xl-8 col-lg-12">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Tournaments Overview</h5>
                    <span class="chart-subtitle">Monthly tournament creation trend</span>
                </div>
                <span class="badge bg-primary">12 months</span>
            </div>
            <div style="height: 280px;">
                <canvas id="tournamentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="col-xl-4 col-lg-12">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Tournament Status</h5>
                    <span class="chart-subtitle">Distribution by status</span>
                </div>
            </div>
            <div style="height: 280px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ── Recent Activity Row ─────────────────────────────────── -->
<div class="row g-4">
    <!-- Recent Tournaments -->
    <div class="col-xl-6 col-lg-12">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Recent Tournaments</h5>
                    <span class="chart-subtitle">Your latest tournaments</span>
                </div>
                <a href="<?= base_url('organizer') ?>" class="text-primary" style="font-size:13px;text-decoration:none;">
                    View All <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <?php if (!empty($recent_tournaments)): ?>
                <ul class="recent-list">
                    <?php foreach ($recent_tournaments as $tournament): 
                        $status = 'upcoming';
                        $current = date('Y-m-d H:i:s');
                        if ($tournament->listing_start_date <= $current && $tournament->listing_end_date >= $current) {
                            $status = 'active';
                        } elseif ($tournament->listing_end_date < $current) {
                            $status = 'completed';
                        }
                    ?>
                        <li>
                            <div class="recent-icon tournament"><i class="fa-solid fa-trophy"></i></div>
                            <div class="recent-content">
                                <p class="recent-title"><?= esc($tournament->listing_title) ?></p>
                                <p class="recent-meta">
                                    <i class="fa-regular fa-calendar"></i> 
                                    <?= date('d M Y', strtotime($tournament->listing_start_date)) ?> - 
                                    <?= date('d M Y', strtotime($tournament->listing_end_date)) ?>
                                </p>
                            </div>
                            <span class="recent-status <?= $status ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted text-center py-4">No tournaments created yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-xl-6 col-lg-12">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Recent Bookings</h5>
                    <span class="chart-subtitle">Latest participant registrations</span>
                </div>
                <a href="<?= base_url('organizer/booking_history') ?>" class="text-primary" style="font-size:13px;text-decoration:none;">
                    View All <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <?php if (!empty($recent_bookings)): ?>
                <ul class="recent-list">
                    <?php foreach ($recent_bookings as $booking): ?>
                        <li>
                            <div class="recent-icon booking"><i class="fa-solid fa-user"></i></div>
                            <div class="recent-content">
                                <p class="recent-title"><?= esc($booking->user_full_name ?? $booking->user_name ?? 'Unknown') ?></p>
                                <p class="recent-meta">
                                    <i class="fa-regular fa-ticket"></i> 
                                    <?= esc($booking->listing_title ?? 'N/A') ?>
                                    <span class="mx-1">•</span>
                                    <i class="fa-regular fa-clock"></i> 
                                    <?= time_ago($booking->book_created_at) ?>
                                </p>
                            </div>
                            <span class="recent-status <?= $booking->book_payment_status ? 'active' : 'pending' ?>">
                                <?= $booking->book_payment_status ? 'Paid' : 'Pending' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted text-center py-4">No bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // ── Tournament Chart ─────────────────────────────────
    var ctx = document.getElementById('tournamentChart').getContext('2d');
    var monthlyData = <?= json_encode($monthly_stats ?? []) ?>;
    var labels = Object.keys(monthlyData);
    var data = Object.values(monthlyData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.map(function(label) {
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var parts = label.split('-');
                return months[parseInt(parts[1]) - 1] + ' ' + parts[0];
            }),
            datasets: [{
                label: 'Tournaments Created',
                data: data,
                backgroundColor: 'rgba(37, 99, 235, 0.6)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 2,
                borderRadius: 6,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // ── Status Distribution Chart ────────────────────────
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusData = <?= json_encode($status_distribution ?? ['upcoming' => 0, 'ongoing' => 0, 'completed' => 0]) ?>;

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Upcoming', 'Ongoing', 'Completed'],
            datasets: [{
                data: [
                    statusData.upcoming || 0,
                    statusData.ongoing || 0,
                    statusData.completed || 0
                ],
                backgroundColor: ['#2563EB', '#059669', '#9CA3AF'],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
<?= $this->endSection(); ?>