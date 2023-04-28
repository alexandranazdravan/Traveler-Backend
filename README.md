<h1 align="center">
  <br>
  <img src="extras\airplanes-are-flying-around-the-world-illustration-in-minimal-style-png.png" alt="Traveler" width="200"></a>
  <br>
  Traveler
  <br>
</h1>

<h4 align="center"> A web app built using <a href="https://rapidapi.com/Travelpayouts/api/flight-data/details" target="_blank">TravelPayouts API</a>.</h4>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#how-to-use">How To Use</a> •
  <a href="#iata-codes">IATA Codes</a> •
  <a href="#search-for-flights">TravelPayouts API</a> •
  <a href="#search-for-flights">PHPMailer</a> •
  <a href="#related">Related</a> •
  <a href="#license">License</a>
</p>

<p align="center">
  <img src="extras\dashboard.png" width="800">
</p>

## Key Features

* Login/Register
  - These are necessary, without them, a user cannot benefit from the other features of the application
* Profile
  - A user can change its password and other data (username, email, fullname), including even their avatar
* Wishlist
   - This way, a user can "keep track" of their favourite flights; they can delete a flight if they are not interested in it anymore
*  Searching for flights
   - A user can make searches based on preferences:
		-  [Cheap Flights](#cheap-flights)
		-  [Direct Flights](#direct-flights)
		-  [Popular City Directions](#popular-city-directions)
		-  [Prices per Month](#prices-per-month)
		-  [Popular Airlines](#popular-airlines)
* Admin
   - This feature is exclusively for app admins. From here, they can view the [IATA Codes](#iata-codes) used by the TravelPayouts API and also manage users(edit, delete, update)


## How To Use

To clone and run this application, you'll need [Vuetify 3](https://vuetifyjs.com/en/) and [Vuejs Router](https://router.vuejs.org/installation.html). <br> From your command line:

```bash
# Clone this repository
$ git clone https://github.com/alexandranazdravan/Traveler-Frontend.git
```
Create a built-in web server: [https://www.jetbrains.com/help/phpstorm/php-built-in-web-server.html](https://www.jetbrains.com/help/phpstorm/php-built-in-web-server.html)
<br>
Make requests to localhost:*port*/*uri*

**Note:**
<blockquote>
Available Endpoints:
    <ul>
        <li>/admin (only for admins)</li>
        <li>/login</li>
        <li>/register</li>
        <li>/forgotpass</li>
        <li>/logout</li>
        <li>/userprofile</li>
        <li>/dashboard</li>
        <li>/wishlist</li>
    </ul>
</blockquote>
	
## IATA Codes
IATA codes are three-letter codes assigned by the International Air Transport Association (IATA) to identify airports, airlines, and aircrafts. <br>
GET https://api.travelpayouts.com/data/en/cities.json  <br>
GET https://api.travelpayouts.com/data/en/airports.json  <br>
GET https://api.travelpayouts.com/data/en/airlines.json


## TravelPayouts API
<h3>Endpoints: </h3>
  * [/prices/cheap](#cheap-direct-flights)
``` bash
 $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->request_url . $this->endpoint . $this->assemblyOptions($request_options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-Access-Token: 05f40c220e8193fc8297804b069de4d8",
                "X-RapidAPI-Host: travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com",
                "X-RapidAPI-Key: 54db60a185mshe7972e9ad846b31p1ee7b6jsnc676d9a1406e"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
```

<h3>Example of responses</h3>
<h4 id="cheap-direct-flights">1. Cheap and Direct Flights</h4>

``` bash
{
"success": true,
"data": {
    "HKT": {
        "0": {
            "price": 35443,
            "airline": "UN",
            "flight_number": 571,
            "departure_at": "2015-06-09T21:20:00Z",
            "return_at": "2015-07-15T12:40:00Z",
            "expires_at": "2015-01-08T18:30:40Z"
        }}
    }
}
```

<h4>2. Popular Airline Routes</h4>

``` bash
{
    "success": true,
    "data": {
        "MOW-BKK": 187491,
        "MOW-BCN": 113764,
        "MOW-PAR": 91889,
        "MOW-NYC": 77417,
        "MOW-PRG": 71449,
        "MOW-ROM": 67190,
        "MOW-TLV": 62132,
        "MOW-HKT": 58549,
        "MOW-GOI": 47341,
        "MOW-IST": 45553
    },
    "error": null,
    "currency":"rub"
}
```

<h4>3. Popular City Directions</h4>

``` bash
{
    "success":true,
    "data":{
        "AER":{
            "origin":"MOW",
            "destination":"AER",
            "price":3673,
            "transfers":0,
            "airline":"WZ",
            "flight_number":125,
            "departure_at":"2016-03-08T16:35:00Z",
            "return_at":"2016-03-17T16:05:00Z",
            "expires_at":"2016-02-22T09:32:44Z"
        }
    },
    "error":null,
    "currency":"rub"
}
```

<h4>4. Prices for a Month</h4>

``` bash
{
    "success":true,
    "data":[{
        "show_to_affiliates":true,
        "trip_class":0,
        "origin":"LED",
        "destination":"HKT",
        "depart_date":"2015-10-01",
        "return_date":"",
        "number_of_changes":1,
        "value":29127,
        "found_at":"2015-09-24T00:06:12+04:00",
        "distance":8015,
        "actual":true
    }]
}
```

## Related
  - [Traveler -> Frontend](https://github.com/alexandranazdravan/Traveler-Frontend.git) - The frontend of the app


## License

MIT

---

