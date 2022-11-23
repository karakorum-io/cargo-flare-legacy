<?php
	class RouteHelper {
		public static $routeUrl = 'https://maps.googleapis.com/maps/api/directions/xml?mode=driving&sensor=false&alternatives=false&key=AIzaSyDwpoomGRTEVGyHrfS-5eA2AidGQVS3Ols';
		
		protected static function routeRequest($origin, $destination) {
			$url = sprintf("%s&origin=%s&destination=%s", self::$routeUrl, urlencode($origin), urlencode($destination));
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}
		
		public static function getRouteDistance($origin, $destination, $string = false) {
			$response = self::routeRequest($origin, $destination);
			$xml=new SimpleXMLElement($response);
			$status = $xml->status;
			$routes = array();
			if ($status != "OK") return null;
			foreach($xml->route as $route) {
				$routes[] = $route;
			}
			
			if ($string) {
				return $routes[0]->leg->distance->text;
			} else {
				return $routes[0]->leg->distance->value;
			}
		}
		
		public static function getMiles($distance, $formatted = true) {
			if (!ctype_digit((string)$distance)) throw new FDException("RouteHelper->getMiles: invalid input data");
			$distance = $distance/1609.344;
			if ($formatted) {
				return number_format($distance, 2, ".", ",");
			}
			return $distance;
		}
	}
?>