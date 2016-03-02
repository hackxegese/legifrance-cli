<?php
declare(strict_types = 1);

namespace Legifrance\Command;

class Step
{
    const START = 0;
    const NEXT_LINE = 1;
    const NEW_LINE = 2;
    const END_OF_ART = 3;
    const IN_ART = 4;
    const STOP = 99;
}
