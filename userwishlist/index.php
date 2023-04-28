<?php
namespace Traveler\Wishlist;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';
require 'Wishlist.php';

$wishlist = new Wishlist();
$database = new Database();
$conn = $database->getConn();
$wishlist->checkRequest($conn);
