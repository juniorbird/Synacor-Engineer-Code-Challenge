<?php header('Access-Control-Allow-Origin: *'); ?>
<?php

// synacor.php
// (C) 2019 wm.l.scheding. All Rights Reserved.

// https://cors.io/?u=://http://wls.org/synacor.php

// Ref: https://stackoverflow.com/questions/3102819/disable-same-origin-policy-in-chrome
// open -a Google\ Chrome --args --disable-web-security --user-data-dir

// get passed params to set the default for items on the page.
parse_str($_SERVER['QUERY_STRING'], $_GET);
// print_r($_GET);

// PHP tests, fills initial elements, as a test.
// http://www.wls.org/synacor.php?city=Los%20Angeles&temp=68%E2%80%A2F&outlook=rain
// http://www.wls.org/synacor.php?city=Los%20Angeles&temp=68%E2%80%A2F&outlook=sunny
// http://www.wls.org/synacor.php?city=Los%20Angeles&temp=68%E2%80%A2F&outlook=Clear%20Skies

/* PHP interface to get started.
Array
(
    [city] => los Angeles
    [temp] => 68•F
    [outlook] => rain
)

*/

/*
// 20190318134807
// https://weathersync.herokuapp.com/ip

{
  "latitude": 34.0762,
  "longitude": -118.3029
}
*/

// https://weathersync.herokuapp.com/weather/34.0762,-118.3029

/*
// 20190318135015
// https://weathersync.herokuapp.com/weather/34.0762,-118.3029

{
  "coord": {
    "lon": -118.3,
    "lat": 34.08
  },
  "weather": [
    {
      "id": 800,
      "main": "Clear",
      "description": "clear sky",
      "icon": "02d"
    }
  ],
  "base": "stations",
  "main": {
    "temp": 297.82,
    "pressure": 1016,
    "humidity": 23,
    "temp_min": 294.15,
    "temp_max": 300.93
  },
  "visibility": 16093,
  "wind": {
    "speed": 3.6,
    "deg": 130
  },
  "clouds": {
    "all": 5
  },
  "dt": 1552942214,
  "sys": {
    "type": 1,
    "id": 6237,
    "message": 0.0114,
    "country": "US",
    "sunrise": 1552917588,
    "sunset": 1552960967
  },
  "id": 5357527,
  "name": "Hollywood",
  "cod": 200
}
*/

// add degree symbol to the Temp from PHP
$_GET['temp'] = preg_replace('/•/', '&deg;', $_GET['temp']);

$none = 'none'; // hide long / lat in the page.

?>

<html>
<head>
<title>A Web Page
</title>

<script type="text/javascript">
/**
 * Short notation for document.getElementById().
 */
function $id(theId) {
	try {return document.getElementById(theId);}
	catch (e) {return null;}
}

var myObj = {
   theXHR: null,
   dialogOpen: false,
   fadedBackground: false,
   objTop: 0,
   longitude: '',
   latitude: '',
   
	'init':
	function() {
      let dbg = 0;
	   this.theXHR = null;
	   if (dbg)  sconsole.log('init here.');
   },
   

  'getIp':
   function() {
      let dbg = 0;
      let URL = 'https://weathersync.herokuapp.com/ip';
      let reqParams = '';

      if (dbg) console.log('URL:'+URL);
      if (dbg) console.log('reqParams:'+reqParams);
      URL += reqParams;
      if (dbg) console.log('getIp :: URL is '+URL); // null ????
   
      let xmlhttp = null;
      if (window.XMLHttpRequest) {
         // code for modern browsers
         xmlhttp = new XMLHttpRequest();
       } else {
         // code for old IE browsers
         xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }

      xmlhttp.onreadystatechange=function(){
         if (xmlhttp.readyState==4 && xmlhttp.status==200){
            if (dbg) console.log('getIp :: this.responseText is ', this.responseText);
            let dataObj = JSON.parse(this.responseText);
            if (dbg) console.log('getIp :: dataObj is ', dataObj);
            myObj.latitude  = dataObj.latitude; // these are hidden, generally.
            myObj.longitude = dataObj.longitude;
            if (dbg) console.log('getIp :: myObj.latitude  is ', myObj.latitude);
            if (dbg) console.log('getIp :: myObj.longitude is ', myObj.longitude);
            let ipLat  = $id('cityLatitude'); // these are generally hidden
            let idLong = $id('cityCLongitude'); // ibid
            idLong.innerHTML = myObj.longitude;
            ipLat.innerHTML  = myObj.latitude;
            myObj.getWeather(myObj.latitude, myObj.longitude); // fixed here by integrating this call.
         }
      }

      xmlhttp.open('GET', URL, true); // no HTTPS check?
      xmlhttp.send(reqParams);
   },
 
   'getWeather':
   function(lat, long) {
      let dbg = 0;
      if (dbg) console.log('lat:'+lat);
      if (dbg) console.log('long:'+long);
      if (! lat) return false;
      if (! long) return false;

      let URL = 'https://weathersync.herokuapp.com/weather/';
      let reqParams = '';

      if (lat) reqParams += lat;
      if (lat && long) reqParams += ',';
      if (long) reqParams += long;
      URL += reqParams;

      if (dbg) console.log('URL:'+URL);
      if (dbg) console.log('lat:'+lat);
      if (dbg) console.log('long:'+long);
      if (dbg) console.log('reqParams:'+reqParams);
   
      let xmlhttp = null;
      if (window.XMLHttpRequest) {
         // code for modern browsers
         xmlhttp = new XMLHttpRequest();
       } else {
         // code for old IE browsers
         xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }

      xmlhttp.onreadystatechange = function() {
         if (dbg) console.log('this.status is ', this.status);
         if (this.readyState == 4) {
            if (this.status == 200) {
               if (dbg) console.log('getWeather :: this.responseText is ', this.responseText);
               let dataObj = JSON.parse(this.responseText);

               if (dbg) console.log('getWeatherHandler :: dataObj is ', dataObj);
               if (dbg) console.log('getWeatherHandler :: dataObj.main.temp is ', dataObj.main.temp);
               if (dbg) console.log('getWeatherHandler :: dataObj is ', dataObj.weather[0].main);
               if (dbg) console.log('getWeatherHandler :: dataObj is ', dataObj.weather[0].icon);

               let idCity = $id('cityContainer');
               let idTemp = $id('tempContainer');
               let idOutlook = $id('outlookContainer');
               let idImgSrc = $id('imageContainer');
               let idIweatherImg = $id('weatherImg'); // added recently

               idCity.innerHTML    = dataObj.name;
               let degK = dataObj.main.temp;
               let degF = ((degK - 273.15) * (9/5) + 32);
               idTemp.innerHTML    = Math.floor(degF)+'&deg; F';
               idOutlook.innerHTML = dataObj.weather[0].main;
               let imageSource = 'http://openweathermap.org/img/w/'+dataObj.weather[0].icon+'.png';
               if (dbg) console.log('getWeatherHandler :: imageSource is ', imageSource);
               idIweatherImg.src = imageSource;
               idIweatherImg.style.background = '#FFFFFF';
            }
          } // else wait for it.
      };

      xmlhttp.open('GET', URL, true); // no HTTPS check?
      xmlhttp.send(null);
   },

   'showFadedBackground':
   function () { /* bugfix 2598 */
      var fadedBackground = $id('FadedBackground');
      fadedBackground.style.height = myObj.getContentHeight() + 'px';
      fadedBackground.style.width = myObj.getContentWidth() + 'px';
   
      fadedBackground.style.top = 0;
      fadedBackground.style.left = 0;
//       myObj.hideDropDowns();
      fadedBackground.style.display = 'inline';
      fadedBackground = null;
   },

   'hideFadedBackground':
   function (argElem) { 
//       this.showDropDowns();
      if (argElem) {
         argElem.style.display = 'none';
      }
      this.dialogOpen = false;
   },

	'ieHack':
	function() {
		if (this.IS_IE6) {
			$id("sourcePorts").style.height='200px';
			$id("targetPorts").style.backgroundColor='transparent';
		}
	},

};

