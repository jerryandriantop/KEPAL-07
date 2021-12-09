<?php
session_start();

class Transaction
{
    public $dbconn;

    public function __construct($dbconn = null)
    {
        $this->dbconn = mysqli_connect("localhost", "root", "", "electricks");
    }

    public function store_order($data)
    {
        $query = "INSERT INTO `order` (user_id, order_details_id) VALUES ('" . $data['user_id'] . "', '" . $data[`order_details_id`] . "')";
        $result = mysqli_query($this->dbconn, $query);
        return $result;
    }

    public function get_transaction()
    {
        $query = mysqli_query($this->dbconn, "SELECT * FROM `users` WHERE user_id='" . $_SESSION['id'] . "'");
        $row = mysqli_fetch_array($query);
        return $row;
    }
}
