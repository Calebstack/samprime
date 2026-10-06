<?php
require_once "adminguard.php";
require_once "../classes/Vehicle.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicleObj = new Vehicle();
$car    = $vehicleObj->fetch_vehicle_by_id($id);
$images = $car ? $vehicleObj->fetch_vehicle_images($id) : [];
$videos = $car ? $vehicleObj->fetch_vehicle_videos($id) : [];
$features = ($car && !empty($car['vehicle_features'])) ? json_decode($car['vehicle_features'], true) : [];

$has_videos = !empty($videos) && file_exists(__DIR__ . "/../uploads/" . $videos[0]['video_name']);
$has_images = !empty($images) && file_exists(__DIR__ . "/../uploads/" . $images[0]['img_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car ? htmlspecialchars($car['vehicle_name']) . ' — Admin View' : 'Not Found'; ?> | Samprime Autos Global</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Admin Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="admindashboard.php" class="brand-logo">
            <img src="../assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME <span>AUTOS</span>
        </a>
        <div class="nav-links">
            <a href="admindashboard.php" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="detail-container">

    <?php if (!$car): ?>
        <div class="filter-card" style="text-align:center; padding: 4rem 2rem;">
            <i class="fa-solid fa-circle-exclamation" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
            <h2>Vehicle Not Found</h2>
            <p style="color:var(--text-muted); margin:1rem 0;">This vehicle may have been deleted or does not exist.</p>
            <a href="admindashboard.php" class="btn btn-primary" style="margin-top:1rem;">
                <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
            </a>
        </div>

    <?php else: ?>

        <!-- Feedback Messages -->
        <?php if (isset($_SESSION['feedback'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($_SESSION['feedback']); unset($_SESSION['feedback']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errormsg'])): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['errormsg']); unset($_SESSION['errormsg']); ?>
            </div>
        <?php endif; ?>

        <!-- Admin Preview Banner -->
        <div class="admin-welcome-banner" style="margin-bottom:1.8rem; padding:1.2rem 2rem; display:flex; align-items:center; gap:1rem; text-align:left;">
            <i class="fa-solid fa-user-shield" style="font-size:1.5rem;"></i>
            <div>
                <strong style="font-size:1.05rem;">Admin Preview</strong>
                <p style="font-size:0.88rem; margin:0; opacity:0.85;">You are viewing this vehicle as an administrator. Inquiry links are not shown.</p>
            </div>
        </div>

        <div class="detail-grid">

            <!-- Gallery Section (Photos & Videos) -->
            <div class="gallery-box">
                <div class="main-image-view" style="position: relative;">
                    <?php if (($car['vehicle_status'] ?? 'available') === 'sold'): ?>
                        <div class="image-status-overlay overlay-sold">
                            <i class="fa-solid fa-circle-xmark"></i> SOLD
                        </div>
                    <?php else: ?>
                        <div class="image-status-overlay overlay-available">
                            <i class="fa-solid fa-circle-check"></i> AVAILABLE
                        </div>
                    <?php endif; ?>

                    <!-- Video Element (Autoplay, Loop, Muted, Playsinline) -->
                    <video id="mainGalleryVideo"
                           src="<?php echo $has_videos ? '../uploads/' . htmlspecialchars($videos[0]['video_name']) : ''; ?>"
                           autoplay muted loop playsinline controls
                           class="<?php echo $has_videos ? 'media-active' : 'media-hidden'; ?>">
                    </video>

                    <!-- Image Element -->
                    <?php if ($has_images): ?>
                        <img id="mainGalleryImg"
                             src="../uploads/<?php echo htmlspecialchars($images[0]['img_name']); ?>"
                             alt="<?php echo htmlspecialchars($car['vehicle_name']); ?>"
                             class="<?php echo !$has_videos ? 'media-active' : 'media-hidden'; ?>">
                    <?php else: ?>
                        <img id="mainGalleryImg"
                             src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80"
                             alt="No image"
                             class="<?php echo !$has_videos ? 'media-active' : 'media-hidden'; ?>">
                    <?php endif; ?>
                </div>

                <!-- Unified Media Thumbnail Slider -->
                <?php if ((count($images) + count($videos)) > 1 || (!empty($videos) && !empty($images))): ?>
                    <div class="thumbnail-slider">
                        <!-- Video Thumbnails -->
                        <?php foreach ($videos as $vIndex => $vid): ?>
                            <?php if (file_exists(__DIR__ . "/../uploads/" . $vid['video_name'])): ?>
                                <div class="thumb-item video-thumb <?php echo $vIndex === 0 ? 'active' : ''; ?>"
                                     data-type="video"
                                     data-full-src="../uploads/<?php echo htmlspecialchars($vid['video_name']); ?>">
                                    <video src="../uploads/<?php echo htmlspecialchars($vid['video_name']); ?>#t=0.5" muted preload="metadata"></video>
                                    <span class="thumb-video-icon"><i class="fa-solid fa-play"></i> VIDEO</span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <!-- Image Thumbnails -->
                        <?php foreach ($images as $iIndex => $img): ?>
                            <?php if (file_exists(__DIR__ . "/../uploads/" . $img['img_name'])): ?>
                                <div class="thumb-item <?php echo (!$has_videos && $iIndex === 0) ? 'active' : ''; ?>"
                                     data-type="image"
                                     data-full-src="../uploads/<?php echo htmlspecialchars($img['img_name']); ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($img['img_name']); ?>" alt="Thumb <?php echo $iIndex+1; ?>">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p style="margin-top:1rem; color:var(--text-muted); font-size:0.88rem; text-align:center;">
                    <i class="fa-solid fa-camera"></i> <?php echo count($images); ?> photo<?php echo count($images) !== 1 ? 's' : ''; ?>
                    <?php if (!empty($videos)): ?>
                        &bull; <i class="fa-solid fa-video" style="color:var(--primary);"></i> <?php echo count($videos); ?> video<?php echo count($videos) !== 1 ? 's' : ''; ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Details Card -->
            <div class="detail-info-card">
                <h1 class="detail-title"><?php echo htmlspecialchars($car['vehicle_name']); ?></h1>
                <p style="color:var(--text-muted); font-size:0.93rem; margin-bottom:1.5rem;">
                    <i class="fa-solid fa-clock"></i> Listed on <?php echo date('M d, Y', strtotime($car['created_at'])); ?>
                </p>

                <!-- Price badge -->
                <?php if (!empty($car['vehicle_price'])): ?>
                    <div class="price-badge">
                        <i class="fa-solid fa-tag"></i>
                        ₦<?php echo number_format((float)$car['vehicle_price'], 0, '.', ','); ?>
                    </div>
                <?php endif; ?>

                <div class="detail-meta-list">
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-hashtag"></i> Vehicle ID</span>
                        <span class="meta-val">#<?php echo $car['vehicle_id']; ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-car"></i> Vehicle Name</span>
                        <span class="meta-val"><?php echo htmlspecialchars($car['vehicle_name']); ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-signal"></i> Listing Status</span>
                        <span class="meta-val">
                            <?php if (($car['vehicle_status'] ?? 'available') === 'sold'): ?>
                                <span class="status-badge status-sold"><i class="fa-solid fa-circle-xmark"></i> Sold</span>
                            <?php else: ?>
                                <span class="status-badge status-available"><i class="fa-solid fa-circle-check"></i> Available</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-calendar-days"></i> Make Year</span>
                        <span class="meta-val"><?php echo htmlspecialchars($car['vehicle_year']); ?></span>
                    </div>
                    <?php if (!empty($car['vehicle_entry_year'])): ?>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-calendar-check"></i> Entry Year</span>
                        <span class="meta-val"><?php echo htmlspecialchars($car['vehicle_entry_year']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-palette"></i> Color</span>
                        <span class="meta-val"><?php echo htmlspecialchars($car['vehicle_color']); ?></span>
                    </div>
                    <?php if (!empty($car['vehicle_condition'])): ?>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-circle-check"></i> Condition</span>
                        <span class="meta-val">
                            <span class="condition-badge condition-<?php echo strtolower(str_replace([' ', '(', ')'], ['-','',''], $car['vehicle_condition'])); ?>">
                                <?php echo htmlspecialchars($car['vehicle_condition']); ?>
                            </span>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-images"></i> Total Pictures</span>
                        <span class="meta-val"><?php echo count($images); ?> Available</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label"><i class="fa-solid fa-calendar-plus"></i> Date Added</span>
                        <span class="meta-val"><?php echo date('F j, Y', strtotime($car['created_at'])); ?></span>
                    </div>
                </div>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                    <div class="features-section">
                        <p class="features-heading"><i class="fa-solid fa-list-check"></i> Features</p>
                        <div class="features-tags">
                            <?php foreach ($features as $feature): ?>
                                <?php if (!empty(trim($feature))): ?>
                                    <span class="feature-tag"><?php echo htmlspecialchars(trim($feature)); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Admin Status Update Control -->
                <div class="admin-status-control" style="background: #f8fafc; padding: 1.2rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-top: 1.5rem;">
                    <p style="font-weight: 700; margin-bottom: 0.8rem; font-size: 0.95rem; color: var(--text-main);">
                        <i class="fa-solid fa-sliders" style="color:var(--primary);"></i> Update Item Availability Status
                    </p>
                    <form action="action/update_status.php" method="POST" style="display: flex; flex-direction: column; gap: 0.8rem;">
                        <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                        <input type="hidden" name="redirect_to" value="view_vehicle.php?id=<?php echo $car['vehicle_id']; ?>">
                        
                        <div style="display: flex; gap: 0.8rem; align-items: center;">
                            <select name="vehicle_status" class="form-control" style="font-weight: 600;">
                                <option value="available" <?php echo (($car['vehicle_status'] ?? 'available') === 'available') ? 'selected' : ''; ?>>
                                    Available (For Sale)
                                </option>
                                <option value="sold" <?php echo (($car['vehicle_status'] ?? 'available') === 'sold') ? 'selected' : ''; ?>>
                                    Sold
                                </option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary" style="white-space: nowrap;">
                                <i class="fa-solid fa-floppy-disk"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Admin Actions -->
                <div style="display:flex; flex-direction:column; gap:0.9rem; margin-top:1.5rem;">
                    <a href="admindashboard.php" class="btn btn-outline" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="edit_vehicle.php?id=<?php echo $car['vehicle_id']; ?>" class="btn btn-secondary" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit This Listing
                    </a>
                    <form action="action/delete_vehicle.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this vehicle and all its pictures?');">
                        <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                        <button type="submit" name="delete_vehicle" class="btn btn-danger" style="width:100%; justify-content:center;">
                            <i class="fa-solid fa-trash"></i> Delete This Vehicle
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<script src="../assets/js/main.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/main.js'); ?>"></script>
</body>
</html>
