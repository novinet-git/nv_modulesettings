<?php

$addon = rex_addon::get("nv_modulesettings");

if (rex::isBackend())
{
    rex_view::addJsFile($addon->getAssetsUrl('rangeInput.js'));
}