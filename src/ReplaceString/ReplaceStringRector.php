<?php

declare(strict_types=1);

namespace Selrahcd\LearnRector\ReplaceString;

use PhpParser\Comment;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ReplaceStringRector extends AbstractRector
{

    public function getNodeTypes(): array
    {
        return [
            Node\Scalar\String_::class
        ];
    }

    /**
     * @param Node $node
     */
    public function refactor(Node $node)
    {
        return new Node\Scalar\String_('b');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        throw new \Exception('getRuleDefinition() not implemented yet');
    }
}