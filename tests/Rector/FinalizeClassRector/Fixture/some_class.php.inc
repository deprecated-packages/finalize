<?php

namespace TomasVotruba\Finalize\Tests\Rector\FinalizeClassRector\Fixture;

class SomeClass
{
}

?>
-----
<?php

namespace TomasVotruba\Finalize\Tests\Rector\FinalizeClassRector\Fixture;

final class SomeClass
{
}

?>
