Useful utils for [gist.cafe](https://gist.cafe) Apps.

## Usage

Simple Usage Example:

```php
use gistcafe\Inspect;

$orgName = "php";

$opts = [
    "http" => [
        "header" => "User-Agent: gist.cafe\r\n"
    ]
];
$context = stream_context_create($opts);
$json = file_get_contents("https://api.github.com/orgs/{$orgName}/repos", false, $context);
$orgRepos = array_map(function($x) {
    $x = get_object_vars($x);
    return [
        "name"        => $x["name"],
        "description" => $x["description"],
        "url"         => $x["url"],
        "lang"        => $x["language"],
        "watchers"    => $x["watchers"],
        "forks"       => $x["forks"],
    ]; 
}, json_decode($json));
usort($orgRepos, function($a,$b) { return $b["watchers"] - $a["watchers"]; });

echo  "Top 3 {$orgName} GitHub Repos:\n";
Inspect::printDump(array_slice($orgRepos, 0, 3));

echo  "\nTop 10 {$orgName} GitHub Repos:\n";
Inspect::printDumpTable(array_map(function($x) {
    return [
        "name"        => $x["name"],
        "lang"        => $x["lang"],
        "watchers"    => $x["watchers"],
        "forks"       => $x["forks"],
    ]; 
}, array_slice($orgRepos, 0, 10)));
```

Which outputs:

```
Top 3 php GitHub Repos:
[
    {
        name: php-src,
        description: The PHP Interpreter,
        url: https://api.github.com/repos/php/php-src,
        lang: C,
        watchers: 29300,
        forks: 6484
    },
    {
        name: web-php,
        description: The www.php.net site,
        url: https://api.github.com/repos/php/web-php,
        lang: PHP,
        watchers: 565,
        forks: 344
    },
    {
        name: php-gtk-src,
        description: The PHP GTK Bindings,
        url: https://api.github.com/repos/php/php-gtk-src,
        lang: C++,
        watchers: 192,
        forks: 48
    }
]

Top 10 php GitHub Repos:
+------------------------------------------------+
|      name      |    lang    | watchers | forks |
|------------------------------------------------|
| php-src        | C          |    29300 |  6484 |
| web-php        | PHP        |      565 |   344 |
| php-gtk-src    | C++        |      192 |    48 |
| web-qa         | PHP        |       61 |    32 |
| phd            | PHP        |       55 |    25 |
| web-bugs       | PHP        |       45 |    55 |
| web-doc-editor | JavaScript |       42 |    29 |
| presentations  | HTML       |       42 |    19 |
| web-wiki       | PHP        |       31 |    19 |
| systems        | C          |       26 |    11 |
+------------------------------------------------+
```

## Features and bugs

Please file feature requests and bugs to the [issue tracker](https://github.com/ServiceStack/gistcafe-php/issues).