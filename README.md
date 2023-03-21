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

Das Addon benötigt die Addons "Developer" und "Theme".<br>
Wenn man das Addon für ein spezifisches Modul verwenden möchte, legt man in den Modulordner eine Datei modulesettings.json ab.

# Input- & Output

## Beispiel Input

Generiert im Input automatisch das gesamte Formular

```php
<?php
$oSettings = new nvModuleSettings(
  REX_MODULE_ID,
  rex_var::toArray('REX_VALUE[20]')
);
echo $oSettings->buildForm();
?>
```

## Beispiel Output

Um auf die gespeicherten Werte zuzugreifen, muss das Addon im Output aufgerufen werden.

```php
<?php
$oSettings = new nvModuleSettings(
  REX_MODULE_ID,
  rex_var::toArray('REX_VALUE[20]')
);
$oSettings->getValue('marginBottom'); // Parameter entspricht dem Feldnamen, welchen man zurückbekommen möchte
$oSettings->getValues(); // Gibt alle verfügbaren Parameter zurück
?>
```

## Beispiel Output Zusammenfassung aller Parameter im Backend anzeigen (Im Ansichtsmodus)

Im Ansichts- (nicht Bearbeitungs-) modus können alle zur Verfügung stehenden Keys und ihre Werte ausgegeben werden.

```php
<?php
$oSettings = new nvModuleSettings(
  REX_MODULE_ID,
  rex_var::toArray('REX_VALUE[20]')
);
$oSettings->getBackendSummary();
?>
```

## Hierarchie

Die Dateien werden der Hierarchiebene nach eingelesen und verarbeitet. So können spezifische Angaben zu einem einzelnen Modul globale Angaben überschreiben.

```html
- modulesettings.global.json (im Addonordner) -- modulesettings.json (im
Addonordner) --- modulesettings.json (im Modulordner)
```

<b>Globale Settings (z.B. für verschiedene Projekte)</b><br />
Liegt im Addon-Ordner unter theme/private/redaxo/modules/modulesettings.global.json

<b>Projektweite Settings</b><br />
Liegt im Addon-Ordner unter theme/private/redaxo/modules/modulesettings.json

<b>Spezifische Modulsettings</b><br />
Datei modulesettings.json im Modulordner theme/private/redaxo/modules/Modulordner anlegen

## Beispiel Überschreiben von Projektoptionen

Abstandsdefinition im Projekt (theme/private/redaxo/modules/modulesettings.json)

```json
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

```json
{
	"key": "marginBottom",
	"default": "mb-5"
}
```

## Beispiel Übernehmen von Attributen für neues Optionsfeld

Angenommen man hat eine Option mit Auswahlfeldern, die man bei einer anderen Option wiederverwenden möchte. Dann können mit dem Key 'parent' Optionen übernommen werden

```json
{
	"key": "marginTop",
	"parent": "marginBottom",
	"label": "Block Außenabstand oben",
	"default": "mb-3"
}
```

# Weitere Optionen

## Optionen Kategorien (Tabs) zuordnen

Optionen können Kategorien zugeordnet werden. Kategorien werden in Tabs dargestellt.

```json
{
	"categories": [
		{
			"key": "dimension",
			"label": "Abstände & Größen",
			"icon": "fa fa-arrows"
		},

		{
			"key": "design",
			"label": "Hintergrund",
			"icon": "fa fa-paint-brush"
		},

		{
			"key": "other",
			"label": "Sonstiges",
			"icon": "fa fa-cog"
		}
	]
}
```

Die Zuordnung einer Option zu einer Kategorie erfolgt durch das Attribut <b>category</b>

```json
{
	"key": "marginBottom",
	"category": "dimension",
	...
}
```

## Optionen gruppieren (Akkordeons)

Optionen können zu Gruppen zusammengefasst werden. Diese Gruppen werden als Akkordeon dargestellt

```json
{
	"options": [
		{
			"category": "design", // Zuordnung zu einer Kategorie
			"key": "group_bgcolor", // Schlüssel für showOptions
			"type": "group", // muss als group definiert werden
			"label": "Farbe", // Label des Akkordeons
			"icon": "", // Optionales Icon des Labels
			"open": true, // Beim Laden geöffnet (true) oder geschlossen (false)
			"options": ["bgColor", "bgColorHEX"] // Array mit allen Optionskeys, welche zusammengefasst werden sollen
		}
	]
}
```

## Weitere Attribute (z.B. data-Attribute) für Select-Felder

Wenn weitere Attribute übergeben werden sollen, muss der angezeigte Wert als "label" übergeben werden. Die anderen Werte werden in die Option übernommen

```php
{
	"key": "marginBottom",
	"label": "Block Außenabstand Unten",
	"type": "select",
	"data": {
		"mb-0": "Kein",
		"mb-1": "Sehr klein",
		"mb-2": {
					"label": "Klein",
					"data-thumbnail": "/media/icon-chrome.png",
					"data-icon": "glyphicon glyphicon-eye-open",
					"data-subtext": "subtext"
				},
		"mb-3": "Mittel",
		"mb-4": "Groß",
		"mb-5": "Sehr groß"
	},
	"default": "mb-3"
}
```

## nvModuleSettings für ein Addon deaktivieren

Im Ordner des Moduls eine leere modulesettings.ignore.json ablegen (theme/private/redaxo/modules/Modulordner/modulesettings.ignore.json)
