<?php
namespace Traveler\Wishlist;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';
require 'Wishlist.php';

$wishlist = new Wishlist();
$database = new Database();
$conn = $database->getConn();

if(!isset($_COOKIE['loggedin'])) {
    $data = json_decode(file_get_contents('php://input'), true);


    $cookie = $data['cookie'];
}
else {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $cookie = $_COOKIE['loggedin'];
    }
}

$query = mysqli_query($conn, "select u.user_id from `users` u join auth_details a on u.user_name = a.user_name
WHERE a.cookie = '$cookie';");
$rows = mysqli_fetch_array($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wishlist->addToWishlist($conn, $data['flight'], $rows['user_id']);
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $wishlist->removeFromWishlist($conn, $data['flight'], $rows['user_id']);
} else  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $wishlist->getWishlist($conn, $rows['user_id']);
}