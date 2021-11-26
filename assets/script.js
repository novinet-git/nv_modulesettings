// nvModuleSettings
// v2.0

$(function(){

	//Prüfungen & PlugIns aktivieren
	$(document).on("rex:ready",function() { nvModuleSettings_initModuleSettings(); });
	nvModuleSettings_initModuleSettings();
	
	function nvModuleSettings_initModuleSettings()
	{	
		$('.nv-modulesettings .selectpicker').selectpicker({ width: "100%"});
		$('.nv-modulesettings .dropdown.bootstrap-select.w-100.bs3').css('width','100%');
		$('.nv-modulesettings input.bootstap-slider').slider({});   

		//Color-Abgleich
		$('.nv-modulesettings div.nv-modulesettings-colorinput-group input[type=color]').on("input change", function(){
			$(this).parent().prevAll('input[type=text]').val(this.value);
		});
		$('.nv-modulesettings div.nv-modulesettings-colorinput-group input[type=text]').on("input change", function(){
			$(this).next().children('input[type=color]').val(this.value);
		});
	}
});