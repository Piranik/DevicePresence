Device Presence
===============
This app scans the network every x minutes and records every available device by it's MAC address.
This information is stored in a database and plotted on a timeline.

This is especially interesting for mobile devices, it shows you when the devices was online, thus within the WiFi range.
Devices are recognized by their MAC address, so even if you use a DHCP server with short lease times, it always knows which IP belongs to the device.

Scanning is done by Nmap. Nmap knows the vendor of most MAC addresses, but the app is connected to the MACVendorLookup API with a more up to date vendor database.
This way it’s easy to know what kind of device it is.

[![Build Status](https://travis-ci.org/TrafeX/DevicePresence.png?branch=master)](https://travis-ci.org/TrafeX/DevicePresence)

![Device Presence](http://www.trafex.nl/wp-content/uploads/2013/08/devicepresence.png "Device Presence screenshot")

Requirements
------------

- PHP 5.4
- Nmap 6.0
- Sudo/root rights for the scanner
- SQLite 3.x
- PDO SQLlite PHP extension

or if you want to use the Vagrant box:

- VirtualBox 4.1 or 4.2
- Vagrant 1.2

Installation using Vagrant
-------------------------
I've created a Vagrant box that automatically starts scanning the network when you start it.

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Start the box. For scanning it needs to have a bridged interface:
    ```vagrant up```

3. The scanner and webinterface will be started by [supervisor](http://supervisord.org/).
4. After a few minutes you should see the scan results on http://127.0.0.1:9999

5. Check the following logs for any issues:
    ```sudo tail -f /var/log/supervisor/*```


Installation on your own server
----------------------------

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


- [/] Add unit tests
- [ ] Make API to find out if the device is available atm
- [X] Generate data/chart per device with available/offline times
- [ ] Show table with all devices
- [ ] Use other database that can handle large amounts of data
- [ ] Aggregate the results of devicelogs to timeblocks and cleanup devicelogs
- [ ] Use Phing or Make to install
