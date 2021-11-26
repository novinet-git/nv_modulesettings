# nvModuleSettings-AddOn für REDAXO 5

Redaxo 5 Addon zum Verwalten von Design-Einstellungen für Module

## Hinweis zu Breaking Changes (Version 2.0)
Version 2.0 enthält einige Breaking Changes. Wer bisher die Version 1.2 genutzt hat kann zwar updaten, die neuen Funktionen greifen aber nur, wenn die Syntax und Methoden der Version 2.0 verwendet werden!


## Features

- Verwaltung von wiederkehrenden Moduloptionen (z.B. Abstände, Breite, Hintergrundfarbe)
- Generiert in der Moduleingabe unten ein Akkordeon mit den definierten Optionen
- Standardoptionen werden direkt über das Addon geladen
- Es können Optionen für ein ganzes Projekt festgelegt werden
- Es können Optionen für einzelne Module festgelegt werden
- Es können Optionen überschrieben werden (z.B. im Projekt Abstand nach unten mb-5 aber bei einem speziellen Modul mb-3)
- Installation legt eine Beispieldatei redaxo/data/addons/nv_modulesettings/modulesettings.json.example an. Diese kann umbenannt werden (modulesettings.json) um beispielhafte Optionen einzublenden


## Konfiguration

Das Addon benötigt die Addons "Developer" und "Theme".
Wenn man das Addon für ein spezifisches Modul verwenden möchte, legt man in den Modulordner eine Datei modulesettings.json ab. 

Projektweite Settings
Liegt im Addon-Ordner unter redaxo/data/addons/nv_modulesettings/modulesettings.json

Spezifische Modulsettings
Datei modulesettings.json im Modulordner theme/private/redaxo/modules/Modulordner anlegen


## Beispiel Input

```php
$oSettings = new nvModuleSettings(REX_MODULE_ID);
echo $oSettings->buildForm(rex_var::toArray("REX_VALUE[20]"));
```

## Beispiel Output

```php
$oSettings = new nvModuleSettings(REX_MODULE_ID);
$oSettings->getSettings(rex_var::toArray("REX_VALUE[20]"))
```
Nun kann auf alle Einstellungsvariablen über $oSettings->getValue(marginBottom) (oder einen anderen Key) zugegriffen werden.
Alle Werte können über die Methode $oSettings->getValues() ausgegeben werden.
Wenn die Methode getSettings() aufgerufen wurde, kann im Output auch am Ende die Methode getBackendSummary() ausgegeben werden.
Diese zeigt eine Zusammenfassung aller Einstellungen des Moduls.


## Beispiel Überschreiben von Projektoptionen

Abstandsdefinition im Projekt (redaxo/data/addons/nv_modulesettings/modulesettings.json)

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

Überschreiben des Standardabstands für ein einzelnes Modul (theme/private/redaxo/modules/Modulordner/modulesettings.json)

```php
{
	"key": "marginBottom",
	"default": "mb-5"
}
```