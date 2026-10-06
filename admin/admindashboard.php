<?php
require_once "adminguard.php";
require_once "../classes/Admin.php";

$adminObj = new Admin();
$adminData = $adminObj->fetch_adminid($_SESSION['adminonline']);
$vehicles = $adminObj->get_all_vehicles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Samprime Autos Global</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
            <a href="admindashboard.php" class="brand-logo">
                <img src="../assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME <span>AUTOS</span>
            </a>
            <div class="nav-links">
                <a href="logout.php" class="btn btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
                </a>
            </div>
    </div>
</nav>

<div class="row admin-container">
    <!-- Centered Blue Welcome Banner -->
    <div class="col-md-12">
        <div class="admin-welcome-banner">
            <div class="welcome-text-side">
                <span class="admin-badge"><i class="fa-solid fa-shield-halved"></i> Control Panel</span>
                <h2>Welcome Back, <span>SamPrime</span> 👋</h2>
                <p>Manage your inventory, update vehicle availability, and upload photos with ease.</p>
            </div>
            <div class="welcome-stats-side">
                <div class="stat-pill">
                    <span class="stat-num"><?php echo count($vehicles); ?></span>
                    <span class="stat-lbl">Total Vehicles</span>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-header-row">
            <div>
                <h2 class="admin-section-title"><i class="fa-solid fa-car"></i> Vehicle Management</h2>
            </div>
            <a href="upload_vehicle.php" class="btn btn-primary admin-upload-btn">
                <i class="fa-solid fa-cloud-arrow-up"></i> <span>Upload New Vehicle</span>
            </a>
        </div>

        <?php if (isset($_SESSION['feedback'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($_SESSION['feedback']); unset($_SESSION['feedback']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errormsg'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['errormsg']); unset($_SESSION['errormsg']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($vehicles)): ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 0.8rem;"></i>
                <p>No vehicles uploaded yet. Click "Upload New Vehicle" to add your first item.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Vehicle Name</th>
                            <th>Make Year</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th>Pictures</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $index => $car): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <?php if (!empty($car['primary_img']) && file_exists(__DIR__ . "/../uploads/" . $car['primary_img'])): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($car['primary_img']); ?>" class="admin-thumb" alt="Thumbnail">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=200&q=80" class="admin-thumb" alt="Placeholder">
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($car['vehicle_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($car['vehicle_year']); ?></td>
                                <td><span class="spec-item"><?php echo htmlspecialchars($car['vehicle_color']); ?></span></td>
                                <td>
                                    <form action="action/update_status.php" method="POST" style="margin:0; display:flex; align-items:center; gap:0.4rem;">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                                        <input type="hidden" name="redirect_to" value="admindashboard.php">
                                        <select name="vehicle_status" onchange="this.form.submit()" class="form-control" style="padding: 0.3rem 0.6rem; font-size: 0.82rem; font-weight: 600; width: auto;">
                                            <option value="available" <?php echo (($car['vehicle_status'] ?? 'available') === 'available') ? 'selected' : ''; ?>>🟢 Available</option>
                                            <option value="sold" <?php echo (($car['vehicle_status'] ?? 'available') === 'sold') ? 'selected' : ''; ?>>🔴 Sold</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-camera"></i> <?php echo $car['total_images']; ?></div>
                                    <?php if (!empty($car['total_videos'])): ?>
                                        <div style="font-size:0.82rem; color:var(--primary); font-weight:600; margin-top:0.2rem;">
                                            <i class="fa-solid fa-video"></i> <?php echo $car['total_videos']; ?> Vid
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($car['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <a href="view_vehicle.php?id=<?php echo $car['vehicle_id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                        <form action="action/delete_vehicle.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this vehicle and all its pictures?');" style="margin:0;">
                                            <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                                            <button type="submit" name="delete_vehicle" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
