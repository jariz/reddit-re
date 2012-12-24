#Reddit.re Official Github Repository  
##What is Reddit.re?  
Reddit.re is a site with a mission: We want to provide tools for people without programming skills.  
To create your own bot with reddit.re you will require absolutely zero programming knowledge.  
##Where does Reddit.re run on?  
Reddit.re wouldn't exist without these great pieces of software.  
* Heavily modified version of [Reddit PHP API Client](https://github.com/h2s/reddit-api-client)  
The best PHP reddit api client out there, foh sure.  
* [Bootstrap](https://github.com/twitter/bootstrap)  
The best front-end framework to date.  
* [Bootswatch](https://github.com/thomaspark/bootswatch)  
Awesome site that has several beautiful bootstrap 'templates'  
* [Codeigniter](https://github.com/EllisLab/CodeIgniter)  
My favorite PHP-framework.  
* [IcoMoon](http://icomoon.io/app/)  
Great icons. Very useful app  
  
##What specs does my server need to run this?  
* (at least) PHP 5.2  
* MySQL  
* xdebug extension on PHP  
  
##How to set it up?  
1. Edit `application/config/config.php` and fill in random strings pretty much everywhere except for the username and password which will contain your default's bot username and password  
2. Edit `application/config/database.php` and enter DB info.
3. Run `db.sql`  
4. Edit hardcoded URL's, Sorry, Sadly there are quite a lot of hardcoded URL's.  
5. Enjoy.
