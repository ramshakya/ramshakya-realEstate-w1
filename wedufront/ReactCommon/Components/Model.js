import React, { useState, useEffect, useRef } from "react";
import Map from "./PropertyDetails/Map";
import StreetView from "./../Components/StreetView";
import PropertyCarousel from "./../Components/PropertyDetails/PropertyCarousel";
import ReactCarousel from "./ReactCarousel";
import detect from "./../utility/detect";
const Model = (props) => {
  const [showModalClass, setShow] = useState("");
  const [activeClass, setActive] = useState(true);
  const [MapResize, setMapResize] = useState(false);
  const [googleMapResize, setGoogleMapResize] = useState(false);
  const [detailData, setDetailData] = useState(props.detailData);
  const [centerMap, setCenter] = useState([]);
  useEffect(() => {
    if (props.show) {
      setShow("d-block");
    } else {
      setShow("");
    }
  }, [props.show]);
  useEffect(() => {
    let ac = document.getElementById("map").click();
    let cen = [props.detailData.Longitude, props.detailData.Latitude];
    setCenter(cen);
  }, []);
  const closeModal = (e) => {
    setShow("");
    if (props.handleClose) {
      props.handleClose();
    }
  };
  const changeTabs = (e) => {
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
      let lat = parseFloat(detailData.Latitude);
      let lng = parseFloat(detailData.Longitude);
      let center = { lat: lat, lng: lng };
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
    <div className={`reactModal `}>
      <div
        className={` ${
          showModalClass ? showModalClass + "  model-container" : ""
        }`}
      >
        <div className={`modal ${showModalClass}`}>
          <div className="modal-header1">
            <span className="closeBtn" onClick={closeModal}>
              x
            </span>
            <div className="tab">
              <button
                className="tablinks"
                id="photos"
                data-set="photos"
                onClick={changeTabs}
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
                      alt={props.alt}
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
              <Map mapResize={MapResize} {...props} centerMap={centerMap} />
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
