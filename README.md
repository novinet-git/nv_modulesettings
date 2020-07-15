# nvModuleSettings-AddOn für REDAXO 5

Redaxo 5 Addon zum Verwalten von Design-Einstellungen für Module

## Features

- Verwaltung von wiederkehrenden Moduloptionen (z.B. Abstände, Breite, Hintergrundfarbe)
- Generiert in der Moduleingabe unten ein Akkordeon mit den definierten Optionen
- Standardoptionen werden direkt über das Addon geladen
- Es können Optionen für ein ganzes Projekt festgelegt werden
- Es können Optionen für einzelne Module festgelegt werden
- Es können Optionen überschrieben werden (z.B. im Projekt Abstand nach unten mb-5 aber bei einem speziellen Modul mb-3)
- Optional können in mform weitere Optionen im Input über die settings.json ergänzt werden


## Konfiguration

Das Addon benötigt die Addons "Developer" und "Theme".
Wenn man das Addon für ein spezifisches Modul verwenden möchte, legt man in den Modulordner eine Datei settings.json ab. 

Coresettings & Beispieldatei
Beispiel-Datei (Coresettings) liegt im Addon-Ordner unter redaxo/src/addons/nv_modulesettings/lib/settings.json

Projektweite Settings
Liegt im Addon-Ordner unter redaxo/data/addons/nv_modulesettings/settings.json

Spezifische Modulsettings
Datei settings.json im Modulordner theme/private/redaxo/modules/Modulordner anlegen


## Beispiel Input

```php
$oSettings = new nvModuleSettings(REX_MODULE_ID);
echo $oSettings->getForm();
```

## Beispiel Output

```php
$oSettings = new nvModuleSettings(REX_MODULE_ID);
$oSettings->settings = $oSettings->parseSettings(rex_var::toArray("REX_VALUE[9]")[0]);
```
Nun kann auf alle Einstellungsvariablen über $oSettings->settings->marginBottom (oder einen anderen Key) zugegriffen werden.


## Beispiel Überschreiben von Core- oder Projektoptionen

Abstandsdefinition im Core

```php
{
	"key": "marginBottom",
	"label": "Block Außenabstand Unten",
	"type": "select",
	"data": {
		"mb-0": "Kein",
		"mb-1": "Sehr klein",
		"mb-2": "Klein",
		"mb-3": "Mittel",
		"mb-4": "Groß",
		"mb-5": "Sehr groß"
	},
	"default": "mb-3"
}
```

Überschreiben des Standardabstands für alle Module im gesamten Projekt (redaxo/data/addons/nv_modulesettings/settings.json)

```php
{
	"key": "marginBottom",
	"default": "mb-4"
}
```
Überschreiben des Standardabstands für ein einzelnes Modul (theme/private/redaxo/modules/Modulordner/settings.json)

```php
{
	"key": "marginBottom",
	"default": "mb-5"
}
```

## Beispiel weitere Inhaltoptionen im Input (Contentoptions)

Wenn man im direkten Input (außerhalb des Akkordeons) weitere Optionen (z.B. Farbe einer HR im Trenner-Modul) hinzufügen möchte, kann man das in der Modul-settings.json tun. Dazu muss der "Key" im Bereich "contentOptions" ergänzt werden.

```php
{
	"defaultOptions" : [
	],
	"additionalOptions" : [
	],
	"hideOptions" : [
	],
	"contentOptions": [
        "type"
	],
	"options": [
		{
			"key": "type",
			"label": "Typ",
			"data": {
				"none": "Keine Linie",
				"grey": "Graue Linie",
				"red": "Rote Linie",
				"greyFull": "Graue Linie (100% Hintergrundfarbe)"
			},
			"default": "red"
		}
	]
}
```

Im Input des Moduls kann folgendermaßen auf diese "Contentoptions" zugegriffen werden:

```php
$id = 1;
$mf = new MForm();
$oSettings = new nvModuleSettings(REX_MODULE_ID);
$mf = $oSettings->getContentForm($mf,$id);
echo MBlock::show($id, $mf->show(), ["min" => 1, "max" => 1]);
echo $oSettings->getForm();
```

Im Output kann folgendermaßen auf die Werte zugegriffen werden

```php
$oSettings = new nvModuleSettings(REX_MODULE_ID);
$oContentsettings = $oSettings->parseContentSettings(rex_var::toArray("REX_VALUE[1]")[0], 1);
```