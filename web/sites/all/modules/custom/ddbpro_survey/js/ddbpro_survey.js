jQuery(document).ready(function() {
	
	if(typeof Cookies.get('ddbpro_survey') === 'undefined') {
		jQuery("#surveyModalCenter").modal("show");
		jQuery('#surveyModalCenterCloseButton').on('click', function(event) {
			Cookies.set('ddbpro_survey', 1, { expires: 1 });
		});
		jQuery('#surveyModalCenterParticipateButton').on('click', function(event) {
			Cookies.set('ddbpro_survey', 1);
			jQuery("#surveyModalCenter").modal("hide");
		});
	}
	
});