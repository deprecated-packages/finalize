# Finalize

Finalize classes in a safe way. We first look for those, that should be skipped:

* classes who are in parent position
* Doctrine entities by docblocks and attribute

## Install

```bash
composer require tomasvotruba/finalize --dev
```

## Usage

1. First run command, that detects parent classes, entities etc.

```bash
vendor/bin/finalize detect src tests
```

It will generate `.finalize.json` files with all found classes, that should be skipped.

<br>

2. Run Rector with config that contains `TomasVotruba\Finalize\Rector\FinalizeClassRector` rule.

Rector uses data from `.finalize.json` to keep used classes non final and finalize only the safe ones:

```bash
vendor/bin/rector process --config vendor/tomasvotruba/finalize/config/prepared-rector.php
```

Do not keep this run in your main `rector.php`. Family map can change with any new class, e.g. some new class will come and it will be extended, and Rector would not finalize valid class.
