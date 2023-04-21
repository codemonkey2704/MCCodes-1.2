# MCCodes-1.2
Upgraded to PHP 8.2 and require 8.2+ to work

You made need to run ```SET GLOBAL sql_mode=''``` in your phpmyadmin to install this code base

I suggest not using this code without extensive testing for production 

Changes made to some installer code to inject mysql Shim

Shim included for mysql conversion to mysqli

Still uses MD5 encryption (May change)

This is a raw conversion and will have some errors, if you find any errors please feel free to submit a pull request
