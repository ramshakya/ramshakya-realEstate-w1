import React from "react";
import Constants from "./../../../constants/GlobalConstants";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
import ReactDOM from "react-dom";
import Marker from "./../../../components/Marker/Marker";
import ReactCarousel from "./../ReactCarousel";
import detect from "./../../utility/detect";
// /var/www/html/ReactJs/wedufrontend/ReactCommon/Components/ReactCarousel.js
class Mapload extends React.Component {
  constructor(props) {
    console.log("constructorprops",props);
    super(props);
    this.map = "";
    this.state = {
      lat: props.detailData ? props.detailData.Latitude : 51.5074,
      lng: props.detailData ? props.detailData.Longitude : -0.1278,
      zoom: 9,
      mapDiv: "",
      mapResize: props.mapResize,
      isSlotsSelected: "",
      menuList: [
        { text: "Schools", value: "schools" },
        { text: "Restaurant & Bar", value: "restaurant_bar" },
        { text: "Grocery", value: "grocery" },
        { text: "Service", value: "service" },
        { text: "Hospitals", value: "hospitals" },
      ],
    };
    this.mapResize = this.mapResize.bind(this);
    this.mapInit = this.mapInit.bind(this);
    this.selectedSlot = this.selectedSlot.bind(this);
    this.setMarkers = this.setMarkers.bind(this);
  }
  componentDidMount() {
    if (this.props.mapResize) {
    } else {
    }
    this.mapInit();
  }
  componentDidUpdate(prevProps, prevState, snapshot) {
    if (this.props.amenities !== prevProps) {
      if (this.props.mapResize) {
        if (detect.isMobile()) {
          return;
        }
        this.setMarkers();
      } else {
      }
    }
  }
  setMarkers() {
    let markerElm = document.getElementsByClassName("prop_marker");
    for (let index = 0; index < markerElm.length; index++) {
      const element = markerElm[index];
      element.remove();
    }
    let amenities = this.props.amenities ? this.props.amenities : {};
    if (amenities.businesses) {
      amenities.businesses.map((res, key) => {
        const markerNode = document.createElement("div");
        ReactDOM.render(<Marker id={res.id} key={key} />, markerNode);
        // add marker to map
        new mapboxgl.Marker(markerNode)
          .setLngLat([res.coordinates.longitude, res.coordinates.latitude])
          .addTo(this.map);
      });
      let center = amenities.region.center;
      let Latitude = amenities.businesses.length?amenities.businesses[0].coordinates.latitude:0.22;
      let Longitude = amenities.businesses.length?amenities.businesses[0].coordinates.longitude:0.112;
      this.map.flyTo({
        center: [Longitude, Latitude],
        zoom: 10,
        essential: true,
      });
    }
  }
  mapInit() {
    if (detect.isMobile()) {
      return;
    }
    mapboxgl.accessToken = Constants.accessToken;
    const { lat, lng, zoom } = this.state;
    const latitude = 51.5074;
    const longitude = -0.1278;
    if (!lat && !lng) {
      return;
    }

    if (!this.mapDiv) {
      return;
    }
    let Latitude = this.props.detailData.Latitude;
    let Longitude = this.props.detailData.Longitude;
    let mapContain = new mapboxgl.Map({
      container: this.mapDiv,
      style: Constants.mapStyle,
      center: [Latitude, Longitude],
      zoom: zoom,
    });
    this.setState({
      mapDiv: mapContain,
    });
    this.map = mapContain;

    var geolocate = new mapboxgl.GeolocateControl({
      positionOptions: {
        enableHighAccuracy: true,
      },
      trackUserLocation: true,
    });
    this.map.addControl(geolocate, "top-left");
    this.map.addControl(new mapboxgl.NavigationControl(), "top-left");
    geolocate.on("geolocate", function (e) {
      let eventLngLat = [e.coords.longitude, e.coords.latitude];
      var point = turf.point(eventLngLat);
      var searchRadius = turf.buffer(point, 1500, {
        units: "kilometers",
      });
    });
    mapContain.on("load", () => {
      this.setMarkers();
      setTimeout(() => {
        mapContain.resize();
      }, 200);
    });
  }
  mapResize() {}
  generateList() {
    let amenities = this.props.amenities ? this.props.amenities : {};
    let markerElm = document.getElementsByClassName("prop_marker");
    for (let index = 0; index < markerElm.length; index++) {
      const element = markerElm[index];
      element.remove();
    }
    if (amenities.businesses) {
      const list = amenities.businesses.map((res, key) => {
        const markerNode = document.createElement("div");
        markerNode.setAttribute("key", key);
        ReactDOM.render(<Marker id={res.id} />, markerNode);
        return (
          <>
            <div className={`map-tab-inner${key}`}>
              <div className="menu-one">
                <div className="menu-left">
                  {/* <div className="imageHold"> */}
                  <img
                    src={res.image_url}
                    alt={res.categories ? res.categories[0].title : "img"}
                    loading="lazy"
                    className="border"
                  />
                  {/* </div> */}
                  <div className="menu-text-left">
                    <a rel="noopener noreferrer" target="_blank" href={res.url}>
                      <h6>{res.name}</h6>
                    </a>
                    <span>{res.alias}</span>
                    <img
                      className=""
                      style={{ height: "20px" }}
                      src="https://agentiwebs.com/assets/assist/img/web_and_ios/small/small_2@2x.png"
                      alt="Yelp rating"
                      height="100"
                    />
                    <span> {res.phone}</span>
                    <span>{res.location.display_address}</span>
                    <span>Based on {res.review_count} Reviews</span>
                  </div>
                </div>
              </div>
            </div>
          </>
        );
      });
      return list;
    }
  }
  selectedSlot(item, index) {
    // this.map.flyTo({
    //     center: [
    //         this.props.detailData.Longitude+2  ,
    //         this.props.detailData.Latitude  +3
    //     ],
    //     zoom: 0,
    // });
    this.setState({
      isSlotsSelected: index,
    });
    let obj = {
      Latitude: this.props.detailData.Latitude,
      Longitude: this.props.detailData.Longitude,
      type: item.value,
    };

    this.props.getYelpData(obj);
  }
  showMarkers() {}
  timeSince(date) {
    var seconds = Math.floor((new Date() - date) / 1000);
    var interval = seconds / 31536000;
    if (interval > 1) {
      return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
      return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
      return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
      return Math.floor(interval) + " hours";
    }
    interval = seconds / 60;
    if (interval > 1) {
      return Math.floor(interval) + " minutes";
    }
    return Math.floor(seconds) + " seconds";
  }
  diff_hours(dt2, dt1) {
    var diff = (dt2.getTime() - dt1.getTime()) / 1000;
    diff /= 60 * 60;
    return Math.abs(Math.ceil(diff));
  }
  render() {
    const renderList = (e) => {
      const list = this.state.menuList.map((res, index) => {
        return (
          <>
            <div
              className={`list_block menu-list  ${
                this.state.isSlotsSelected == index ? "menuActive" : ""
              }`}
              id={"isSlotsSelected" + index}
              onClick={() => this.selectedSlot(res, index)}
              data-set={index}
              data-value={res}
            >
              <span date-set={res}>{res.text}</span>
            </div>
          </>
        );
      });

      return list;
    };
    return (
      <>
        <div className="row">
          {!detect.isMobile() && (
            <div className="col-md-6 col-xs-12 ">
              <div ref={(e) => (this.mapDiv = e)} className="mapDetail"></div>
            </div>
          )}
          <div className="col-md-6 col-xs-12 ">
            <div className="listContainers">
              <h4>Nearby Lifestyle</h4>
              <ReactCarousel show={3}>{renderList()}</ReactCarousel>
            </div>
            <div className="nearBySection ml-2">
              <div className="map-tab p-3">{this.generateList()}</div>
            </div>
          </div>
        </div>
      </>
      // <div className="mapSlidContainer">
      //     <div className="row">
      //         <div className="col-md-6 col-xs-12 ">
      //             <div ref={e => this.mapDiv = e} className="mapDetail">
      //             </div>
      //         </div>
      //         <div className="col-md-6 col-xs-12 ">
      //             <div className="slideContainer mt-1">
      //                 <div className="row mt-2">
      //                     <div className="col-md-12 ">
      //                         <div className="listContainers">
      //                             <h4>Nearby Lifestyle</h4>
      //                             <ReactCarousel show={3}>
      //                                 {
      //                                     renderList()
      //                                 }
      //                             </ReactCarousel>
      //                         </div>
      //                         <div className="nearBySection ml-2">
      //                             <div className="map-tab p-3">
      //                                 {
      //                                     this.generateList()
      //                                 }
      //                             </div>
      //                         </div>
      //                     </div>
      //                 </div>
      //             </div>
      //         </div>
      //     </div>

      // </div>
    );
  }
}
export default Mapload;
