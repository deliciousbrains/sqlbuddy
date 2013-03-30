<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/REC-html40/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" version="-//W3C//DTD XHTML 1.1//EN" xml:lang="en">
<head>
    <title>SQL Buddy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link type="text/css" rel="stylesheet" href="<?php echo smartCaching("css/common.css"); ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo smartCaching("css/navigation.css"); ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo smartCaching("css/print.css"); ?>" media="print" />
    <link type="text/css" rel="stylesheet" href="<?php echo themeFile("css/main.css"); ?>" media="all" />
    <!--[if lte IE 7]>
    <link type="text/css" rel="stylesheet" href="<?php echo themeFile("css/ie.css"); ?>" media="all" />
    <![endif]-->
    <script type="text/javascript" src="<?php echo smartCaching("js/mootools-1.2-core.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo smartCaching("js/helpers.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo smartCaching("js/core.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo smartCaching("js/movement.js"); ?>"></script>
</head>
<body>
<div id="container">
    <div id="header">
        <div id="headerlogo">
            <a href="#page=home" onclick="sideMainClick('home.php', 0); return false;"><img src="images/logo.png" /></a>
        </div>
        <div id="toptabs"><ul></ul></div>
        <div id="headerinfo">
            <span id="load" style="display: none"><?php echo __("Loading..."); ?></span>
            <?php

            // if set to auto login, providing a link to logout wouldnt be much good
            if (!((isset($sbconfig['DefaultPass']) && $conn->getAdapter() == "mysql") || (isset($sbconfig['DefaultDatabase']) && $conn->getAdapter() == "sqlite")))
                echo '<a href="logout.php">' . __("Logout") . '</a>';

            ?>
        </div>
        <div class="clearer"></div>
    </div>

    <div id="bottom">

        <div id="leftside">
            <div id="sidemenu">
                <div class="dblist"><ul>
                        <?php

                        if ($conn->getAdapter() != "sqlite") {

                            ?>
                            <li id="sidehome"><a href="#page=home" onclick="sideMainClick('home.php', 0); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Home"); ?></div></a></li>
                            <li id="sideusers"><a href="#page=users&topTab=1" onclick="sideMainClick('users.php', 1); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Users"); ?></div></a></li>
                            <li id="sidequery"><a href="#page=query&topTab=2" onclick="sideMainClick('query.php', 2); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Query"); ?></div></a></li>
                            <li id="sideimport"><a href="#page=import&topTab=3" onclick="sideMainClick('import.php', 3); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Import"); ?></div></a></li>
                            <li id="sideexport"><a href="#page=export&topTab=4" onclick="sideMainClick('export.php', 4); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Export"); ?></div></a></li>
                        <?php

                        } else {

                            ?>
                            <li id="sidehome"><a href="#page=home" onclick="sideMainClick('home.php', 0); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Home"); ?></div></a></li>
                            <li id="sidequery"><a href="#page=query&topTab=1" onclick="sideMainClick('query.php', 1); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Query"); ?></div></a></li>
                            <li id="sideimport"><a href="#page=import&topTab=2" onclick="sideMainClick('import.php', 2); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Import"); ?></div></a></li>
                            <li id="sideexport"><a href="#page=export&topTab=3" onclick="sideMainClick('export.php', 3); return false;"><div class="menuicon">&gt;</div><div class="menutext"><?php echo __("Export"); ?></div></a></li>
                        <?php

                        }

                        ?>
                    </ul></div>

                <div class="dblistheader"><?php echo __("Databases"); ?></div>
                <div class="dblist" id="databaselist"><ul></ul></div>
            </div>
        </div>
        <div id="rightside">

            <div id="content">
                <div class="corners"><div class="tl"></div><div class="tr"></div></div>
                <div id="innercontent"></div>
                <div class="corners"><div class="bl"></div><div class="br"></div></div>
            </div>

        </div>

    </div>
</div>

