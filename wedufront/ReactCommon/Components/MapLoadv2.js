import React, { Component, useRef } from "react";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import "mapbox-gl/dist/mapbox-gl.css";
import Marker from "../../components/Marker/Marker";
import Loader from "../../components/loader/mapLoader"

import {
  accessToken,
  defaultImage,
  markerApi,
} from "../../constants/GlobalConstants";
import API from "./../utility/api";
import MapCard from "./../Components/MapCard";
import ReactDOM from "react-dom";
mapboxgl.accessToken = accessToken;
class MapLoadv2 extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDrawBtnEnabled: true,
      isDraged: false,
      isZooming: false,
      flag: true,
      onMarkerClicked: false,
      didMountRender: false,
      markerArr: [],
      favIconImg: "/images/icons/empty_heart.svg",
    };
    this.mapDiv = React.createRef();
    this.map = React.createRef();
    this.draw = React.createRef();
    this.apiCall = this.apiCall.bind(this);
    this.createCluster = this.createCluster.bind(this);
    this.polygonClick = this.polygonClick.bind(this);
    this.clearPrevMarkers = this.clearPrevMarkers.bind(this);
    this.setMarkers = this.setMarkers.bind(this);
  }

  componentDidMount() {
    this.map = new mapboxgl.Map({
      container: this.mapDiv,
      style: "mapbox://styles/mapbox/streets-v11?optimize=true",
      center: this.props.center,
      zoom: this.props.zoom,
    });
    this.map.addControl(new mapboxgl.NavigationControl(), "bottom-left");
    this.props.setOnMarkerClicked(true);
    this.setState(
      {
        onMarkerClicked: true,
        didMountRender: true,
      },
      () => {
        this.createCluster();
      }
    );
    // this.createCluster();
    let map = this.map;
    map.on("load", () => {
      this.map.on("zoomend", (event) => {
        if (event.originalEvent!==undefined) {
          this.setState({
            isZooming:true
          })
          console.log(this.state.isDraged,this.state.isZooming,"hello zoomend");
          var bounds = this.map.getBounds();
          let bndstr =
            "" +
            bounds.getNorthEast().wrap().toArray() +
            "###" +
            bounds.getSouthWest().wrap().toArray() +
            "";

          this.props.mapdragenCb({ bndstr });
        }
      });
    });
  }
  clearPrevMarkers() {
    this.state.markerArr.map((item, index) => {
      const element = document.getElementById("markers-" + item);
      if (element) {
        element.remove();
      }
    });
  }
  createCluster() {
    const map = this.map;
    this.clearPrevMarkers();
    map.on("load", () => {
      map.loadImage("../marker2.png", function (error, image) {
        if (error) throw error;
        map.addImage("pointer", image);
      });

      map.on("click", (e) => {
        this.props.setOnMarkerClicked(true);
        localStorage.setItem("markerClicked", true);
        if (e.originalEvent.target) {
          if (e.originalEvent.target.attributes) {
            if (e.originalEvent.target.attributes.dataset) {
              if (e.originalEvent.target.attributes.dataset.value) {
                let id = e.originalEvent.target.attributes.dataset.value;
                this.setState({
                  onMarkerClicked: "clicked",
                });
                try {
                  id = JSON.parse(id);
                  id = id.id;
                } catch (error) {}
                let urls = markerApi;
                let uri = window.location.origin;
                let propertySearchFilter = this.props.propertySearchFilter;

                let data = {
                  id: id,
                  status: propertySearchFilter.status,
                };
                API.jsonApiCall(urls, data, "POST", null, {
                  "Content-Type": "application/json",
                })
                  .then((res) => {
                    // console.log("zoom end map marker");
                    this.props.setOnMarkerClicked(true);

                    let html = "";
                    let item = res.data;
                    // this.state.isDraged ? "" : map.flyTo({ center: this.props.center, essential: true, zoom: 12 });
                    // console.log("map marker data", item.Latitude);
                    let centers = [item.Longitude, item.Latitude];
                    this.map.flyTo({ center: centers });
                    let data = item;
                    if (res.status == 200) {
                      if (data) {
                        let loader = document.getElementById(
                          "marker-loader" + data.ListingId
                        );

                        loader.remove();
                        ReactDOM.render(
                          <MapCard
                            showIsFav={true}
                            openUserPopup={true}
                            item={data}
                            isMarker={true}
                            isMarkerClass={"markerClass"}
                            openLoginCb={this.props.togglePopUp}
                            isLogin={this.props.isLogin}
                          />,
                          document.getElementById(
                            "marker-info" + data.ListingId
                          )
                        );
                      } else {
                        let loader = document.getElementById(
                          "marker-loader" + id
                        );
                        loader.remove();
                        ReactDOM.render(
                          <h5>Not Found !</h5>,
                          document.getElementById("marker-info" + id)
                        );
                      }
                    } else {
                      let loader = document.getElementById(
                        "marker-loader" + id
                      );
                      loader.remove();
                      ReactDOM.render(
                        <h5>Not Found !</h5>,
                        document.getElementById("marker-info" + id)
                      );
                    }
                    this.setState({
                      onMarkerClicked: false,
                    });
                    this.props.setOnMarkerClicked(false);
                   
                  })
                  .catch((e) => {});
              }
            }
          }
        }
      });
      // Example of a MapTouchEvent of type "touch"
      // map.on('touchstart', (e) => {
      //     if (e.originalEvent.target) {
      //     if (e.originalEvent.target.attributes) {
      //         if (e.originalEvent.target.attributes.dataset) {
      //             if (e.originalEvent.target.attributes.dataset.value) {
      //                 let id = e.originalEvent.target.attributes.dataset.value;
      //                 try {
      //                     id = JSON.parse(id);
      //                     id = id.id;
      //                 } catch (error) {

      //                 }
      //                 let urls = markerApi;
      //                 let uri = window.location.origin;
      //                 let data = {
      //                     id: id
      //                 }
      //                 API.jsonApiCall(urls, data, "POST", null, {
      //                     "Content-Type": "application/json",
      //                 }).then((res) => {
      //                     let mobileBox = document.getElementById('markerInMobile');
      //                     mobileBox.style.display="block";
      //                     let infoWindo = document.getElementById('markerMobileInfo');
      //                     let html = "";
      //                     let item = res.data;
      //                     let data = item;
      //                     if (res.status == 200) {
      //                         if (data) {
      //                             ReactDOM.render(<MapCard showIsFav={true}
      //                                 openUserPopup={true}
      //                                 item={data}
      //                                 isMarker={true}
      //                                 isMarkerClass={"markerClass"}
      //                                 openLoginCb={this.props.togglePopUp}
      //                                 isLogin={this.props.isLogin} />, infoWindo);

      //                         } else {
      //                             ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
      //                         }
      //                     }
      //                     else {
      //                         let loader = document.getElementById('marker-loader' + id);
      //                         loader.remove();
      //                         ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
      //                     }
      //                 }).catch((e) => {
      //                 });
      //             }
      //         }
      //     }
      // }

      // })
     
      // map.on("zoomend", (event) => {
        
      //   if (this.state.onMarkerClicked) {
      //     this.setState({
      //       onMarkerClicked: false,
      //       isZooming: true,
      //     });
      //     // return;
      //   }
      //   if (this.state.didMountRender) {
      //     setTimeout(() => {
      //       this.setState({
      //         didMountRender: false,
      //       });
      //     }, 1500);
      //     return;
      //   }
      //   if (localStorage.getItem("markerClicked")) {
      //     setTimeout(() => {
      //       localStorage.removeItem("markerClicked");
      //     }, 1500);
          
      //     return;
      //   }

      //   if (this.state.onMarkerClicked === "clicked") {
      //     return;
      //   }

      //   if (this.props.onMarkerClicked) {
      //     return;
      //   }

      //   if (event && event.type === "zoomend") {
      //     var bounds = map.getBounds();
      //     let bndstr =
      //       "" +
      //       bounds.getNorthEast().wrap().toArray() +
      //       "###" +
      //       bounds.getSouthWest().wrap().toArray() +
      //       "";
      //      this.props.mapdragenCb({ bndstr });
      //   }
      // });
      map.on("dragend", (event) => {
        this.setState({
          isDraged: true,
        });
        var bounds = map.getBounds();
        let bndstr =
          "" +
          bounds.getNorthEast().wrap().toArray() +
          "###" +
          bounds.getSouthWest().wrap().toArray() +
          "";
        this.props.mapdragenCb({ bndstr });
      });
      this.clusterMapping();
    });

    this.setState({
      flag: !this.state.flag,
      onMarkerClicked: false,
    });
  }
  clusterMapping() {
    let isBoundSet = false;
    if (this.map.getLayer("city-new_adv")) {
      this.map.removeLayer("city-new_adv");
    }
    if (this.map.getSource("city_adv")) {
      this.map.removeSource("city_adv");
    }
    if (this.map.getLayer("areas-new_adv")) {
      this.map.removeLayer("areas-new_adv");
    }
    if (this.map.getSource("areas_adv")) {
      this.map.removeSource("areas_adv");
    }
    if (this.props.areaData && this.props.areaData.areasPolygons) {
      if (isBoundSet) {
        return;
      }
      isBoundSet = true;
      let sourceData = [];
      try {
        sourceData = JSON.parse(this.props.areaData.areasPolygons);
      } catch (error) {
        // sourceData = this.props.areaData.areasPolygons;
      }
      try {
        this.map.addSource("areas_adv", {
          type: "geojson",
          data: {
            type: "Feature",
            geometry: {
              type: "Polygon",
              coordinates: [sourceData],
            },
          },
        });
        this.map.addLayer({
          id: "areas-new_adv",
          type: "line",
          source: "areas_adv",
          layout: {},
          paint: {
            "line-width": 2,
            "line-color": "#ff5f64",
          },
        });
      } catch (error) {}
    } else {
      if (isBoundSet) {
        return;
      }
      if (this.props.cityData) {
        let sourceData = [];
        try {
          sourceData = JSON.parse(this.props.cityData.cityPolygons);
        } catch (error) {
          // sourceData = this.props.cityData.cityPolygons;
        }
        try {
          this.map.addSource("city_adv", {
            type: "geojson",
            data: {
              type: "Feature",
              geometry: {
                type: "Polygon",
                coordinates: [sourceData],
              },
            },
          });
          this.map.addLayer({
            id: "city-new_adv",
            type: "line",
            source: "city_adv",
            layout: {},
            paint: {
              "line-width": 2,
              "line-color": "#ff5f64",
            },
          });
        } catch (error) {}
        isBoundSet = true;
      }
    }
  }
  apiCall(data, type) {
    this.props.handlePropertyCall(data[0], type);
  }
  polygonClick() {
    var btn = document.getElementsByClassName("mapbox-gl-draw_polygon");
    btn[0].click();
    this.setState({ isDrawBtnEnabled: false });
  }
  setMarkers() {
    if (
      this.props.mapData &&
      this.props.mapData.length &&
      Array.isArray(this.props.mapData)
    ) {
      let map = this.map;
      let tempArr = [];
      this.props.mapData.map((res, key) => {
        let htmlContent =
          '<div className="marker-info" id="marker-info' +
          res.ListingId +
          '"><p id="marker-loader' +
          res.ListingId +
          '" >loading......</p></div>';
        const markerNode = document.createElement("div");
        tempArr.push(res.ListingId);
        ReactDOM.render(
          <Marker
            id={res.ListingId}
            timeMicro={""}
            highLight={this.props.highLight}
          />,
          markerNode
        );
        const popup = new mapboxgl.Popup({ offset: 10 }).setHTML(htmlContent);
        new mapboxgl.Marker(markerNode)
          .setLngLat([res.Longitude, res.Latitude])
          .addTo(map)
          .setPopup(popup);
      });
      this.setState({
        markerArr: tempArr,
      });
      this.state.isDraged || this.state.isZooming
        ? ""
        : map.flyTo({ center: this.props.center, essential: true, zoom: 10 });
    }
    this.setState({
      isZooming:false,
      isDraged:false
    })
    this.props.setOnMarkerClicked(false);
  }
  componentDidUpdate(prevProps) {
    if (
      JSON.stringify(this.props.mapData) !== JSON.stringify(prevProps.mapData)
    ) {
      this.props.setOnMarkerClicked(true);
      this.setState({
        onMarkerClicked: true,
      });
      this.clearPrevMarkers();
      this.setMarkers();
      if (this.props.isReset) {
        this.map.flyTo({
          center: this.props.center,
          essential: true,
          zoom: 9,
        });
      }
    }

    if (this.props.cityData !== prevProps.cityData) {
      this.clusterMapping();
    }
    if (this.props.areaData !== prevProps.areaData) {
      this.clusterMapping();
    }

    if (this.props.resetMapDraw !== prevProps.resetMapDraw) {
      if (!this.isDrawBtnEnabled) {
        this.draw.deleteAll();
      }
    }
  }
  closeInfoWindow() {
    document.getElementById("markerInMobile").style.display = "none";
  }
  render() {
    const { isDrawBtnEnabled } = this.state;
    return (
      <>
        {this.props.mapLoader && <div className="position-relative">
          <Loader />
        </div>}
        <div
          id="mapDiv"
          ref={(e) => (this.mapDiv = e)}
          className="mapInMbile"
          style={{'opacity':`${this.props.mapLoader?0.6:1}`}}
        ></div>
        {/*<div id="markerInMobile">
                    <span className="InfoWindowClose" onClick={this.closeInfoWindow}>x</span>
                    <div id="markerMobileInfo"></div>
                </div>*/}
      </>
    );
  }
}
export default MapLoadv2;
