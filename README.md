# PitBull Checker
<img align="right" alt="PitBull Checker Website Monitoring Logo" src="http://www.aperion.info/pitbull-checker/assets/img/h180.png">
PitBull Checker is a remote Website and Performance Monitoring Script. It is lock-free, open source, self-hosted, and easy deployable in any PHP7 hosting. No DB needed just simple PHP and file storage.

Stop spending money in oversized services, create your own website monitoring service for you or for your clients. 

Check any http/s URL in GET or POST public or via basic auth, define your server health rules, define minimum consecutives erros before alerting, receive notification of failures and up_again events. Access the web interface to get deep insight for any of your check: graphs, success and failures, dns lookup time, connection time, start of transfer time (TTFB), transfer time, total time. 

We built PitBull Checker to monitor hundreds of our client's web sites. We're open-sourcing it in the hope that others find it useful.


## Features
* Multi user (each user can have his own checks)
* Monitor thousands of websites (powered by PHP7 Guzzle asynchronous library)
* Tweak frequency of monitoring on a per-check basis, up to the minute
* Check the presence of a pattern in the response body
* Receive notifications whenever a check goes down:
  * On screen
  * By email
  * By sms
  * By Telegram chat or private channel
* Record availability statistics for further reporting 
* Detailed uptime reports with charts
* Monitor availability, responsiveness, average response time, TTFB and total uptime/downtime
* Get details about failed checks (HTTP error code, etc.)
* Familiar web interface (powered by Twitter Bootstrap 2.0)
* Easy installation and zero administration
* No Database needed
* Only standard PHP7 and Apache functions

## Installing PitBull Checker
PitBull Checker requires Apache, Php7, Php-curl (available in any hosting plan) and pure CRON access.

To install from GitHub, clone the repository and install dependencies using composer:
```bash
  $ git clone git://github.com/andreacardelli/pitbull-checker.git
  $ cd pitbull-checker/lib/
  $ php composer.phar update 
```
Rename sample configuration file 
```bash
 cd pitbull-checker
 cp ./config/config-sample.php ./config/config.php
```
Edit configuration file located in /your-path/pitbull-checker/config/config.php (it is inline well documented)

Enable the cron job at each minute (* * * * *):
```bash
/path/to/your/php /yourhostingrootpath/pitbull-checker/checker/index.php >> /your-tmp-dir/checker.log 2>&1 
```
Visit your new application at: http://yourdomain/pitbull-checker/

* admin username: pitbull (remember to change it)
* password: pitbull (remember to change it)

Add users and contact channels (emails,sms,telegram bot)
Add checks via the interface for each user (it is self explanatory) and define rules and alert channels.

## SMS (Text Messages)
Please note that there are many SMS Gateway out there. You need to write your own functions to use your gateway.
We provided a special function in config.php that you can use as a sample to write your own. Most of the time they are just simple GET requests with query parameters.

* Infobip (https://www.infobip.com/)
* more to come...

All the gateways will need an account with sufficient credits in order to send SMS.

## Telegram configuration
Our preferred notification system is via Telegram. Please follow the below instructions to create a Telegram Bot, create a Private Channel and add members to read/write including the Bot. We suggest to create a global Bot and let him write to many different Private Channnel/Chat, one for each user. Finally find all the Telegram codes needed for our config.php.

## Comments and suggestions
We will be more than glad to receive your comments, bug notifications, and wishlist.

## License
PitBull Checker is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

PitBull Checker is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with PitBull Checker. If not, see http://www.gnu.org/licenses/.
