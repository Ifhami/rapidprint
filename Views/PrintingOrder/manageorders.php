<?php
include '../../public/includes/db_connect.php';


// Fetch Orders
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT o.Order_ID, c.CustomerName, o.Status, o.Total_Cost 
              FROM `Order` o 
              JOIN Customer c ON o.CustomerID = c.CustomerID";
    $result = mysqli_query($conn, $query);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($orders);
}

// Update Order Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $query = "UPDATE `Order` SET Status = '$status' WHERE Order_ID = '$order_id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Order updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating order.']);
    }
}
?>