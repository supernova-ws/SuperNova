window.addEvent('domready', function() {
	var status = {
		'true': 'Развернуто',
		'false': 'Свернуто'
	};
	
	//-vertical

	var myVerticalSlide = new Fx.Slide('vertical_slide');
	myVerticalSlide.toggle();
	$('v_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});

	// When Vertical Slide ends its transition, we check for its status
	// note that complete will not affect 'hide' and 'show' methods
	myVerticalSlide.addEvent('complete', function() {
		$('vertical_status').set('html', status[myVerticalSlide.open]);
	});

	var myHorizontalSlide = new Fx.Slide('horizontal_slide', {mode: 'horizontal'});	
			myHorizontalSlide.toggle();
	$('h_toggle').addEvent('click', function(e){
		e.stop();
		myHorizontalSlide.toggle();
	});

	// When Horizontal Slide ends its transition, we check for its status
	// note that complete will not affect 'hide' and 'show' methods
	myHorizontalSlide.addEvent('complete', function() {
		$('horizontal_status').set('html', status[myHorizontalSlide.open]);
	});
});