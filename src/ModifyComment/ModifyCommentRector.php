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
        return [
            Node\Stmt\Nop::class
        ];
    }

    /**
     * @param Node $node
     */
    public function refactor(Node $node)
    {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        throw new \Exception('getRuleDefinition() not implemented yet');
    }
}