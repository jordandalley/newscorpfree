<?php

$homepage = "https://www.townsvillebulletin.com.au/";

// Set allowed hosts here
$hosts = array(
    'www.townsvillebulletin.com.au',
    'weather.townsvillebulletin.com.au',
    'www.heraldsun.com.au',
    'www.theaustralian.com.au',
    'www.cairnspost.com.au'
);

// Get the url
$url = $_GET['get'];
unset($_GET['get']);

if ( !isset($url) ) {
	$url = $homepage;
}

// Get the location of this php file
$proxylocation = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?get=";

// Get the host
$host = parse_url($url, PHP_URL_HOST);

// Get the scheme
$scheme = parse_url($url, PHP_URL_SCHEME);

if (in_array($host,$hosts)) {
    // Set Headers for Google Bot and X-Forwarded-For
    $headers = array(
	'X-Forwarded-For: 66.249.66.1',
	'User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
    );

    // If there are other GET arguments, they belong to the requested url
    if(!empty($_GET)) {
      $restArguments = http_build_query($_GET);
      $url .= "&" . $restArguments;
    }
    // Initialise curl
    $curl = curl_init();
    // Set cookie
    $tmpfile = tempnam (sys_get_temp_dir(), "CURLCOOKIE");
    // Set curl options
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_COOKIEJAR => $tmpfile,
	CURLOPT_VERBOSE => false,
	CURLOPT_HTTPHEADER => $headers
    ));
    // Execute curl and dump stuff into response
    $response = curl_exec($curl);
    // Set content type header
    $requestContentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $contentType = "Content-Type: " . $requestContentType;
    header($contentType);

    // Close curl
    curl_close($curl);
    $urlreplacefrom = array(
        '<a href="//',
        '<a href="/',
	'<a  href="/',
        '<a href="',
        '<a href="' . $scheme . '://' . $host . '/' . $scheme . '://',
	'<a href="',
	'<a class="tge-cardv2_wrapper" href="',
	'<a class="tge-headerv2-hero_logo" href="',
	'										   href="',
	' url(\'/',
	'class="tge-promo_link" href="',
	'a class="tge-componenttitle_link" href="',
	'tge-headerv2-hero_logo" href="',
	'href="/"',
    );
    $urlreplaceto = array(
        '<a href="' . $scheme . '://',
        '<a href="' . $scheme . '://' . $host . '/',
        '<a href="' . $scheme . '://' . $host . '/',
        '<a href="' . $scheme . '://' . $host . '/',
        '<a href="' . $scheme . '://',
	'<a href="' . $proxylocation,
	'<a class="tge-cardv2_wrapper" href="' . $proxylocation,
	'<a class="tge-headerv2-hero_logo" href="' . $proxylocation,
	'href="' . $proxylocation,
	' url(\'' . $scheme . '://' . $host . '/',
	'class="tge-promo_link" href="' . $proxylocation . $scheme . '://' . $host,
	'a class="tge-componenttitle_link" href="' . $proxylocation . $scheme . '://' . $host,
	'tge-headerv2-hero_logo" href="' . $proxylocation,
	'href="' . $proxylocation . $scheme . '://' . $host . '"',
    );
    $response = str_replace($urlreplacefrom,$urlreplaceto,$response);

    // Output response
    print_r($response);
}
else {
    echo "Host not allowed";
}
