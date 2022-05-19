<?php
namespace Fql\FqlAnnotation\hook;

interface HookHandler
{
    function beforeHandle($array);
    function afterHandle($array);
}