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

    function storeVehicle($vehicleInput) {
        global $conn;

        $customerId = mysqli_real_escape_string($conn, $vehicleInput['customerId']);
        $type = mysqli_real_escape_string($conn, $vehicleInput['type']);
        $model = mysqli_real_escape_string($conn, $vehicleInput['model']);
        $year = mysqli_real_escape_string($conn, $vehicleInput['year']);
        $passengerCount = mysqli_real_escape_string($conn, $vehicleInput['passengerCount']);
        $manufacturer = mysqli_real_escape_string($conn, $vehicleInput['manufacturer']);
        $price = mysqli_real_escape_string($conn, $vehicleInput['price']);

        $fuelType = mysqli_real_escape_string($conn, $vehicleInput['fuelType']);
        $trunkSize = mysqli_real_escape_string($conn, $vehicleInput['trunkSize']);

        $wheelCount = mysqli_real_escape_string($conn, $vehicleInput['wheelCount']);
        $cargoAreaSize = mysqli_real_escape_string($conn, $vehicleInput['cargoAreaSize']);

        $luggageSize = mysqli_real_escape_string($conn, $vehicleInput['luggageSize']);
        $fuelCapacity = mysqli_real_escape_string($conn, $vehicleInput['fuelCapacity']);

        if ($type == "Car") {
            if (empty(trim($fuelType))) {
                return error422("Enter fuel type");
            } else if (empty(trim($trunkSize))) {
                return error422("Enter trunk size");
            }
        
        } else if ($type == 'Truck') {
            if (empty(trim($wheelCount))) {
                return error422("Enter wheel count");
            } else if (empty(trim($cargoAreaSize))) {
                return error422("Enter cargo area size");
            }
        

        } else if ($type == 'Motorcycle') {
            if (empty(trim($luggageSize))) {
                return error422("Enter luggage size");
            } else if (empty(trim($fuelCapacity))) {
                return error422("Enter fuel capacity");
            }
        
        }

        if (empty(trim($customerId))) {
            return error422("Enter customer ID");
        } else if (empty(trim($type))) {
            return error422("Enter vehicle type");
        } else if (empty(trim($model))) {
            return error422("Enter vehicle model");
        } else if (empty(trim($year))){
            return error422("Enter vehicle year");
        } else if (empty(trim($passengerCount))) {
            return error422("Enter vehicle passenger count");
        } else if (empty(trim($manufacturer))) {
            return error422("Enter vehicle manufacturer");
        } else if (empty(trim($price))) {
            return error422("Enter vehicle price");
        } 

        else {
            $query = "INSERT INTO vehicle (customerId, type, model, year, passengerCount, manufacturer, price) VALUES (' $customerId', '$type', '$model', '$year', '$passengerCount', '$manufacturer', '$price')";
            // echo $query;
            $result = mysqli_query($conn, $query);
            // echo $result;

            $vehicleId = $conn->insert_id;
            // echo $vehicleId;

            if ($type == "Car") {
                $query2 = "INSERT INTO car (vehicleId, fuelType, trunkSize) VALUES ('$vehicleId', '$fuelType', '$trunkSize')";
                echo $query2;
                $result = mysqli_query($conn, $query2);
            } else if ($type == 'Truck') {
                $query2 = "INSERT INTO truck (vehicleID, wheelCount, cargoAreaSize) VALUES ('$vehicleId', '$wheelCount', '$cargoAreaSize')";
                $result = mysqli_query($conn, $query2);
                echo $query2;
            } else if ($type == 'Motorcycle') {
                $query2 = "INSERT INTO motorcycle (vehicleID, luggageSize, fuelCapacity) VALUES ('$vehicleId', '$luggageSize', '$fuelCapacity')";
                $result = mysqli_query($conn, $query2);
                echo $query2;
            }

            if ($result) {
                $data = [
                    'status' => 201,
                    'message' => 'Vehicle Created Successfully',
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
    function getVehicleList() {
        global $conn;

        $query = "SELECT * FROM vehicle";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            if (mysqli_num_rows($query_run) > 0) {

                $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

                $data = [
                    'status' => 200,
                    'message' => 'Vehicle List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);

            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Vehicle Found',
                ];
                header("HTTP/1.0 404 No Vehicle Found");
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

    function getVehicle($vehicleParams) {
        global $conn;

        if ($vehicleParams['id'] == null) {
            return error422('Enter your vehicle id');
        }

        $vehicleId = mysqli_real_escape_string($conn, $vehicleParams['id']);

        $query = "SELECT * FROM vehicle WHERE id = '$vehicleId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result) {
            $res = mysqli_fetch_assoc($result);
            if (mysqli_num_rows($result) == 1) {
                

                $data = [
                    'status' => 200,
                    'message' => 'Vehicle Fetched Successfuly',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);

            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Vehicle Found',
                    'data' => $res
                ];
                header("HTTP/1.0 404 No Vehicle Found");
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

    function updateVehicle($vehicleInput, $vehicleParams) {
        global $conn;

        if (!isset($vehicleParams['id'])){
            return error422('Vehicle id not found in URL');
        } else if ($vehicleParams['id'] == null){
            return error422('Enter the customer id');
        }

        $vehicleId = mysqli_real_escape_string($conn, $vehicleParams['id']);

        $customerId = mysqli_real_escape_string($conn, $vehicleInput['customerId']);
        $type = mysqli_real_escape_string($conn, $vehicleInput['type']);
        $model = mysqli_real_escape_string($conn, $vehicleInput['model']);
        $year = mysqli_real_escape_string($conn, $vehicleInput['year']);
        $passengerCount = mysqli_real_escape_string($conn, $vehicleInput['passengerCount']);
        $manufacturer = mysqli_real_escape_string($conn, $vehicleInput['manufacturer']);
        $price = mysqli_real_escape_string($conn, $vehicleInput['price']);
        
        if (empty(trim($customerId))) {
            return error422("Enter customer ID");
        } else if (empty(trim($type))) {
            return error422("Enter vehicle type ");
        } else if (empty(trim($model))) {
            return error422("Enter vehicle model");
        } else if (empty(trim($year))){
            return error422("Enter vehicle year");
        } else if (empty(trim($passengerCount))) {
            return error422("Enter vehicle passenger count");
        } else if (empty(trim($manufacturer))) {
            return error422("Enter vehicle manufacturer");
        } else if (empty(trim($price))) {
            return error422("Enter vehicle price");
        }

        else {
            $query = "UPDATE vehicle SET customerId='$customerId', type='$type', model='$model', year='$year', passengerCount='$passengerCount', manufacturer='$manufacturer', price='$price' WHERE id='$vehicleId' LIMIT 1";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                $data = [
                    'status' => 200,
                    'message' => 'Customer Updated Successfully',
                    'data' => $result
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

    function deleteVehicle($vehicleParams) {
        global $conn;

        if (!isset($vehicleParams['id'])){
            return error422('Vehicle id not found in URL');
        } else if ($vehicleParams['id'] == null){
            return error422('Enter the vehicle id');
        }

        $vehicleID = mysqli_real_escape_string($conn, $vehicleParams['id']);

        $query = "DELETE FROM vehicle WHERE id='$vehicleID' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $data = [
                'status' => 200,
                'message' => 'Vehicle Deleted Successfully',
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);

        } else {
            $data = [
                'status' => 404,
                'message' => 'Vehicle Not Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }

    }

?>