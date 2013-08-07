Device Presence
===============
This app scans the network every x seconds and saved every available device.
The devices are recognized by their MAC address.

The MAC address is lookup up in a free database to retrieve the vendor.
This makes it easy to see what kind of device it is.


Installation
------------

1. Clone/download this repo and install the dependencies using composer

    ```php composer.phar install```

2. Make sure you've fping or nmap installed

    ```apt-get install fping```
    ```apt-get install nmap```

3. Copy config/app/config.yml-dist to config/app/config.yml
4. If you want to lookup the vendor, get the API key from http://www.macvendorlookup.com/api. Fill in your e-mail address and choose JSON as output format.
5. Change the network and interval of the scan to your needs.
6. Let Doctrine create the database:

    ```php vendor/bin/doctrine orm:schema-tool:create```

7. Run the scanner:

    ```php cli/command.php scanner```


W.I.P
-----

Currently, saving the devices to a SQLite DB is all this app does.

A webinterface is on the todo list.
