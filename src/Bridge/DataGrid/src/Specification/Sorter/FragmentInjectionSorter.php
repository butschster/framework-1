<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\Database\Injection\Fragment;

final class FragmentInjectionSorter extends InjectionSorter
{
    protected const INJECTION = Fragment::class;
}
