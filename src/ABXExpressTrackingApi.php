<?php 

namespace Afzafri;

class ABXExpressTrackingApi
{
    public static function crawl($trackingNo, $include_info = false)
    {
		$url = "https://home.abxexpress.com.my/track_multipleResult.asp?vsearch=True";
		
		# store post data into array
		$postdata = http_build_query(
				array(
						'tairbillno' => $trackingNo,
				)
		);

		# use cURL instead of file_get_contents(), this is because on some server, file_get_contents() cannot be used
		# cURL also have more options and customizable
		$ch = curl_init(); # initialize curl object
		curl_setopt($ch, CURLOPT_URL, $url); # set url
		curl_setopt($ch, CURLOPT_POST, 1); # set option for POST data
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); # set post data array
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); # tell cURL to accept an SSL certificate on the host server
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # tell cURL to graciously accept an SSL certificate on the target server
	        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
		$result = curl_exec($ch); # execute curl, fetch webpage content
		$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
		$errormsg = (curl_error($ch)) ? curl_error($ch) : "No error"; # catch error message
		curl_close($ch);  # close curl

		$trackres = array();
		$trackres['http_code'] = $httpstatus; # set http response code into the array
	    $trackres['error_msg'] = $errormsg; # set error message into array

	    # use DOMDocument to parse HTML
		$dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($result);
		libxml_clear_errors();
	    
	    // xpath
	    $xpath = new \DOMXPath($dom);

	    // ----- Get tracking result box -----
		$trackUL = $xpath->query("//*[contains(@id, 'first-list')]");
		$tmp_dom = new \DOMDocument(); 
		$tmp_dom->appendChild($tmp_dom->importNode($trackUL[0],true));
		// xpath
		$xpath = new \DOMXPath($tmp_dom);

		// Get tracking list
		$trackDetails = $xpath->query("li");

	    if($trackDetails->length > 0) # check if there is records found or not
		{
			$trackres['status'] = 1;
	        $trackres['message'] = "Record Found"; # return record found if number of row > 0

	        foreach ($trackDetails as $detail) 
	        {
	            $tmp_dom = new \DOMDocument(); 
	            $tmp_dom->appendChild($tmp_dom->importNode($detail,true));
	            // xpath
	            $xpath = new \DOMXPath($tmp_dom);

				// ----- Get Date and Time -----
				$date = "";
				$time = "";
				$trackTime = $xpath->query("//*[contains(@class, 'time')]");
				if($trackTime->length > 0) {
					$tmp_dom_datetime = new \DOMDocument(); 
					$tmp_dom_datetime->appendChild($tmp_dom_datetime->importNode($trackTime[0],true));
					// xpath
					$xpath_datetime = new \DOMXPath($tmp_dom_datetime);
					$dateTimeSpan = $xpath_datetime->query("span");
					
					// Get Date
					$date = ($dateTimeSpan->length > 0) ? self::cleanDetail($dateTimeSpan[0]->nodeValue) : "";

					// Get Time
					$time = ($dateTimeSpan->length > 1) ? self::cleanDetail($dateTimeSpan[1]->nodeValue) : "";
				}

	            // ---- Get Tracking Details----
	            $location = "";
				$process = "";
				
				$trackingLocation = $xpath->query("//*[contains(@class, 'title')]");
	            if($trackingLocation->length > 0) {
	                $location = (isset($trackingLocation[0])) ? self::cleanDetail($trackingLocation[0]->nodeValue) : "";
	            }

	            $trackingInfo = $xpath->query("//*[contains(@class, 'info')]");
	            if($trackingInfo->length > 0) {
	                $process = (isset($trackingInfo[0])) ? self::cleanDetail($trackingInfo[0]->nodeValue) : "";
	            }

	            // Append Data into JSON
	            $trackres['data'][] = array(
	                "date" => $date,
	                "time" => $time,
	                "location" => $location,
	                "process" => $process,
	            );
	        }
	    } 
	    else 
	    {
	    	$trackres['status'] = 0;
	        $trackres['message'] = "No Record Found"; # return record not found if number of row < 0
	        # since no record found, no need to parse the html furthermore
	    }

		if ($include_info) {
		    $trackres['info']['creator'] = "Afif Zafri (afzafri)";
		    $trackres['info']['project_page'] = "https://github.com/afzafri/ABX-Express-Tracking-API";
		    $trackres['info']['date_updated'] =  "08/12/2020";
		}

		return $trackres;
    }

	static function cleanDetail($str, $explode = false) {
	    if($str != null || $str != "") {
	        if($explode) {
	            $strArr = explode(":", $str);
	            $str = (count($strArr) > 1) ? $strArr[1] : ""; 
	        } 

	        $converted = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES))); 
	        $str = trim($converted, chr(0xC2).chr(0xA0));
	        $str = trim(preg_replace('/\s+/', ' ', $str));
	    }

	    return $str;
	}
}
