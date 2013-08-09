$("#weather_query").click(function(){
query_woeid();
  });

$("input:text[name=weather_place]").keydown(function(event){
if (event.which == 13)
{
event.preventDefault();
}
})

function query_woeid()
{
weather_place = $("input:text[name=weather_place]").val();
url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22" + encodeURI(weather_place) + "%22&format=json" ;
$.getJSON( url, {
tags: "woeid",
tagmode: "any",
format: "json"
})
 .done(function( data ) {
if (data["query"]["results"] != null)
{
alert("Informations récupérés!");
woeid = data["query"]["results"]["place"]["woeid"];
town = data["query"]["results"]["place"]["name"];
country = data["query"]["results"]["place"]["country"]["content"];
region = data["query"]["results"]["place"]["admin1"]["content"];
$("input:text[name=weather_place]").val(town + " " + region + " " + country);
$("input:text[name=woeid]").val(woeid);
}
else
{
alert("Aucune informations trouvée! :-(")
}
})
 .fail(function(){
alert("Impossible de récupérer l'informations!");
 });
}