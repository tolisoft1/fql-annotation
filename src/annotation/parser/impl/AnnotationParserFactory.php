<?php

namespace Fql\FqlAnnotation\annotation\parser\impl;

interface AnnotationParserFactory
{
    public static function  parse($className);
}