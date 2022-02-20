
# :file_folder: Diridea

The idea of this library is to provide a simple and easy to understand directory structure for your static files in your project.

### abstract

```*--[web|backend]_[public|private]_[expire|archive]\d[d|h]?_[encrypt]?_[backup]?_[cache]?```

### regex

https://regex101.com/r/RVr7SJ/1

```#(?<prefix>.*?)--(?<location>web|backend)_(?<visibility>public|private)_(?<timings>(?<timing_option>expire|archive)(?<timing_value>\d*)(?<timing_unit>[d|h])[_]?)?(?<encrypt>encrypt[_]?)?(?<backup>backup[_]?)?(?<cache>cache[_]?)?#```

### example

e.g.: ```download--web_public_expire30d```

-> directory with the prefix "download" available in the web, public for all system-users and the files will be expired and deleted after 30 days

e.g.: ```article_images--backend_private_archive7h_encrypt```

-> directory with the prefix "article_images" available in the backend, only accessible for the current system-user and the files will be moved into the "article_images*/archive/" directory after 7 hours


### Unit Test:

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit
```


### Support

For support and donations please visit [Github](https://github.com/voku/diridea/) | [Issues](https://github.com/voku/diridea/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/diridea/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Managment, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerfull code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for relly great Static analysis tools and for discover bugs in the code!

### License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fvoku%2Fdiridea.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fvoku%2Fdiridea?ref=badge_large)
