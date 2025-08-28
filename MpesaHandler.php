<?php
class MpesaHandler {
    private $consumerKey     = "FxJcKtZm7nAxRGwmvRURG9x4CAjzMQO619QP9Ea3VaTrPFMT";
    private $consumerSecret  = "vImAqERbQKh2HiWxZ6lPAyBWvnPFp5mAB7opszB50iaHz8WI3kNn78w4c11w0yak";
    private $shortcode       = "174379";   // AlgoSpace Paybill 542542 acc 06007082636350
    private $passkey         = "D65OygvbIpdzRXo3xqSq1GzPtls2zykD8c+zFNj1bCGOG0jAaKpS7U8pM/rG9XlGQnH28cj264hJGrRuZBtVqTYhVNny5NPW2nnXHC2Mjhcfl99BkGsnFpIpTfEbBe5Vi2OElpfPi7jUO5ezg+gs1PaxhxcHRhqiAN7sstYO2rOTAU4TkQbf4QxAEhuQGS+en2mbODwidpjmnQSHJUTcrwiFr5Np2AV23CoGrGfjSnay03gGyRKPlygMn0jFsRHfSlxLu8C50+5DQr8IzO8WqDpINrk8B0tdqlLcYKl0rcIlDPG3k4h46CeRPIwKHvy3c36DbPTlwE1Z0w50M4Brqg==";     // from Safaricom portal
    private $callbackUrl     = "https://yourdomain.com/deposit_action.php"; 

    private $tokenUrl        = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    private $stkPushUrl      = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

    // Get access token
    private function getAccessToken() {
        $credentials = base64_encode($this->consumerKey . ":" . $this->consumerSecret);

        $ch = curl_init($this->tokenUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);
        return $result->access_token ?? null;
    }

    // Initiate STK Push
    public function initiateSTKPush($phone, $amount, $accountReference) {
        $token = $this->getAccessToken();
        if (!$token) {
            return ["success" => false, "message" => "Failed to generate access token"];
        }

        $timestamp = date("YmdHis");
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            "BusinessShortCode" => $this->shortcode,
            "Password"          => $password,
            "Timestamp"         => $timestamp,
            "TransactionType"   => "CustomerPayBillOnline",
            "Amount"            => $amount,
            "PartyA"            => $phone,
            "PartyB"            => $this->shortcode,
            "PhoneNumber"       => $phone,
            "CallBackURL"       => $this->callbackUrl,
            "AccountReference"  => $accountReference,
            "TransactionDesc"   => "Deposit Payment"
        ];

        $ch = curl_init($this->stkPushUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $token
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Process Callback from Safaricom
    public function processCallback($data) {
        // Example: extract values
        $body     = $data["Body"]["stkCallback"] ?? null;
        $resultCode = $body["ResultCode"] ?? null;
        $amount   = null;
        $mpesaRef = null;
        $phone    = null;

        if ($resultCode === 0 && isset($body["CallbackMetadata"]["Item"])) {
            foreach ($body["CallbackMetadata"]["Item"] as $item) {
                if ($item["Name"] === "Amount") $amount = $item["Value"];
                if ($item["Name"] === "MpesaReceiptNumber") $mpesaRef = $item["Value"];
                if ($item["Name"] === "PhoneNumber") $phone = $item["Value"];
            }

            // Save to DB here (pseudo example)
            $this->saveTransaction($amount, $mpesaRef, $phone, "SUCCESS");
        } else {
            $this->saveTransaction(0, null, null, "FAILED");
        }

        return true;
    }

    // Save transaction to database
    private function saveTransaction($amount, $mpesaRef, $phone, $status) {
        // Example only: replace with real DB logic
        $log = date("Y-m-d H:i:s") . " - Status: $status - Amount: $amount - Ref: $mpesaRef - Phone: $phone\n";
        file_put_contents(__DIR__ . "/mpesa_transactions.log", $log, FILE_APPEND);
    }
}
