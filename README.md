# devmon
Saving and presenting device data from encrypted POST data.

<img src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/DevSanternoData.png" width="256" height="256"/> <img src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/PVStats.png" width="256" height="256"/>

# What is it and what is for?

It is a web application for saving and presenting sensors/devices readouts received from encrypted JSON request. You can see this project: https://github.com/id872/data_logger for more details. Decrypted devices data readouts are inserted into DB. Charts are generated with using [Chart.js library](https://www.chartjs.org/).

See [demo app](https://demodevmon.000webhostapp.com/current).

The application works with data from the following devices:
* Santerno Solar Inverters  (https://en.wikipedia.org/wiki/Solar_inverter)
* DS18B20 temperature sensors (https://www.maximintegrated.com/en/products/sensors/DS18B20.html)
* Xiaomi Air Purifier 2H (https://www.mi.com/global/mi-air-purifier-2h)
* Tasmota Plugs (https://tasmota.github.io/docs/Tuya-Convert/)

This is personal project. It has been implemented mainly for tracking a performance of my solar installation. Another devices were added in a further implementation.
Feel free to fork and improve according to your needs.

# Summary, features, general info

The application has two main goals – Save devices data in the DB and present devices data in graphical form (with using the Chart.js library).

The application decrypts encrypted JSON POST data and inserts devices data into MySQL DB. Charts are generated from data stored in the DB. Application structure was divided into two sections:

1. Sql – containing SQL Request class definition for SQL query execution and device data getters.
2. Chartgenerators – here are generators for devices data presentation in graphical form. The device data is taken from SQL tables. Chart data is prepared as JSON format for Chart.js library.

There is no need to refresh page to change data scope or type. It works with AJAX requests.

To handle new device data for saving/presentation, following need to be done:

* (*DeviceDataSaver.php*) insert function for new device data has to be created and used
* (SQL DB) new device must be added into “devices” table, new device data type must be added into “dev_data_type” table, readouts table for new device must be created
* (**1**) device data getter implementation
* (**2**) chart data generator implementation

For sure there is a room for improvement to simplify it, but for now it has to be done in that way.

SQL DB is defined to handle many devices from many users. See the *sql_create.sql* in the *sql_db*.
SQL DB configuration *sql_db.ini* is in the *sql_db*. It is used by *SqlRequest.php*.

“users” table contains *api_hash* used to determine *api_key* for data decryption for particular user. [At the logger side](https://github.com/id872/data_logger) (which prepares encrypted JSON request) need to be the same pair defined in a configuration, otherwise the devices data will not be decrypted and stored in DB – please see the device logger project for details.
There is also *user_password_hash* (bcrypt hash) for additional authentication – required only for inserting data into DB. You can see an authentication steps in the *add.php* file. Check also .htaccess where rewrite engine rules were defined.

Devices names defined in a logger configuration must be the same as those defined in the DB.

# Data presentation - examples
## Solar data

<img align=left src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/DevSanternoData.png"/>

## Solar power production statistics

<img align=left src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/PVStats.png"/>

## DS18B20 temperature sensor data

<img align=left src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/DevDs18b20Data.png"/>

## Xiaomi AirPurifier data

<img align=left src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/DevAirPurifierData.png"/>

## Tasmota Plug data

<img align=left src="https://raw.githubusercontent.com/id872/devmon/main/screens_examples/DevTasmotaData.png"/>
