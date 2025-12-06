<?php
include 'db_connect.php';

// পাসওয়ার্ড যা আপনি চান
$new_password = "123456"; 
// পাসওয়ার্ডটি এনক্রিপ্ট (Hash) করা হচ্ছে
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

$email = "admin@gmail.com";
$name = "Super Admin";

// ১. আগের অ্যাডমিন ডিলিট করা (যাতে ডুপ্লিকেট না হয়)
$conn->query("DELETE FROM users WHERE email='$email'");

// ২. নতুন অ্যাডমিন তৈরি করা
$sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "<div style='font-family: sans-serif; text-align: center; padding: 50px;'>
            <h1 style='color: green;'>✅ সফল হয়েছে!</h1>
            <p>অ্যাডমিন রিসেট করা হয়েছে।</p>
            <p><strong>ইমেইল:</strong> $email</p>
            <p><strong>পাসওয়ার্ড:</strong> $new_password</p>
            <br>
            <a href='index.php' style='background: #166534; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>লগিন করতে এখানে ক্লিক করুন</a>
          </div>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>