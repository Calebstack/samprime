<?php
require_once "classes/Vehicle.php";
require_once "partials/header.php";

$vehicleObj = new Vehicle();

$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_year = isset($_GET['search_year']) ? trim($_GET['search_year']) : '';
$search_color = isset($_GET['search_color']) ? trim($_GET['search_color']) : '';
$search_status = isset($_GET['search_status']) ? trim($_GET['search_status']) : '';

if (!empty($search_name) || !empty($search_year) || !empty($search_color) || !empty($search_status)) {
    $vehicles = $vehicleObj->search_vehicles($search_name, $search_year, $search_color, $search_status);
} else {
    $vehicles = $vehicleObj->get_all_vehicles();
}
?>

<section class="hero-section" style="padding: 3.5rem 1.5rem 2.5rem; margin-bottom: 0;">
    <div class="hero-content">
        <h1 class="hero-title" style="font-size: 2.8rem;">Browse Our <span>Automobile Inventory</span></h1>
        <p class="hero-subtitle">Explore our wide selection of verified cars, find the perfect match, and get in touch with us directly for the best deals.</p>
    </div>
</section>

<div class="search-section">
    <div class="filter-card">
        <h3 class="filter-title"><i class="fa-solid fa-sliders" style="color: var(--primary);"></i> Search Inventory</h3>
        <form action="cars.php" method="GET" class="filter-form">
            <div class="form-group">
                <label>Vehicle Name / Keyword</label>
                <input type="text" name="search_name" class="form-control" placeholder="Search by name, model (e.g. Camry, Benz, BMW)..." value="<?php echo htmlspecialchars($search_name); ?>">
            </div>
            <div class="form-group">
                <label>Make Year</label>
                <input type="number" name="search_year" class="form-control" placeholder="e.g. 2023" value="<?php echo htmlspecialchars($search_year); ?>">
            </div>
            <div class="form-group">
                <label>Vehicle Color</label>
                <input type="text" name="search_color" class="form-control" placeholder="e.g. Black, White, Silver..." value="<?php echo htmlspecialchars($search_color); ?>">
            </div>
            <div class="form-group">
                <label>Availability</label>
                <select name="search_status" class="form-control">
                    <option value="">All Cars</option>
                    <option value="available" <?php echo $search_status === 'available' ? 'selected' : ''; ?>>Available Only</option>
                    <option value="sold" <?php echo $search_status === 'sold' ? 'selected' : ''; ?>>Sold</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
                <?php if (!empty($search_name) || !empty($search_year) || !empty($search_color) || !empty($search_status)): ?>
                    <a href="cars.php" class="btn btn-outline">
                        <i class="fa-solid fa-rotate"></i> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="cars-container">
    <div class="section-header">
        <h2>Automobile Inventory (<?php echo count($vehicles); ?>)</h2>
    </div>

    <?php if (empty($vehicles)): ?>
        <div class="filter-card" style="text-align: center; padding: 4rem 2rem;">
            <i class="fa-solid fa-car-burst" style="font-size: 3.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
            <h3 style="color: var(--text-muted);">No cars found matching your search criteria</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Try resetting your search filters or check back later.</p>
            <a href="cars.php" class="btn btn-outline" style="margin-top: 1.5rem;"><i class="fa-solid fa-rotate"></i> View All Cars</a>
        </div>
    <?php else: ?>
        <div class="cars-grid">
            <?php foreach ($vehicles as $car): ?>
                <div class="car-card">
                    <div class="car-image-box">
                        <!-- Status Overlay Badge on Image -->
                        <?php if (($car['vehicle_status'] ?? 'available') === 'sold'): ?>
                            <span class="image-status-overlay overlay-sold">
                                <i class="fa-solid fa-circle-xmark"></i> SOLD
                            </span>
                        <?php else: ?>
                            <span class="image-status-overlay overlay-available">
                                <i class="fa-solid fa-circle-check"></i> AVAILABLE
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($car['primary_img']) && file_exists(__DIR__ . "/uploads/" . $car['primary_img'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($car['primary_img']); ?>" alt="<?php echo htmlspecialchars($car['vehicle_name']); ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80" alt="Placeholder Car">
                        <?php endif; ?>
                    </div>
                    <div class="car-content">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.6rem;">
                            <h3 class="car-title" style="margin-bottom:0;"><?php echo htmlspecialchars($car['vehicle_name']); ?></h3>
                        </div>
                        <div class="car-specs" style="margin-bottom: 1.2rem;">
                            <span class="spec-item"><i class="fa-solid fa-calendar-days"></i> Year: <?php echo htmlspecialchars($car['vehicle_year']); ?></span>
                            <span class="spec-item"><i class="fa-solid fa-palette"></i> Color: <?php echo htmlspecialchars($car['vehicle_color']); ?></span>
                            <?php if (!empty($car['vehicle_price'])): ?>
                                <span class="spec-item" style="color: var(--primary); font-weight: 700;">₦<?php echo number_format((float)$car['vehicle_price'], 0, '.', ','); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="car-card-footer">
                            <a href="details.php?id=<?php echo $car['vehicle_id']; ?>" class="btn btn-primary" style="width: 100%;">
                                <i class="fa-solid fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once "partials/footer.php"; ?>
