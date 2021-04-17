<?php
require 'vendor/autoload.php';

use Carbon\Carbon;


define("HOST_NAME", "172.104.186.185");
define("USER_NAME", "inovace");
define("PASSWORD", "inovace360");
define("DATABASE_NAME", "walton_oeedb");

if (!$link = mysqli_connect(HOST_NAME, USER_NAME, PASSWORD)) {
    echo 'Could not connect to mysql, ' . mysqli_error();
    exit;
}
if (!mysqli_select_db($link, DATABASE_NAME)) {
    echo 'Could not select database';
    exit;
}

while (true) {
    $id = (int)file_get_contents('dump.lock');

    echo "Dump Lock : {$id}\n";

    $sql    = "SELECT * FROM production_log WHERE product_id=1 and id > {$id} ORDER BY id ASC";
    $result = mysqli_query($link, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        try {
            $syncDate = Carbon::parse($row['sync_datetime']);
            $prodTime = Carbon::parse($row['production_date'] . ' ' . $row['production_time']);

            $data = pack('CCCCCCCCCCCCC',
                $syncDate->year - 2000,
                $syncDate->month,
                $syncDate->day,
                $syncDate->hour,
                $syncDate->minute,
                $syncDate->second,

                $prodTime->year - 2000,
                $prodTime->month,
                $prodTime->day,
                $prodTime->hour,
                $prodTime->minute,
                $prodTime->second,
                $row['station_id'],
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://walton.api-inovace360.com/device/v1/logs");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/octet-stream']);

            file_put_contents('output.html', curl_exec($ch));

            file_put_contents('dump.lock', $row['id']);
            echo "LOG: {$row['id']} \n";

        } catch (Exception $ex) {
            var_dump($ex);
        }
    }
}
