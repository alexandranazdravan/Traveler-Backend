<?php
namespace Traveler\Admin;

class AdminGetUsers{
    public function __construct($conn) {
        $query = mysqli_query($conn, "select `user_id`, `user_name`, `user_fullname`, `user_email` from `users`;");
        $rows = array();

        while($row = mysqli_fetch_array($query)){
            if($row['user_name'] !== 'admin') {
                $rows[] = $row;
            }
        }
        $result = array("users" => $rows);
        echo "," . json_encode($result);
    }
}