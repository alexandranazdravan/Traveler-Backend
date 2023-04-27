<?php
namespace Traveler\Wishlist;
use DateTime;

class Wishlist {
    public function addToWishlist($conn, $flight, $user_id)
    {
        if (isset($flight['flight_number'])) {
           $this->addToWishlistV1($conn, $flight, $user_id);
        } else {
            $this->addToWishlistV2($conn, $flight, $user_id);
        }
    }

    private function addToWishlistV1($conn, $flight, $user_id)
    {
        $origin = $flight['origin'];
        $destination = $flight['destination'];
        $airline = $flight['airline'];
        $lowcost = $flight['is_lowcost'];
        $price = $flight['price'];
        $f_no = $flight['flight_number'];

        $depart_day_str = $flight['departure_at'];
        $date = DateTime::createFromFormat('d-m-Y', $depart_day_str);
        $depart_day = $date->format('Y-m-d');

        $depart_time = $flight['departure_time'];

        $return_day_str = $flight['return_at'];
        $date = DateTime::createFromFormat('d-m-Y', $return_day_str);
        $return_day = $date->format('Y-m-d');

        $return_time = $flight['return_time'];
        $select_query = "select * from `wishlist` where user_id = '$user_id' and destination = '$destination' 
                        and origin = '$origin' and depart_day = '$depart_day' and depart_time = '$depart_time'and
                        return_day = '$return_day' and  return_time = '$return_time' and airline = '$airline'
                        and flight_number = '$f_no' and price = '$price' and lowcost = '$lowcost'";
        $query = mysqli_query($conn, $select_query);
        $rows = mysqli_fetch_array($query);

        if ($rows == null) {
            $insert_query = "insert into `wishlist`(user_id, destination, origin, depart_day, depart_time,
                        return_day, return_time, airline, flight_number, price, lowcost)
                        values('$user_id','$destination','$origin', '$depart_day', '$depart_time', 
                        '$return_day', '$return_time', '$airline', '$f_no', '$price', '$lowcost')";
            $conn->query($insert_query);
        }
    }

    private function addToWishlistV2($conn, $flight, $user_id)
    {
        $origin = $flight['origin'];
        $destination = $flight['destination'];
        $class = $flight['class'];
        $no_of_changes = $flight['no_of_changes'];
        $price = $flight['price'];
        $duration = $flight['duration'];
        $distance = $flight['distance'];
        $depart_day = $flight['departure_at'];
        $return_day = $flight['return_at'];

        $select_query = "select * from `wishlist` where user_id = '$user_id' and destination = '$destination' 
                        and origin = '$origin' and depart_day = '$depart_day' and return_day = '$return_day' and  
                        class = '$class' and duration = '$duration' and no_of_changes = '$no_of_changes' and
                        distance = '$distance' and price = '$price'";
        $query = mysqli_query($conn, $select_query);
        $rows = mysqli_fetch_array($query);

        if ($rows == null) {
            $insert_query = "insert into `wishlist`(user_id, destination, origin, depart_day, return_day, class, 
                            duration, price, distance, no_of_changes, flag) values('$user_id','$destination','$origin', 
                            '$depart_day',  '$return_day', '$class', '$duration', '$price', '$distance', 
                            '$no_of_changes', 'v2')";
            $conn->query($insert_query);
        }
    }

    public function removeFromWishlist($conn, $flight, $user_id) {
        if (isset($flight['flight_number'])) {
            $this->removeFromWishlistV1($conn, $flight, $user_id);
        } else {
            $this->removeFromWishlistV2($conn, $flight, $user_id);
        }
    }

    public function getWishlist($conn, $user_id) {
        $select_query = "select * from `wishlist`where user_id = '$user_id'";
        $query = mysqli_query($conn, $select_query);
        $rows = [];
        while($row = mysqli_fetch_array($query)){
            $rows[] = $row;
        }
        echo json_encode($rows);
    }

    private function removeFromWishlistV1($conn, $flight, $user_id) {
        $origin = $flight['origin'];
        $destination = $flight['destination'];
        $airline = $flight['airline'];
        $lowcost = $flight['is_lowcost'];
        $price = $flight['price'];
        $f_no = $flight['flight_number'];

        $depart_day_str = $flight['departure_at'];
        $date = DateTime::createFromFormat('d-m-Y', $depart_day_str);
        $depart_day = $date->format('Y-m-d');

        $depart_time = $flight['departure_time'];

        $return_day_str = $flight['return_at'];
        $date = DateTime::createFromFormat('d-m-Y', $return_day_str);
        $return_day = $date->format('Y-m-d');

        $return_time = $flight['return_time'];

        $delete_query = "delete from `wishlist`where user_id = '$user_id' and destination = '$destination' 
                            and origin = '$origin' and depart_day = '$depart_day' and depart_time = '$depart_time'and
                            return_day = '$return_day' and  return_time = '$return_time' and airline = '$airline'
                            and flight_number = '$f_no' and price = '$price' and lowcost = '$lowcost'";
        $conn->query($delete_query);
    }

    private function removeFromWishlistV2($conn, $flight, $user_id) {
        $origin = $flight['origin'];
        $destination = $flight['destination'];
        $class = $flight['class'];
        $no_of_changes = $flight['no_of_changes'];
        $price = $flight['price'];
        $duration = $flight['duration'];
        $distance = $flight['distance'];
        $depart_day = $flight['depart_day'];

        $delete_query = "delete from `wishlist`where user_id = '$user_id' and destination = '$destination' 
                            and origin = '$origin' and depart_day = '$depart_day' and  class = '$class' and 
                            duration = '$duration' and distance = '$distance' and no_of_changes = '$no_of_changes' 
                        and price = '$price'";
        $conn->query($delete_query);
    }
}