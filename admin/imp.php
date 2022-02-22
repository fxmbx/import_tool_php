<?php
error_reporting(0);
// Include database class
include_once '../inc/db.php';
if (isset($_FILES['csv'])) {
    $admin = $_POST['admin'];
    $csv = array();
    $issues_paycodes = array();
    $uploadable = array();
    $row = 0;
    $table = $_POST['table'];

    if ($_FILES['csv']['error'] == 0) {
        $name = $_FILES['csv']['name'];
        $ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
        $type = $_FILES['csv']['type'];
        $tmpName = $_FILES['csv']['tmp_name'];

        if ($type === 'text/csv') {
            if (($handle = fopen($tmpName, 'r')) !== false) {
                set_time_limit(0);
                while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                    if ($data[0] == NULL) {
                        // add name if neccessary
                        array_push($issues_paycodes, array("id" => $data[0], "department" => $data[1]));
                        $row++;
                        echo 'got here 1';
                        continue;
                    } else {
                        // $paycode = Database::getInstance()->get_paycode($data[0]);
                        // array_push($csv, $paycode);
                        if ($table == 'Department') {
                            array_push($uploadable, array("id" => $data[0], "department" => $data[1]));
                            $row++;
                        }
                        // if (empty($paycode)) {
                        //     // add name if neccessary
                        //     $name = explode(' ', $data[2]);
                        //     $middle_name = ($name[2] == null || $name[2] == '.') ? "" : $name[2];
                        //     array_push($uploadable, array("paycode" => $data[0], "department" => $data[1], "surname" => str_replace("|", "", $name[0]), "first_name" => $name[1], "middle_name" => $middle_name, "phone_number" => $data[3], "email" => $data[4]));
                        //     $row++;
                        // } else {
                        //     // add name if neccessary
                        //     array_push($issues_paycodes, array("paycode" => $data[0], "fullname" => $data[2]));
                        //     $row++;

                        //     echo 'got here 3';
                        //     continue;
                        // }
                    }
                }
                fclose($handle);
            }
        }
    }


    $count = 0;
    foreach ($uploadable as $department) {
        $id = $department['id'];
        // $last = $department['surname'] == null ? " " : $department['surname'];
        // $first = $department['first_name'] == null ? " " : $department['first_name'];
        // $middle = $department['middle_name'] == null ? " " : $department['middle_name'];
        // $pnumber = $department['phone_number'];
        // $department = $department['department'];
        // $enrollment_year = '2021';
        // $email = $department['email'];
        // $hash1 = bin2hex(openssl_random_pseudo_bytes(4));
        // $hash = password_hash($hash1, PASSWORD_DEFAULT);
        $name = $department['department'];

        $newDepartment = Database::getInstance()->insert_department($id, $name);
        if ($newDepartment == 'Done') {
            $myfile = fopen("../csv/department_logs.txt", "a") or die("Unable to open file!");
            $txt = $count . " id: " . $id . " department: " . $name;
            fwrite($myfile, "\n" . $txt);
            fclose($myfile);
            $count++;
        } else {
            echo $id + "\n";
        }
    }
    if ($count == 0) {
    } else {
        echo $count;
    }
}
