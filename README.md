# Pi-hole config

## Installation Introduction

1. Install [Raspbian Stretch Lite](https://www.raspberrypi.org/downloads/raspbian/) on SD-Card.
2. Create a file `ssh` in `/boot` folder, if not exist for enable ssh.
3. Connect Raspberry Pi with the network, search the IP of it and try to login via ssh.
    - Default login credentials are  
      **Username:** pi  
      **Password:** raspberry  
4. Change password with command `passwd`.
5. Install updates and upgrades with command `sudo apt-get update && sudo apt-get upgrade`.
6. Run installation script for Pi-hole `curl -sSL https://install.pi-hole.net | bash`.
    - Don't forget to write down your password, which was shown after installation.
7. After installation steps, take a look, if admin site is reachable.
    - http://IP.OF.PI.HOLE/admin (as example: http://192.168.1.5/admin)
    - http://pi.hole/admin (only if you had add it as DNS Server to your router)
8. Configure the autoupdater for Pi-hole via cronjob.
    - Open the cronjob file `sudo nano /etc/cron.d/pihole`
    - Add entry (if not available) `30 2    * * 7   root    PATH="$PATH:/usr/local/bin/" pihole updatePihole` (every seventh day on 2:30 AM)
    - Save and close file
    - Restart the cronjob service `sudo service cron restart`
9. Set up a new password for your admin interface via `pihole -a -p p4ssW0rd#`.
8. Go to your router and configure the Pi-hole as DNS Server (only this one, no second one is allowed. It will not work correctly, if you add 8.8.8.8 or similar as second DNS Server). Use the IP-Address if your Pi-hole as DNS Server.
9. Restart all your devices (router, smartphones, pc, ...) to make sure, that all receives an IP update with the new settings.
10. Test, if all works fine with a page like this [https://blockads.fivefilters.org/?pihole](https://blockads.fivefilters.org/?pihole).
    - Don't forget to disable the browser extensions which will block ad's also.
11. Add additional blacklists and whitelists to your Pi-hole configuration.

### Manual update

To trigger a manual update, connect with your SSH client to your pihole and type in the command `pihole -up`.

### Repair/Reinstall (if something was wrong during update)

Use command `pihole reconfigure` for repair or reinstall after something was broken during installation or update.

### Additional info

The install log and all custom configurations (like blacklists, whitelists, ...) will be saved under `/etc/pihole`.

## Blacklist

Currently only my [Pi-hole blacklist](/blacklist/blacklist-complete.txt) is available which you can configure under the [Pi-hole blocklist setting page](http://pi.hole/admin/settings.php?tab=blocklists).  
It includes all the lists from [https://firebog.net/](https://firebog.net/).  
With this you may add a lot of websites to your whitelist.

## Whitelist

A whitelist is hosted here on bottom [https://firebog.net/](https://firebog.net/).  
A more detailed whitelist you can find here [https://discourse.pi-hole.net/t/commonly-whitelisted-domains/212](https://discourse.pi-hole.net/t/commonly-whitelisted-domains/212).  
If you will not fill in all manually, you can use this method over an script [https://github.com/anudeepND/whitelist](https://github.com/anudeepND/whitelist).

I also had added my current [whitelist](/whitelist/whitelist.txt) as backup.

## Pi-hole documentation

A documentation with also a detailed command list can be found [here](https://docs.pi-hole.net/).

A command list with examples for Pi-hole can be found [here](https://discourse.pi-hole.net/t/the-pihole-command-with-examples/738).

## Additional links

- [https://pi-hole.net/](https://pi-hole.net/)
- [https://www.kuketz-blog.de/pi-hole-schwarzes-loch-fuer-werbung-raspberry-pi-teil1/](https://www.kuketz-blog.de/pi-hole-schwarzes-loch-fuer-werbung-raspberry-pi-teil1/)
- [https://datenschutz.ekd.de/2018/04/12/pi-hole-ein-erfahrungsbericht/](https://datenschutz.ekd.de/2018/04/12/pi-hole-ein-erfahrungsbericht/)
- [https://www.mielke.de/blog/Mit-dem-Pi-hole-einen-Werbeblocker-fuer-das-gesamte-lokale-Netz-einrichten--488/](https://www.mielke.de/blog/Mit-dem-Pi-hole-einen-Werbeblocker-fuer-das-gesamte-lokale-Netz-einrichten--488/)
- [https://community.ubnt.com/t5/EdgeRouter/Simplest-setup-to-integrate-a-pi-hole/td-p/2437427](https://community.ubnt.com/t5/EdgeRouter/Simplest-setup-to-integrate-a-pi-hole/td-p/2437427)
