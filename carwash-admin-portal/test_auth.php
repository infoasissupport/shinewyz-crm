<?php
$url = "http://127.0.0.1:8000/api/login";
$data = json_encode(["email" => "shalwin@shinewise.com", "password" => "password"]);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Accept: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
if($response===false) { echo "curl error: ".curl_error($ch); exit; }
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
preg_match_all("/Set-Cookie:\s*(.*?)(?=
)/i", $headers, $matches);
print_r($matches[1]);