</body>
<script type="text/javascript">
    <!--

    <?php

    if ($conn->getAdapter() == "sqlite") {
        echo "var showUsersMenu = false;\n";
    } else {
        echo "var showUsersMenu = true;\n";
    }

    echo "var adapter = \"" . $conn->getAdapter() . "\";\n";

    if (isset($requestKey)) {
        echo 'var requestKey = "' . $requestKey . '";';
        echo "\n";
    }

    // javascript translation strings
    echo "\t\tvar getTextArr = {";

    if ($lang != "en_US") {

        echo '"Home":"' . __("Home") . '", ';
        echo '"Users":"' . __("Users") . '", ';
        echo '"Query":"' . __("Query") . '", ';
        echo '"Import":"' . __("Import") . '", ';
        echo '"Export":"' . __("Export") . '", ';

        echo '"Overview":"' . __("Overview") . '", ';

        echo '"Browse":"' . __("Browse") . '", ';
        echo '"Structure":"' . __("Structure") . '", ';
        echo '"Insert":"' . __("Insert") . '", ';

        echo '"Your changes were saved to the database.":"' . __("Your changes were saved to the database.") . '", ';

        echo '"delete this row":"' . __("delete this row") . '", ';
        echo '"delete these rows":"' . __("delete these rows") . '", ';
        echo '"empty this table":"' . __("empty this table") . '", ';
        echo '"empty these tables":"' . __("empty these tables") . '", ';
        echo '"drop this table":"' . __("drop this table") . '", ';
        echo '"drop these tables":"' . __("drop these tables") . '", ';
        echo '"delete this column":"' . __("delete this column") . '", ';
        echo '"delete these columns":"' . __("delete these columns") . '", ';
        echo '"delete this index":"' . __("delete this index") . '", ';
        echo '"delete these indexes":"' . __("delete these indexes") . '", ';
        echo '"delete this user":"' . __("delete this user") . '", ';
        echo '"delete these users":"' . __("delete these users") . '", ';
        echo '"Are you sure you want to":"' . __("Are you sure you want to") . '", ';

        echo '"The following query will be run:":"' . __("The following query will be run:") . '", ';
        echo '"The following queries will be run:":"' . __("The following queries will be run:") . '", ';

        echo '"Confirm":"' . __("Confirm") . '", ';
        echo '"Are you sure you want to empty the \'%s\' table? This will delete all the data inside of it. The following query will be run:":"' . __("Are you sure you want to empty the '%s' table? This will delete all the data inside of it. The following query will be run:") . '", ';
        echo '"Are you sure you want to drop the \'%s\' table? This will delete the table and all data inside of it. The following query will be run:":"' . __("Are you sure you want to drop the '%s' table? This will delete the table and all data inside of it. The following query will be run:") . '", ';
        echo '"Are you sure you want to drop the database \'%s\'? This will delete the database, the tables inside the database, and all data inside of the tables. The following query will be run:":"' . __("Are you sure you want to drop the database '%s'? This will delete the database, the tables inside the database, and all data inside of the tables. The following query will be run:") . '", ';

        echo '"Successfully saved changes":"' . __("Successfully saved changes") . '", ';

        echo '"New field":"' . __("New field") . '", ';

        echo '"Full Text":"' . __("Full Text") . '", ';

        echo '"Loading...":"' . __("Loading...") . '", ';
        echo '"Redirecting...":"' . __("Redirecting...") . '", ';

        echo '"Okay":"' . __("Okay") . '", ';
        echo '"Cancel":"' . __("Cancel") . '", ';

        echo '"Error":"' . __("Error") . '", ';
        echo '"There was an error receiving data from the server":"' . __("There was an error receiving data from the server") . '"';

    }

    echo '};';

    echo "\n";


    echo 'var menujson = {"menu": [';
    echo $conn->getMetadata();
    echo ']};';

    ?>
    //-->
</script>
</html>
