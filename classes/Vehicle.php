<?php

require_once "Db.php";

class Vehicle extends Db {

    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function get_all_vehicles() {
        try {
            $sql = "SELECT v.*, 
                    (SELECT img_name FROM vehicle_images vi WHERE vi.vehicle_id = v.vehicle_id ORDER BY vi.img_id ASC LIMIT 1) as primary_img,
                    (SELECT COUNT(*) FROM vehicle_images vi WHERE vi.vehicle_id = v.vehicle_id) as total_images,
                    (SELECT COUNT(*) FROM vehicle_videos vv WHERE vv.vehicle_id = v.vehicle_id) as total_videos
                    FROM vehicles v 
                    ORDER BY v.vehicle_id DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function search_vehicles($name = '', $year = '', $color = '', $status = '') {
        try {
            $sql = "SELECT v.*, 
                    (SELECT img_name FROM vehicle_images vi WHERE vi.vehicle_id = v.vehicle_id ORDER BY vi.img_id ASC LIMIT 1) as primary_img,
                    (SELECT COUNT(*) FROM vehicle_images vi WHERE vi.vehicle_id = v.vehicle_id) as total_images,
                    (SELECT COUNT(*) FROM vehicle_videos vv WHERE vv.vehicle_id = v.vehicle_id) as total_videos
                    FROM vehicles v WHERE 1=1";

            $params = [];

            if (!empty($name)) {
                $sql .= " AND v.vehicle_name LIKE ?";
                $params[] = "%" . trim($name) . "%";
            }

            if (!empty($year)) {
                $sql .= " AND v.vehicle_year = ?";
                $params[] = (int)$year;
            }

            if (!empty($color)) {
                $sql .= " AND v.vehicle_color LIKE ?";
                $params[] = "%" . trim($color) . "%";
            }

            if (!empty($status) && in_array(strtolower($status), ['available', 'sold'])) {
                $sql .= " AND v.vehicle_status = ?";
                $params[] = strtolower($status);
            }

            $sql .= " ORDER BY v.vehicle_id DESC";

            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetch_vehicle_by_id($id) {
        try {
            $sql = "SELECT * FROM vehicles WHERE vehicle_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function fetch_vehicle_images($vehicle_id) {
        try {
            $sql = "SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY img_id ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([(int)$vehicle_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetch_vehicle_videos($vehicle_id) {
        try {
            $sql = "SELECT * FROM vehicle_videos WHERE vehicle_id = ? ORDER BY video_id ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([(int)$vehicle_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

?>
