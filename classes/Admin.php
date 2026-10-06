<?php

require_once "Db.php";

class Admin extends Db {

    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function login($email, $pass1) {
        $email = strtolower(trim($email));
        $pass1 = trim($pass1);

        if (empty($email) || empty($pass1)) {
            return "Input your email and password";
        }

        try {
            $sql = "SELECT * FROM admin WHERE LOWER(TRIM(admin_email)) = LOWER(?)";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$email]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $stored_hash = $data['admin_pwd'];
                $chk = password_verify($pass1, $stored_hash);
                if ($chk == false) {
                    return "Invalid Password";
                } else {
                    return $data['admin_id'];
                }
            } else {
                return "Invalid Email";
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function fetch_adminid($id) {
        try {
            $sql = "SELECT * FROM admin WHERE admin_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$id]);
            $rsp = $stmt->fetch(PDO::FETCH_ASSOC);
            return $rsp;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function signup($fullname, $email, $pass1) {
        $fullname = trim($fullname);
        $email = strtolower(trim($email));
        $pass1 = trim($pass1);

        if (empty($fullname) || empty($email) || empty($pass1)) {
            return "Full name, email, and password are required.";
        }

        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO admin(admin_fullname, admin_email, admin_pwd) VALUES(?,?,?)";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$fullname, $email, $hash]);
            $id = $this->dbconn->lastInsertId();
            return $id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function upload_vehicle($name, $year, $color, $price, $entry_year, $condition, $features, $files, $status = 'available') {
        try {
            // Encode features array as JSON
            $features_json = !empty($features) ? json_encode(array_filter(array_map('trim', $features))) : null;
            $status = in_array(strtolower($status), ['available', 'sold']) ? strtolower($status) : 'available';

            // Insert vehicle record with all fields
            $sql = "INSERT INTO vehicles (vehicle_name, vehicle_year, vehicle_color, vehicle_price, vehicle_entry_year, vehicle_condition, vehicle_features, vehicle_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([
                $name,
                (int)$year,
                $color,
                !empty($price) ? (float)$price : null,
                !empty($entry_year) ? (int)$entry_year : null,
                !empty($condition) ? $condition : null,
                $features_json,
                $status
            ]);
            $vehicle_id = $this->dbconn->lastInsertId();

            if (!$vehicle_id) {
                return "Failed to save vehicle information.";
            }

            // Handle multiple image uploads
            if (isset($files['vehicle_pics']) && !empty($files['vehicle_pics']['name'][0])) {
                $target_dir = __DIR__ . "/../uploads/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $total_files = count($files['vehicle_pics']['name']);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                for ($i = 0; $i < $total_files; $i++) {
                    $file_name = $files['vehicle_pics']['name'][$i];
                    $file_tmp = $files['vehicle_pics']['tmp_name'][$i];
                    $file_error = $files['vehicle_pics']['error'][$i];

                    if ($file_error === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        if (in_array($ext, $allowed_extensions)) {
                            $new_file_name = "car_" . time() . "_" . uniqid() . "." . $ext;
                            $target_file = $target_dir . $new_file_name;

                            if (move_uploaded_file($file_tmp, $target_file)) {
                                $sql_img = "INSERT INTO vehicle_images (vehicle_id, img_name) VALUES (?, ?)";
                                $stmt_img = $this->dbconn->prepare($sql_img);
                                $stmt_img->execute([$vehicle_id, $new_file_name]);
                            }
                        }
                    }
                }
            }

            // Handle multiple video uploads
            if (isset($files['vehicle_videos']) && !empty($files['vehicle_videos']['name'][0])) {
                $target_dir = __DIR__ . "/../uploads/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $total_vid_files = count($files['vehicle_videos']['name']);
                $allowed_vid_extensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi'];

                for ($i = 0; $i < $total_vid_files; $i++) {
                    $file_name = $files['vehicle_videos']['name'][$i];
                    $file_tmp  = $files['vehicle_videos']['tmp_name'][$i];
                    $file_error = $files['vehicle_videos']['error'][$i];

                    if ($file_error === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        if (in_array($ext, $allowed_vid_extensions)) {
                            $new_vid_name = "vid_" . time() . "_" . uniqid() . "." . $ext;
                            $target_file = $target_dir . $new_vid_name;

                            if (move_uploaded_file($file_tmp, $target_file)) {
                                $sql_vid = "INSERT INTO vehicle_videos (vehicle_id, video_name) VALUES (?, ?)";
                                $stmt_vid = $this->dbconn->prepare($sql_vid);
                                $stmt_vid->execute([$vehicle_id, $new_vid_name]);
                            }
                        }
                    }
                }
            }

            return true;
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        }
    }

    public function update_vehicle($vehicle_id, $name, $year, $color, $price, $entry_year, $condition, $features) {
        try {
            $features_json = !empty($features) ? json_encode(array_filter(array_map('trim', $features))) : null;
            $sql = "UPDATE vehicles SET vehicle_name = ?, vehicle_year = ?, vehicle_color = ?, vehicle_price = ?, vehicle_entry_year = ?, vehicle_condition = ?, vehicle_features = ? WHERE vehicle_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([
                $name,
                (int)$year,
                $color,
                !empty($price) ? (float)$price : null,
                !empty($entry_year) ? (int)$entry_year : null,
                !empty($condition) ? $condition : null,
                $features_json,
                (int)$vehicle_id
            ]);
            return true;
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        }
    }

    public function add_vehicle_images($vehicle_id, $files) {
        if (!isset($files['vehicle_pics']) || empty($files['vehicle_pics']['name'][0])) {
            return true;
        }

        try {
            $target_dir = __DIR__ . "/../uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $total_files = count($files['vehicle_pics']['name']);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            for ($i = 0; $i < $total_files; $i++) {
                $file_name = $files['vehicle_pics']['name'][$i];
                $file_tmp = $files['vehicle_pics']['tmp_name'][$i];
                $file_error = $files['vehicle_pics']['error'][$i];

                if ($file_error === UPLOAD_ERR_OK && !empty($file_tmp)) {
                    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed_extensions, true)) {
                        $new_file_name = "car_" . time() . "_" . uniqid() . "." . $ext;
                        $target_file = $target_dir . $new_file_name;
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $sql_img = "INSERT INTO vehicle_images (vehicle_id, img_name) VALUES (?, ?)";
                            $stmt_img = $this->dbconn->prepare($sql_img);
                            $stmt_img->execute([(int)$vehicle_id, $new_file_name]);
                        }
                    }
                }
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_vehicle_videos($vehicle_id, $files) {
        if (!isset($files['vehicle_videos']) || empty($files['vehicle_videos']['name'][0])) {
            return true;
        }

        try {
            $target_dir = __DIR__ . "/../uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $total_vid_files = count($files['vehicle_videos']['name']);
            $allowed_vid_extensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi'];

            for ($i = 0; $i < $total_vid_files; $i++) {
                $file_name = $files['vehicle_videos']['name'][$i];
                $file_tmp  = $files['vehicle_videos']['tmp_name'][$i];
                $file_error = $files['vehicle_videos']['error'][$i];

                if ($file_error === UPLOAD_ERR_OK && !empty($file_tmp)) {
                    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed_vid_extensions, true)) {
                        $new_vid_name = "vid_" . time() . "_" . uniqid() . "." . $ext;
                        $target_file = $target_dir . $new_vid_name;
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $sql_vid = "INSERT INTO vehicle_videos (vehicle_id, video_name) VALUES (?, ?)";
                            $stmt_vid = $this->dbconn->prepare($sql_vid);
                            $stmt_vid->execute([(int)$vehicle_id, $new_vid_name]);
                        }
                    }
                }
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete_vehicle_image($img_id) {
        try {
            $sql = "SELECT img_name FROM vehicle_images WHERE img_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([(int)$img_id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$image) {
                return "Image not found.";
            }

            $file_path = __DIR__ . "/../uploads/" . $image['img_name'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }

            $sql_delete = "DELETE FROM vehicle_images WHERE img_id = ?";
            $stmt_delete = $this->dbconn->prepare($sql_delete);
            $stmt_delete->execute([(int)$img_id]);
            return true;
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        }
    }

    public function delete_vehicle_video($video_id) {
        try {
            $sql = "SELECT video_name FROM vehicle_videos WHERE video_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([(int)$video_id]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$video) {
                return "Video not found.";
            }

            $file_path = __DIR__ . "/../uploads/" . $video['video_name'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }

            $sql_delete = "DELETE FROM vehicle_videos WHERE video_id = ?";
            $stmt_delete = $this->dbconn->prepare($sql_delete);
            $stmt_delete->execute([(int)$video_id]);
            return true;
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        }
    }

    public function update_vehicle_status($vehicle_id, $status) {
        $status = strtolower(trim($status));
        if (!in_array($status, ['available', 'sold'])) {
            return "Invalid status specified.";
        }

        try {
            $sql = "UPDATE vehicles SET vehicle_status = ? WHERE vehicle_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $res = $stmt->execute([$status, (int)$vehicle_id]);
            return $res ? true : "Failed to update status.";
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        }
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

    public function delete_vehicle($vehicle_id) {
        try {
            $target_dir = __DIR__ . "/../uploads/";

            // First fetch images to delete files from disk
            $sql_fetch = "SELECT img_name FROM vehicle_images WHERE vehicle_id = ?";
            $stmt_fetch = $this->dbconn->prepare($sql_fetch);
            $stmt_fetch->execute([$vehicle_id]);
            $images = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

            foreach ($images as $img) {
                $file_path = $target_dir . $img['img_name'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            // Fetch videos to delete files from disk
            $sql_fetch_vid = "SELECT video_name FROM vehicle_videos WHERE vehicle_id = ?";
            $stmt_fetch_vid = $this->dbconn->prepare($sql_fetch_vid);
            $stmt_fetch_vid->execute([$vehicle_id]);
            $videos = $stmt_fetch_vid->fetchAll(PDO::FETCH_ASSOC);

            foreach ($videos as $vid) {
                $file_path = $target_dir . $vid['video_name'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            // Delete vehicle record (cascade deletes vehicle_images and vehicle_videos)
            $sql = "DELETE FROM vehicles WHERE vehicle_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$vehicle_id]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function logout() {
        unset($_SESSION['adminonline']);
        session_destroy();
    }
}

// $admin = new Admin();
// $admins = $admin->signup('Yankee', 'samprimeglobalenterprises@gmail.com 
// ', 'SamPrime15_');
// echo $admins;

?>
