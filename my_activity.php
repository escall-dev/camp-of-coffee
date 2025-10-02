<?php
$pageTitle = 'My Activity';
require_once 'includes/header.php';
require_once 'includes/activity.php';

$logs = getMyActivity(getCurrentUserId(), 200);
?>
<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>My Activity
        </h2>
        <p class="text-muted">Only your own actions are shown here.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (count($logs) === 0): ?>
            <p class="text-center text-muted mb-0">No activity yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date/Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
