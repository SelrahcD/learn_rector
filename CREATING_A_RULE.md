# Creating a Rector rule

## Create Rector class
Create a class extending `Rector\Core\Rector\AbstractRector`

```php
<?php

declare(strict_types=1);

namespace Selrahcd\LearnRector\ModifyComment;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ModifyCommentRector extends AbstractRector
{

    public function getNodeTypes(): array
    {
        return [];
    }

    public function refactor(Node $node)
    {
    }
    
    public function getRuleDefinition(): RuleDefinition
    {
        throw new \Exception('getRuleDefinition() not implemented yet');
    }
}
```
## Create a test class
Create a test class extending `Rector\Core\Rector\AbstractRector`

Below is the body of a typical Rector test.

```php
<?php

declare(strict_types=1);

namespace Selrahcd\LearnRector\ModifyComment;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ModifyCommentRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/config.php';
    }
}

```

## Create a configuration file

Create a `config.php` configuration file in the `config` directory next to the test class.

Add the new rule to the list of services.

```php
<?php

declare(strict_types=1);


use Selrahcd\LearnRector\ModifyComment\ModifyCommentRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $services = $containerConfigurator->services();
    $services->set(ModifyCommentRector::class);
};
```

## Add examples

In the `Fixture` directory add examples with the `.php.inc` extension.

First part of the file is the original code, second part is the expected code after applying the rule.

```php

<?php

// A comment

?>
-----
<?php

// A modified comment

?>

```

## Get the AST of the original code

Copy the original code in `temp.php` and run `vendor/bin/php-parse --var-dump temp.php`

```
./vendor/nikic/php-parser/bin/php-parse:91:
array(1) {
  [0] =>
  class PhpParser\Node\Stmt\Nop#1181 (1) {
    protected $attributes =>
    array(5) {
      'startLine' =>
      int(5)
      'startFilePos' =>
      int(21)
      'comments' =>
      array(1) {
        [0] =>
        class PhpParser\Comment#1180 (7) {
          protected $text =>
          string(12) "// A comment"
          protected $startLine =>
          int(3)
          protected $startFilePos =>
          int(7)
          protected $startTokenPos =>
          int(2)
          protected $endLine =>
          int(3)
          protected $endFilePos =>
          int(18)
          protected $endTokenPos =>
          int(2)
        }
      }
      'endLine' =>
      int(5)
      'endFilePos' =>
      int(22)
    }
  }
}

```

Looking at the node hierarchy we can identify the node type we want to work on. Here `PhpParser\Node\Stmt\Nop`.

## Add node type to Rector class

```diff
final class ModifyCommentRector extends AbstractRector
 
     public function getNodeTypes(): array
     {
-        return [];
+        return [
+             Node\Stmt\Nop::class
+        ];
     }
```

Because `ModifyCommentRector::refactor` doesn't return anything yet no change is made in the original code.

```
src/ModifyComment/Fixture/replace-comment.php.inc
Failed asserting that string matches format description.
--- Expected
+++ Actual
@@ @@
 <?php
 
-// A modified comment
+// A comment
 
 ?>
-
```

## Try returning a new node



