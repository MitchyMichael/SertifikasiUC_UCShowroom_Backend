<?php

    require '../inc/dbcon.php';

    function error422($message) {
        $data = [
            'status' => 422,
            'message' => $message
        ];
        header("HTTP/1.0 422 Unprocessable Entity");
        echo json_encode($data);
        exit();
    }

    function storeCustomer($customerInput) {
        global $conn;

        $name = mysqli_real_escape_string($conn, $customerInput['name']);
        $address = mysqli_real_escape_string($conn, $customerInput['address']);
        $phoneNumber = mysqli_real_escape_string($conn, $customerInput['phoneNumber']);
        $idCard = mysqli_real_escape_string($conn, $customerInput['idCard']);
        
        if (empty(trim($name))) {
            return error422("Enter your name");
        } else if (empty(trim($address))){
            return error422("Enter your address");
        } else if (empty(trim($phoneNumber))) {
            return error422("Enter your phone number");
        } else if (empty(trim($idCard))) {
            return error422("Enter your id card");
        } 

        else {
            $query = "INSERT INTO customers (name, address, phoneNumber, idCard) VALUES ('$name', '$address', '$phoneNumber', '$idCard')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $data = [
                    'status' => 201,
                    'message' => 'Customer Created Successfully',
                ];
                header("HTTP/1.0 201 Created");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                ];
                header("HTTP/1.0 500 Internal Server Error");
                return json_encode($data);
            }
        }
    }
    function getCustomerList() {
        global $conn;

        $query = "SELECT * FROM customers";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            if (mysqli_num_rows($query_run) > 0) {

                $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

                $data = [
                    'status' => 200,
                    'message' => 'Customer List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);

            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Customer Found',
                ];
                header("HTTP/1.0 404 No Customer Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }

    function getCustomer($customerParams) {
        global $conn;

        if ($customerParams['id'] == null) {
            return error422('Enter your customer id');
        }

        $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

        $query = "SELECT * FROM customers WHERE id = '$customerId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result) {
            $res = mysqli_fetch_assoc($result);
            if (mysqli_num_rows($result) == 1) {
                

                $data = [
                    'status' => 200,
                    'message' => 'Customer Fetched Successfuly',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);

            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Customer Found',
                    'data' => $res
                ];
                header("HTTP/1.0 404 No Customer Found");
                return json_encode($data);
            }

        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }

    function getTotalPriceByCustomerId($customerParams) {
        global $conn;

        if ($customerParams['id'] == null) {
            return error422('Enter your customer id');
        }

        $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

        $query = "SELECT SUM(price) AS total_price FROM vehicle WHERE customerId = $customerId";
        $result = mysqli_query($conn, $query);

        if($result) {
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (mysqli_num_rows($result)) {
                

                $data = [
                    'status' => 200,
                    'message' => 'Price Fetched Successfuly',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);

            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Price Found',
                    'data' => $res
                ];
                header("HTTP/1.0 404 No Price Found");
                return json_encode($data);
            }

        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }

    function updateCustomer($customerInput, $customerParams) {
        global $conn;

        if (!isset($customerParams['id'])){
            return error422('Customer id not found in URL');
        } else if ($customerParams['id'] == null){
            return error422('Enter the customer id');
        }

        $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

        $name = mysqli_real_escape_string($conn, $customerInput['name']);
        $address = mysqli_real_escape_string($conn, $customerInput['address']);
        $phoneNumber = mysqli_real_escape_string($conn, $customerInput['phoneNumber']);
        $idCard = mysqli_real_escape_string($conn, $customerInput['idCard']);
        
        if (empty(trim($name))) {
            return error422("Enter your name");
        } else if (empty(trim($address))){
            return error422("Enter your address");
        } else if (empty(trim($phoneNumber))) {
            return error422("Enter your phone number");
        } else if (empty(trim($idCard))) {
            return error422("Enter your id card");
        }

        else {
            $query = "UPDATE customers SET name='$name', address='$address', phoneNumber='$phoneNumber', idCard='$idCard' WHERE id='$customerId' LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $data = [
                    'status' => 200,
                    'message' => 'Customer Updated Successfully',
                ];
                header("HTTP/1.0 200 Success");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                ];
                header("HTTP/1.0 500 Internal Server Error");
                return json_encode($data);
            }
        }
    }

    function deleteCustomer($customerParams) {
        global $conn;

        if (!isset($customerParams['id'])){
            return error422('Customer id not found in URL');
        } else if ($customerParams['id'] == null){
            return error422('Enter the customer id');
        }

        $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

        $query = "DELETE FROM customers WHERE id='$customerId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        $query2 = "DELETE FROM vehicle WHERE vehicle.customer='$customerId'";
        $result2 = mysqli_query($conn, $query2);

        if ($result || $result2) {

            $data = [
                'status' => 200,
                'message' => 'Customer Deleted Successfully',
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);

        } else {
            $data = [
                'status' => 404,
                'message' => 'Customer Not Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }

    }

?>