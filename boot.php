<?php

if (rex::isBackend()) {
    rex_view::addJsFile($this->getAssetsUrl('bootstrap-slider.min.js?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('script.js?v=' . $this->getVersion()));
    rex_view::addCssFile($this->getAssetsUrl('bootstrap-slider.min.css?v=' . $this->getVersion()));
    rex_view::addCssFile($this->getAssetsUrl('style.css?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('rangeInput.js'));

    $oSettings = new nvModuleSettings;
    $oSettings->syncWithThemeAddon();
}





rex_extension::register('SLICE_OUTPUT', function ($ep) {
    $sSubject = (string) "<?php \$oSettings = new nvModuleSettings(REX_MODULE_ID,rex_var::toArray(\"REX_VALUE[20]\")); ?>\r\n";
    $sSubject .= $ep->getSubject();
    return $sSubject;
});

rex_extension::register('STRUCTURE_CONTENT_MODULE_INPUT_ADD', function ($ep) {
    $iModuleId = (int) $ep->getParam("module_id");
    $oDb = rex_sql::factory();
    $oDb->setQuery('Select name FROM ' . rex::getTablePrefix() . 'module WHERE id = :id Limit 1',['id' => $iModuleId]);
    if ($oDb->getRows()) {
        if ($oDb->getValue("name") == "01 - Gridblock") {
            return $ep->getSubject();
        }
    }
    $sSubject = $ep->getSubject();

    preg_match_all('/\<\?/ims',$ep->getSubject(),$aOpenMatches);
    preg_match_all('/\?\>/ims',$ep->getSubject(),$aClosedMatches);

    if (count($aOpenMatches)) {
        if (count($aOpenMatches[0]) > count($aClosedMatches[0])) {
            $sSubject .= (string) "?>\r\n";
        }
    }

    $sSubject .= (string) "<?php\n
    \$oSettings = new nvModuleSettings(REX_MODULE_ID,rex_var::toArray(\"REX_VALUE[20]\"));\n
    echo \$oSettings->buildForm();";
    return $sSubject;
});

rex_extension::register('STRUCTURE_CONTENT_MODULE_INPUT_EDIT', function ($ep) {

    $iModuleId = (int) $ep->getParam("module_id");
    $oDb = rex_sql::factory();
    $oDb->setQuery('Select name FROM ' . rex::getTablePrefix() . 'module WHERE id = :id Limit 1',['id' => $iModuleId]);
    if ($oDb->getRows()) {
        if ($oDb->getValue("name") == "01 - Gridblock") {
            return $ep->getSubject();
        }
    }

    $sSubject = $ep->getSubject();

    preg_match_all('/\<\?/ims',$ep->getSubject(),$aOpenMatches);
    preg_match_all('/\?\>/ims',$ep->getSubject(),$aClosedMatches);

    if (count($aOpenMatches)) {
        if (count($aOpenMatches[0]) > count($aClosedMatches[0])) {
            $sSubject .= (string) "?>\r\n";
        }
    }
    

    $sSubject .= (string) "<?php\n 
    \$oSettings = new nvModuleSettings(REX_MODULE_ID,rex_var::toArray(\"REX_VALUE[20]\"));\n
    echo \$oSettings->buildForm();";
    return $sSubject;
});

rex_extension::register('STRUCTURE_CONTENT_MODULE_OUTPUT', function ($ep) {
    $iModuleId = (int) $ep->getParam("module_id");
    $oDb = rex_sql::factory();
    $oDb->setQuery('Select name FROM ' . rex::getTablePrefix() . 'module WHERE id = :id Limit 1',['id' => $iModuleId]);
    if ($oDb->getRows()) {
        if ($oDb->getValue("name") == "01 - Gridblock") {
            return $ep->getSubject();
        }
    }
    $sSubject = (string) "<?php \$oSettings = new nvModuleSettings(REX_MODULE_ID,rex_var::toArray(\"REX_VALUE[20]\")); ?>\r\n";
    $sSubject .= $ep->getSubject();

    preg_match_all('/\<\?/ims',$ep->getSubject(),$aOpenMatches);
    preg_match_all('/\?\>/ims',$ep->getSubject(),$aClosedMatches);

    if (count($aOpenMatches)) {
        if (count($aOpenMatches[0]) > count($aClosedMatches[0])) {
            $sSubject .= (string) "?>\r\n";
        }
    }

    $sSubject .= (string) "\r\n<?php echo \$oSettings->getBackendSummary(); ?>";
    return $sSubject;
});

rex_extension::register('GRIDBLOCK_CONTENT_MODULE_INPUT', function ($ep) {
    $sSubject = $ep->getSubject();

    preg_match_all('/\<\?/ims',$ep->getSubject(),$aOpenMatches);
    preg_match_all('/\?\>/ims',$ep->getSubject(),$aClosedMatches);

    if (count($aOpenMatches)) {
        if (count($aOpenMatches[0]) > count($aClosedMatches[0])) {
            $sSubject .= (string) "?>\r\n";
        }
    }

    $sSubject .= (string) "<?php\n
    \$oSettings = new nvModuleSettings(REX_MODULE_ID,rex_var::toArray(\"REX_VALUE[20]\"));\n
    echo \$oSettings->buildForm();";
    return $sSubject;
});

rex_extension::register('GRIDBLOCK_CONTENT_MODULE_OUTPUT', function ($ep) {
    $sSubject = (string) "<?php \$oSettings = new nvModuleSettings(REX_MODULE_ID); \$oSettings->getSettings(rex_var::toArray(\"REX_VALUE[20]\")); ?>\r\n";
    $sSubject .= $ep->getSubject();
    
    preg_match_all('/\<\?/ims',$ep->getSubject(),$aOpenMatches);
    preg_match_all('/\?\>/ims',$ep->getSubject(),$aClosedMatches);

    if (count($aOpenMatches)) {
        if (count($aOpenMatches[0]) > count($aClosedMatches[0])) {
            $sSubject .= (string) "?>\r\n";
        }
    }
    $sSubject .= (string) "\r\n<?php echo \$oSettings->getBackendSummary(); ?>";
    return $sSubject;
});
