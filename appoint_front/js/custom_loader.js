function loader() {
	if($('.loader').hasClass('hide')){
		$('.loader').removeClass('hide');
		$('.fh5co-narrow-content').addClass('opacity-5');
	}else{
		$('.loader').addClass('hide');
		$('.fh5co-narrow-content').removeClass('opacity-5');
	}
}