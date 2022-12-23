import React, { Component, useState, useEffect, useRef } from "react";

const Carousel = (props) => {
    const [slideIndex, setSlideIndex] = useState(1);
    const [slideModelIndex, setModelSlideIndex] = useState(1);
    const [modelToggle, setModelState] = useState(false);
    const [modelClass, setModelClassState] = useState("");

    useEffect(() => {
        showMainSlides();
    }, []);

    function showMainSlides() {
        var i;
        var n = slideIndex;
        //console.log("nnn", n);

        var slides = document.getElementsByClassName("mainSlides");
        if (n > slides.length) {
            n = 1;
            setSlideIndex(1);
           // console.log("if first", n);
        }
        if (n < 1) {
           // console.log("if second", n);
            n = slides.length;
            setSlideIndex(slides.length);
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
           // console.log("lengthImage", slides.length);

        }
        // 0,1,2
        // 1,2,3
        // 2,3,4
        let start = n;
        for (let l = 0; l < 3; l++) {
            if (slides.length <= start + 3) {
                if (slides.length === start) {
                    slides[0].style.display = "block";
                    slides[1].style.display = "block";
                    slides[2].style.display = "block";
                    return;
                }
                if (start - l < 1) {
                    if (start - l < 0) {
                        slides[start + l].style.display = "block";
                    }
                    else{
                        slides[start - l].style.display = "block";
                    }
                }
                else {
                    slides[start - l].style.display = "block";
                }
            }
            if (slides.length > start + 3) {
                if (slides.length === start) {
                    slides[start - l].style.display = "block";
                }
                else {
                    slides[start + l].style.display = "block";
                }
            }
        }
    }
    const prevModelSlides = (e) => {
        let index = slideModelIndex - 1;
       // console.log("pre click", index);
        setModelSlideIndex(index);
        showModelSlides()

    }
    const nextModelSlides = (e) => {
        let index = slideModelIndex + 1;
        setModelSlideIndex(index);
        showModelSlides()
    }
    //main action button
    const prevSlides = (e) => {
        let index = slideIndex - 1;
        setSlideIndex(index);
        showMainSlides()

    }
    const nextSlides = (e) => {
        let index = slideIndex + 1;
        setSlideIndex(index);
        showMainSlides()
    }
    //main slider
    const renderSliderImgs = () => {
        if (Array.isArray(props.images) && props.images.length > 0) {
            return props.images.map((image, index, key) => {
               // console.log("image lnth renderSliderImgs", props.images.length);
                return (
                    <div className="mySlides mainSlides  fade" key={index}>
                        <img src={image} className="slideImages" alt="slideimage" key={key} onClick={openModel} />
                    </div>
                )
            });
        }
    }
    const renderModelSliderImgs = () => {
        if (Array.isArray(props.images) && props.images.length > 0) {
            return props.images.map((image, index, key) => {
               // console.log("image lnth renderModelSliderImgs", props.images.length);
                return (
                    <div className="mySlides modelSlider" key={index}>
                        <img src={image} className="slideImages" alt="slideimage" key={key} />
                    </div>
                )
            });
        }
    }
    const renderColumnModelSliderImgs = () => {
        if (Array.isArray(props.images) && props.images.length > 0) {
            return props.images.map((image, index, key) => {
               // console.log("image lnth renderColumnModelSliderImgs", props.images.length);
                return (
                    <div className="column" key={index}>
                        <img src={image} className="slideImages demo cursor" alt="slideimage" onClick={handleDotsSlider} key={key} />
                    </div>
                )
            });
        }
    }
    const handleDotsSlider =(e)=>{
        var slides = document.getElementsByClassName("demo");
        for (let i = 0; i < slides.length; i++) {
            slides[i].className = slides[i].className.replace(" active", "");
        }
        e.currentTarget.classList.add("active");
        
    }
    const openModel = () => {
        if (modelToggle) {
            setModelState(false);
            setModelClassState("hideCls")
        }
        else {
            setModelState(true)
            setModelClassState("showCls")
        }
        showModelSlides()
        // currentSlide();
    }
    //
    function showModelSlides() {
        var i;
        let n = slideModelIndex;
      //  console.log("nnnnn", n);
        var slides = document.getElementsByClassName("modelSlider");
        var dots = document.getElementsByClassName("demo");
        if (n > slides.length) {
            n = 1;
            setModelSlideIndex(1);
        }
        if (n < 1) {
            n = slides.length
            setModelSlideIndex(n);
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        let index = n;
        slides[index - 1].style.display = "block";
       // console.log("dot lenght", dots.length);
      //  console.log("dot", index - 1, dots);
        dots[index - 1].className += " active";
    }
    return (<>
        <div className="slider">
            <div className="slideshow-container">
                {
                    renderSliderImgs()
                }
                <a className="prev" onClick={prevSlides}>&#10094;</a>
                <a className="next2" onClick={nextSlides}>&#10095;</a>

            </div>
        </div>
        <div id="myModal" className={`modal slider ${modelClass}`}>
            <span className="close cursor" onClick={openModel} >&times;</span>
            <div className="modal-content">
                {
                    renderModelSliderImgs()
                }
                <a className="prev modelAction" onClick={prevModelSlides}>&#10094;</a>
                <a className="next2 modelAction" onClick={nextModelSlides}>&#10095;</a>
                <div className="modelSliderImgs">
                    {
                        renderColumnModelSliderImgs()
                    }
                </div>
            </div>
        </div>
    </>
    );
};
export default Carousel;
 