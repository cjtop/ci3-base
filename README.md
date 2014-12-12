CodeIgniter 3.x Basic Application
========

My base setup for a new CodeIgniter 3.x application. (Note this is NOT a full CodeIgniter package)

I put this here primarily for my own use, but others are welcome to use it and
build on it. The idea is to drop in a CodeIgniter 3.x system and full application
directory, then grab the files in this repo and build. An ant build file that will
SFTP the changed files to your server is included. You'll need to set the attributes using
either command line arguments or Eclipse or something. The arguments are
* -Duser= Your FTP username
* -Dpassword= Your FTP password
* -Dserver= Your FTP server
* -Dport= Your SFTP port (usually 22 but might be something else)
* -Dremote= Your remote CodeIgniter application directory on the server
* -Dassets_remote= Your remote public assets directory on the server

CI_Controller has been extended with a MY_Controller that sets up profiling
in development, executes a less compiler using caching, and renders HTML using
a template defined at application/views/templates/site.php.

Happy building!
