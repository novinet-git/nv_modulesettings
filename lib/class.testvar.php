<?php
class rex_var_nvmodulesettings extends rex_var
{
    protected function getOutput()
    {



        //dump($this->getArg("key"));
        $oSettings = new nvModuleSettings($this->getContextData()->getValue('module_id'), rex_var::toArray($this->getContextData()->getValue('value20')));
        $aArr = array(
            "module_id" => $this->getContextData()->getValue('module_id'),
            "field" => $this->getContextData()->getValue('value20')
        );
        return "ja";
        return $aArr;


        if ($this->hasArg['module_id']) {
            echo "fuck"; exit;
            return $this->getContextData()->getValue('module_id');
        }

        if ($this->hasArg['field']) {
            return $this->getContextData()->getValue('value20');
        }




        if ($this->hasArg('key')) {
            $out = $oSettings->getValue($this->getArg('key'));
            return self::quote($out);
        }

        if ($this->hasArg('keys')) {
            $out = $oSettings->getValues();
            $out2 = rex_var::toArray($out);
            $aArr = array();
            foreach($out AS $sKey => $sVal) {
                $aArr[$sKey] = $sVal;
            }
            return self::quote($aArr);
            dump($aArr);
            exit;
            return $out;

            $out = "Alle";
        }


        return $oSettings;
        exit;
        $out = "test";
        return self::quote($out);

        return $out;

        return $this->getContext();



        $controls = $poster = NULL;
        $id = $this->getArg('id', 0, true);
        if (!in_array($this->getContext(), ['module', 'action']) || !is_numeric($id) || $id < 1 || $id > 20) {
            return false;
        }
        $value = $this->getContextData()->getValue('value' . $id);

        if ($this->hasArg('controls') && $this->getArg('controls')) {
            $controls = $this->getArg('controls');
        }

        if ($this->hasArg('poster') && $this->getArg('poster')) {
            $poster = $this->getArg('poster');
        }

        $out = '';
        if ($value) {
            $out = rex_plyr::outputMedia($value, $controls, $poster);
        }
        // Reine Textausgaben müssen mit 'self::quote()' als String maskiert werden.
        return self::quote($out);
    }
}
