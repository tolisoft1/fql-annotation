<?php
namespace Fql\FqlAnnotation\hook;

interface HookHandle
{
    function beforeHandle($array);
    function afterHandle($array);
}