<?php class nvModuleSettings
{
    public static $settings = '';

    public function __construct($iModuleId = 0, ?array $aSavedOptions = array())
    {
        $this->addon = rex_addon::get('nv_modulesettings');

        $this->sSettingsFilename = "modulesettings.json";
        $this->sIgnoreFilename = "modulesettings.ignore.json";

        // true wenn noch alte CS (unter version 2) genutzt werden
        $this->bDeprecated = false;
        $this->iSettingsId = "20";

        $this->fileGlobal = $this->addon->getDataPath('modulesettings.global.json');
        $this->fileProject = $this->addon->getDataPath($this->sSettingsFilename);
        $this->aSettings = [];
        $this->settings = "";
        $this->ignore = false;
        $this->aSavedOptions = $aSavedOptions;

        if ($iModuleId) {
            $this->iModuleId = $iModuleId;
        }

        $this->getAllSettings();

        if (isset($this->aSavedOptions)) {
            $this->getSettings($this->aSavedOptions);
        }

        self::factory($this);
    }


    public static function factory($oObj)
    {
        self::$settings = $oObj;
    }

    function getAllSettings()
    {

        if (file_exists($this->fileGlobal)) {
            $sGlobal = $this->getJsonContent($this->fileGlobal);
            $this->globalData = (json_decode($sGlobal, true));
        }

        if (file_exists($this->fileProject)) {
            $sProject = $this->getJsonContent($this->fileProject);
            $this->projectData = (json_decode($sProject, true));
        }

        if ($this->iModuleId) {
            $sBaseDir = theme_path::base("private/redaxo/modules/");
            $aDirs = glob($sBaseDir . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK);

            foreach ($aDirs as $sDir) {
                if (strpos($sDir, "[" . $this->iModuleId . "]") !== false) {
                    break;
                }
            }

            if (file_exists($sDir . $this->sSettingsFilename)) {
                $sModule = file_get_contents($sDir . $this->sSettingsFilename);
                $this->moduleData = (json_decode($sModule, true));
            }

            if (file_exists($sDir . $this->sIgnoreFilename)) {
                $this->ignore = true;
            }

            rex_fragment::addDirectory($sDir . "fragments");
        }

        if (isset($this->globalData["showOptions"])) {
            if (count($this->globalData["showOptions"])) {
                $this->aSettings["showOptions"] = $this->globalData["showOptions"];
            }
        }

        if (isset($this->projectData["showOptions"])) {
            if (count($this->projectData["showOptions"])) {
                $this->aSettings["showOptions"] = $this->projectData["showOptions"];
            }
        }

        if (isset($this->moduleData["showOptions"])) {
            if (count($this->moduleData["showOptions"])) {
                $this->aSettings["showOptions"] = $this->moduleData["showOptions"];
            }
        }


        $this->aSettings["categories"] = array();

        if (isset($this->globalData["categories"])) {
            if (count($this->globalData["categories"])) {
                foreach ($this->globalData["categories"] as $aCategory) {
                    $sKey = $aCategory["key"];
                    $sIcon = isset($aCategory["icon"]) ? $aCategory["icon"] : "";
                    $this->aSettings["categories"][$sKey] = array("label" => $aCategory["label"], "icon" => $sIcon);
                }
            }
        }

        if (isset($this->projectData["categories"])) {
            if (count($this->projectData["categories"])) {
                foreach ($this->projectData["categories"] as $aCategory) {
                    $sKey = $aCategory["key"];
                    $sIcon = isset($aCategory["icon"]) ? $aCategory["icon"] : "";
                    $this->aSettings["categories"][$sKey] = array("label" => $aCategory["label"], "icon" => $sIcon);
                }
            }
        }

        if (isset($this->moduleData["categories"])) {
            if (count($this->moduleData["categories"])) {
                foreach ($this->moduleData["categories"] as $aCategory) {
                    $sKey = $aCategory["key"];
                    $sIcon = isset($aCategory["icon"]) ? $aCategory["icon"] : "";
                    $this->aSettings["categories"][$sKey] = array("label" => $aCategory["label"], "icon" => $sIcon);
                }
            }
        }


        // Hide Options in Projekt
        if (isset($this->projectData["hideOptions"])) {
            foreach ($this->projectData["hideOptions"] as $sKey) {
                if (($iX = array_search($sKey, $this->aSettings["showOptions"])) !== false) {
                    unset($this->aSettings["showOptions"][$iX]);
                }
            }
        }

        // Hide Options in Module
        if (isset($this->moduleData["hideOptions"])) {
            foreach ($this->moduleData["hideOptions"] as $sKey) {
                if (($iX = array_search($sKey, $this->aSettings["showOptions"])) !== false) {
                    unset($this->aSettings["showOptions"][$iX]);
                }
            }
        }

        // Options
        $aUsedKeys = [];

        if (isset($this->globalData["options"])) {
            if (count($this->globalData["options"])) {
                foreach ($this->globalData["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->projectData["options"])) {
            if (count($this->projectData["options"])) {
                foreach ($this->projectData["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }


        if (isset($this->moduleData["options"])) {
            foreach ($this->moduleData["options"] as $aOption) {
                $sKey = $aOption["key"];
                if (!in_array($sKey, $aUsedKeys)) {
                    $this->aSettings["options"][$sKey] = $aOption;
                    array_push($aUsedKeys, $sKey);
                } else {
                    foreach ($aOption as $sOptionKey => $mOptionVal) {
                        if (isset($mOptionVal)) {
                            $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                        }
                    }
                }
            }
        }


        // aSettings aufgebaut


        // parents

        foreach ($this->aSettings["options"] as $sKey => $aOptions) {
            if (isset($aOptions["parent"])) {
                if (isset($this->aSettings["options"][$sKey])) {
                    $sParent = $aOptions["parent"];
                    foreach ($this->aSettings["options"][$sParent] as $sKeyParent => $sValueParent) {
                        if (!isset($aOptions[$sKeyParent])) {
                            $this->aSettings["options"][$sKey][$sKeyParent] = $sValueParent;
                        }
                    }
                }
            }
        }


        // start_group & end_group
        if (isset($this->aSettings["showOptions"])) {
            $iPos = "1";

            foreach ($this->aSettings["showOptions"] as $sOption) {
                $aOption = $this->aSettings["options"][$sOption];
                if ($aOption["type"] == "group") {
                    $aNewShowOptions = array();
                    $sLabel = $this->aSettings["options"][$sOption]["label"];
                    $this->aSettings["options"][$sOption]["type"] = "html";

                    $sAccordionOpen = $sAriaExpanded = "";
                    if (isset($this->aSettings["options"][$sOption]["open"])) {
                        if ($this->aSettings["options"][$sOption]["open"] == true) {
                            $sAccordionOpen = "in";
                            $sAriaExpanded = 'aria-expanded="true"';
                        }
                    }

                    $sHtml = '<div class="nvmodulesettings-group" id="nvmodulesettings-accordion_' . $aOption["key"] . '"><div class="nvmodulesettings-heading"><a data-toggle="collapse" data-parent="#nvmodulesettings-accordion_' . $aOption["key"] . '" href="#nvmodulesettings-accordion_collapes_' . $aOption["key"] . '" ' . $sAriaExpanded . '>';
                    if (isset($this->aSettings["options"][$sOption]["icon"]) && $this->aSettings["options"][$sOption]["icon"] != "") {
                        $sTippy = "";
                        if (isset($this->aSettings["options"][$sOption]["icon_tooltip"])) {
                            $sTippy = ' data-tippy-content="' . $this->aSettings["options"][$sOption]["icon_tooltip"] . '"';
                        }
                        $sHtml .= ' <i class="' . $this->aSettings["options"][$sOption]["icon"] . '" style="padding-right:10px" ' . $sTippy . '></i>';
                    }
                    $sHtml .= $sLabel . '</a></div><div id="nvmodulesettings-accordion_collapes_' . $aOption["key"] . '" class="nvmodulesettings-body collapse ' . $sAccordionOpen . '"><div class="gbpanel-body">';

                    $this->aSettings["options"][$sOption]["text"] = $sHtml;
                    $this->aSettings["options"][$sOption]["label"] = "";

                    $sCategory = "";
                    if (isset($aOption["category"])) {
                        $sCategory = $aOption["category"];
                    }

                    $aGroupOptions = $aOption["options"];
                    foreach ($aGroupOptions as $sGroupOption) {

                        $iArrKey = array_search($sGroupOption, $this->aSettings["showOptions"]);
                        if ($iArrKey != "") {
                            $this->aSettings["showOptions"][$iArrKey] = "nvmodulesettings_deleted_option";
                        }

                        $aNewShowOptions[] = $sGroupOption;
                        $this->aSettings["options"][$sGroupOption]["category"] = $sCategory;
                    }

                    $aNewShowOptions[] = "nvmodulesettings_group_end_" . $aOption["key"];

                    $this->aSettings["options"]["nvmodulesettings_group_end_" . $aOption["key"]] = array("type" => "html", "text" => "</div></div></div>", "category" => $sCategory);

                    array_splice($this->aSettings["showOptions"], $iPos, 0, $aNewShowOptions);
                    $iPos = $iPos + count($aGroupOptions) + 1;
                }
                $iPos++;
            }

            $aTmp = array();
            foreach ($this->aSettings["showOptions"] as $sOption) {
                if ($sOption != "nvmodulesettings_deleted_option") {
                    $aTmp[] = $sOption;
                }
            }
            $this->aSettings["showOptions"] = $aTmp;
        }

        // Kategorien aufbauen
        $this->aSettings["options_in_categories"] = false;
        foreach ($this->aSettings["showOptions"] as $sOption) {
            $aOption = $this->aSettings["options"][$sOption];
            $sCategory = isset($aOption["category"]) ? $aOption["category"] : "";
            if ($sCategory != "") {
                if (!isset($this->aSettings["categories"][$sCategory])) {
                    $this->aSettings["categories"][$sCategory] = array("label" => $sCategory);
                }
                $this->aSettings["categories"][$sCategory]["showOptions"][] = $sOption;
                $this->aSettings["options_in_categories"] = true;
            }
        }

        if ($this->aSettings["options_in_categories"] == true) {
            foreach ($this->aSettings["showOptions"] as $sOption) {
                $aOption = $this->aSettings["options"][$sOption];
                $sCategory = isset($aOption["category"]) ? $aOption["category"] : "";
                if ($sCategory == "") {
                    $sCategory = "nvmodulesettingsgeneral";
                    if (!isset($this->aSettings["categories"][$sCategory])) {
                        $this->aSettings["categories"][$sCategory] = array("label" => "Sonstige Einstellungen");
                    }
                    $this->aSettings["categories"][$sCategory]["showOptions"][] = $sOption;
                }
            }
        }

        if ($this->aSettings["options_in_categories"] == false) {
            $this->aSettings["categories"] = array();
            $this->aSettings["categories"]["nvmodulesettingsgeneral"] = array("label" => "Keine Kategorien verwendet");
            foreach ($this->aSettings["showOptions"] as $sOption) {
                $this->aSettings["categories"]["nvmodulesettingsgeneral"]["showOptions"][] = $sOption;
            }
        }
    }

    public function getOptions($sKey)
    {
        $aSelectData = $this->getSelectData($sKey);
        if (isset($aSelectData)) {
            $this->aSettings["options"][$sKey]["selectdata"] = $aSelectData;
        }
        return $this->aSettings["options"][$sKey];
    }

    public function getDefault($sKey)
    {
        if (isset($this->aSettings["options"][$sKey]["default"])) {
            return $this->aSettings["options"][$sKey]["default"];
        }
    }

    function getOptionLabel($sKey, $sValue)
    {
        return $this->aSettings["options"][$sKey]["selectdata"][$sValue];
    }


    function getSelectData($sKey)
    {

        $aOption = $this->aSettings["options"][$sKey];

        $aData = array();

        if (isset($aOption["data"])) {
            foreach ($aOption["data"] as $sOptionsKey => $sOptionsVal) {
                if (isset($aOption["default"])) {
                    if ($sOptionsKey == $aOption["default"]) {
                        $sOptionsKey = "nvmodulesettingsdefault";
                        $sOptionsVal .= " " . $this->addon->i18n("nvmodulesettingsdefault_default_option");
                    }
                }
                $aData[$sOptionsKey] = $sOptionsVal;
            }
        }

        return $aData;
    }


    function buildForm(?array $aSavedOptions = array())
    {
        if (!count($aSavedOptions)) {
            $aSavedOptions = $this->aSavedOptions;
        }

        if ($this->ignore) return;
        $sForm = "";
        $aUsedTypes = array();
        $iTabRand = rand(0, 100) * rand(0, 100);
        if (isset($this->aSettings["categories"])) {

            if ($this->aSettings["options_in_categories"] == true) {
                $sForm .= '<ul class="nav nav-tabs">';
                $iX = 0;
                foreach ($this->aSettings["categories"] as $aCategory) {
                    if (isset($aCategory["showOptions"])) {
                        $sForm .= '<li><a href="#nv-modulesettings-content-' . $iTabRand . '-' . $iX . '" data-toggle="tab">';
                        if (isset($aCategory["icon"])) {
                            if ($aCategory["icon"] != "") {
                                $sForm .= ' <i class="' . $aCategory["icon"] . '" style="padding-right:10px"></i>';
                            }
                        }
                        $sForm .= $aCategory["label"] . '</a></li>' . PHP_EOL;
                        $iX++;
                    }
                }
                $sForm .= '</ul>';
            }

            $sForm .= '<div class="tab-content';
            if ($this->aSettings["options_in_categories"] == true) {
                $sForm .= ' options_in_categories';
            } else {
                $sForm .= ' no_options_in_categories';
            }
            $sForm .= '">';

            $iX = 0;

            foreach ($this->aSettings["categories"] as $aCategory) {
                if (isset($aCategory["showOptions"])) {
                    if ($this->aSettings["options_in_categories"] == true) {
                        $sForm .= '<div class="tab-pane fade" id="nv-modulesettings-content-' . $iTabRand . '-' . $iX . '">';
                    }
                    $sForm .= '<div class="rex-form-group">';

                    foreach ($aCategory["showOptions"] as $sKey) {
                        $aOption = $this->aSettings["options"][$sKey];

                        if (isset($aOption)) {
                            if (count($aOption)) {

                                if (!in_array($aOption["type"], $aUsedTypes)) {
                                    array_push($aUsedTypes, $aOption["type"]);
                                }

                                if ($aOption["type"] != "html") {
                                    $sForm .= '<dl class="rex-form-group form-group nv-modulesettings-form-group">' . PHP_EOL;
                                }
                                if ($aOption["label"] != "") {
                                    $sForm .= '<dt><label for="">' . $aOption["label"];
                                    if (isset($aOption["icon"])) {
                                        $sForm .= ' <i class="' . $aOption["icon"] . '" style="padding-left:10px"></i>';
                                    }
                                    $sForm .= '</label></dt>' . PHP_EOL;
                                }

                                $sDisabled = '';
                                if (isset($aOption["disabled"])) {
                                    if ($aOption["disabled"] == true) {
                                        $sDisabled = 'disabled="disabled" ';
                                    }
                                }

                                switch ($aOption["type"]) {
                                    case "text":
                                    case "tel":
                                    case "url":
                                    case "number":
                                    case "color":
                                    case "numeric":
                                    case "email":
                                    case "date":
                                    case "datetime":
                                    case "datetime-local":
                                    case "month":
                                    case "week":

                                        $sFieldType = $aOption["type"];

                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = '';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sKey])) {
                                            $sValue = $aSavedOptions[$sKey];
                                        } else if ($aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }
                                        $sForm .= '<dd><input name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" type="' . $sFieldType . '" ' . $sClass . ' value="' . $sValue . '" ' . $sPlaceholder . ' ' . $sDisabled . '></dd>' . PHP_EOL;
                                        break;

                                    case "textarea":
                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = '';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sKey])) {
                                            $sValue = $aSavedOptions[$sKey];
                                        } else if ($aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }
                                        $sForm .= '<dd><textarea name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" ' . $sClass . ' ' . $sPlaceholder . ' ' . $sDisabled . '>' . $sValue . '</textarea></dd>' . PHP_EOL;
                                        break;

                                    case "colorpicker":
                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = 'placeholder="Bsp. #003366"';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sKey])) {
                                            $sValue = $aSavedOptions[$sKey];
                                        } else if ($aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }
                                        $sForm .= '<dd><div class="input-group nv-modulesettings-colorinput-group"><input data-parsley-excluded="true" type="text" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" value="' . $sValue . '" maxlength="7" ' . $sPlaceholder . ' pattern="^#([A-Fa-f0-9]{6})$" ' . $sClass . '><span class="input-group-addon nv-modulesettings-colorinput"><input type="color" value="' . @$aSavedOptions[$sKey] . '" pattern="^#([A-Fa-f0-9]{6})$" class="form-control"></span></div>' . PHP_EOL;
                                        break;

                                    case "select":
                                        $sClass = 'class="selectpicker w-100"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }

                                        $aSelectData = $this->getSelectData($sKey);
                                        $sForm .= '<dd><select name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" ' . $sClass . ' ' . $sDisabled . '>' . PHP_EOL;
                                        foreach ($aSelectData as $sSelectKey => $sSelectValue) :
                                            $sSelected = '';
                                            if (isset($aSavedOptions[$sKey])) {
                                                if ($sSelectKey == @$aSavedOptions[$sKey] or ($sSelectKey == "nvmodulesettingsdefault" && $aOption["default"] == @$aSavedOptions[$sType][$sKey])) {
                                                    $sSelected = 'selected="selected"';
                                                    if ($sSelectKey == "nvmodulesettingsdefault" && @$aSavedOptions[$sKey] != "nvmodulesettingsdefault") {
                                                        $sSelectKey = @$aSavedOptions[$sKey];
                                                    }
                                                }
                                            } else {
                                                $sSelected = ($sSelectKey == "nvmodulesettingsdefault") ? 'selected="selected"' : '';
                                            }
                                            $sForm .= '<option value="' . $sSelectKey . '" ' . $sSelected . '>' . $sSelectValue . '</option>' . PHP_EOL;
                                        endforeach;
                                        $sForm .= '</select></dd>' . PHP_EOL;
                                        break;

                                    case "checkbox":
                                        $sClass = 'class=""';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sChecked = ("1" == @$aSavedOptions[$sKey]) ? 'checked="checked"' : '';
                                        $sForm .= '<dd><div class="checkbox toggle"><label><input type="checkbox" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" value="1" ' . $sChecked . ' ' . $sClass . ' ' . $sDisabled . '></label></div></dd>' . PHP_EOL;
                                        break;

                                    case "radio":
                                        $sClass = 'class=""';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sForm .= '<dd><div class="radio toggle switch">' . PHP_EOL;
                                        $aSelectData = $this->getSelectData($sKey);
                                        foreach ($aSelectData as $sSelectKey => $sSelectValue) :
                                            $iRand = rand(0, 1000000) * rand(0, 100000);
                                            if (isset($aSavedOptions[$sKey])) {
                                                if ($sSelectKey == @$aSavedOptions[$sKey] or ($sSelectKey == "nvmodulesettingsdefault" && $aOption["default"] == @$aSavedOptions[$sType][$sKey])) {
                                                    $sSelected = 'checked="checked"';
                                                    if ($sSelectKey == "nvmodulesettingsdefault" && @$aSavedOptions[$sKey] != "nvmodulesettingsdefault") {
                                                        $sSelectKey = @$aSavedOptions[$sKey];
                                                    }
                                                }
                                            } else {
                                                $sSelected = ($sSelectKey == "nvmodulesettingsdefault") ? 'checked="checked"' : '';
                                            }
                                            $sForm .= '<label for="' . $iRand . '">' . PHP_EOL;
                                            $sForm .= '<input id="' . $iRand . '" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" type="radio" value="' . $sSelectKey . '" ' . $sSelected . ' ' . $sClass . ' ' . $sDisabled . '/> ' . $sSelectValue . PHP_EOL;
                                            $sForm .= '</label>';

                                        endforeach;
                                        $sForm .= '</div></dd>' . PHP_EOL;
                                        break;

                                    case "media":
                                        $aArgs = array();
                                        if (isset($aOption["preview"])) {
                                            if ($aOption["preview"]) {
                                                $aArgs["preview"] = "1";
                                            }
                                        }
                                        if (isset($aOption["types"])) {
                                            if ($aOption["types"]) {
                                                $aArgs["types"] = $aOption["types"];
                                            }
                                        }
                                        $iRand = rand(0, 1000000) * rand(0, 100000);
                                        $sForm .= rex_var_media::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sKey]", @$aSavedOptions[$sKey], $aArgs);
                                        break;

                                    case "medialist":
                                        $aArgs = array();
                                        if ($aOption["preview"]) {
                                            $aArgs["preview"] = "1";
                                        }
                                        if ($aOption["types"]) {
                                            $aArgs["types"] = $aOption["types"];
                                        }
                                        $iRand = rand(0, 1000000) * rand(0, 100000);
                                        $sForm .= rex_var_medialist::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sKey]", @$aSavedOptions[$sKey], $aArgs);
                                        break;

                                    case "link":
                                        $aArgs = array();
                                        $iRand = rand(0, 1000000) * rand(0, 100000);
                                        $sForm .= rex_var_link::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sKey]", @$aSavedOptions[$sKey], $aArgs);
                                        break;

                                    case "linklist":
                                        $aArgs = array();
                                        $iRand = rand(0, 1000000) * rand(0, 100000);
                                        $sForm .= rex_var_linklist::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sKey]", @$aSavedOptions[$sKey], $aArgs);
                                        break;
                                    case "customlink":
                                        if (!rex_addon::get('mform')->isAvailable()) {
                                            $sForm .= 'To use customlink please install addon mform';
                                        } else {
                                            $aArgs = array();
                                            $iRand = rand(0, 100) * rand(0, 100);
                                            $sForm .= rex_var_custom_link::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sKey]", @$aSavedOptions[$sKey], $aArgs);
                                        }
                                        break;

                                    case "html":
                                        $sForm .= $aOption["text"];
                                        break;

                                    case "slider":
                                        $sClass = 'class="form-control bootstap-slider"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        if (!empty($aSavedOptions[$sKey])) {
                                            $sValue = @$aSavedOptions[$sKey];
                                        } else {
                                            $sValue = $aOption["default"];
                                        }
                                        $sSliderMin = '';
                                        if (isset($aOption["slider-min"])) {
                                            $sSliderMin = 'data-slider-min="' . $aOption['slider-min'] . '"';
                                        }
                                        $sSliderMax = '';
                                        if (isset($aOption["slider-max"])) {
                                            $sSliderMax = 'data-slider-max="' . $aOption['slider-max'] . '"';
                                        }

                                        $sSliderTooltipSplit = 'data-slider-tooltip-split="true"';
                                        if (isset($aOption["slider-tooltip-split"])) {
                                            $sSliderTooltipSplit = 'data-slider-tooltip-split="' . $aOption["slider-tooltip-split"] . '"';
                                        }

                                        $sSliderRange = '';
                                        if (isset($aOption["slider-range"]) && ($aOption["slider-range"] == "1")) {
                                            $sSliderRange = 'data-slider-range="' . $aOption['slider-range'] . '"';
                                        } else {
                                            $sSliderTooltipSplit = '';
                                        }

                                        $sSliderStep = 'data-slider-step="1"';
                                        if (isset($aOption["slider-step"])) {
                                            $sSliderStep = 'data-slider-step="' . $aOption['slider-step'] . '"';
                                        }
                                        if (strpos($sValue, ',') !== false) {
                                            $sSliderValue = 'data-slider-value="[' . $sValue . ']"';
                                        } else {
                                            $sSliderValue = 'data-slider-value="' . $sValue . '"';
                                        }

                                        $sSliderShowTooltip = 'data-slider-tooltip="always"';
                                        if (isset($aOption["slider-tooltip"])) {
                                            if ($aOption["slider-tooltip"] == "hover") {
                                                $aOption["slider-tooltip"] = "show";
                                            }
                                            $sSliderShowTooltip = 'data-slider-tooltip="' . $aOption["slider-tooltip"] . '"';
                                        }


                                        $iRand = rand(0, 1000000) * rand(0, 100000);



                                        $sForm .= '<dd><input id="nvmodulesettings-slider-' . $iRand . '" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sKey . ']" type="text" ' . $sClass . ' value="' . $sValue . '" ' . $sSliderTooltipSplit . ' ' . $sSliderMin . ' ' . $sSliderMax . ' ' . $sSliderRange . ' ' . $sSliderStep . ' ' . $sSliderValue . ' ' . $sSliderShowTooltip . ' ' . $sDisabled . '>';
                                        if (isset($aOption["slider-unit"])) {
                                            $sForm .= "<script>
                                        
                                        $('#nvmodulesettings-slider-" . $iRand . "').slider({			
                                            formatter: function(value) {
                                                return value + ' " . $aOption["slider-unit"] . "';
                                            }
                                        });
                                        </script>";
                                        }
                                        break;
                                }

                                if ($aOption["type"] != "html") {
                                    $sForm .= '</dl>' . PHP_EOL;
                                }
                            }
                        }
                    }



                    $sForm .= '</div>';
                    if ($this->aSettings["options_in_categories"] == true) {
                        $sForm .= '</div>';
                    }
                    $iX++;
                }
            }
            $sForm .= '</div>';
            if ($this->aSettings["options_in_categories"] == true) {
                $sForm .= '<script>$(function(){ $(\'a[href="#nv-modulesettings-content-' . $iTabRand . '-0"]\').tab("show"); });</script>';                    //immer ersten Tab einblenden

            }
            if (!count($aUsedTypes)) {
                return;
            }
            return ($this->getFormWrapper($sForm));
        }
    }

    public function getFormWrapper($sForm)
    {

        $iBlockId = rand(0, 100000) . time() . rand(0, 10000000);
        $sHtml = '<div class="nv-modulesettings">';
        $sHtml .= '<a href="javascript:void(0)" class="btn btn-abort w-100 nv-modulesettings-toggler nv-modulesettings-toggler-' . $iBlockId . '" data-id="#nv-modulesettings-' . $iBlockId . '"><div class="nv-modulesettings-toggler-left"><i class="rex-icon fa-cog"></i> &nbsp; Moduleinstellungen</div> <div class="nv-modulesettings-toggler-right"><i class="rex-icon fa-angle-down"></i></div></a>' . PHP_EOL;
        $sHtml .= '<div id="nv-modulesettings-' . $iBlockId . '" class="nv-modulesettings-form">' . PHP_EOL;
        $sHtml .= $sForm . PHP_EOL;
        $sHtml .= '</div>' . PHP_EOL;
        $sHtml .= '</div>' . PHP_EOL;
        $sHtml .= '<script>' . PHP_EOL;
        $sHtml .= '$( document ).ready(function() {
			$(".nv-modulesettings-toggler-' . $iBlockId . '").click(function(){
				var iBlockId = $(this).attr("data-id");
                $(this).find(".nv-modulesettings-toggler-right i").toggleClass("fa-angle-down").toggleClass("fa-angle-up");
				$(iBlockId).slideToggle();
			});
		})' . PHP_EOL;
        $sHtml .= '</script>';
        return $sHtml;
    }

    public function getSettings($aArr = [])
    {

        $aData = array();

        if (isset($this->aSettings["showOptions"])) {
            foreach ($this->aSettings["showOptions"] as $sKey) {
                if ($this->aSettings["options"][$sKey]["type"] != "html") {
                    $aData[$sKey] = "";

                    if (!isset($aArr[$sKey]) or $aArr[$sKey] == "nvmodulesettingsdefault" OR $this->aSettings["options"][$sKey]["disabled"]) {
                        $aData[$sKey] = $this->getDefault($sKey);
                    } else {
                        $aData[$sKey] = $aArr[$sKey];
                    }
                }
            }
        }

        $oData = json_decode(json_encode($aData), FALSE);
        $this->settings = $oData;
    }

    public static function getStaticValue($sKey)
    {
        return self::$settings->settings->{$sKey};
    }

    public static function getValue($sKey)
    {
        return self::$settings->settings->{$sKey};
    }

    public static function getValues()
    {
        return self::$settings->settings;
    }

    public static function parseCustomLink($sLink = null)
    {
        if ($sLink == "") {
            return;
        }

        // email
        if (strpos($sLink, "mailto:") !== "false") {
            $sEmail = str_replace("mailto:", "", $sLink);
            if (filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
                $aArr = array(
                    "email" => $sEmail,
                    "type" => "email",
                    "source" => $sLink,
                );
                return $aArr;
            };
        }

        // tel
        if (strpos($sLink, "tel:") !== "false") {
            $sNr = str_replace("tel:", "", $sLink);
            if (filter_var($sNr, FILTER_VALIDATE_EMAIL)) {
                $aArr = array(
                    "number" => $sNr,
                    "type" => "tel",
                    "source" => $sLink,
                );
                return $aArr;
            };
        }

        // external url
        if (filter_var($sLink, FILTER_VALIDATE_URL)) {
            $aArr = array(
                "url" => $sLink,
                "target" => "_blank",
                "type" => "external_url",
                "source" => $sLink,
            );
            return $aArr;
        };



        // internal url
        if (filter_var($sLink, FILTER_VALIDATE_INT)) {
            $aArr = array(
                "url" => rex_getUrl($sLink),
                "target" => "_self",
                "type" => "internal_url",
                "source" => $sLink,
            );
            return $aArr;
        };

        // media
        $oMedia = rex_media::get($sLink);
        if ($oMedia) {
            $aArr = array(
                "url" => $oMedia->getUrl(),
                "type" => "media",
                "media" => $oMedia,
                "source" => $sLink,
            );
            return $aArr;
        }

        // other
        $aArr = array(
            "url" => $sLink,
            "type" => "other",
            "source" => $sLink,
        );
        return $aArr;
    }

    public function getJsonContent($sFile)
    {
        $sContent = "";
        if (file_exists($sFile)) {
            $sContent = file_get_contents($sFile);
            json_decode($sContent);
            if ($sContent && json_last_error() != "JSON_ERROR_NONE") {
                if (rex::isBackend()) {
                    throw new rex_exception("nvModuleSettings: json Error in File $sFile");
                }
            }
        }
        return $sContent;
    }

    function syncWithThemeAddon()
    {
        if (!rex_addon::get('theme')->isAvailable()) {
            return;
        }

        $oTheme = rex_addon::get('theme');
        $sThemePath = rex_path::base($oTheme->getProperty('theme_folder') . "/private/redaxo/modules/");

        $sFile = $sThemePath . $this->sSettingsFilename;
        $sFileOrigin = $this->addon->getDataPath($this->sSettingsFilename);
        if (!file_exists($sFile) && file_exists($sFileOrigin)) {
            copy($sFileOrigin, $sFile);
        } else if (file_exists($sFile) && !file_exists($sFileOrigin)) {
            copy($sFile, $sFileOrigin);
        } else if (file_exists($sFile) && file_exists($sFileOrigin)) {
            if (filectime($sFile) > filectime($sFileOrigin)) {
                copy($sFile, $sFileOrigin);
            } else {
                copy($sFileOrigin, $sFile);
            }
        }


        $sFile = $sThemePath . "modulesettings.global.json";
        $sFileOrigin = $this->addon->getDataPath("modulesettings.global.json");
        if (!file_exists($sFile) && file_exists($sFileOrigin)) {
            copy($sFileOrigin, $sFile);
        } else if (file_exists($sFile) && !file_exists($sFileOrigin)) {
            copy($sFile, $sFileOrigin);
        } else if (file_exists($sFile) && file_exists($sFileOrigin)) {
            if (filectime($sFile) > filectime($sFileOrigin)) {
                copy($sFile, $sFileOrigin);
            } else {
                copy($sFileOrigin, $sFile);
            }
        }
    }

    public function getBackendSummary()
    {
        if (!rex::isBackend()) {
            return;
        }
        if ($this->ignore) {
            return;
        }
        $sHtml = "<style>.rex-slice .panel-default .panel-body div.row { margin-left: 0px; margin-right: 0px; }</style>";
        $iBlockId = rand(0, 100000) . time() . rand(0, 10000000);
        $sHtml .= '<a href="javascript:void(0)" class="btn btn-abort w-100 text-center nv-modulesettings-backend-toggler nv-modulesettings-backend-toggler-' . $iBlockId . '" data-id="#nv-modulesettings-backend-' . $iBlockId . '" style="width:100%;margin-top:20px"><strong style="float:left">ModuleSettings</strong> &nbsp; <i class="fa fa-cog" style="float:right;padding-top:3px"></i></a><br />' . PHP_EOL;
        $sHtml .= '<div class="nv-modulesettings-backend-options" id="nv-modulesettings-backend-' . $iBlockId . '"><br />' . PHP_EOL;

        if (isset($this->aSettings["showOptions"])) {
            $sHtml .= '<ul class="list-group">' . PHP_EOL;
            foreach ($this->aSettings["showOptions"] as $sKey) {
                $aOption = $this->aSettings["options"][$sKey];

                $sLabel = $aOption["label"] . " (" . $sKey . ")";
                $sValue = $this->getValue($sKey);
                if ($aOption["data"][$sValue] != "") {
                    $sValue .= " (" . $aOption["data"][$sValue] . ")";
                }
                if ($aOption["label"]) {
                    $sHtml .= '<li class="list-group-item"><div class="row"><div class="col-12 col-lg-6" style="padding:0">' . $sLabel . '</div><div class="col-12 col-lg-6" style="padding:0">' . $sValue . '</div></div></li>' . PHP_EOL;
                }
            }
            $sHtml .= '</ul>' . PHP_EOL;
        }

        $sHtml .= '</div>' . PHP_EOL;
        $sHtml .= '<script>' . PHP_EOL;
        $sHtml .= '$( document ).ready(function() {
			$(".nv-modulesettings-backend-toggler-' . $iBlockId . '").click(function(){
				var iBlockId = $(this).attr("data-id");
				$(iBlockId).slideToggle();
                console.log("click "+iBlockId);
			});
		})' . PHP_EOL;
        $sHtml .= '</script>' . PHP_EOL;


        #$sHtml .= "<pre>" . print_r($oData, 1) . "</pre>";


        return $sHtml;
    }

    // ab hier deprecated (unter v2.0.0)

    function initDeprecated()
    {
        $this->bDeprecated = true;
        $this->sSettingsFilename = "settings.json";
        $this->iSettingsId = "9";
        $this->fileProject = $this->addon->getDataPath($this->sSettingsFilename);
        $this->fileCore = $this->addon->getPath('lib/' . $this->sSettingsFilename);
        $this->aSettings = [];
        $this->settings = "";
        $this->getAllSettingsDeprecated();
    }

    function getForm($sLabel = "Weitere Optionen")
    {
        $this->initDeprecated();
        return $this->getFormDeprecated($sLabel = "Weitere Optionen");
    }

    function getContentForm($oMform, $iId, $aOptions = array())
    {
        $this->initDeprecated();
        return $this->getContentFormDeprecated($oMform, $iId, $aOptions = array());
    }

    function parseSettings($aArr, $iSettingsId = 0)
    {
        $this->initDeprecated();
        return $this->parseSettingsDeprecated($aArr, $iSettingsId = 0);
    }

    function parseContentSettings($aArr, $iSettingsId = 0)
    {
        $this->initDeprecated();
        return $this->parseContentSettingsDeprecated($aArr, $iSettingsId = 0);
    }

    function getAllSettingsDeprecated()
    {
        $this->aSettings = [];

        $sCore = file_get_contents($this->fileCore);
        $this->coreData = (json_decode($sCore, true));


        if (file_exists($this->fileProject)) {
            $sProject = file_get_contents($this->fileProject);
            $this->projectData = (json_decode($sProject, true));
        }

        if ($this->iModuleId) {
            $sBaseDir = theme_path::base("private/redaxo/modules/");
            $aDirs = glob($sBaseDir . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK);

            foreach ($aDirs as $sDir) {
                if (file_exists($sDir . $this->iModuleId . ".rex_id")) {
                    break;
                }
            }

            if (file_exists($sDir . "settings.json")) {
                $sModule = file_get_contents($sDir . "settings.json");
                $this->moduleData = (json_decode($sModule, true));
            }
        }

        // Defaults
        if (isset($this->coreData["defaultOptions"])) {
            if (count($this->coreData["defaultOptions"])) {
                $this->aSettings["defaultOptions"] = $this->coreData["defaultOptions"];
            }
        }

        if (isset($this->projectData["defaultOptions"])) {
            if (count($this->projectData["defaultOptions"])) {
                $this->aSettings["defaultOptions"] = $this->projectData["defaultOptions"];
            }
        }
        if (isset($this->moduleData["defaultOptions"])) {
            if (count($this->moduleData["defaultOptions"])) {
                $this->aSettings["defaultOptions"] = $this->moduleData["defaultOptions"];
            }
        }
        if (isset($this->moduleData["contentOptions"])) {
            if (count($this->moduleData["contentOptions"])) {
                $this->aSettings["contentOptions"] = $this->moduleData["contentOptions"];
            }
        }

        // Additional Options Projekt & Module
        if (isset($this->projectData["additionalOptions"])) {
            foreach ($this->projectData["additionalOptions"] as $sKey) {
                if (!in_array($sKey, $this->aSettings["defaultOptions"])) {
                    array_push($this->aSettings["defaultOptions"], $sKey);
                }
            }
        }

        if (isset($this->moduleData["additionalOptions"])) {
            foreach ($this->moduleData["additionalOptions"] as $sKey) {
                if (!in_array($sKey, $this->aSettings["defaultOptions"])) {
                    array_push($this->aSettings["defaultOptions"], $sKey);
                }
            }
        }

        if (isset($this->projectData["hideOptions"])) {
            foreach ($this->projectData["hideOptions"] as $sKey) {
                $iX = 0;
                foreach ($this->aSettings["defaultOptions"] as $sVal) {
                    if ($sKey == $sVal) {
                        unset($this->aSettings["defaultOptions"][$iX]);
                    }
                    $iX++;
                }
            }
        }

        if (isset($this->moduleData["hideOptions"])) {
            foreach ($this->moduleData["hideOptions"] as $sKey) {
                if (($iX = array_search($sKey, $this->aSettings["defaultOptions"])) !== false) {
                    unset($this->aSettings["defaultOptions"][$iX]);
                }
            }
        }

        // Options
        $aUsedKeys = [];
        if (isset($this->coreData["options"])) {
            foreach ($this->coreData["options"] as $aOption) {
                $sKey = $aOption["key"];
                $this->aSettings["options"][$sKey] = $aOption;
                array_push($aUsedKeys, $sKey);
            }
        }

        if (isset($this->projectData["options"])) {
            foreach ($this->projectData["options"] as $aOption) {
                $sKey = $aOption["key"];
                if (!in_array($sKey, $aUsedKeys)) {
                    $this->aSettings["options"][$sKey] = $aOption;
                    array_push($aUsedKeys, $sKey);
                } else {
                    foreach ($aOption as $sOptionKey => $mOptionVal) {
                        if (isset($mOptionVal)) {
                            $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                        }
                    }
                }
            }
        }


        if (isset($this->moduleData["options"])) {
            foreach ($this->moduleData["options"] as $aOption) {
                $sKey = $aOption["key"];
                if (!in_array($sKey, $aUsedKeys)) {
                    $this->aSettings["options"][$sKey] = $aOption;
                    array_push($aUsedKeys, $sKey);
                } else {
                    foreach ($aOption as $sOptionKey => $mOptionVal) {
                        if (isset($mOptionVal)) {
                            $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                        }
                    }
                }
            }
        }
    }

    public function getOptionsDeprecated($sKey)
    {
        $aSelectData = $this->getSelectDataDeprecated($sKey);
        if (isset($aSelectData)) {
            $this->aSettings["options"][$sKey]["selectdata"] = $aSelectData;
        }
        return $this->aSettings["options"][$sKey];
    }

    public function getDefaultDeprecated($sKey)
    {
        return $this->aSettings["options"][$sKey]["default"];
    }

    function getContentOptionsDeprecated()
    {
        return $this->aSettings["contentOptions"];
    }

    function getOptionLabelDeprecated($sKey, $sValue)
    {
        return $this->aSettings["options"][$sKey]["selectdata"][$sValue];
    }

    public function getFormDeprecated($sLabel = "Weitere Optionen")
    {
        $iBlockId = rand(0, 100000) . time() . rand(0, 10000000);
        $this->mf = new MForm();
        $this->mf->addHtml('<a href="javascript:void(0)" class="btn btn-abort w-100 text-center nv-modulesettings-toggler-' . $iBlockId . '" data-id="#nv-modulesettings-' . $iBlockId . '" style="width:100%"><strong><span class = "caret"></span> &nbsp; ' . $sLabel . '</strong> &nbsp; <span class = "caret"></span></a>');
        $this->mf->addHtml('<div id="nv-modulesettings-' . $iBlockId . '" style="border: 1px solid #c1c9d4;border-top:none; padding: 20px;display:none"><br>');

        foreach ($this->aSettings["defaultOptions"] as $sKey) {
            $aOption = $this->aSettings["options"][$sKey];
            if ($aOption) {
                $this->mf = $this->getFormFieldDeprecated($aOption, $sKey, $this->mf, $this->iSettingsId);
            }
        }

        $this->mf->addHtml('</div>');

        $sForm = "";

        $sForm .= MBlock::show($this->iSettingsId, $this->mf->show(), ["min" => 1, "max" => 1]);

        $sForm .= '<script>';
        $sForm .= '$( document ).ready(function() {
			$(".nv-modulesettings-toggler-' . $iBlockId . '").click(function(){
				var iBlockId = $(this).attr("data-id");
				$(iBlockId).slideToggle();
			});
		})';
        $sForm .= '</script>';
        return $sForm;
    }

    public function getContentFormDeprecated($oMform, $iId, $aOptions = array())
    {

        if (isset($aOptions)) {
            $aTmpOptions = array();
            foreach ($aOptions as $sKey => $aOption) {
                if (!is_array($aOption)) {
                    array_push($aTmpOptions, $aOption);
                } else {
                    array_push($aTmpOptions, $sKey);
                    $aOption["key"] = $sKey;
                    if (!$aOption["type"]) {
                        $aOption["type"] = "select";
                    }
                    $this->aSettings["options"][$sKey] = $aOption;
                }
            }
            $aOptions = $aTmpOptions;
        }


        if (!isset($aOptions)) {
            $aOptions = $this->aSettings["contentOptions"];
        }

        foreach ($aOptions as $sKey) {
            $aOption = $this->aSettings["options"][$sKey];
            if ($aOption) {
                $oMform = $this->getFormFieldDeprecated($aOption, $sKey, $oMform, $iId);
            }
        }
        return $oMform;
    }

    public function getFormFieldDeprecated($aOption, $sKey, $oMform, $iId)
    {
        switch ($aOption["type"]) {
            default:
            case "select":
                $aData = $this->getSelectDataDeprecated($sKey);

                $oMform->addSelectField($iId . ".0." . $sKey, $aData, ["label" => $aOption["label"]]);
                return $oMform;
                break;

            case "text":
                $oMform->addTextField($iId . ".0." . $sKey, ["label" => $aOption["label"], "placeholder" => $aOption["placeholder"]]);
                return $oMform;
                break;

            case "media":
                $oMform->addMediaField($iId, ["label" => $aOption["label"]]);
                return $oMform;
                break;

            case "fieldset":
                $oMform->addTab($aOption["label"]);
                return $oMform;
                break;

            case "range":
                $aData = $this->getSelectDataDeprecated($sKey);
                $sDataDefault = $this->getDefaultDeprecated($sKey);
                $aKeyValue = [];
                foreach ($aData as $key => $value) {
                    if ($key === "") continue;
                    if ($key == $sDataDefault && $sDataDefault != "") {
                        array_unshift($aKeyValue, ',' . "Automatisch (" . $value . ")");
                    }
                    $aKeyValue[] = $key . ',' . $value;
                }

                $iLength = count($aKeyValue);
                $iMax = $iLength - 1;
                $sData = implode(";", $aKeyValue);

                $oMform->addElement("range", $iId . ".0." . $sKey . "_range", NULL, ["label" => $aOption["label"], "min" => 0, "max" => $iMax, "data-values" => $sData, "data-default" => $sDataDefault, "class" => "nv-range-listener",  "oninput" => "onRangeInput()"]);
                $oMform->addHiddenField($iId . ".0." . $sKey);
                return $oMform;
                break;
        }
    }

    public function parseSettingsDeprecated($aArr, $iSettingsId = 0)
    {
        if (!$iSettingsId) {
            $iSettingsId = $this->iSettingsId;
        }

        $oData = new stdClass();
        foreach ($this->aSettings["defaultOptions"] as $sKey) {
            $aOptions = $this->getOptionsDeprecated($sKey);
            if ($aOptions["type"] == "media") {
                $oData->{$sKey} = $aArr["REX_MEDIA_" . $iSettingsId] ? MEDIA . $aArr["REX_MEDIA_" . $iSettingsId] : "";
            } else {
                $oData->{$sKey} = $aArr[$sKey] ? $aArr[$sKey] : $this->getDefaultDeprecated($sKey);
            }
        }

        return $oData;
    }

    public function parseContentSettingsDeprecated($aArr, $iSettingsId = 0)
    {
        if (!$iSettingsId) {
            $iSettingsId = $this->iSettingsId;
        }
        $oData = new stdClass();
        foreach ($this->aSettings["contentOptions"] as $sKey) {
            $aOptions = $this->getOptionsDeprecated($sKey);
            if ($aOptions["type"] == "media") {
                $oData->{$sKey} = $aArr["REX_MEDIA_" . $iSettingsId] ? MEDIA . $aArr["REX_MEDIA_" . $iSettingsId] : "";
            } else {
                $oData->{$sKey} = $aArr[$sKey] ? $aArr[$sKey] : $this->getDefaultDeprecated($sKey);
            }
        }
        return $oData;
    }

    function getSelectDataDeprecated($sKey)
    {
        $aOption = $this->aSettings["options"][$sKey];
        foreach ($aOption["data"] as $sOptionsKey => $sOptionsVal) {
            if ($sOptionsKey === "") {
                if (rex::isBackend()) {
                    throw new rex_exception("nvModuleSettings: empty data key in option " . $sKey);
                }
            }
        }

        if (isset($aOption["default"])) {
            foreach ($aOption["data"] as $sOptionsKey => $sOptionsVal) {
                if ($sOptionsKey == "" && $aOption["default"] == "") {
                    $aDefaultData = array(
                        "" => "Automatisch (" . $sOptionsVal . ")",
                    );
                    unset($aOption["data"][$sOptionsKey]);
                } else {
                    if ($sOptionsKey == $aOption["default"]) {
                        $aDefaultData = array(
                            "" => "Automatisch (" . $sOptionsVal . ")",
                        );
                    }
                }
            }
        }

        if (isset($aDefaultData)) {
            foreach ($aOption["data"] as $sDataKey => $sDataVal) {
                $aDefaultData[$sDataKey] = $sDataVal;
            }
            $aData = $aDefaultData;
        } else {
            $aData = $aOption["data"];
        }

        return $aData;
    }
}
