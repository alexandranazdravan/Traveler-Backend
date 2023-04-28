<?php
namespace Traveler\Admin;

class AdminGetUsers {

    private array $uri;
    private array $data;
    public function getUsers($conn) {
        $query = mysqli_query($conn, "select `user_id`, `user_name`, `user_fullname`, `user_email`, `user_avatar` from `users`;");
        $rows = array();

        while($row = mysqli_fetch_array($query)){
            if($row['user_name'] !== 'admin') {
                $rows[] = $row;
            }
        }
        $result = array("users" => $rows);
        echo "," . json_encode($result);
    }

    public function setParams($uri, $data) {
        $this->uri = $uri;
        if($data != null) {
            $this->data = $data;
        }
        return true;
    }

    public function checkIfAdmin($conn) {
        if (isset($_COOKIE['loggedin'])) {
            $cookie = $_COOKIE['loggedin'];
        } else {
            if (isset($this->data['cookie'])) {
                $cookie = $this->data['cookie'];
            } else {
                echo json_encode(array('isAdmin' => false));
                exit();
            }
        }
        $query = mysqli_query($conn, "select * from `auth_details` where user_name='admin'");

        if (mysqli_num_rows($query) == 0) {
            echo json_encode(array('isAdmin' => false));
            exit();
        } else {
            $row = mysqli_fetch_array($query);
            $admin_cookie = $row['cookie'];
            if ($admin_cookie == $cookie) {
                echo json_encode(array('isAdmin' => true));
            } else {
                echo json_encode(array('isAdmin' => false));
                exit();
            }
        }
    }

    public function checkRequest($conn) {
        if (isset($this->uri[3])) {
            if ($this->uri[3] == 'read') {
                $this->getUsers($conn);
            } else if ($this->uri[3] == 'delete') {
                $id = $this->data['user_id'];
                $query = "delete from `users` where `user_id`='$id';";
                $conn->query($query);
            } else if ($this->uri[3] == 'create') {
                $user_name = $this->data['user_name'];
                $user_fullname = isset($this->data['user_fullname']) ? $this->data['user_fullname'] : '';
                $user_email = $this->data['user_email'];
                $pass = $this->data['user_pass'];
                $user_pass = password_hash($pass, PASSWORD_DEFAULT);
                $query = "insert into `users` (user_name, user_email, user_fullname, user_pass) values ('$user_name', '$user_email', '$user_fullname', '$user_pass');";
                $conn->query($query);
            } else if ($this->uri[3] == 'update') {
                $user_name = $this->data['user_name'];
                $user_fullname = $this->data['user_fullname'];
                $user_email = $this->data['user_email'];
                $user_id = $this->data['user_id'];
                $user_avatar = $this->data['user_avatar'];
                $query = "update `users` set user_name='$user_name', user_email='$user_email', user_fullname='$user_fullname', user_avatar='$user_avatar' where user_id = '$user_id';";
                $conn->query($query);
            }
        }
    }
}