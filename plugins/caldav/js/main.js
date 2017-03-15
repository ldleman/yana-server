
$(document).ready(function() {

		$(document).on('keypress',function(e){
			if(e.keyCode!=46 || $('.fc-event[data-selected]').length==0 ) return;
			
			var toDelete = [];
			$('.fc-event[data-selected]').each(function(i,element){
				if($.inArray($(element).attr('data-ics'),toDelete) == -1)
				toDelete.push($(element).attr('data-ics'));
			});
			
			$.getJSON('action.php?action=caldav_delete_event',{events:toDelete},function(r){
				$('.fc-event[data-selected]').each(function(i,element){
					$('#calendar').fullCalendar( 'removeEvents' ,$(element).attr('data-ics') );
				});
			});
			
			
			
		});


		$('#calendar').fullCalendar({
			
			locale: 'fr',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay,listWeek'
			},
			//defaultDate: '2017-02-12',
			navLinks: true, // can click day/week names to navigate views
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			
			events: {
				url: 'action.php?action=caldav_get_events',
				error: function() {
					$('#script-warning').show();
				}
			},
			
			dayRender: function(event, element,view) {
			  element.bind('dblclick', function() {

					var calculatedEnd =  parseInt(event.format("HH"))+1;
					var newEvent = {
						label : 'Rendez vous',
						startDay : event.format("DD/MM/YYYY"),
						endDay : event.format("DD/MM/YYYY"),
						startHour : event.format("HH"),
						endHour :  "00".substring(0, 2 - calculatedEnd.length) + ''+ calculatedEnd,
						startMinut : event.format("mm"),
						endMinut : event.format("mm")
					};
					if(view.name=='month'){
						newEvent.startHour = '08';
						newEvent.endHour = '12';
						newEvent.startMinut = '00';
						newEvent.endMinut = '00';
					}
				  
					edit_event(newEvent);
			  });
			},
			eventRender: function(event, element) {
			  element.bind('dblclick', function() {
					
					edit_event({
						id : event.id,
						label : event.title,
						startDay : event.start.format("DD/MM/YYYY"),
						endDay : event.end.format("DD/MM/YYYY"),
						startHour : event.start.format("HH"),
						endHour : event.end.format("HH"),
						startMinut : event.start.format("mm"),
						endMinut : event.end.format("mm")
					});
			  });
			},
			
			eventAfterRender:function( event, element, view ) { 
				$(element).attr("data-ics",event._id);
			},
			
			eventClick: function(event,jsevent,view) {
				$('[data-selected]').removeAttr('data-selected');
				$(this).attr('data-selected','selected');
				
				$('[data-ics="'+$(this).attr('data-ics')+'"]').attr('data-selected','selected');
				
			},
			eventResize: function(a) {
				alert('a day has been eventResize: '+a);
			},
			eventDrop: function(event, delta, revertFunc) {
				
				$.getJSON('action.php?action=caldav_save_event',{
						ics : event.id,
						label : event.title,
						startDay : event.start.format("DD/MM/YYYY"),
						endDay : event.end.format("DD/MM/YYYY"),
						startHour : event.start.format("HH"),
						endHour : parseInt(event.end.format("HH")),
						startMinut : event.start.format("mm"),
						endMinut : event.end.format("mm")
					},function(r){
					// if(r.error) revertFunc();
				});
						
				

			}
		});
		
	});
	
	function edit_event(event){
		$('#eventModal').modal('show');
		$('#eventModal').attr('data-ics',event.id);
		$('#eventModal #label').val(event.label);
		$('#eventModal #startDay').val(event.startDay);
		$('#eventModal #endDay').val(event.endDay);
		$('#eventModal #startHour').val(event.startHour);
		$('#eventModal #endHour').val(event.endHour);
		$('#eventModal #startMinut').val(event.startMinut);
		$('#eventModal #endMinut').val(event.endMinut);
	}
	
	function caldav_save_event(){
		var event = $('#eventModal').toData();
		event.ics = $('#eventModal').attr('data-ics');
		$.getJSON('action.php?action=caldav_save_event',event,function(r){
			//modification
			if($('#eventModal').attr('data-ics')!=null){
				var originalEvent = $('#calendar').fullCalendar( 'clientEvents',r.event.id );
				originalEvent = originalEvent[0];
				//originalEvent.title = r.event.title;
				originalEvent = $.extend(originalEvent,r.event);
			
				$('#calendar').fullCalendar('updateEvent', originalEvent);
			//creation
			}else{
				$('#calendar').fullCalendar( 'renderEvent',r.event);
			}
			$('#eventModal').removeAttr('data-ics','');
		});
		$('#eventModal').modal('hide');
		return;
	}