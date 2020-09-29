# To run this code sample locally

1. Remove (_windows)  from composer.phar file and run `php composer.phar install`
2. Run the app with `php -S localhost:8080`
3. To reproduce High CPU browse `http://localhost:8080/cpu`, this code will detect the number of CPU cores available and will run a php script to keep them busy.
4. To reproduce High Memory browse `http://localhost:8080/memory`, to get a memory exception in php, try several times (about 5-10 requests) until it reaches 1 GB of memory, then you can check php error log.

# To deploy to Azure App Windows

1. Remove (_windows) from all files, set up your deployment option (LocalGit/GitHub),
2. Kudu site will run the custom deployment script installing all libraries with composer.
3. Same steps to reproduce high memory and cpu described above.

# To deploy to Azure App Linux

1. Add (_windows) to the following files: `.user.ini`, `composer.phar`, `.deployment`, `deploy.cmd`, `web.config`, these files are not needed for Linux and can conflict with Oryx build.
2. Set up your deployment option (LocalGit/GitHub).
3. Kudu and Oryx will run composer and install all the libraries.
4. Same steps to reproduce high memory and cpu described above.