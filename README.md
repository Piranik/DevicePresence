Device Presence
===============
This app scans the network (using Nmap) every x seconds and stores every available device in a database.
The devices are recognized by their MAC address.

The MAC address is looked up in a free database to retrieve the vendor.
This makes it easy to see what kind of device it is.

[![Build Status](https://travis-ci.org/TrafeX/DevicePresence.png?branch=master)](https://travis-ci.org/TrafeX/DevicePresence)

![Device Presence](http://www.trafex.nl/wp-content/uploads/2013/08/devicepresence.png "Device Presence screenshot")
Requirements
------------

- PHP 5.4
- Nmap 6.0
- Sudo/root rights for the scanner
- SQLite 3.x
- PDO SQLlite PHP extension


Installation
------------

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Make sure you've nmap installed

    ```apt-get install nmap```

3. Copy config/app/config.yml-dist to config/app/config.yml
4. If you want to lookup the vendor, get the API key from http://www.macvendorlookup.com/api. Fill in your e-mail address and choose JSON as output format.
5. Change the network and interval of the scan to your needs.
6. Let Doctrine create the database:

    ```php vendor/bin/doctrine orm:schema-tool:create```

7. Run the scanner (as root, possible with sudo):

    ```php cli/command.php scanner```

8. You can use the builtin webserver from PHP5.4 to run the webinterface:

    ``` ./run ```

9. After the scanner has found the first results, you can see them at
http://127.0.0.1:9999/graph


W.I.P
-----

Currently, this app is being developed.
There's still a lot todo:


- [ ] Add unit tests
- [ ] Make API to find out if the device is available atm
- [X] Generate data/chart per device with available/offline times
- [ ] Show table with all devices
- [ ] Use other database that can handle large amounts of data
- [ ] Aggregate the results of devicelogs to timeblocks and cleanup devicelogs
- [ ] Use Phing or Make to install
