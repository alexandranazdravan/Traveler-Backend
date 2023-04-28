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
  <a href="#search-for-flights">Search for flights</a> •
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

**Note
Available Endpoints:
	* /admin  (only for admins)
	* /login
	* /register
	* /forgotpass
	* /logout
	* /userprofile
	* /dashboard
	* /wishlist
:**	

## IATA Codes
IATA codes are three-letter codes assigned by the International Air Transport Association (IATA) to identify airports, airlines, and aircrafts. <br>
GET https://api.travelpayouts.com/data/en/cities.json  <br>
GET https://api.travelpayouts.com/data/en/airports.json  <br>
GET https://api.travelpayouts.com/data/en/airlines.json


## Search for flights
<h3 id="cheap-flights">Cheap Flights</h3>

* Options:
  - departure city
  - destination city
  - departure time
  - return time
  - currency
* Response:
  - airline
  - is it lowcost?
  - flight number
  - price
  - departure day + departure time
  - return day + return time
  
<h3 id="direct-flights">Direct Flights</h3>

* Options:
  - departure city
  - destination city
  - departure time
  - return time
  - currency
  
* Response:
  - airline
  - is it lowcost?
  - flight number
  - price
  - departure day + departure time
  - return day + return time
  
<h3 id="popular-city-directions">Popular City Directions</h3>

* Options:
  - departure city
  - currency
  
* Response:
  - airline
  - is it lowcost?
  - flight number
  - price
  - departure day + departure time
  - return day + return time
  
<h3 id="prices-per-month">Prices per Month</h3>

* Options:
  - departure city
  - destination city
  - currency
  
* Response:
  - class
  - price
  - duration (minutes)
  - distance (km)
  - number of changes
  - departure day
  - return day
  
<h3 id="popular-airlines">Popular Airlines</h3>

* Options:
  - airline
  - limit
  
* Response:
  - departure city
  - destination city
  - popularity score


## Related
  - [Traveler -> Frontend](https://github.com/alexandranazdravan/Traveler-Frontend.git) - The frontend of the app


## License

MIT

---

