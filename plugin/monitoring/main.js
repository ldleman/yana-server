var circle;

function widget_clock_init(){
	console.log('ee');
	circle = new ProgressBar.Circle('#clock', {
		color: '#50C8FB',
		duration: 3000,
		easing: 'easeInOut',
		text: {
		style : { fontSize : '38px'},
		value : '00:00:00'
		}
	});

	refresh_clock();
	setInterval(function(){
		refresh_clock();
	},1000);

}

function refresh_clock(){
	var d = new Date();
	var hour = d.getHours(); 
	var minut = ("00" + d.getMinutes()).slice(-2) ; 
	var second = ("00" + d.getSeconds()).slice(-2) ; 
	var year = d.getFullYear() ; 
	var month = ("00" + (d.getMonth()+1)).slice(-2) ; 
	var day = ("00" + d.getDate()).slice(-2) ; 
	var days = ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"];

	var dayName = days[d.getDay()];
	circle.setText(hour+':'+minut+':'+second+'<div class="dayName">'+dayName+'</div><div class="dayDate">'+day+'/'+month+'/'+year+'</div>');
	circle.set(second/60);
}
