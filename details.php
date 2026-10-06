<?php
require_once "classes/Vehicle.php";
require_once "partials/header.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicleObj = new Vehicle();
$car = $vehicleObj->fetch_vehicle_by_id($id);

if (!$car) {
    echo '<div class="detail-container"><div class="filter-card" style="text-align:center; padding: 4rem 2rem;">
            <h2>Vehicle Not Found</h2>
            <p style="color:var(--text-muted); margin: 1rem 0;">The vehicle you are looking for may have been removed or does not exist.</p>
            <a href="cars.php" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Back to Cars</a>
          </div></div>';
    require_once "partials/footer.php";
    exit;
}

$images   = $vehicleObj->fetch_vehicle_images($id);
$videos   = $vehicleObj->fetch_vehicle_videos($id);
$features = !empty($car['vehicle_features']) ? json_decode($car['vehicle_features'], true) : [];

$whatsapp_num     = defined('WHATSAPP_NUMBER') && !empty(WHATSAPP_NUMBER) ? WHATSAPP_NUMBER : '2349078918123';
$instagram_handle = defined('INSTAGRAM_HANDLE') && !empty(INSTAGRAM_HANDLE) ? INSTAGRAM_HANDLE : 'https://www.instagram.com/samprimeautos_?igsh=aXk1cDFzOXV5anE2';
$x_handle         = defined('X_HANDLE') && !empty(X_HANDLE) ? X_HANDLE : 'https://x.com/Samthebasisst_';

$wa_msg = "Hello, I am interested in inquiring about the " . $car['vehicle_name'] . " (" . $car['vehicle_year'] . ", Color: " . $car['vehicle_color'] . ") listed on Samprime Autos Global.";
$wa_url = (preg_match('#^https?://#i', $whatsapp_num) ? $whatsapp_num : 'https://wa.me/' . $whatsapp_num) . '?text=' . urlencode($wa_msg);
$ig_url = (stripos($instagram_handle, 'instagram.com') !== false) ? $instagram_handle : 'https://www.instagram.com/' . ltrim($instagram_handle, '@/');
$x_url  = (stripos($x_handle, 'x.com') !== false || stripos($x_handle, 'twitter.com') !== false) ? $x_handle : 'https://x.com/' . ltrim($x_handle, '@/');

// Determine initial media item
$has_videos = !empty($videos) && file_exists(__DIR__ . "/uploads/" . $videos[0]['video_name']);
$has_images = !empty($images) && file_exists(__DIR__ . "/uploads/" . $images[0]['img_name']);
?>

<div class="detail-container">
    <a href="cars.php" class="btn btn-outline" style="margin-bottom: 1.5rem;">
        <i class="fa-solid fa-arrow-left"></i> Back to Cars
    </a>

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
                       src="<?php echo $has_videos ? 'uploads/' . htmlspecialchars($videos[0]['video_name']) : ''; ?>"
                       autoplay muted loop playsinline controls
                       class="<?php echo $has_videos ? 'media-active' : 'media-hidden'; ?>">
                </video>

                <!-- Image Element -->
                <?php if ($has_images): ?>
                    <img id="mainGalleryImg"
                         src="uploads/<?php echo htmlspecialchars($images[0]['img_name']); ?>"
                         alt="<?php echo htmlspecialchars($car['vehicle_name']); ?>"
                         class="<?php echo !$has_videos ? 'media-active' : 'media-hidden'; ?>">
                <?php else: ?>
                    <img id="mainGalleryImg"
                         src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80"
                         alt="Placeholder Car"
                         class="<?php echo !$has_videos ? 'media-active' : 'media-hidden'; ?>">
                <?php endif; ?>
            </div>

            <!-- Unified Media Thumbnail Slider -->
            <?php if ((count($images) + count($videos)) > 1 || (!empty($videos) && !empty($images))): ?>
                <div class="thumbnail-slider">
                    <!-- Video Thumbnails -->
                    <?php foreach ($videos as $vIndex => $vid): ?>
                        <?php if (file_exists(__DIR__ . "/uploads/" . $vid['video_name'])): ?>
                            <div class="thumb-item video-thumb <?php echo $vIndex === 0 ? 'active' : ''; ?>"
                                 data-type="video"
                                 data-full-src="uploads/<?php echo htmlspecialchars($vid['video_name']); ?>">
                                <video src="uploads/<?php echo htmlspecialchars($vid['video_name']); ?>#t=0.5" muted preload="metadata"></video>
                                <span class="thumb-video-icon"><i class="fa-solid fa-play"></i> VIDEO</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Image Thumbnails -->
                    <?php foreach ($images as $iIndex => $img): ?>
                        <?php if (file_exists(__DIR__ . "/uploads/" . $img['img_name'])): ?>
                            <div class="thumb-item <?php echo (!$has_videos && $iIndex === 0) ? 'active' : ''; ?>"
                                 data-type="image"
                                 data-full-src="uploads/<?php echo htmlspecialchars($img['img_name']); ?>">
                                <img src="uploads/<?php echo htmlspecialchars($img['img_name']); ?>" alt="Thumbnail">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info & Inquiry Section -->
        <div class="detail-info-card">
            <h1 class="detail-title"><?php echo htmlspecialchars($car['vehicle_name']); ?></h1>
            <p style="color: var(--text-muted); font-size: 0.93rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-clock"></i> Listed on <?php echo date('M d, Y', strtotime($car['created_at'])); ?>
            </p>

            <!-- Price badge -->
            <?php if (!empty($car['vehicle_price'])): ?>
                <div class="price-badge">
                    <i class="fa-solid fa-tag"></i>
                    ₦<?php echo number_format((float)$car['vehicle_price'], 0, '.', ','); ?>
                </div>
            <?php endif; ?>

            <!-- Vehicle Specs -->
            <div class="detail-meta-list">
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
            </div>

            <!-- Features -->
            <?php if (!empty($features)): ?>
                <div class="features-section">
                    <p class="features-heading"><i class="fa-solid fa-list-check"></i> Features</p>
                    <div class="features-tags">
                        <?php foreach ($features as $feature): ?>
                            <?php if (!empty(trim($feature))): ?>
                                <p class="feature-tag"><?php echo htmlspecialchars(trim($feature)); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Social Inquiry Buttons / Sold Notice -->
            <?php if (($car['vehicle_status'] ?? 'available') === 'sold'): ?>
                <div class="sold-notice-card">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <div>
                        <h4>Vehicle Currently Sold</h4>
                        <p>This car has been marked as <strong>SOLD</strong>. You can still view photos or contact us for similar available models!</p>
                    </div>
                </div>
                <div class="inquiry-box" style="margin-top: 1rem;">
                    <a href="<?php echo $wa_url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="width: 100%;">
                        <i class="fa-brands fa-whatsapp"></i> Inquire About Similar Cars
                    </a>
                </div>
            <?php else: ?>
                <div class="inquiry-box">
                    <a href="<?php echo $wa_url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="width: 100%;">
                        <i class="fa-brands fa-whatsapp"></i> Inquire on WhatsApp
                    </a>
                    <a href="<?php echo $ig_url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-instagram" style="width: 100%;">
                        <i class="fa-brands fa-instagram"></i> Inquire on Instagram
                    </a>
                    <a href="<?php echo $x_url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-x" style="width: 100%;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 0.5rem; vertical-align: middle; flex-shrink: 0;">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg> Inquire on X
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "partials/footer.php"; ?>
