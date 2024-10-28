<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Attributio_Proxy' ) ) {

	class Attributio_Proxy {

		public function ping_google() {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$request_path = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "attributio-ubga") + 15);
			$request_path = $request_path . "&" . "uip=" . urlencode($ip);

			$protocol = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
			$url = "www.google-analytics.com";

			$url = $protocol . $url . $request_path;

			$response = wp_remote_get( $url );
			return $response;
			
		}

	} // End Class


}