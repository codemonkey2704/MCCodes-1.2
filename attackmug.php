<?php
/*
MCCodes FREE
attackmug.php Rev 1.1.0
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
$h->userdata($ir, $lv, $fm, $cm, 0);
$h->menuarea();

$_GET['ID'] = abs((int) $_GET['ID']);
$_SESSION['attacking'] = 0;
$od = mysql_query("SELECT * FROM users WHERE userid={$_GET['ID']}", $c);
if ($_SESSION['attackwon'] != $_GET['ID'])
{
    die("Cheaters don't get anywhere.");
}
if (mysql_num_rows($od))
{
    $r = mysql_fetch_array($od);
    if ($r['hp'] == 1)
    {
        print "What a cheater u are.";
    }
    else
    {
        $stole = (int) (rand($r['money'] / 500, $r['money'] / 20));
        print "You beat {$r['username']} and stole \$$stole";
        $qe = $r['level'] * $r['level'] * $r['level'];
        $expgain = rand($qe / 4, $qe / 2);
        $expperc = (int) ($expgain / $ir['exp_needed'] * 100);
        print " and gained $expperc% EXP!";
        mysql_query(
                "UPDATE users SET exp=exp+$expgain,money=money+$stole WHERE userid=$userid",
                $c);
        mysql_query(
                "UPDATE users SET hp=1,money=money-$stole WHERE userid={$r['userid']}",
                $c);
        event_add($r['userid'],
                "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> attacked you and stole $stole.",
                $c);

        mysql_query(
                "UPDATE users SET hp=1,hospital=hospital+20+(rand()*20),hospreason='Attacked by <a href=\'viewuser.php?u={$userid}\'>{$ir['username']}</a>' WHERE userid={$r['userid']}",
                $c);
        $atklog = mysql_escape_string($_SESSION['attacklog']);
        mysql_query(
                "INSERT INTO attacklogs VALUES(NULL,$userid,{$_GET['ID']},'won',"
                        . time() . ",$stole,'$atklog');", $c);
        $_SESSION['attackwon'] = 0;
        $bots = array(2477, 2479, 2480, 2481, 263, 264, 265);
        $moneys =
                array(2477 => 80000, 2479 => 30000, 2480 => 30000,
                        2481 => 30000, 263 => 10000, 264 => 10000,
                        265 => 15000, 536 => 100000, 720 => 1400000,
                        721 => 1400000, 722 => 1400000, 585 => 5000000,
                        820 => 10000000);
        if (in_array($r['userid'], $bots))
        {
            $qk =
                    mysql_query(
                            "SELECT * FROM challengesbeaten WHERE userid=$userid AND npcid={$r['userid']}",
                            $c);
            if (!mysql_num_rows($qk))
            {
                $gain = $moneys[$r['userid']];
                mysql_query(
                        "UPDATE users SET money=money+$gain WHERE userid=$userid",
                        $c);
                mysql_query(
                        "INSERT INTO challengesbeaten VALUES ($userid,{$r['userid']})",
                        $c);
                print
                        "<br /><br />Congrats, for beating the Challenge Bot {$r['username']}, you have earnt \$$gain!";
            }
        }
    }
}
else
{
    print "You beat Mr. non-existant!";
}
$h->endpage();
