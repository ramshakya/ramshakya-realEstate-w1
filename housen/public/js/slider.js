$(document).ready(function() {
        var slider = $("#light-slider").lightSlider({
          controls: false,
           auto: true,
           loop: true
        });
        $('.slideControls .slidePrev').click(function() {
            slider.goToPrevSlide();
        });

        $('.slideControls .slideNext').click(function() {
            slider.goToNextSlide();
        });
   
    // new slide control
    var slider2 = $("#light-slider-2").lightSlider({
          controls: false,
           auto: true,
           loop: true
        });
        $('.slideControls2 .slidePrev').click(function() {
            slider2.goToPrevSlide();
        });

        $('.slideControls2 .slideNext').click(function() {
            slider2.goToNextSlide();
        });
      // new slide control 
   
    });