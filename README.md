Device Presence
===============
This app scans the network every x minutes and records every available device by it's MAC address.
This information is stored in ElasticSearch and plotted on a timeline.

This is especially interesting for mobile devices, it shows you when the devices was online, thus within the WiFi range.
Devices are recognized by their MAC address, so even if you use a DHCP server with short lease times, it always knows which IP belongs to the device.

Scanning is done by Nmap. Nmap knows the vendor of most MAC addresses, but the app is connected to the MACVendorLookup API with a more up to date vendor database.
This way it’s easy to know what kind of device it is.

[![Build Status](https://travis-ci.org/TrafeX/DevicePresence.png?branch=master)](https://travis-ci.org/TrafeX/DevicePresence)
[![Code Coverage](https://scrutinizer-ci.com/g/TrafeX/DevicePresence/badges/coverage.png?s=f7b20390ea47d3c3af3c42f0d72a668ea14fbed8)](https://scrutinizer-ci.com/g/TrafeX/DevicePresence/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/TrafeX/DevicePresence/badges/quality-score.png?s=67482909a4c50187a3e61b9d8fd9b1872a300105)](https://scrutinizer-ci.com/g/TrafeX/DevicePresence/)

![Device Presence](http://www.trafex.nl/wp-content/uploads/2014/04/Device-Presence-Google-Chrome_066.png "Device Presence screenshot")

Requirements
------------

- PHP >= 5.4
- Nmap >= 6.0
- Sudo/root rights for the scanner
- SQLite 3.x
- PDO SQLlite PHP extension
- Curl PHP extension
- ElasticSearch >= 0.90

or if you want to use the Vagrant box:

- VirtualBox >= 4.1
- Vagrant >= 1.2

Installation using Docker
-------------------------

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Start the Docker containers with Docker Compose

    ```docker-compose up```

Installation using Vagrant
--------------------------
I've created a Vagrant box that automatically starts scanning the network when you start it.

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Start the box. For scanning it needs to have a bridged interface:

    ```vagrant up```

3. The default timezone is UTC, if you want to change this, run these commands:

    ```vagrant ssh``` (SSH into the Vagrantbox)

    ```sudo dpkg-reconfigure tzdata``` (change the timezone)

    ```exit``` (leave the Vagrantbox)

    ```vagrant reload``` (restart the Vagrantbox to make sure the new timezone is used)


4. The scanner and webinterface will be started by [supervisor](http://supervisord.org/).
5. After a few minutes you should see the scan results on http://127.0.0.1:9999

6. Check the following logs for any issues:
    ```sudo tail -f /var/log/supervisor/*```


Installation on your own server (or Raspberry Pi!)
--------------------------------------------------

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Make sure you've nmap installed

    ```apt-get install nmap```

3. Make sure you've ElasticSearch installed.
4. Copy config/app/config.yml-dist to config/app/config.yml
5. If you want to lookup the vendor, get the API key from http://www.macvendorlookup.com/api. Fill in your e-mail address and choose JSON as output format.
6. Change the network and interval of the scan to your needs.
7. Let Doctrine create the database:

    ```php vendor/bin/doctrine orm:schema-tool:create```

8. Run the scanner (as root, possible with sudo):

    ```php cli/command.php scanner```

9. You can use the builtin webserver from PHP5.5 to run the webinterface:

    ``` ./run ```

10. After the scanner has found the first results, you can see them at
http://127.0.0.1:9999/graph


W.I.P
-----

I'm trying to keep the development going on this project.
There's still a lot todo:


- [x] Use ElasticSearch as storage
- [x] Add unit tests
- [ ] Make API to find out if the device is available atm
- [x] Generate data/chart per device with available/offline times
- [x] Aggregate the results of devicelogs to timeblocks and cleanup devicelogs
- [ ] Use Phing or Make to install
- [x] Handle state when the're no devicelogs yet
- [x] Add datepicker for timeline graph
- [ ] Support Kismet as scanning tool
- [ ] Use Events to handle the scanner command
- [ ] Use the elasticsearch puppet module
- [ ] Add the ability to get a notification with Pushbullet when a device is discovered
