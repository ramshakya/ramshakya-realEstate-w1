import React, { useEffect, useState } from "react";
import Style from "../../styles/css/ReactCommon/ReactCarousel.module.css";
import backArrow from "../../public/images/icon/backArrow.svg";
import forwordArrow from "../../public/images/icon/forwordArrow.svg";

//
const Carousel = (props) => {
  const { children, show, mainImage, homeSlider } = props;
  const [currentIndex, setCurrentIndex] = useState(
    props.showIcon ? props.show : 0
  );
  const [length, setLength] = useState(children.length);

  const [touchPosition, setTouchPosition] = useState(null);
  function checkImgs() {
    //console.log("children", children);
  }
  useEffect(() => {
    if (props.selectedCarouselIndex) {
      setCurrentIndex(props.selectedCarouselIndex);
    }
  }, [props.selectedCarouselIndex]);
  // Set the length to match current children from props
  useEffect(() => {
    setLength(children.length);
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
  let width = false;

  if (show !== 1) {
    if (length == 1) {
      width = "width-25";
    }
    if (length == 2) {
      width = "width-50";
    }
    if (length == 3) {
      width = "width-75";
    }
  }
  if (show === 2) {
    if (length == 1) {
      width = "width-50";
    }
    if (length == 2) {
      width = "width-100";
    }
  }

  // if(secondRow.length==1){
  //   width2="33.33%";
  // }
  // if(secondRow.length==2){
  //   width2="66.66%";
  // }
  // if(thirdRow.length==1){
  //   width3="33.33%";
  // }
  // if(thirdRow.length==2){
  //   width3="66.66%";
  // }

  return (
    <div
      className={` ${props.parentCls ? props.parentCls : ""} ${
        Style.carouselContainer
      }`}
    >
      <div className={Style.carouselWrapper}>
        {/* You can alwas change the content of the button to other things */}
        {currentIndex > 0 && (
          <button onClick={prev} id="leftArrow" className={Style.leftArrow}>
            {/* <img {...backArrow} /> */}
            {/*<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M15.293 3.293 6.586 12l8.707 8.707 1.414-1.414L9.414 12l7.293-7.293-1.414-1.414z"/></svg>*/}
            {/* <span style={{color: "#fff",fontSize: "42px"}}>&lt;</span> */}
            <i className="fa fa-angle-left sliderArrow"></i>
          </button>
        )}
        <div
          className={Style.carouselContentWrapper}
          onTouchStart={handleTouchStart}
          onTouchMove={handleTouchMove}
        >
          {/* ${!homeSlider?Style[indexStyle]:""}
            ${width && !homeSlider?Style[width]:''} 
            ${homeSlider=='homeSlider'?Style.homeSlider:''} 
            ${homeSlider=='homeSlider1'?Style.homeSlider1:''} */}
          <div
            className={`${Style.carouselContent} 
            ${Style[indexStyle]}
            ${width ? Style[width] : ""} 
         `}
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
            {/* <img {...forwordArrow} /> */}
            {/*<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"><path d="M7.293 4.707 14.586 12l-7.293 7.293 1.414 1.414L17.414 12 8.707 3.293 7.293 4.707z"/></svg>*/}
            {/* <span style={{color: "#fff",fontSize: "42px"}}>&gt;</span> */}
            <i className="fa fa-angle-right sliderArrow"></i>
          </button>
        )}
      </div>
    </div>
  );
};

export default Carousel;
