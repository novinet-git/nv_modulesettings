<?php

if (rex::isBackend())
{
    rex_view::addJsFile($this->getAssetsUrl('bootstrap-slider.min.js?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('script.js?v=' . $this->getVersion()));
    rex_view::addCssFile($this->getAssetsUrl('bootstrap-slider.min.css?v=' . $this->getVersion()));
    rex_view::addCssFile($this->getAssetsUrl('style.css?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('rangeInput.js'));

    $oSettings = new nvModuleSettings;
    $oSettings->syncWithThemeAddon();
}