<?php
/*
MCCodes FREE
itembuy.php Rev 1.1.0
Copyright (C) 2005-2012 Dabomstew

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
require "global_func.php";
if ($_SESSION['loggedin'] == 0)
{
    header("Location: login.php");
    exit;
}
$userid = $_SESSION['userid'];
require "header.php";
$h = new headers;
$h->startheaders();
include "mysql.php";
global $c;
$is =
        mysql_query(
                "SELECT u.*,us.* FROM users u LEFT JOIN userstats us ON u.userid=us.userid WHERE u.userid=$userid",
                $c) or die(mysql_error());
$ir = mysql_fetch_array($is);
check_level();
$fm = money_formatter($ir['money']);
$cm = money_formatter($ir['crystals'], '');
$lv = date('F j, Y, g:i a', $ir['laston']);
$h->userdata($ir, $lv, $fm, $cm);
$h->menuarea();
$_GET['ID'] = abs((int) $_GET['ID']);
$_POST['qty'] = abs((int) $_POST['qty']);
if (!$_GET['ID'] || !$_POST['qty'])
{
    print "Invalid use of file";
}
else if ($_POST['qty'] <= 0)
{
    print
            "You have been added to the delete list for trying to cheat the game.";
}
else
{
    $q = mysql_query("SELECT * FROM items WHERE itmid={$_GET['ID']}", $c);
    if (mysql_num_rows($q) == 0)
    {
        print "Invalid item ID";
    }
    else
    {
        $itemd = mysql_fetch_array($q);
        if ($ir['money'] < $itemd['itmbuyprice'] * $_POST['qty'])
        {
            print "You don't have enough money to buy this item!";
            $h->endpage();
            exit;
        }
        if ($itemd['itmbuyable'] == 0)
        {
            print "This item can't be bought!";
            $h->endpage();
            exit;
        }
        $price = ($itemd['itmbuyprice'] * $_POST['qty']);
        mysql_query(
                "INSERT INTO inventory VALUES(NULL,{$_GET['ID']},$userid,{$_POST['qty']});",
                $c);
        mysql_query(
                "UPDATE users SET money=money-$price WHERE userid=$userid",
                $c);
        mysql_query(
                "INSERT INTO itembuylogs VALUES (NULL, $userid, {$_GET['ID']}, $price, {$_POST['qty']}, "
                        . time()
                        . ", '{$ir['username']} bought {$_POST['qty']} {$itemd['itmname']}(s) for {$price}')",
                $c);
        print "You bought {$_POST['qty']} {$itemd['itmname']}(s) for \$$price";
    }
}
$h->endpage();
