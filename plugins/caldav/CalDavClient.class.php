<?php
// doc http://sabre.io/dav/building-a-caldav-client/
class CaldavClient{
	public $host,$login,$password,$user;
	const debug = false;
	
	
	public function create_event($event,$eventId=null){
	
		$body = '';
		
		$eventId  = isset($eventId)? $eventId : time().'-'.rand(0,1000).'.ics';
			
		$body .= 'BEGIN:VCALENDAR'."\r\n";
		$body .= 'VERSION:2.0'."\r\n";
		$body .= 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN'."\r\n";
		$uid   = time().'-'.rand(0,1000);
		$start = date('Ymd\THis',$event->start);
		$end   = date('Ymd\THis',$event->end);
		$body .= 'BEGIN:VEVENT'."\r\n";
		$body .= 'UID:'.$uid."\r\n";
		$body .= 'DTSTART:'.$start.'Z'."\r\n";
		$body .= 'DTEND:'.$end.'Z'."\r\n";
		$body .= 'SUMMARY:'.$event->title."\r\n";
		$body .= 'LOCATION:'.$event->location."\r\n";
		$body .= 'STATUS:CONFIRMED'."\r\n";
		$body .= 'DESCRIPTION:'.$event->description."\r\n";
		$body .= 'SEQUENCE:1'."\r\n";
		
		$body .= 'CREATED:'.date('Ymd\THis').'Z'."\r\n";
		$body .= 'LAST-MODIFIED:'.date('Ymd\THis').'Z'."\r\n";
		
		if(isset($event->categories)):
			foreach($event->categories as $category):
			$body .= 'CATEGORIES:'.$category."\r\n";
			endforeach;
		endif;
		
		if(isset($event->frequency)):
			$body .= 'RRULE:FREQ='.strtoupper($event->frequency)."\r\n";
		endif;
		
		if(isset($event->alarms)):
			foreach($event->alarms as $alarm):
				$alarm = strtoupper($alarm);
				
				$alarm = in_array(substr($alarm,-1,1),array('H','M')) ? 'T'.$alarm:$alarm;
				$body .= 'BEGIN:VALARM'."\r\n";
				$body .= 'ACTION:DISPLAY'."\r\n";
				$body .= 'TRIGGER;VALUE=DURATION:-P'.strtoupper($alarm)."\r\n";
				$body .= 'DESCRIPTION:'.$event->title."\r\n";
				$body .= 'END:VALARM'."\r\n";
			endforeach;
		endif;
		$body .= 'END:VEVENT'."\r\n";
		$body .= 'END:VCALENDAR'."\r\n";
		
	
		$headers = array();
		$headers []= 'Content-Type: text/calendar; charset=utf-8';
		//if($etag!='') $headers []= 'ETag: "'.$etag.'"';
		$out = self::parse_xml(
			self::custom_request(
				$this->host.'/'.$this->user.'/'.$this->calendar.'/'.$eventId ,
				$this->login.":".$this->password,
				'PUT',
				$headers,
				$body
			)
		);
		if($out!='') throw new Exception($out);
		return $eventId;
			
	}
	
	public function delete_event($ics){
		return self::parse_xml(
			self::custom_request(
				$this->host.'/'.$this->user.'/'.$this->calendar.'/'.$ics ,
				$this->login.":".$this->password,
				'DELETE',
				array(),
				''
			)
		);
			
	}
	
	public function get_events($calendar){

			$body = '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
				<d:prop>
					<d:getetag />
					<c:calendar-data />
				</d:prop>
				<c:filter>
					<c:comp-filter name="VCALENDAR" />
				</c:filter>
			</c:calendar-query>';

			
			$response = self::parse_xml(self::custom_request(
			 $this->host.'/'.$this->user.'/'.$calendar,
			$this->login.":".$this->password,
			'REPORT',
			array(
				'Depth: 1',
				'Prefer: return-minimal',
				'Content-Type: application/xml; charset=utf-8'
			),
			$body));
			
			
			$events = array();
		
			$xml = simplexml_load_string($response);
			
			foreach($xml->xpath('//d:multistatus/d:response') as $xmlEvent) {
				$xmlCalendar =  (string)$xmlEvent->xpath('d:propstat/d:prop/cal:calendar-data')[0];
				$event = IcalEvent::fromFile($xmlCalendar);
				$url = explode('/',(string)$xmlEvent->xpath('d:href')[0]);
				$event->ics = array_pop($url);
				$events[] = $event;
			}
			
			
			return $events ;
	
	}
	

	
	public function get_calendar_infos($calendar){

		$body = '<d:propfind xmlns:d="DAV:" xmlns:cs="http://calendarserver.org/ns/">
		  <d:prop>
			 <d:displayname />
			 <cs:getctag />
		  </d:prop>
		</d:propfind>';
		
		return self::parse_xml(self::custom_request(
			$this->host.'/'.$this->user.'/'.$calendar.'/' ,
			$this->login.":".$this->password,
			'PROPFIND',
			array(
				'Content-Type: application/xml; charset=utf-8',
				'Depth: 0',
				'Prefer: return-minimal'
			),
			$body));

	}
	
	public static function custom_request($url,$digest,$method,$headers,$body){

		if(self::debug){
			echo '<hr/>';
			echo '<b>URL :</b> '.$url.'<br>';
			echo '<b>DIGEST :</b> '.$digest.'<br>';
			echo '<b>METHOD :</b> '.$method.'<br>';
			echo '<b>HEADER :</b> '.json_encode($headers).'<br>';
			echo '<pre>'.htmlentities($body).'</pre>';
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $digest);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		$response = curl_exec($ch);
		curl_close($ch);
		if(self::debug){
			echo '<h3>Response</h3><pre>'.htmlentities($response).'</pre>';
		}
		return $response;
	}
	
	public static function parse_xml($xml){
		//TODO
		return $xml;
	}
}

class IcalEvent{
	public $title,$description,$start,$end,$frequency,$location,$categories,$alarms,$ics;
	
	public static function fromFile($ical){
		$event = new self();
		$lines = array();
		foreach(explode("\n",$ical) as $line):
			$columns = explode(":",$line);
			if(!isset($columns[1])) continue;
			$key = $columns[0];
			$value = $columns[1];
			
			$keyvalues = explode(';',$key);
			$key = array_shift($keyvalues);
			
			$lines[$key] = $value;
		endforeach;
		
		if(isset($lines['SUMMARY']))  $event->title = $lines['SUMMARY'];
		if(isset($lines['DESCRIPTION']))  $event->description = $lines['DESCRIPTION'];
		if(isset($lines['DTSTART']))  	  $event->start = strtotime($lines['DTSTART']);
		if(isset($lines['DTEND']))  $event->end = strtotime($lines['DTEND']);
		if(isset($lines['RRULE']))  $event->frequency = $lines['RRULE'];
		if(isset($lines['LOCATION']))  $event->location = $lines['LOCATION'];
		
		return $event;
	}
	
	
}

?>