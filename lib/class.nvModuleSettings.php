<?php class nvModuleSettings
{


    public function __construct($iModuleId = null)
    {
        $this->addon = rex_addon::get('nv_modulesettings');
        $this->fileCore = $this->addon->getPath('lib/settings.json');
        $this->fileProject = $this->addon->getDataPath('settings.json');

        $this->iSettingsId = 9;

        if ($iModuleId) {
            $this->iModuleId = $iModuleId;
        }

        $this->getAllSettings();
    }

    function getAllSettings()
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
                if (strpos($sDir, "[" . $this->iModuleId . "]") !== false) {
                    break;
                }
            }

            if (file_exists($sDir . "settings.json")) {
                $sModule = file_get_contents($sDir . "settings.json");
                $this->moduleData = (json_decode($sModule, true));
            }
        }

        // Defaults
        if (count($this->coreData["defaultOptions"])) {
            $this->aSettings["defaultOptions"] = $this->coreData["defaultOptions"];
        }

        if (count($this->projectData["defaultOptions"])) {
            $this->aSettings["defaultOptions"] = $this->projectData["defaultOptions"];
        }

        if (count($this->moduleData["defaultOptions"])) {
            $this->aSettings["defaultOptions"] = $this->moduleData["defaultOptions"];
        }

        if (count($this->moduleData["contentOptions"])) {
            $this->aSettings["contentOptions"] = $this->moduleData["contentOptions"];
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
        if (count($this->coreData["options"])) {
            foreach ($this->coreData["options"] as $aOption) {
                $sKey = $aOption["key"];
                $this->aSettings["options"][$sKey] = $aOption;
                array_push($aUsedKeys, $sKey);
            }
        }

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

        if (count($this->moduleData["options"])) {
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

    public function getOptions($sKey)
    {
        $aSelectData = $this->getSelectData($sKey);
        if (count($aSelectData)) {
            $this->aSettings["options"][$sKey]["selectdata"] = $aSelectData;
        }
        return $this->aSettings["options"][$sKey];
    }

    public function getDefault($sKey)
    {
        return $this->aSettings["options"][$sKey]["default"];
    }

    function getContentOptions()
    {
        return $this->aSettings["contentOptions"];
    }

    function getOptionLabel($sKey, $sValue)
    {
        return $this->aSettings["options"][$sKey]["selectdata"][$sValue];
    }

    public function getForm($sLabel = "Weitere Optionen")
    {
        $iBlockId = rand(0, 100000) . time() . rand(0, 10000000);
        $this->mf = new MForm();
        $this->mf->addHtml('<a href="javascript:void(0)" class="btn btn-abort w-100 text-center nv-modulesettings-toggler" data-id="#nv-modulesettings-' . $iBlockId . '" style="width:100%"><strong><span class = "caret"></span> &nbsp; ' . $sLabel . '</strong> &nbsp; <span class = "caret"></span></a>');
        $this->mf->addHtml('<div id="nv-modulesettings-' . $iBlockId . '" style="border: 1px solid #c1c9d4;border-top:none; padding: 20px;display:none"><br>');

        foreach ($this->aSettings["defaultOptions"] as $sKey) {
            $aOption = $this->aSettings["options"][$sKey];
            if ($aOption) {
                $this->mf = $this->getFormField($aOption, $sKey, $this->mf,$this->iSettingsId);
            }
        }

        $this->mf->addHtml('</div>');

        $sForm = "";

        $sForm .= MBlock::show($this->iSettingsId, $this->mf->show(), ["min" => 1, "max" => 1]);

        $sForm .= '<script>';
        $sForm .= '$( document ).ready(function() {
			$(".nv-modulesettings-toggler").click(function(){
				var iBlockId = $(this).attr("data-id");
				$(iBlockId).slideToggle();
			});
		})';
        $sForm .= '</script>';
        return $sForm;
    }

    public function getContentForm($oMform, $iId)
    {

        foreach ($this->aSettings["contentOptions"] as $sKey) {
            $aOption = $this->aSettings["options"][$sKey];
            if ($aOption) {
                $oMform = $this->getFormField($aOption, $sKey, $oMform,$iId);
            }
        }
        return $oMform;
    }

    public function getFormField($aOption, $sKey, $oMform,$iId)
    {
        switch ($aOption["type"]) {
            default:
            case "select":
                $aData = $this->getSelectData($sKey);

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
                $aData = $this->getSelectData($sKey);
                $sDataDefault = $this->getDefault($sKey);
                $aKeyValue = [];
                foreach ($aData as $key => $value) {
                    if (!$key) continue;
                    if ($key == $sDataDefault) {
                        $value = "Automatisch (".$value.")";
                        $aKeyValue[] = ',' . $value;
                    }
                }
                foreach ($aData as $key => $value) {
                    if (!$key) continue;
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

    public function parseSettings($aArr, $iSettingsId = 0)
    {
        if (!$iSettingsId) {
            $iSettingsId = $this->iSettingsId;
        }

        $oData = new stdClass();
        foreach ($this->aSettings["defaultOptions"] as $sKey) {
            $aOptions = $this->getOptions($sKey);
            if ($aOptions["type"] == "media") {
                $oData->{$sKey} = $aArr["REX_MEDIA_" . $iSettingsId] ? MEDIA . $aArr["REX_MEDIA_" . $iSettingsId] : "";
            } else {
                $oData->{$sKey} = $aArr[$sKey] ? $aArr[$sKey] : $this->getDefault($sKey);
            }
        }

        return $oData;
    }

    public function parseContentSettings($aArr, $iSettingsId = 0)
    {
        if (!$iSettingsId) {
            $iSettingsId = $this->iSettingsId;
        }
        $oData = new stdClass();
        foreach ($this->aSettings["contentOptions"] as $sKey) {
            $aOptions = $this->getOptions($sKey);
            if ($aOptions["type"] == "media") {
                $oData->{$sKey} = $aArr["REX_MEDIA_" . $iSettingsId] ? MEDIA . $aArr["REX_MEDIA_" . $iSettingsId] : "";
            } else {
                $oData->{$sKey} = $aArr[$sKey] ? $aArr[$sKey] : $this->getDefault($sKey);
            }
        }
        return $oData;
    }

    function getSelectData($sKey)
    {
        $aOption = $this->aSettings["options"][$sKey];
        foreach ($aOption["data"] as $sOptionsKey => $sOptionsVal) {
            if ($sOptionsKey === "") {
                if (rex::isBackend()) {
                    throw new rex_exception("nvModuleSettings: empty data key in option ".$sKey);
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

        if (count($aDefaultData)) {
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
