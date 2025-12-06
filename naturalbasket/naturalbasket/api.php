<?php
session_start();
header('Content-Type: application/json');
// ডাটাবেস কানেকশন ফাইল ইনক্লুড করুন
include 'db_connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$method = $_SERVER['REQUEST_METHOD'];

// JSON ইনপুট হ্যান্ডলিং (POST রিকোয়েস্টের জন্য)
$input = json_decode(file_get_contents("php://input"), true);

// ======================================================
// 1. AUTHENTICATION (লগিন ও রেজিস্ট্রেশন)
// ======================================================

// রেজিস্ট্রেশন
if ($action == 'register' && $method === 'POST') {
    $name = $conn->real_escape_string($input['name']);
    $email = $conn->real_escape_string($input['email']);
    $password = password_hash($input['password'], PASSWORD_BCRYPT);
    $phone = $conn->real_escape_string($input['phone']);
    
    // ডুপ্লিকেট ইমেইল চেক
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) { 
        echo json_encode(["status" => "error", "message" => "ইমেইলটি ইতিমধ্যে ব্যবহৃত হয়েছে"]); 
        exit; 
    }

    $sql = "INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";
    if ($conn->query($sql)) echo json_encode(["status" => "success"]);
    else echo json_encode(["status" => "error", "message" => $conn->error]);
    exit;
}

// লগিন
if ($action == 'login' && $method === 'POST') {
    $email = $conn->real_escape_string($input['email']);
    $password = $input['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            echo json_encode(["status" => "success", "user" => ["name" => $user['name'], "role" => $user['role']]]);
        } else {
            echo json_encode(["status" => "error", "message" => "ভুল পাসওয়ার্ড"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ইউজার পাওয়া যায়নি"]);
    }
    exit;
}

// সেশন চেক
if ($action == 'check_session') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "logged_in", "user" => ["name" => $_SESSION['name'], "role" => $_SESSION['role']]]);
    } else {
        echo json_encode(["status" => "guest"]);
    }
    exit;
}

// লগআউট
if ($action == 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
    exit;
}

// ======================================================
// 2. PRODUCTS (পণ্য ব্যবস্থাপনা)
// ======================================================

// সব পণ্য দেখা
if ($action == 'get_products') {
    $result = $conn->query("SELECT p.*, (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating FROM products p ORDER BY id DESC");
    $products = [];
    while($row = $result->fetch_assoc()) $products[] = $row;
    echo json_encode($products);
    exit;
}

// পণ্য যোগ করা (Admin Only)
if ($action == 'add_product' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error", "message" => "Access Denied"]); exit; }
    
    // Form Data রিড করা
    $name = $_POST['name'] ?? '';
    $cat = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $weight = $_POST['weight'] ?? '';
    $desc = $_POST['description'] ?? '';
    $img = $_POST['image_url'] ?? 'https://placehold.co/400x400?text=No+Image';

    // ফাইল আপলোড হ্যান্ডলিং
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $filename = time() . "_" . uniqid() . "." . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $img = $target_file;
        }
    }

    $name = $conn->real_escape_string($name);
    $cat = $conn->real_escape_string($cat);
    $weight = $conn->real_escape_string($weight);
    $img = $conn->real_escape_string($img);
    $desc = $conn->real_escape_string($desc);

    $conn->query("INSERT INTO products (name, category, price, weight, image, description) VALUES ('$name', '$cat', '$price', '$weight', '$img', '$desc')");
    echo json_encode(["status" => "success"]);
    exit;
}

// পণ্য ডিলিট করা (Admin Only)
if ($action == 'delete_product' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }
    $id = $input['id'];
    $conn->query("DELETE FROM products WHERE id=$id");
    echo json_encode(["status" => "success"]);
    exit;
}

// ======================================================
// 3. REVIEWS (রিভিউ)
// ======================================================

// নির্দিষ্ট পণ্যের রিভিউ পাওয়া
if ($action == 'get_reviews' && isset($_GET['id'])) {
    $pid = $_GET['id'];
    $result = $conn->query("SELECT * FROM reviews WHERE product_id = $pid ORDER BY id DESC");
    $reviews = [];
    while($row = $result->fetch_assoc()) $reviews[] = $row;
    echo json_encode($reviews);
    exit;
}

// রিভিউ যোগ করা
if ($action == 'add_review' && $method === 'POST') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(["status" => "error", "message" => "লগিন করুন"]); exit; }
    
    $pid = $input['product_id'];
    $rating = $input['rating'];
    $comment = $conn->real_escape_string($input['comment']);
    $uid = $_SESSION['user_id'];
    $uname = $_SESSION['name'];

    $conn->query("INSERT INTO reviews (product_id, user_id, user_name, rating, comment) VALUES ('$pid', '$uid', '$uname', '$rating', '$comment')");
    echo json_encode(["status" => "success"]);
    exit;
}

// ======================================================
// 4. BLOGS & COUPONS (ব্লগ ও কুপন)
// ======================================================

// ব্লগ লিস্ট
if ($action == 'get_blogs') {
    $result = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
    $blogs = [];
    while($row = $result->fetch_assoc()) $blogs[] = $row;
    echo json_encode($blogs);
    exit;
}

// কুপন চেক
if ($action == 'check_coupon' && $method === 'POST') {
    $code = $conn->real_escape_string($input['code']);
    $res = $conn->query("SELECT * FROM coupons WHERE code='$code' AND status='active'");
    if ($res->num_rows > 0) {
        echo json_encode(["status" => "success", "discount" => $res->fetch_assoc()['discount_percent']]);
    } else {
        echo json_encode(["status" => "error", "message" => "অবৈধ কুপন"]);
    }
    exit;
}

