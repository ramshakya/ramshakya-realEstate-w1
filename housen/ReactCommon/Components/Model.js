import React, { useState, useEffect, useRef } from "react";
import Map from "./PropertyDetails/Map";
import StreetView from "./../Components/StreetView";
import PropertyCarousel from "./../Components/PropertyDetails/PropertyCarousel";
import ReactCarousel from "./ReactCarousel";
import detect from "./../utility/detect";
const Model = (props) => {
  const extraProps = [];
  const prop = [];
  // const [showModalClass, setShow] = useState(props.show ? "d-block" : "")
  const [showModalClass, setShow] = useState("");
  const [activeClass, setActive] = useState(true);
  const [MapResize, setMapResize] = useState(false);
  const [googleMapResize, setGoogleMapResize] = useState(false);
  const [detailData, setDetailData] = useState(props.detailData);
  useEffect(() => {
    if (props.show) {
      setShow("d-block");
    } else {
      setShow("");
    }
  }, [props.show]);
  useEffect(() => {
    let ac = document.getElementById("map").click();
  }, []);
  const closeModal = (e) => {
    let cls = e.target;
    if (
      cls.classList.contains("model-container") ||
      cls.classList.contains("closeBtn")
    ) {
      setShow("");
      if (props.handleClose) {
        props.handleClose();
      }
    }
  };
  const changeTabs = (e) => {
    // tabcontent
    var tabs = document.getElementsByClassName("tablinks");
    var tabcontent = document.getElementsByClassName("tabcontent");
    for (let i = 0; i < tabcontent.length; i++) {
      tabcontent[i].className = tabcontent[i].className.replace(
        " showCls",
        "  hideCls"
      );
    }
    for (let i = 0; i < tabs.length; i++) {
      tabs[i].className = tabs[i].className.replace("active", "");
    }
    if (e.target.id === "photos") {
      let ac = document.getElementById("photosTab");
      ac.classList.add("showCls");
    }
    if (e.target.id === "map") {
      let ac = document.getElementById("mapTab");
      ac.classList.add("showCls");
      setMapResize(true);
    }
    if (e.target.id === "street_view") {
      let ac = document.getElementById("street_viewTab");
      ac.classList.add("showCls");
      setGoogleMapResize(true);
      initializeView();
    }
    e.currentTarget.classList.add("active");
  };
  function initializeView() {
    try {
      let lat = (detailData.Latitude);
      let lng = (detailData.Longitude);
      let center = { lat: lat, lng: lng };
      console.log("center=> b4 ", detailData.Latitude);
      console.log("center=> b4 ", detailData.Longitude);
      // center={ lat: 37.86926, lng: -122.254811};
      console.log("center=> after ", center);
      const map2 = new google.maps.Map(document.getElementById("pano"), {
        center: center,
        zoom: 12,
      });
      const panorama = new google.maps.StreetViewPanorama(
        document.getElementById("pano"),
        {
          position: center,
          pov: {
            heading: 34,
            pitch: 10,
          },
        }
      );
      map2.setStreetView(panorama);
    } catch (error) {}
  }
  return (
    <div
      className={`reactModal ${showModalClass ? "height-100" : ""}`}
      onClick={closeModal}
    >
      <div
        className={` ${
          showModalClass ? showModalClass + "  model-container" : ""
        }`}
      >
        {/* <button className="btn">Click me</button> */}
        <div className={`modal ${showModalClass}`}>
          <div className="modal-header1">
            {/* <div className="closeBtn" > */}
            <span className="closeBtn btn-close" onClick={closeModal}></span>
            {/* </div> */}
            <div className="tab">
              <button
                className="tablinks"
                id="photos"
                data-set="photos"
                onClick={changeTabs}
                hidden
              >
                Photos
              </button>
              <button
                className="tablinks"
                id="map"
                data-set="map"
                onClick={changeTabs}
              >
                Map &amp; Amenities
              </button>
              <button
                className="tablinks"
                id="street_view"
                data-set="street_view"
                onClick={changeTabs}
              >
                Street View
              </button>
            </div>
          </div>
          <div className=" secondHeader">
            <p className="addrText">{props.detailData.Addr} </p>
            {/* <button className="scheduleBtn">Schedule Showing</button> */}
          </div>
          <div className="modal-body">
            <div id="photosTab" className="tabcontent showCls py-2">
              <div className="mt-2">
                <div
                  style={{
                    marginTop: "-40px",
                  }}
                >
                  {!detect.isMobile() ? (
                    <PropertyCarousel
                      imageToShow={6}
                      inRowImage={3}
                      propertyImage={props.carouselImages}
                      showSingleFirstSlideImage={true}
                      firstSliderRow={4}
                      sliderHeight={"600px"}
                    />
                  ) : (
                    <>
                      <ReactCarousel show={1}>
                        {props.sliderImages}
                      </ReactCarousel>
                    </>
                  )}
                </div>
              </div>
            </div>
            <div id="mapTab" className="tabcontent hideCls">
              <Map mapResize={MapResize} {...props} />
            </div>

            <div id="street_viewTab" className="tabcontent hideCls">
              <StreetView
                detailData={props.detailData}
                config={props.streetViewConfig}
                resize={googleMapResize}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
export default Model;
