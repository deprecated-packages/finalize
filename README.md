# Finalize

Note: this package might be added to https://github.com/rectorphp/swiss-knife, as it covers multiple useful scripts.

Finalize classes in a safe way. We first look for those, that should be skipped:

* classes who are in parent position
* Doctrine entities by docblocks and attribute

## Install

```bash
composer require tomasvotruba/finalize --dev
```

## Usage

```bash
vendor/bin/finalize finalize src tests
```

It will:

1. generate `finalize.json` file in your temp directory with all found classes, that should be skipped
2. it will go through your files and finalize every class, that is not in this list

<br>

Happy coding!
