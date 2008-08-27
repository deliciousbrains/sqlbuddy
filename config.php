<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

config.php
- options for customizing sql buddy

MIT license

2008 Calvin Lough <http://calv.in>

*/


/* The values below are the ones displayed on the login.php page */

$sbconfig['DefaultHost'] = "localhost";
$sbconfig['DefaultUser'] = "root";

/*
* If you want to enable automatic login, you can include your password below. 
* Note: This is generally not recommended because it means that anyone with the 
* proper url will have access to your data. It should only be used on machines
* that are not accessible from the internet (local testing boxes) or if you have
* set up some other form of authentication. Use as your own discretion.
*/

// $sbconfig['DefaultPass'] = "";


/*
* By default, when you view the homepage of your SQL Buddy installation, a check is
* performed to see if a newer version of the application is available. No personal 
* information is sent about your installation and the response from the server is sent
* as plain text. If, for some reason, you want to disable this behaviour, set this option
* to true to disable automatic checking for updates. To learn about new versions, you can
* always check the projects website at http://www.sqlbuddy.com/
*/

$sbconfig['EnableUpdateCheck'] = true;

/*
* This controls how many rows are displayed at once on the browse tab. If you are on a local
* machine or have a fast internet connection, you could increase this value.
*/

$sbconfig['RowsPerPage'] = 100;

?>