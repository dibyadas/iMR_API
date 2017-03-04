<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://localhost/Pharma/Doctor/upload/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"doc_image\"; filename=\"[object Object]\"\r\nContent-Type: false\r\n\r\n\r\n-----011000010111000001101001--",
  CURLOPT_HTTPHEADER => array(
    "authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiU2FoaWxfMTAxIiwibmFtZSI6IlNhaGlsIiwiZW1haWwiOiJzYWhpbHZzMDAwQGdtYWlsLmNvbSIsInBob25lIjoiMDc4NzI3MDU5OTciLCJhZ2UiOiIxOSIsInNleCI6Im1hbGUiLCJlbXBsb3llX2lkIjoiMTAxIiwiY29tcGFueSI6IlBoYXJtYV9rYV9LYXJtYSIsImlhdCI6MTQ3NDczNzI4OC4wNiwib3duIjoiTVIiLCJhdXRob3JpemF0aW9uIjoxMCwidG9rZW5fdHlwZSI6IkJlYXJlciJ9.44qKAUupwshKVY_YAvNtB-sLKGWTYqm2qxsh2aQpGkw",
    "cache-control: no-cache",
    "content-type: multipart/form-data; boundary=---011000010111000001101001",
    "postman-token: f20c030d-4be0-09b7-f823-8e114a67525e"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
