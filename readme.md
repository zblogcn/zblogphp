
Z-BlogPHP
=============

Z-BlogPHP is an open-source website building program provided by the Z-Blog community. The first version was released in 2005, giving the project a 21-year history. It has always been dedicated to giving users in China an excellent experience, and it is one of the few open-source CMS systems in the country that still receive regular updates.

Our goal is to free users from tedious settings. From personal blogs and corporate websites, to knowledge bases, news portals and community forums — whatever website you can think of, and plenty you can't, Z-BlogPHP can build it.

For users, it is simple to use, small in size, fast, and able to handle large amounts of data. For developers, it offers powerful customizability, a rich set of plugin interfaces, and beautifully designed theme templates.

We are constantly working to make Z-BlogPHP a highly playable, LEGO-brick-style website program!

## Security Vulnerabilities

For supported versions, reporting channels, acceptance criteria and the full disclosure process, please see [SECURITY.md](SECURITY.md). Do not discuss vulnerability details in public issues.

## Community
1. For usage questions and development suggestions, please visit the [Z-Blog Developer Community](https://bbs.zblogcn.com/);
2. For developer documentation, see the [Docs](https://docs.zblogcn.com/);
3. For functional bugs, please post in the developer community or open a GitHub Issue;
4. Pull requests are welcome, and if you like this project, please give us a Star :)

## Disclaimer

[Disclaimer](https://www.zblogcn.com/disclaimer/)

## Requirements

- Windows / Linux / macOS and so on...
- IIS / Apache / nginx / Lighttpd / Kangle / Tengine / Caddy and so on...
- PHP 7, 8.0-8.4
- MySQL 5+ / MariaDB 10+ / SQLite 3 / PostgreSQL

## Installation

First, make sure the website directory has 755 permissions. If you use the development version from GitHub, please [download the stable release](http://www.zblogcn.com/zblogphp/) and install it first, then overwrite it with the files from GitHub.

1. Upload the Z-BlogPHP program files to the website directory
2. Open http://your-website/ to enter the installation wizard
3. Set up the database
   - For MySQL, enter the MySQL account and password provided by your hosting provider
   - For SQLite, make sure the server supports SQLite; the installer will create the SQLite database file automatically after you click Next
   - For PostgreSQL, enter the host, database name, account and password
4. Fill in the administrator account and password for your site; please use a strong password
5. Click Next. Once the installation succeeds, you can enter your site

After installation, please delete the `zb_install` folder. For development versions from GitHub, please also delete the `standards`, `tests`, `utils` and other such folders.

## Coding Standards

[Coding Standards](standards)

## License

The Z-BlogPHP project is open-sourced under [The MIT License](http://opensource.org/licenses/MIT).
