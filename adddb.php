<?php
// Koneksi ke database WordPress
define('DB_NAME', 'kiasedum_2024');
define('DB_USER', 'kiasedum_2024');
define('DB_PASSWORD', 'Admin@@123_');
define('DB_HOST', 'localhost');
$table_prefix = 'wppc_';

// Data user baru - DIUBAH DISINI
$new_username = 'adminkias';
$new_password = 'adminadmin';
$new_email = 'adminkias@example.com';
$role = 'administrator';

// Koneksi database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Fungsi hash password WordPress (tanpa dependensi WordPress)
function wp_hash_password_wordpress($password) {
    // Menggunakan password_hash (WordPress 5.2+)
    if (function_exists('password_hash')) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Fallback untuk versi lama: MD5 (WordPress lama)
    return md5($password);
}

// Hash password
$hashed_password = wp_hash_password_wordpress($new_password);

// Cek apakah username sudah ada
$check_sql = "SELECT ID FROM {$table_prefix}users WHERE user_login = ? OR user_email = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("ss", $new_username, $new_email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "Username '$new_username' atau email '$new_email' sudah terdaftar!\n";
    $check_stmt->close();
    $mysqli->close();
    exit;
}
$check_stmt->close();

// Insert user baru
$sql = "INSERT INTO {$table_prefix}users 
        (user_login, user_pass, user_nicename, user_email, user_registered, user_status, display_name) 
        VALUES (?, ?, ?, ?, NOW(), 0, ?)";

$stmt = $mysqli->prepare($sql);
$user_nicename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $new_username));
$display_name = 'Admin KIAS';

$stmt->bind_param("sssss", $new_username, $hashed_password, $user_nicename, $new_email, $display_name);

if ($stmt->execute()) {
    $user_id = $mysqli->insert_id;
    echo "User berhasil dibuat! ID: $user_id\n";
    
    // Tambah user meta (capabilities)
    $capabilities = serialize(array($role => true));
    
    $meta_sql = "INSERT INTO {$table_prefix}usermeta (user_id, meta_key, meta_value) VALUES (?, ?, ?)";
    $meta_stmt = $mysqli->prepare($meta_sql);
    
    // wp_capabilities
    $meta_key = $table_prefix . 'capabilities';
    $meta_stmt->bind_param("iss", $user_id, $meta_key, $capabilities);
    $meta_stmt->execute();
    
    // wp_user_level
    $user_level = 10; // Level untuk administrator
    $meta_key2 = $table_prefix . 'user_level';
    $meta_stmt->bind_param("iss", $user_id, $meta_key2, $user_level);
    $meta_stmt->execute();
    
    echo "===============================\n";
    echo "USER BERHASIL DITAMBAHKAN:\n";
    echo "===============================\n";
    echo "Username: $new_username\n";
    echo "Password: $new_password\n";
    echo "Email: $new_email\n";
    echo "Role: $role\n";
    echo "===============================\n";
    
    $meta_stmt->close();
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$stmt->close();
$mysqli->close();
?>