// ======================================================
// 5. ORDERS (অর্ডার ব্যবস্থাপনা)
// ======================================================

// অর্ডার প্লেস করা
if ($action == 'place_order' && $method === 'POST') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(["status" => "error", "message" => "লগিন করুন"]); exit; }
    
    $user_id = $_SESSION['user_id'];
    $name = $conn->real_escape_string($input['name']); 
    $phone = $conn->real_escape_string($input['phone']);
    $address = $conn->real_escape_string($input['address']);
    $payment = $conn->real_escape_string($input['payment']);
    $total = $input['total'];
    $discount = $input['discount'] ?? 0;

    // মেইন অর্ডার টেবিল ইনসার্ট
    $conn->query("INSERT INTO orders (user_id, customer_name, phone, address, total_amount, discount_amount, payment_method) VALUES ('$user_id', '$name', '$phone', '$address', '$total', '$discount', '$payment')");
    $order_id = $conn->insert_id;

    // অর্ডার আইটেম ইনসার্ট (লুপ)
    foreach ($input['items'] as $item) {
        $p_id = $item['id'];
        $p_name = $conn->real_escape_string($item['name']);
        $qty = $item['qty'];
        $price = $item['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ('$order_id', '$p_id', '$p_name', '$qty', '$price')");
    }
    
    echo json_encode(["status" => "success", "order_id" => $order_id]);
    exit;
}

// অর্ডারের লিস্ট পাওয়া (এডমিন হলে সব, ইউজার হলে শুধু তার নিজের)
if ($action == 'get_orders') {
    if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }

    $sql = "";
    if ($_SESSION['role'] === 'admin') {
        $sql = "SELECT * FROM orders ORDER BY id DESC";
    } else {
        $uid = $_SESSION['user_id'];
        $sql = "SELECT * FROM orders WHERE user_id = $uid ORDER BY id DESC";
    }
    
    $result = $conn->query($sql);
    $orders = [];
    while($row = $result->fetch_assoc()) $orders[] = $row;
    echo json_encode($orders);
    exit;
}

// অর্ডারের ডিটেইলস দেখা (পণ্য সহ) - Admin Only
if ($action == 'get_order_details' && isset($_GET['id'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }
    
    $id = $_GET['id'];
    
    // মেইন অর্ডার তথ্য
    $orderQuery = $conn->query("SELECT * FROM orders WHERE id = $id");
    $order = $orderQuery->fetch_assoc();
    
    // আইটেমগুলো আনা
    $itemsQuery = $conn->query("SELECT * FROM order_items WHERE order_id = $id");
    $items = [];
    while($row = $itemsQuery->fetch_assoc()) $items[] = $row;
    
    $order['items'] = $items;
    echo json_encode($order);
    exit;
}

// অর্ডার স্ট্যাটাস আপডেট (Admin Only)
if ($action == 'update_status' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }
    
    $id = $input['id'];
    $status = $input['status'];
    $conn->query("UPDATE orders SET status='$status' WHERE id=$id");
    echo json_encode(["status" => "success"]);
    exit;
}

// অর্ডার ডিলিট করা (Admin Only)
if ($action == 'delete_order' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }
    
    $id = $input['id'];
    $conn->query("DELETE FROM orders WHERE id=$id"); // Cascade Delete এর কারণে আইটেমও ডিলিট হবে
    echo json_encode(["status" => "success"]);
    exit;
}

// ======================================================
// 6. USERS (কাস্টমার লিস্ট - Admin Only)
// ======================================================

if ($action == 'get_users') {
    // Security check: only admin can see user list
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
        echo json_encode([]); 
        exit; 
    }

    $result = $conn->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC");
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
    exit;
}

$conn->close();
// ======================================================
// 7. BLOG MANAGEMENT (Admin Only)
// ======================================================

// ব্লগ যোগ করা
if ($action == 'add_blog' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error", "message" => "Access Denied"]); exit; }

    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $content = $conn->real_escape_string($_POST['content']);
    $image = 'https://placehold.co/600x400/green/white?text=Blog'; // ডিফল্ট ইমেজ

    // ইমেজ আপলোড
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $filename = time() . "_blog_" . uniqid() . "." . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }

    $sql = "INSERT INTO blogs (title, author, content, image) VALUES ('$title', '$author', '$content', '$image')";
    
    if ($conn->query($sql)) echo json_encode(["status" => "success"]);
    else echo json_encode(["status" => "error", "message" => $conn->error]);
    exit;
}

// ব্লগ ডিলিট করা
if ($action == 'delete_blog' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }
    
    $id = $input['id'];
    $conn->query("DELETE FROM blogs WHERE id=$id");
    echo json_encode(["status" => "success"]);
    exit;
}
// ======================================================
// 8. UPDATE PRODUCT (পণ্য এডিট করা - Admin Only)
// ======================================================
if ($action == 'update_product' && $method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { echo json_encode(["status" => "error"]); exit; }

    $id = $_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = $_POST['price'];
    $weight = $conn->real_escape_string($_POST['weight']);
    $desc = $conn->real_escape_string($_POST['description']);
    
    // প্রথমে ইমেজ ছাড়া আপডেট কুয়েরি তৈরি করি
    $sql = "UPDATE products SET name='$name', category='$cat', price='$price', weight='$weight', description='$desc' WHERE id=$id";

    // যদি নতুন ছবি আপলোড করা হয়
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $filename = time() . "_" . uniqid() . "." . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // ছবি সহ আপডেট
            $sql = "UPDATE products SET name='$name', category='$cat', price='$price', weight='$weight', description='$desc', image='$target_file' WHERE id=$id";
        }
    }

    if ($conn->query($sql)) echo json_encode(["status" => "success"]);
    else echo json_encode(["status" => "error", "message" => $conn->error]);
    exit;
}
?>