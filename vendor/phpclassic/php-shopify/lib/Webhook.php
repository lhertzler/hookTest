<?php
/**
 * Created by PhpStorm.
 * @author Tareq Mahmood <tareqtms@yahoo.com>
 * Created at: 9/10/16 10:52 AM UTC+06:00
 */

namespace PHPShopify;


class Webhook extends SimpleResource
{

    private $response = array();

    public function receiveData() {
        $data = file_get_contents("php://input");
        if(!$data){echo 'no data';return;}
        $jsonArray = json_decode($data, true);
        //file_put_contents('tmp/test.txt', $jsonArray);
        var_dump($jsonArray);
        header("HTTP/1.1 202 OK");
        echo $data;
        return $jsonArray;
    }

    public function openConn(){
        $dbhost = 'us-cdbr-iron-east-01.cleardb.net';
        $dbusername = 'bd46883aed1e23';
        $dbpassword = '4a6f8fd6';
        $dbname = 'heroku_b8eb62d00a21f06';

        // Connect
        $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    public function subscriptionHook(){
        $url_webhook = "https://4f4db15a.ngrok.io/";
        // The data to send to the API
        $postData = array(
                "address" => $url_webhook,
                "topic" => "subscription/created",
                "format" => "json"
        );

        // Create the context for the request
        $context = stream_context_create(array(
            'http' => array(
            // http://www.php.net/manual/en/context.http.php
            'method' => 'POST',
            'header' => "x-recharge-access-token: yeD4I03tSNeUiTEKXbcmdCuNCdEUVM\r\n".
            "Content-Type: application/json\r\n",
            'content' => json_encode($postData)
            )
        ));

        // Send the request
        $response = file_get_contents('https://api.rechargeapps.com/webhooks', FALSE, $context);

        echo $response;
    }

    public function chargeHook(){
        $url_webhook = "https://4f4db15a.ngrok.io/";
        // The data to send to the API
        $postData = array(
                "address" => $url_webhook,
                "topic" => "charge/created",
                "format" => "json"
        );

        // Create the context for the request
        $context = stream_context_create(array(
            'http' => array(
            // http://www.php.net/manual/en/context.http.php
            'method' => 'POST',
            'header' => "x-recharge-access-token: yeD4I03tSNeUiTEKXbcmdCuNCdEUVM\r\n".
            "Content-Type: application/json\r\n",
            'content' => json_encode($postData)
            )
        ));

        // Send the request
        $response = file_get_contents('https://api.rechargeapps.com/webhooks', FALSE, $context);

        echo $response;
    }

    public function newCharge(){
        
        $data = receiveData();
        $customerId = $data['charge']['customer_id'];

        $conn = openConn();

        $orderInfo = "SELECT * FROM orders WHERE customer_id=$customerId";
        $subscriptionInfo = "SELECT * FROM subscriptions INNER JOIN orders ON subscriptions.customer_id=orders.customer_id";
        
        $orderInfo        = $conn->query($orderInfo);
        $subscriptionInfo = $conn->query($subscriptionInfo);

        $created_at               = $orderInfo['created_at'];
        $updated_at               = $orderInfo['updated_at'];
        $next_charge_date         = $orderInfo['next_charge_scheduled_at'];
        $subscription_recharge_id = $orderInfo['subscription_recharge_id'];
        $shopify_order_id         = $orderInfo['shopify_order_id'];
        $status_response          = $orderInfo['status_response'];

        
        $subscription_name        = $subscriptionInfo['subscription_name'];
        $total_boxes              = $subscriptionInfo['total_boxes'];
        $current_box              = $subscriptionInfo['current_box'];
        $box_1                    = $subscriptionInfo['box_1'];
        $box_2                    = $subscriptionInfo['box_2'];
        $box_3                    = $subscriptionInfo['box_3'];
        $box_4                    = $subscriptionInfo['box_4'];
        $box_5                    = $subscriptionInfo['box_5'];


        // Get next box number
        $new_box       = $current_box + 1;
        $new_box_date  = $current_box + 1;
        $next_box      = $current_box + 2;
        $next_box_date = $current_box + 2;

        if ($next_box > $total_boxes) {
            return;
        } else {
            $new_box = 'box_'.$new_box;
            $new_box_date = 'box_'.$new_box.'_date';
            $new_subscripion_name = explode(' ', $subscription_name);
            $new_subscripion_name = $new_subscripion_name[0] . ' ' . $new_box;
        }

        // Update box records
        $update_subscription_records = "UPDATE subscriptions SET current_box = $new_box WHERE customer_id = $customerId";
        $update_order_records = "UPDATE orders SET updated_at = $new_box_date, next_charge_date = $next_box_date WHERE customer_id = $customerId";

        // Get new records
        $new_records = "SELECT subscription_name, $new_box, $new_box_date FROM subscriptions WHERE customer_id=$customerId";

        $conn->query($update_subscription_records);
        $conn->query($update_order_records);
        $conn->query($new_records);
        
        $conn->close();


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.rechargeapps.com/subscriptions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array(
            'address' => $addressId,
            'next_charge_scheduled_at' => $next_box_date,
            'subscription_name' => $new_subscripion_name),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "x-recharge-access-token: yeD4I03tSNeUiTEKXbcmdCuNCdEUVM"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        

    }

    public function newSubscription() {

        $data = receiveData();
        $addressId = $data['subscription']['address_id'];
        $customerId = $data['subscription']['customer_id'];
        $ns = $data['subscription'];

        $created_at               = $ns['created_at'];
        $updated_at               = $ns['updated_at'];
        $next_charge_date         = $ns['properties']['box_1_date'];
        $subscription_recharge_id = $ns['subscription_recharge_id'];
        $shopify_order_id         = $ns['shopify_order_id'];
        $status_response          = $ns['status_response'];

        $subscription_name        = $ns['subscription_name'];
        $total_boxes              = $ns['properties']['total_boxes'];
        $current_box              = 1;
        $box_1                    = $ns['properties']['box_1'];
        $box_2                    = $ns['properties']['box_2'];
        $box_3                    = $ns['properties']['box_3'];
        $box_4                    = $ns['properties']['box_4'];
        $box_5                    = $ns['properties']['box_5'];
        $box_1_date               = $ns['properties']['box_1_date'];
        $box_2_date               = $ns['properties']['box_2_date'];
        $box_3_date               = $ns['properties']['box_3_date'];
        $box_4_date               = $ns['properties']['box_4_date'];
        $box_5_date               = $ns['properties']['box_5_date'];

        $next_charge_scheduled_at = date('H') >= 16 ? $box_1_date : $box_2_date;

        $conn = openConn();

        $order_data = "INSERT INTO orders (created_at, updated_at, next_charge_date, subscription_recharge_id, shopify_order_id, status_response)
        VALUES ($created_at, $updated_at, $next_charge_date, $subscription_recharge_id, $shopify_order_id, $status_response)";

        $subscription_data = "INSERT INTO subscriptions (id, customer_id, address_id, subscription_name, total_boxes, current_box, box_1, box_2, box_3, box_4, box_5, box_1_date, box_2_date, box_3_date, box_4_date, box_5_date)
        VALUES (LAST_INSERT_ID(), $customerId, $addressId, $subscription_name, $total_boxes, $current_box, $box_1, $box_2, $box_3, $box_4, $box_5, $box_1_date, $box_2_date, $box_3_date, $box_4_date, $box_5_date)";


        if ($conn->query($order_data) === TRUE) {
            $conn->query($subscription_data);
        } else {
            echo "Error: " . $sql_table_1 . "<br>" . $conn->error;
        }

        $conn->close();

        // Send back to recharge our stuff
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.rechargeapps.com/subscriptions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array(
            'address' => $addressId,
            'next_charge_scheduled_at' => $next_charge_scheduled_at),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "x-recharge-access-token: yeD4I03tSNeUiTEKXbcmdCuNCdEUVM"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

    }


}
