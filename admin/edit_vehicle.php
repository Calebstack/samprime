<?php
require_once "adminguard.php";
require_once "../classes/Vehicle.php";
require_once "../classes/Admin.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicleObj = new Vehicle();
$adminObj = new Admin();
$car = $vehicleObj->fetch_vehicle_by_id($id);
$images = $car ? $vehicleObj->fetch_vehicle_images($id) : [];
$videos = $car ? $vehicleObj->fetch_vehicle_videos($id) : [];
$features = ($car && !empty($car['vehicle_features'])) ? json_decode($car['vehicle_features'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car ? htmlspecialchars($car['vehicle_name']) . ' — Edit Vehicle' : 'Vehicle Not Found'; ?> | Samprime Autos Global</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .media-remove-card {
            border: 1px solid var(--border-color);
            padding: 0.95rem;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: #fff;
        }
        .media-thumb {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .features-builder {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }
        .feature-row {
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }
        .feature-row input {
            flex: 1;
        }
        .btn-remove-feature {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 8px;
            background: #fee2e2;
            color: #dc2626;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .btn-remove-feature:hover { background: #fecaca; }
        .btn-add-feature {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary);
            border: 1.5px dashed rgba(37, 99, 235, 0.4);
            border-radius: 8px;
            padding: 0.55rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            width: fit-content;
            transition: all 0.2s;
        }
        .btn-add-feature:hover {
            background: rgba(37, 99, 235, 0.14);
            border-color: var(--primary);
        }
        .section-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 1.8rem 0;
        }
        .section-label {
            font-family: var(--font-heading);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 1.2rem;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="admindashboard.php" class="brand-logo">
            <img src="../assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME <span>AUTOS</span>
        </a>
        <div class="nav-links">
            <a href="admindashboard.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <a href="logout.php" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="detail-container admin-edit-page">
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

        <div class="admin-welcome-banner" style="margin-bottom:1.8rem; padding:1.2rem 2rem; display:flex; align-items:center; gap:1rem; text-align:left;">
            <i class="fa-solid fa-pen-to-square" style="font-size:1.5rem;"></i>
            <div>
                <strong style="font-size:1.05rem;">Edit Vehicle Listing</strong>
            </div>
        </div>

        <div class="detail-grid">
            <div class="gallery-box">
                <h2 style="margin-bottom:1rem;">Current Media</h2>
                <?php if (empty($images) && empty($videos)): ?>
                    <div style="color: var(--text-muted);">No media has been uploaded yet for this vehicle.</div>
                <?php endif; ?>

                <div style="display:grid; gap:1rem;">
                    <?php foreach ($videos as $video): ?>
                        <?php if (file_exists(__DIR__ . "/../uploads/" . $video['video_name'])): ?>
                            <div class="media-remove-card">
                                <video class="media-thumb" src="../uploads/<?php echo htmlspecialchars($video['video_name']); ?>" style="width:200px;" controls muted playsinline></video>
                                <div style="display:flex; align-items:center; gap:0.6rem;">
                                    <form action="action/delete_media.php" method="POST" onsubmit="return confirm('Delete this video?');">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                                        <input type="hidden" name="media_type" value="video">
                                        <input type="hidden" name="media_id" value="<?php echo $video['video_id']; ?>">
                                        <button type="submit" name="delete_media" class="btn btn-danger" style="max-width: 60px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($images as $image): ?>
                        <?php if (file_exists(__DIR__ . "/../uploads/" . $image['img_name'])): ?>
                            <div class="media-remove-card">
                                <img src="../uploads/<?php echo htmlspecialchars($image['img_name']); ?>" style="width:200px;" alt="Vehicle image" class="media-thumb">
                                <div style="display:flex; align-items:center; gap:0.6rem;">
                                    <form action="action/delete_media.php" method="POST" onsubmit="return confirm('Delete this photo?');">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">
                                        <input type="hidden" name="media_type" value="image">
                                        <input type="hidden" name="media_id" value="<?php echo $image['img_id']; ?>">
                                        <button type="submit" name="delete_media" class="btn btn-danger" style="max-width: 60px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="detail-info-card">
                <h1 class="detail-title">Edit: <?php echo htmlspecialchars($car['vehicle_name']); ?></h1>
                <p style="color:var(--text-muted); font-size:0.93rem; margin-bottom:1.5rem;">
                    <i class="fa-solid fa-clock"></i> Originally listed on <?php echo date('M d, Y', strtotime($car['created_at'])); ?>
                </p>

                <div class="admin-status-control" style="background: #f8fafc; padding: 1.2rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
                    <p style="font-weight: 700; margin-bottom: 0.8rem; font-size: 0.95rem; color: var(--text-main);">
                        <i class="fa-solid fa-tag"></i> Listing Snapshot
                    </p>
                    <div class="detail-meta-list">
                        <div class="meta-row">
                            <span class="meta-label"><i class="fa-solid fa-hashtag"></i> Vehicle ID</span>
                            <span class="meta-val">#<?php echo $car['vehicle_id']; ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label"><i class="fa-solid fa-signal"></i> Status</span>
                            <span class="meta-val"><?php echo (($car['vehicle_status'] ?? 'available') === 'sold') ? '<span class="status-badge status-sold"><i class="fa-solid fa-circle-xmark"></i> Sold</span>' : '<span class="status-badge status-available"><i class="fa-solid fa-circle-check"></i> Available</span>'; ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label"><i class="fa-solid fa-images"></i> Total Pictures</span>
                            <span class="meta-val"><?php echo count($images); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label"><i class="fa-solid fa-video"></i> Total Videos</span>
                            <span class="meta-val"><?php echo count($videos); ?></span>
                        </div>
                    </div>
                </div>

                <form action="action/update_vehicle.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="vehicle_id" value="<?php echo $car['vehicle_id']; ?>">

                    <p class="section-label">Vehicle Details</p>
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label>Vehicle Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="vehicle_name" class="form-control" value="<?php echo htmlspecialchars($car['vehicle_name']); ?>" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                        <div class="form-group">
                            <label>Make Year <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="vehicle_year" class="form-control" value="<?php echo htmlspecialchars($car['vehicle_year']); ?>" min="1950" max="<?php echo date('Y') + 1; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Vehicle Color <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="vehicle_color" class="form-control" value="<?php echo htmlspecialchars($car['vehicle_color']); ?>" required>
                        </div>
                    </div>

                    <hr class="section-divider">

                    <p class="section-label">Pricing & Status</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                        <div class="form-group">
                            <label>Price (₦)</label>
                            <input type="number" name="vehicle_price" class="form-control" value="<?php echo htmlspecialchars($car['vehicle_price']); ?>" min="0" step="any">
                        </div>
                        <div class="form-group">
                            <label>Entry Year</label>
                            <input type="number" name="vehicle_entry_year" class="form-control" value="<?php echo htmlspecialchars($car['vehicle_entry_year']); ?>" min="1950" max="<?php echo date('Y') + 1; ?>">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label>Vehicle Condition</label>
                        <select name="vehicle_condition" class="form-control">
                            <option value="">— Select Condition —</option>
                            <option value="Foreign Used" <?php echo ($car['vehicle_condition'] === 'Foreign Used') ? 'selected' : ''; ?>>Foreign Used</option>
                            <option value="Registered" <?php echo ($car['vehicle_condition'] === 'Registered') ? 'selected' : ''; ?>>Registered (Nigeria)</option>
                            <option value="Brand New" <?php echo ($car['vehicle_condition'] === 'Brand New') ? 'selected' : ''; ?>>Brand New</option>
                            <option value="Locally Used" <?php echo ($car['vehicle_condition'] === 'Locally Used') ? 'selected' : ''; ?>>Locally Used</option>
                        </select>
                    </div>

                    <hr class="section-divider">

                    <p class="section-label">Features</p>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
                        Edit the features for this listing. Leave unused fields blank.
                    </p>
                    <div class="features-builder" id="featuresBuilder">
                        <?php if (!empty($features)): ?>
                            <?php foreach ($features as $feature): ?>
                                <div class="feature-row">
                                    <input type="text" name="features[]" class="form-control" value="<?php echo htmlspecialchars($feature); ?>" placeholder="e.g. Sunroof, Reverse Camera...">
                                    <button type="button" class="btn-remove-feature" onclick="removeFeature(this)" title="Remove">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="feature-row">
                                <input type="text" name="features[]" class="form-control" placeholder="e.g. Leather seats, GPS navigation...">
                                <button type="button" class="btn-remove-feature" onclick="removeFeature(this)" title="Remove">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-add-feature" id="addFeatureBtn" style="margin-top: 0.8rem;">
                        <i class="fa-solid fa-plus"></i> Add Another Feature
                    </button>

                    <hr class="section-divider">

                    <p class="section-label">Upload Additional Media</p>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Upload More Pictures</label>
                        <input type="file" name="vehicle_pics[]" class="form-control" multiple accept="image/*">
                        <small style="color: var(--text-muted); margin-top: 0.4rem; display: block;">
                            Choose extra photos to append to this listing.
                        </small>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.8rem;">
                        <label>Upload More Videos</label>
                        <input type="file" name="vehicle_videos[]" class="form-control" multiple accept="video/mp4,video/webm,video/ogg,video/quicktime,.mp4,.webm,.mov">
                        <small style="color: var(--text-muted); margin-top: 0.4rem; display: block;">
                            Upload additional video clips if available.
                        </small>
                    </div>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                        <button type="submit" name="btn_update" class="btn btn-primary" style="flex:1; min-width:200px;">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                        </button>
                        <a href="view_vehicle.php?id=<?php echo $car['vehicle_id']; ?>" class="btn btn-outline" style="min-width:200px; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-eye"></i> View Listing
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('addFeatureBtn').addEventListener('click', function () {
        const builder = document.getElementById('featuresBuilder');
        const row = document.createElement('div');
        row.className = 'feature-row';
        row.innerHTML = `
            <input type="text" name="features[]" class="form-control" placeholder="e.g. Sunroof, Reverse Camera...">
            <button type="button" class="btn-remove-feature" onclick="removeFeature(this)" title="Remove">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        builder.appendChild(row);
        row.querySelector('input').focus();
    });

    function removeFeature(btn) {
        const rows = document.querySelectorAll('#featuresBuilder .feature-row');
        if (rows.length > 1) {
            btn.closest('.feature-row').remove();
        } else {
            btn.closest('.feature-row').querySelector('input').value = '';
        }
    }
</script>
</body>
</html>
