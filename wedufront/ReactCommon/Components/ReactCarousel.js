import React, { useEffect, useState } from "react";
import Style from "../../styles/css/ReactCommon/ReactCarousel.module.css";
const Carousel = (props) => {
  const { children, show, mainImage } = props;
  const [currentIndex, setCurrentIndex] = useState(
    props.showIcon ? props.show : 0
  );
  const [length, setLength] = useState(children ? children.length : 0);
  const [touchPosition, setTouchPosition] = useState(null);
  useEffect(() => {
    if (props.selectedCarouselIndex) {
      setCurrentIndex(props.selectedCarouselIndex);
    }
  }, [props.selectedCarouselIndex]);
  // Set the length to match current children from props
  useEffect(() => {
    setLength(children ? children.length : 0);
  }, [children]);
  const next = () => {
    if (currentIndex < length - show) {
      setCurrentIndex((prevState) => prevState + 1);
    }
  };
  const prev = () => {
    if (currentIndex > 0) {
      setCurrentIndex((prevState) => prevState - 1);
    }
  };
  const handleTouchStart = (e) => {
    const touchDown = e.touches[0].clientX;
    setTouchPosition(touchDown);
  };
  const handleTouchMove = (e) => {
    const touchDown = touchPosition;
    if (touchDown === null) {
      return;
    }
    const currentTouch = e.touches[0].clientX;
    const diff = touchDown - currentTouch;
    if (diff > 5) {
      next();
    }
    if (diff < -5) {
      prev();
    }
    setTouchPosition(null);
  };
  const indexStyle = `show${show}`;
  return (
    <div
      className={`${props.parentCls ? props.parentCls : ""} ${
        Style.carouselContainer
      }`}
    >
      <div className={Style.carouselWrapper}>
        {/* You can alwas change the content of the button to other things */}
        {currentIndex > 0 && (
          <button onClick={prev} id="leftArrow" className={Style.leftArrow}>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24">
              <path d="M15.293 3.293 6.586 12l8.707 8.707 1.414-1.414L9.414 12l7.293-7.293-1.414-1.414z" />
            </svg>
          </button>
        )}
        <div
          className={`${Style.carouselContentWrapper}`}
          onTouchStart={handleTouchStart}
          onTouchMove={handleTouchMove}
        >
          <div
            className={`${Style.carouselContent} ${Style[indexStyle]}`}
            style={{
              transform: `translateX(-${currentIndex * (100 / show)}%)`,
            }}
          >
            {children}
          </div>
        </div>
        {/* You can alwas change the content of the button to other things */}
        {currentIndex < length - show && (
          <button onClick={next} id="rightArrow" className={Style.rightArrow}>
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25">
              <path d="M7.293 4.707 14.586 12l-7.293 7.293 1.414 1.414L17.414 12 8.707 3.293 7.293 4.707z" />
            </svg>
          </button>
        )}
      </div>
    </div>
  );
};
export default Carousel;
