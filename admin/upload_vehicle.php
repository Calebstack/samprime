<?php
require_once "adminguard.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Vehicle - Samprime Autos Global Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Feature tag builder */
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
            width: 38px; height: 38px;
            border: none;
            border-radius: 8px;
            background: #fee2e2;
            color: #dc2626;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
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
            <img src="../assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME<span>AUTOS</span>
        </a>
        <div class="nav-links">
            <a href="admindashboard.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </div>
</nav>

<div class="admin-container" style="max-width: 750px;">
    <div class="admin-card">
        <div style="margin-bottom: 2rem;">
            <h2><i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary);"></i> Upload Vehicle Listing</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.3rem;">Fill in the vehicle details below and attach one or more photos.</p>
        </div>

        <?php if (isset($_SESSION['errormsg'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?php echo htmlspecialchars($_SESSION['errormsg']); unset($_SESSION['errormsg']); ?>
            </div>
        <?php endif; ?>

        <form action="action/upload_vehicle.php" method="POST" enctype="multipart/form-data">

            <!-- ── Basic Info ── -->
            <p class="section-label">Basic Information</p>

            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label>Vehicle Name / Title <span style="color:#ef4444;">*</span></label>
                <input type="text" name="vehicle_name" class="form-control"
                       placeholder="e.g. Toyota Camry SE, Honda Accord, Mercedes-Benz C300..." required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div class="form-group">
                    <label>Make Year <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="vehicle_year" class="form-control"
                           placeholder="e.g. 2023" min="1950" max="<?php echo date('Y') + 1; ?>" required>
                </div>
                <div class="form-group">
                    <label>Vehicle Color <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="vehicle_color" class="form-control"
                           placeholder="e.g. Metallic Black, Pearl White..." required>
                </div>
            </div>

            <hr class="section-divider">

            <!-- ── Pricing & Status ── -->
            <p class="section-label">Pricing &amp; Status</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div class="form-group">
                    <label>Price (₦)</label>
                    <input type="number" name="vehicle_price" class="form-control"
                           placeholder="e.g. 15000000" min="0" step="any">
                </div>
                <div class="form-group">
                    <label>Entry Year</label>
                    <input type="number" name="vehicle_entry_year" class="form-control"
                           placeholder="e.g. 2022" min="1950" max="<?php echo date('Y') + 1; ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label>Vehicle Condition</label>
                <select name="vehicle_condition" class="form-control">
                    <option value="">— Select Condition —</option>
                    <option value="Foreign Used">Foreign Used</option>
                    <option value="Registered">Registered (Nigeria)</option>
                    <option value="Brand New">Brand New</option>
                    <option value="Locally Used">Locally Used</option>
                </select>
            </div>

            <hr class="section-divider">

            <!-- ── Features ── -->
            <p class="section-label">Vehicle Features</p>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
                Add individual features such as "Leather Seats", "Sunroof", "Reverse Camera", etc.
            </p>

            <div class="features-builder" id="featuresBuilder">
                <div class="feature-row">
                    <input type="text" name="features[]" class="form-control" placeholder="e.g. Leather Seats">
                    <button type="button" class="btn-remove-feature" onclick="removeFeature(this)" title="Remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <button type="button" class="btn-add-feature" id="addFeatureBtn" style="margin-top: 0.8rem;">
                <i class="fa-solid fa-plus"></i> Add Another Feature
            </button>

            <hr class="section-divider">

            <!-- ── Media (Photos & Videos) ── -->
            <p class="section-label">Vehicle Media (Photos &amp; Videos)</p>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Upload Pictures <span style="color:#ef4444;">*</span></label>
                <input type="file" name="vehicle_pics[]" id="vehiclePicsInput" class="form-control"
                       multiple accept="image/*" required>
                <small style="color: var(--text-muted); margin-top: 0.4rem; display: block;">
                    Hold <kbd>Ctrl</kbd> / <kbd>Cmd</kbd> to select multiple images. (JPG, PNG, WEBP supported)
                </small>
                <div id="imagePreviewBox" style="display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.9rem;"></div>
            </div>

            <div class="form-group" style="margin-bottom: 1.8rem;">
                <label><i class="fa-solid fa-video" style="color: var(--primary);"></i> Upload Videos <span style="color:var(--text-muted); font-size:0.85rem; font-weight:normal;">(Optional)</span></label>
                <input type="file" name="vehicle_videos[]" id="vehicleVideosInput" class="form-control"
                       multiple accept="video/mp4,video/webm,video/ogg,video/quicktime,.mp4,.webm,.mov">
                <small style="color: var(--text-muted); margin-top: 0.4rem; display: block;">
                    Upload video clips of the car interior/exterior. (MP4, WebM, MOV supported). Videos will play on autoplay in loop.
                </small>
            </div>

            <!-- ── Submit ── -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" name="btn_upload" class="btn btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Vehicle
                </button>
                <a href="admindashboard.php" class="btn btn-outline">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
    // Dynamic feature row management
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
            // Keep at least one row but clear it
            btn.closest('.feature-row').querySelector('input').value = '';
        }
    }
</script>
</body>
</html>
