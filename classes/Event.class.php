 <?php
	// $event = new Event();
 //     $event->setTime(time());
 //     $event->setRepeat(2);
 //     $event->setContent($sayHello);
 //     $event->save();
    

    class Event extends SQLiteEntity{

	    protected $id,$name,$content,$year,$month,$day,$hour,$minut,$repeat,$recipients;
	    protected $TABLE_NAME = 'event';
	    protected $CLASS_NAME = 'Event';
	    protected $object_fields = 
	    array(
		    'id'=>'key',
		    'name'=>'string',
            'content'=>'longstring',
		    'year'=>'string',
            'month'=>'string',
            'day'=>'string',
            'hour'=>'string',
            'minut'=>'string',
		    'repeat'=>'string',
            'recipients'=>'longstring'
	    );
     
         function __construct(){
            parent::__construct();
            // $this->time = time();
            // $this->repeat = '1';
            // $this->name = 'Evenement sans titre du '.date('d/m/Y H:i:s');
            // $this->setRecipients(array());
         }
     
        function setId($id){
            $this->id= $id;
        }
        function getId(){
            return $this->id;
        }

        function setYear($year){
            $this->year= $year;
        }
        function getYear(){
            return $this->year;
        }

        function setMonth($month){
            $this->month= $month;
        }
        function getMonth(){
            return $this->month;
        }

        function setDay($day){
            $this->day= $day;
        }
        function getDay(){
            return $this->day;
        }

        function setHour($hour){
            $this->hour= $hour;
        }
        function getHour(){
            return $this->hour;
        }

        function setMinut($minut){
            $this->minut= $minut;
        }
        function getMinut(){
            return $this->minut;
        }
     
        function setName($name){
            $this->name= $name;
        }
        function getName(){
            return $this->name;
        }
        function setContent($content){
            $this->content= json_encode($content);
        }
        function getContent(){
            return json_decode($this->content,true);
        }
        function setRepeat($repeat){
            $this->repeat= $repeat;
        }
        function getRepeat(){
            return $this->repeat;
        }
        function addRecipient($recipient){
           $recipients =  $this->getRecipients();
           $recipients[] = $recipient;
           $this->setRecipients($recipients);
        }
        function setRecipients($recipients){
            $this->recipients= json_encode($recipients);
        }
        function getRecipients(){
            $rec = json_decode($this->recipients,true);
            return is_array($rec)?$rec:array();
        }
     }
     ?>