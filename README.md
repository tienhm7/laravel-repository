# Template start helper, library

Template for repository helper, library - Basic, Simple and Lightweight

## Use this Template

First, you can `Use this template` for new project: [Use this template](https://github.com/tienhm7/template-php-lib/generate)

Second, clone your project to your to path in your machine

Finally, your edit file `composer.json` in root folder of project

```json
{
    "type": "library",
    "name": "tienhm7/template-php-lib",
    "description": "Template for repository helper, library - Basic, Simple and Lightweight",
    "keywords": [
        "template",
        "helper",
        "library",
        "php"
    ],
    "homepage": "https://github.com/tienhm7/template-php-lib",
    "license": "MIT",
    "minimum-stability": "stable",
    "authors": [
        {
            "name": "Hoang Manh Tien",
            "email": "tiencntt2@gmail.com",
            "homepage": "https://github.com/tienhm7",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=5.6"
    },
    "autoload": {
        "psr-4": {
            "tienhm7\\Libraries\\REPLACE_FOR_YOUR\\": "src/"
        },
        "files": [
            "helpers/helpers.php"
        ]
    }
}

```

Replace name space `REPLACE_FOR_YOUR` to Library space, example: `JSON`. After change namespace, project namespace same `"tienhm7\\Libraries\\JSON\\": "src/"`

Finished, your can writing new awesome helper and library now time.

## Contact & Support

If any question & request, please contact following information

| Name            | Email               | Skype                | Facebook       |
|-----------------|---------------------|----------------------|----------------|
| Hoang Manh Tien | tiencntt2@gmail.com | conthuyendocmoc_a888 | @tiencntt2hust |

From Ha Noi - Vietnam with Love <3