</script>

<script type="text/javascript">
// for some reason this does not get called 100% of the time and the page does not display the changes.
function myFunction() {
   // now sure why this will not work as I expect!
   let dbg = 0;
   if (dbg) console.log('myFunction');
	myObj.init();
	myObj.getIp(); // integrated call to myObj.getWeather();
};


</script>

</head>
<body>
<span id="divActivites" name="divActivites" style="border:thin">
<center>
<table id='mytable' border='0' style="width: 100%; height: 100%;">
   <tr>
      <td align='center'>CURRENT CONDITIONS FOR
      </td>
   </tr>
   <tr>
      <td align='center'>
         <div id="cityContainer" 
               style="height:20px; width:400px; padding: 1px; position:relative;z-index:10; background:#ffffff; display: ; margin: auto; text-align: center;" 
		   >
            <?php echo $_GET['city']; ?>
         </div>
      </td>
   </tr>
   <tr>
     <td align='center'>
          <div id="tempContainer" 
               style="height:20px; width:400px; padding: 1px; position:relative;z-index:10; background:#ffffff; display: ; margin: auto; text-align: center;" 
		   >
            <?php echo $_GET['temp']; ?>
         </div>
      </td>
   </tr>
   <tr>
      <td align='center'>
         <div id="imageContainer" 
               style="height:100; width:100px; padding: 1px; position:relative;z-index:10; background:#ffffff; display: ; margin: auto; text-align: center;" 
		   >
           <img id='weatherImg' width="67" height="67" src="/images/blue_skies.png" style="backgrpound: ;" border='1'>
         </div>
      </td>
   </tr>
   <tr>
      <td align='center'>
         <div id="outlookContainer" 
               style="height:20px; width:400px; padding: 1px; position:relative;z-index:10; background:#ffffff; display: ; margin: auto; text-align: center;" 
		   >
            <?php echo $_GET['outlook']; ?>
         </div>
      </td>
   </tr>
   <tr style='dislpay: <?php echo $none; ?>;'>
      <td align='center'>
         <div id="cityLatitude" 
               style="height:20px; width:80px; padding: 1px; position:relative;z-index:10; background:#ffffff; display: <?php echo $none; ?>; margin: auto; text-align: center;" 
		   >
            <div id='yourLat'>
               Latitude
            <div>
         </div>
      </td>
      <td>
         <div id='slash' style='display: <?php echo $none; ?>;'>
            &nbsp;/&nbsp;
         </div>
      </td>
      <td align='center'>
         <div id="cityCLongitude" 
               style="height:20px; width:80px; padding: 1px; position:relative;z-index:10; background:#ffffff; display:  <?php echo $none; ?>; margin: auto; text-align: center;" 
		   >
            <div id='yourLong'>
               Longitude
            <div>
         </div>
      </td>
   </tr>
</table>
</center>
<iframe id="myFrame" style='display:none;'></iframe>
</span> 

<script type="text/javascript">

$id('myFrame').addEventListener('load', myFunction());

</script>
</body>
</html>
