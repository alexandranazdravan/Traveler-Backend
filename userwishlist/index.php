<?php
namespace Traveler\Wishlist;

use Traveler\MariaDB\Database;
require_once dirname(__DIR__) . '/MariaDB/Database.php';
require 'Wishlist.php';

$wishlist = new Wishlist();
$database = new Database();
$conn = $database->getConn();
$wishlist->checkRequest($conn);

