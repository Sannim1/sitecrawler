This project contains an utility for generating sitemaps given a particular URL. There are two ways of executing this utility

**NB: All CLI commands assume that you have navigated to the same directory as this README file**

## METHOD 1 (Using Docker)

### STEP 1
Follow the instructions here(https://docs.docker.com/engine/getstarted/step_one/) to install docker for your machine. On completion of the installation, open your CLI of choice and run `docker --version` to verify that it is installed

### STEP 2 (only needs to be done once)
In your CLI, make the script in `install_crawler.sh` executable with the command `chmod a+x install_crawler.sh`.
Execute the script using the command `./install_crawler.sh`

### STEP 3
Execute the following script within your CLI making sure to replace `http://example.com` with the URL of the website you wish to crawl
```
docker run --rm -it \
    -v $(pwd):/sitecrawler \
    sitecrawler/composer:latest \
    php -d memory_limit=-1 crawl.php http://example.com
```
The sitemap(consisting of the visited urls, links and assets) and assetmap(just urls and assets) can be found in the `sitemaps/{url_of_the_crawled_website}` directory

## METHOD 2 (Without Docker)

### STEP 1 (only needs to be done once)
This project is built with php, hence it requires you to have php(version 5.6) installed on your machine. When installation is complete, run `php --version` from your CLI to verify that it is installed.

### STEP 2 (only needs to be done once)
Follow the instructions here(https://getcomposer.org/download/) to download and install composer. When installation is complete, run `composer --version` from your CLI to verify that it is installed.

### STEP 3 (only needs to be done once)
Run `composer install` in order to install the dependencies required by this utility. When installation is done, run `composer dump-autoload`

### STEP 4
Use the command `php -d memory_limit=-1 crawl.php http://example.com` to start crawling from the supplied URL.
Make sure to replace `http://example.com` with a URL of your choosing.

The sitemap(consisting of the visited urls, links and assets) and assetmap(just urls and assets) can be found in the `sitemaps/{url_of_the_crawled_website}` directory

# Running Tests

## METHOD 1 (Using Docker)

**(assumes you've followed STEP 1 in the `Using Docker` section above)** 

* In your CLI, make the script in `test_crawler.sh` executable with the command `chmod a+x test_crawler.sh`.
* Execute the script using the command `./test_crawler.sh`
* View test coverage results by opening `build/coverage/index.html` in a browser of your choice

## METHOD 2 (Without Docker)

**(assumes you've followed STEPS 1, 2 & 3 in the `Without Docker` section above)**

* From your CLI, run `vendor/bin/phpunit -c phpunit.xml`
* View test coverage results by opening `build/coverage/index.html` in a browser of your choice
