# Creating a Rector rule

## Create Rector class
Create a class extending `Rector\Core\Rector\AbstractRector`

```php
<?php

declare(strict_types=1);

namespace Selrahcd\LearnRector\ReplaceString;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ReplaceStringRector extends AbstractRector
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

namespace Selrahcd\LearnRector\ReplaceString;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ReplaceStringRectorTest extends AbstractRectorTestCase
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


use Selrahcd\LearnRector\ReplaceString\ReplaceStringRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $services = $containerConfigurator->services();
    $services->set(ReplaceStringRector::class);
};
```

## Add examples

In the `Fixture` directory add examples with the `.php.inc` extension.

First part of the file is the original code, second part is the expected code after applying the rule.

```php

<?php

'a';

?>
-----
<?php

'b';

?>

```

⚠️ The example file must not contain more than one new line at the end.

## Get the AST of the original code

Copy the original code in `temp.php` and run `vendor/bin/php-parse --var-dump temp.php`

```
array(1) {
  [0] =>
  class PhpParser\Node\Stmt\Expression#1181 (2) {
    protected $attributes =>
    array(4) {
      'startLine' =>
      int(3)
      'startFilePos' =>
      int(7)
      'endLine' =>
      int(3)
      'endFilePos' =>
      int(10)
    }
    public $expr =>
    class PhpParser\Node\Scalar\String_#1180 (2) {
      protected $attributes =>
      array(6) {
        'startLine' =>
        int(3)
        'startFilePos' =>
        int(7)
        'endLine' =>
        int(3)
        'endFilePos' =>
        int(9)
        'kind' =>
        int(1)
        'rawValue' =>
        string(3) "'a'"
      }
      public $value =>
      string(1) "a"
    }
  }
}
```

Looking at the node hierarchy we can identify the node type we want to work on. Here `PhpParser\Node\Scalar\String_`.

## Add node type to Rector class

```diff
final class ReplaceStringRector extends AbstractRector
 
     public function getNodeTypes(): array
     {
-        return [];
+        return [
+            PhpParser\Node\Scalar\String_::class
+        ];
     }
```

Because `ReplaceStringRectorTest::refactor` doesn't return anything yet no change is made in the original code.

```
src/ReplaceString/Fixture/replace-string.php.inc
Failed asserting that string matches format description.
--- Expected
+++ Actual
@@ @@
 <?php
 
-'b';
+'a';
 
 ?>
 ```

## Try returning a new node


```diff
final class ReplaceStringRector extends AbstractRector {
     
     public function refactor(Node $node)
     {
+         return new Node\Scalar\String_('b');
     }

}
```

The returned node replaces the node we're working on.
Here we are replacing the `String_` node with a new `String_` node containing the text `b`;

✅ Tests are green!

Our new rule is obviously not really useful as it modifies every string to the `b` string.

> **Note**
> In order to make the process of creating all these files easier we can use the `rector generate` commands.
> This commande generates the Rector rule, config, examples and test files following the file directory structure followed by the main Rector repository.