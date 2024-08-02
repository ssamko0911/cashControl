<?php

use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $ecsConfig->sets([
        SetList::PHPUNIT,
        SetList::CLEAN_CODE,
        SetList::DOCBLOCK,
        SetList::COMMENTS,
        SetList::ARRAY,
        SetList::NAMESPACES,
        SetList::PSR_12,
        SetList::SPACES,
        SetList::STRICT,
        SetList::CONTROL_STRUCTURES,
        SetList::DOCTRINE_ANNOTATIONS,
        SetList::SYMPLIFY,
    ]);

    $ecsConfig->skip([
        __DIR__.'/src/Kernel.php',
        __DIR__.'/var',
        __DIR__.'/vendor',
        __DIR__.'/ecs.php',
        NativeFunctionInvocationFixer::class,
        PhpUnitTestClassRequiresCoversFixer::class,
        PhpdocToCommentFixer::class,
        NoUselessReturnFixer::class,
        SingleLineThrowFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, [
        'spacing' => 'one',
    ]);
    $ecsConfig->rule(DeclareStrictTypesFixer::class);
};