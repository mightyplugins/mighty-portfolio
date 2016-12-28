(function ($) {
	$('.mpp-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		closeOnContentClick: false,
		closeBtnInside: false,
		mainClass: 'mfp-with-zoom mfp-img-mobile',
		image: {
			verticalFit: true
		},
		gallery: {
			enabled: true
		},
		zoom: {
			enabled: true,
			duration: 300, // don't foget to change the duration also in CSS
			opener: function(element) {
				return element.find('img');
			}
		}
		
	});

	var grid = $('.mpp-items').isotope({
		itemSelector: '.mpp-item',
	});

	$('.mpp-filters a').on('click', function (e) {
		e.preventDefault();

		var filterData = $(this).data('filter');

		$('.mpp-filters a.active').removeClass('active');
		$(this).addClass('active');

		grid.isotope({ filter: filterData })
	});
})(jQuery);