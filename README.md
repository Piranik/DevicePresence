Device Presence
===============
This app scans the network (using Nmap) every x seconds and stores every available device in a database.
The devices are recognized by their MAC address.

The MAC address is looked up in a free database to retrieve the vendor.
This makes it easy to see what kind of device it is.


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


W.I.P
-----

Currently, saving the devices to a SQLite DB is all this app does.

There's also a experimental graph showing all devices in a timeline.
To see it, run the internal webserver by executing:
    ```./run.sh```
    
Then go to http://localhost:9999/graph


TODO
----

- [ ] Add unit testsg
- [ ] Make API to find out if the device is available atm
- [ ] Build webinterface (table of devices)
- [ ] Generate data/chart per device with available/offline times
- [ ] Use other database that can handle large amounts of data
