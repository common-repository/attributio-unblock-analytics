window.addEventListener("load", function() {
		if(window.ga && ga.getAll().length) {
			return;
		} else if (attributio_params.analytics_property) {
			var analytics = document.createElement('script');
			analytics.type = 'text/javascript';			
			document.getElementsByTagName('head')[0].appendChild(analytics); 

			(function(i,s,o,r){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date()})(window,document,'script','ga');
  
			var sendHit = function() {
				ga('create', attributio_params.analytics_property, 'auto');
				ga('send', 'pageview');
				if(attributio_params.analytics_custom_hit) {
					eval(attributio_params.analytics_custom_hit);
				}
			}

			analytics.onload = sendHit;
			analytics.src = attributio_params.analytics_path;

		}
}, false);