import React, { Component, useRef } from "react";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
import mbxGeocoding from "@mapbox/mapbox-sdk/services/geocoding";
import { accessToken, defaultImage, markerApi } from "../../constants/Global";
import API from "./../utility/api";
import MapCard from "../../components/Cards/PropertyCardMarker";
import ReactDOM from "react-dom";
mapboxgl.accessToken = accessToken;
import MapLoader from "../../components/loader/mapLoader"
var formatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 0,
});
const geocodingClient = mbxGeocoding({
  accessToken: mapboxgl.accessToken,
});

function change_price_formate(p) {
  var n = Math.trunc(p);
  var precision = 1;
  if (n < 900) {
    // 0 - 900
    var n_format = n.toFixed(precision);
    var suffix = "";
  } else if (n < 900000) {
    // 0.9k-850k
    var n_format = (n / 1000).toFixed(precision);
    var suffix = "K";
  } else if (n < 900000000) {
    // 0.9m-850m
    var n_format = (n / 1000000).toFixed(precision);
    var suffix = "M";
  } else if (n < 900000000000) {
    // 0.9b-850b
    var n_format = (n / 1000000000).toFixed(precision);
    var suffix = "B";
  } else {
    // 0.9t+
    var n_format = (n / 1000000000000).toFixed(precision);
    var suffix = "T";
  }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
  if (precision > 0) {
    var rep = 0;
    var dotzero = ".0";
    var n_format = n_format.replace(dotzero, "");
  }
  return n_format.concat(suffix);
}
class MapLoadv2 extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDrawBtnEnabled: true,
      flag: true,
      markerArr: [],
      favIconImg: "/images/icons/empty_heart.png",
      prevMarker: "",
      onMarkerClicked: false,
      mapLoad:false
    };
    this.mapDiv = React.createRef();
    this.map = React.createRef();
    this.draw = React.createRef();
    this.drawPolygon = this.drawPolygon.bind(this);
    this.createArea = this.createArea.bind(this);
    this.updateArea = this.updateArea.bind(this);
    this.deleteArea = this.deleteArea.bind(this);
    this.apiCall = this.apiCall.bind(this);
    this.createCluster = this.createCluster.bind(this);
    this.updateTrash = this.updateTrash.bind(this);
    this.polygonClick = this.polygonClick.bind(this);
    this.clearPrevMarkers = this.clearPrevMarkers.bind(this);
    this.popUpHtml = this.popUpHtml.bind(this);
    this.setMarkers = this.setMarkers.bind(this);
    this.renderItem = this.renderItem.bind(this);
    this.closeInfoWindow = this.closeInfoWindow.bind(this);
    this.clearDrawArea = this.clearDrawArea.bind(this);
  }

  componentDidMount() {
    this.map = new mapboxgl.Map({
      container: this.mapDiv,
      style: "mapbox://styles/mapbox/streets-v11",
      center: this.props.center,
      zoom: this.props.zoom+2,
      optimize: true,
      // hash: true
    });

    const southWest = new mapboxgl.LngLat(-73.9876, 40.7661);
    const northEast = new mapboxgl.LngLat(-73.9397, 40.8002);
    const boundingBox = new mapboxgl.LngLatBounds(southWest, northEast);

    let st = this.props.soldOrActive;
    let markerImage = "/images/marker.png";
    if (st == "U") {
      markerImage = "/images/marker1.png";
    }
    let map = this.map;
    map.on("load", () => {
      map.loadImage(markerImage, function (error, image) {
        if (error) throw error;
        map.addImage("marker", image);
      });
      const modes = MapboxDraw.modes;
      this.draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
          polygon: true,
          trash: true,
        },
      });
      modes.simple_select.onDrag = (e) => {
        this.updateTrash();
      };
      this.map.addControl(this.draw);
      this.map.addControl(new mapboxgl.NavigationControl(), "top-left");
      this.map.on("draw.create", this.createArea);
      this.map.on("draw.delete", this.deleteArea);
      this.map.on("draw.update", this.updateArea);
      this.map.on("dragend", (event) => {
        if (event && event.type === "dragend") {
          var bounds = map.getBounds();
          let bndstr =
            "" +
            bounds.getNorthEast().wrap().toArray() +
            "###" +
            bounds.getSouthWest().wrap().toArray() +
            "";
          // this.props.mapdragenCb({ bndstr });
        }
      });
      this.createCluster();
      this.map.on("zoomend", (event) => {
        
        if (this.state.onMarkerClicked) {
          return;
        }
        if (event.originalEvent!==undefined) {

          var bounds = this.map.getBounds();
          let bndstr =
            "" +
            bounds.getNorthEast().wrap().toArray() +
            "###" +
            bounds.getSouthWest().wrap().toArray() +
            "";
            if(!this.props.mapLoading){
            
              this.props.mapdragenCb({ bndstr });
            }
        }
      });
    });
  }
  componentDidUpdate(prevProps) {
    if (this.props.isReset) {
      this.clearDrawArea();
    }
    if (this.props.mapData !== prevProps.mapData) {
      this.clearPrevMarkers();
      if(!this.props.cityData){
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
      }


      if (this.map.getLayer("clusters")) {
        this.map.removeLayer("clusters");
      }
      if (this.map.getLayer("cluster-count")) {
        this.map.removeLayer("cluster-count");
      }
      if (this.map.getLayer("unclustered-point")) {
        this.map.removeLayer("unclustered-point");
      }

      if (this.map.getSource("properties_data")) {
        this.map.removeSource("properties_data");
      }
      if (this.map.getLayer("properties_data")) {
        this.map.removeLayer("properties_data");
      }

      if(!this.props.isZoomed){
        this.map.flyTo({
          center: this.props.center,
          essential: true,
          zoom: 10,
        });
      }
      if (this.props.isReset) {
      }
      let st = this.props.soldOrActive;
      let markerImage = "/images/marker.png";
      if (st == "U") {
        markerImage = "/images/marker1.png";
      }
      let map = this.map;
      this.setMarkers();
      map.loadImage(markerImage, function (error, image) {
        if (error) throw error;
        map.addImage("marker", image);
      });
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
        this.updateTrash;
      }
    }
  }
  clearDrawArea() {
    this.draw.deleteAll();
    let data = this.draw.delete();
    if (this.map.getLayer("maine")) {
      this.map.removeLayer("maine");
    }
    if (this.map.getSource("maine")) {
      this.map.removeSource("maine");
    }
    if (this.map.getSource("maine")) {
      this.map.removeSource("maine");
    }
    this.map.on("draw.delete", () => {
      setTimeout(() => {
        this.draw.deleteAll();
      }, 0);
    });
  }
  clearPrevMarkers() {
    this.state.markerArr.map((item, index) => {
      const element = document.getElementById("marker-info" + item);
      if (element) {
        element.remove();
      }
    });
  }

  popUpHtml(item) {
    let id = item.id;
    let data = item;
    return (
      '<div class="marker-info" id="marker-info' +
      item.ListingId +
      '"><p id="marker-loader' +
      item.ListingId +
      '" >loading......</p></div>'
    );
  }
  renderItem(text = "") {
    let id = 25555;
    let data = id;
    const self = this;
    return (
      <div>
        <div
          onClick={self.handleClick}
          class="marker-info"
          id="marker-info' + id + '"
        >
          <p id="marker-loader' + id + '">loading......</p>
        </div>
      </div>
    );
  }

  createCluster() {
    const map = this.map;
    this.clearPrevMarkers();
    // map.on("load", () => {
    // map.loadImage("/images/marker.png", function (error, image) {
    //     if (error) throw error;
    //     map.addImage("marker", image);
    // });

    if (map.getLayer("clusters")) {
      map.removeLayer("clusters");
    }
    if (map.getLayer("cluster-count")) {
      map.removeLayer("cluster-count");
    }
    if (map.getLayer("unclustered-point")) {
      map.removeLayer("unclustered-point");
    }

    if (map.getSource("properties_data")) {
      map.removeSource("properties_data");
    }
    if (map.getLayer("properties_data")) {
      map.removeLayer("properties_data");
    }
    map.on("click", "marker", (e) => {
      const coordinates = e.features[0].geometry.coordinates.slice();
      const mag = e.features[0].properties.mag;
      const tsunami = e.features[0].properties.tsunami === 1 ? "yes" : "no";
      while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
        coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
      }
      new mapboxgl.Popup()
        .setLngLat(coordinates)
        .setHTML(`magnitude: ${mag}<br>Was there a tsunami?: ${tsunami}`) //need to set the text
        .addTo(map);
    });
    map.on("click", (e) => {
      if (e.originalEvent.target) {
        if (e.originalEvent.target.attributes) {
          if (e.originalEvent.target.attributes.dataset) {
            if (e.originalEvent.target.attributes.dataset.value) {
              let id = e.originalEvent.target.attributes.dataset.value;
              try {
                id = JSON.parse(id);
                id = id.id;
              } catch (error) {}
              let urls = markerApi;
              // let uri = window.location.origin;
              // urls = "http://127.0.0.1:8000/api/v1/services/markerInfo";
              let data = {
                id: id,
              };
              API.jsonApiCall(urls, data, "POST", null, {
                "Content-Type": "application/json",
              })
                .then((res) => {
                  let html = "";
                  let item = res.data;
                  let data = item;
                  if (res.status == 200) {
                    if (data) {
                      let loader = document.getElementById(
                        "marker-loader" + data.ListingId
                      );
                      loader.remove();
                      let infoBox = document.getElementById(
                        "marker-info" + data.ListingId
                      );
                      ReactDOM.render(
                        <MapCard
                          showIsFav={true}
                          openUserPopup={true}
                          item={data}
                          isMarker={true}
                          isInfo={true}
                          isMarkerClass={"markerClass"}
                          openLoginCb={this.props.togglePopUp}
                          gotoDetailPage={this.props.gotoDetailPage}
                          isLogin={this.props.isLogin}
                        />,
                        document.getElementById("marker-info" + data.ListingId)
                      );
                    } else {
                      let loader = document.getElementById(
                        "marker-loader" + id
                      );
                      loader.remove();
                      let infoBox = document.getElementById("marker-info" + id);
                      ReactDOM.render(
                        <h5>Not Found !</h5>,
                        document.getElementById("marker-info" + id)
                      );
                    }
                  } else {
                    let loader = document.getElementById("marker-loader" + id);
                    loader.remove();
                    let infoBox = document.getElementById("marker-info" + id);
                    ReactDOM.render(
                      <h5>Not Found !</h5>,
                      document.getElementById("marker-info" + id)
                    );
                  }
                  // infoBox.insertAdjacentHTML("beforeend", this.renderItem);
                })
                .catch((e) => {});
            }
          }
        }
      }
    });
    map.on("dragend", (event) => {
      var bounds = map.getBounds();
      // var coordinates = map.getCenter();
      // var sw = bounds.getSouthWest().wrap().toArray();
      // var ne = bounds.getNorthEast().wrap().toArray();
      let bndstr =
        "" +
        bounds.getNorthEast().wrap().toArray() +
        "###" +
        bounds.getSouthWest().wrap().toArray() +
        "";
      // let obj={ map, event,bndstr,bounds,sw,ne,coordinates}
      this.props.mapdragenCb({ bndstr });
    });

    this.setMarkers();
    this.setState({
      flag: !this.state.flag,
    });
    this.clusterMapping();
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
      isBoundSet = true;
      let sourceData = [];
      try {
        sourceData = JSON.parse(this.props.areaData.areasPolygons);
      } catch (error) {
        sourceData = this.props.areaData.areasPolygons;
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
            "line-color": "#2f7af6",
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
          sourceData = this.props.cityData.cityPolygons;
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
              "line-color": "#2f7af6",
            },
          });
        } catch (error) {}
        isBoundSet = true;
      }
    }
  }

  drawPolygon(points) {
    this.map.addLayer({
      id: "maine",
      type: "fill",
      source: {
        type: "geojson",
        data: {
          type: "Feature",
          geometry: {
            type: "Polygon",
            coordinates: points,
          },
        },
      },
      layout: {},
      paint: {
        "fill-color": "#2f7af6",
        "fill-opacity": 0.5,
        "fill-outline-color": "#2f7af6",
      },
    });
  }
  createArea(e) {
    let data = this.draw.getAll();
    const currentZoom = this.map.getZoom();
    let zoomLevel = currentZoom;
    if (!this.props.isDraged) {
      this.map.setZoom(zoomLevel);
    }
    const polygonData = data.features[0].geometry.coordinates;
    this.drawPolygon(polygonData);
    this.apiCall(polygonData, data.features[0].geometry.type);
  }

  apiCall(data, type) {
    this.props.handlePropertyCall(data[0], type);
  }
  deleteArea(e) {
    let data = this.draw.getAll();
    this.map.removeLayer("maine").removeSource("maine");
  }
  updateArea(e) {
    let data = this.draw.getAll();
    this.map.removeLayer("maine").removeSource("maine");
    const polygonData = data.features[0].geometry.coordinates;
    this.drawPolygon(polygonData);
    this.apiCall(data);
  }
  polygonClick() {
    var btn = document.getElementsByClassName("mapbox-gl-draw_polygon");
    btn[0].click();
    this.setState({ isDrawBtnEnabled: false });
  }

  updateTrash() {
    this.clearDrawArea();
    var btn = document.getElementsByClassName("mapbox-gl-draw_trash");
    btn[0].click();
    this.setState({ isDrawBtnEnabled: true });
    if(this.props.mobileView===false){
      this.props.changeDrawState();
    }
    
  }
  setMarkers() {
    let prevMarker = this.state.prevMarker;
    if (prevMarker) {
      prevMarker.remove();
    }
    if (this.props.cityMarker !== "") {
      let cityName = this.props.cityMarker + ",canada";
      geocodingClient
        .forwardGeocode({
          query: cityName,
          autocomplete: false,
          limit: 1,
        })
        .send()
        .then((response) => {
          if (
            !response ||
            !response.body ||
            !response.body.features ||
            !response.body.features.length
          ) {
            console.error("Invalid response:", response);
            return;
          }
          const feature = response.body.features[0];
          // Create a marker and add it to the map.

          let newMarker = new mapboxgl.Marker()
            .setLngLat(feature.center)
            .addTo(this.map);
          this.setState({
            prevMarker: newMarker,
          });
          if (!this.props.isDraged) {
            this.map.flyTo({
              center: feature.center,
              essential: true,
              zoom: 9.76,
            });
          }
        });
    }
    if (this.props.mapData && Array.isArray(this.props.mapData)) {
      let map = this.map;
      let tempArr = [];
      let markerGroup = [];
      this.props.mapData.map((res, key) => {
        // const markerNode = document.createElement('div');
        // ReactDOM.render(<MapCard
        //                     item={res}
        //                     showIsFav={true}
        //                     openUserPopup={res}
        //                     openLoginCb={this.props.togglePopUp}
        //                     gotoDetailPage={this.props.gotoDetailPage}
        //                     isLogin={this.props.isLogin}
        //                     />,markerNode);

        markerGroup.push({
          type: "Feature",
          id: res.ListingId,
          geometry: {
            type: "Point",
            coordinates: [res.Longitude, res.Latitude],
          },
          properties: {
            title: "tyu",
            id: res.ListingId,
            props: res,
            class: "",
            description: this.popUpHtml(res),
            price: "$" + change_price_formate(res.ListPrice),
            originalPrice: res.ListPrice,
            "marker-size": "small",
            "marker-color": "#ffa5ff",
            "marker-symbol": "suitcase",
          },
        });

        // const markerNode = document.createElement('div');

        // let timeMic = Math.ceil(Date.now());
        // tempArr.push(res.ListingId);
        // ReactDOM.render(<Marker id={res.ListingId} timeMicro={timeMic} highLight={this.props.highLightCb} price={'$'+change_price_formate(res.ListPrice)} />, markerNode);
        // // add marker to map
        // // Math.ceil(Date.now())
        // const popup = new mapboxgl.Popup({ offset: 25}).setHTML(this.popUpHtml(res));
        // new mapboxgl.Marker(markerNode)
        //     .setLngLat([res.Longitude, res.Latitude])
        //     .addTo(map)
        //     .setPopup(popup);
      });
      if (map.getLayer("clusters")) {
        map.removeLayer("clusters");
      }
      if (map.getLayer("cluster-count")) {
        map.removeLayer("cluster-count");
      }
      if (map.getLayer("unclustered-point")) {
        map.removeLayer("unclustered-point");
      }

      if (map.getSource("properties_data")) {
        map.removeSource("properties_data");
      }
      //
      try {
        map.addSource("properties_data", {
          type: "geojson",
          data: {
            type: "FeatureCollection",
            crs: {
              type: "name",
              properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84",
              },
            },
            features: markerGroup,
          },
          cluster: true,
          clusterMaxZoom: 14, // Max zoom to cluster points on
          clusterRadius: 50,
        });
        map.addLayer({
          id: "unclustered-point",
          type: "symbol",
          source: "properties_data",
          filter: ["!", ["has", "point_count"]],
          paint: {
            "text-color": "#fff",
          },
          layout: {
            "text-field": ["get", "price"],
            "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
            "text-size": 12.5,
            "icon-image": "marker",
            "icon-size": 0.9,
          },
        });
        map.addLayer({
          id: "clusters",
          type: "circle",
          source: "properties_data",
          filter: ["has", "point_count"],
          paint: {
            "circle-color": [
              "step",
              ["get", "point_count"],
              "#0081A7",
              100,
              "#0081A7",
              750,
              "#0081A7",
            ],

            "circle-radius": [
              "step",
              ["get", "point_count"],
              20,
              100,
              30,
              750,
              40,
            ],
          },
        });
        map.addLayer({
          id: "cluster-count",
          type: "symbol",
          source: "properties_data",
          filter: ["has", "point_count"],
          layout: {
            "text-field": "{point_count_abbreviated}",
            "text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
            "text-size": 12,
          },
          paint: {
            "text-color": "white",
          },
        });
      } catch (error) {
        map.on("load", () => {
          if (map.getLayer("clusters")) {
            map.removeLayer("clusters");
          }
          if (map.getLayer("cluster-count")) {
            map.removeLayer("cluster-count");
          }
          if (map.getLayer("unclustered-point")) {
            map.removeLayer("unclustered-point");
          }

          if (map.getSource("properties_data")) {
            map.removeSource("properties_data");
          }
          map.addSource("properties_data", {
            type: "geojson",
            data: {
              type: "FeatureCollection",
              crs: {
                type: "name",
                properties: {
                  name: "urn:ogc:def:crs:OGC:1.3:CRS84",
                },
              },
              features: markerGroup,
            },
            cluster: true,
            clusterMaxZoom: 14, // Max zoom to cluster points on
            clusterRadius: 50,
          });
          map.addLayer({
            id: "unclustered-point",
            type: "symbol",
            source: "properties_data",
            filter: ["!", ["has", "point_count"]],
            paint: {
              "text-color": "#fff",
            },
            layout: {
              "text-field": ["get", "price"],
              "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
              "text-size": 12.5,
              "icon-image": "marker",
              "icon-size": 0.9,
            },
          });
          map.addLayer({
            id: "clusters",
            type: "circle",
            source: "properties_data",
            filter: ["has", "point_count"],
            paint: {
              "circle-color": [
                "step",
                ["get", "point_count"],
                "#0081A7",
                100,
                "#0081A7",
                750,
                "#0081A7",
              ],

              "circle-radius": [
                "step",
                ["get", "point_count"],
                20,
                100,
                30,
                750,
                40,
              ],
            },
          });
          map.addLayer({
            id: "cluster-count",
            type: "symbol",
            source: "properties_data",
            filter: ["has", "point_count"],
            layout: {
              "text-field": "{point_count_abbreviated}",
              "text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
              "text-size": 12,
            },
            paint: {
              "text-color": "white",
            },
          });
        });
      }
      // show popup on click cluster
      //  const popup = new mapboxgl.Popup({
      //     closeButton: false,
      //     closeOnClick: false
      //     });
      // const clusterPopup=new mapboxgl.Popup;
      // map.on("click", "clusters", function(t) {

      // });
      map.on("click", "unclustered-point", (t) => {
        let latlang = t.features[0].geometry.coordinates.slice();
        this.setState({
          onMarkerClicked: true,
        });
        // return;

        let r = t.features[0].properties.description;
        // for (var e = t.features[0].geometry.coordinates.slice(), r = t.features[0].properties.description; Math.abs(t.lngLat.lng - e[0]) > 180;) e[0] += t.lngLat.lng > e[0] ? 360 : -360;
        let listingId = t.features[0].properties.id;
        let property = JSON.parse(t.features[0].properties.props);
        const { soldStatus, status } = this.props.propertySearchFilter;
        let mlsId = t.features[0].properties.id;
        console.log("MapLoadv2", property);
        if (mlsId === localStorage.getItem("mlsId")) {
          // localStorage.getItem("mlsId")
          setTimeout(() => {
            localStorage.removeItem("mlsId")
          }, 450);
          return;
        }
        new mapboxgl.Popup({ offset: 0 })
          .setLngLat(latlang)
          .setHTML(r)
          .addTo(map);
        this.map.flyTo({
          center: latlang,
          essential: true,
        });
        let propData= JSON.parse(t.features[0].properties.props);
        let data = {
          id: t.features[0].properties.id,
          status: propData.Status,
        };
        let urls = markerApi;
        localStorage.setItem("mlsId", mlsId);
        API.jsonApiCall(urls, data, "POST", null, {
          "Content-Type": "application/json",
        })
          .then((res) => {
            setTimeout(() => {
              localStorage.removeItem("mlsId");
            }, 20000);
            let html = "";
            let item = res.data;
            let data = item;
            if (res.status == 200) {
              if (data) {
                let loader = document.getElementById(
                  "marker-loader" + data.ListingId
                );
                loader.remove();
                let infoBox = document.getElementById(
                  "marker-info" + data.ListingId
                );
                this.setState({
                  onMarkerClicked: false,
                });
                ReactDOM.render(
                  <MapCard
                    showIsFav={true}
                    openUserPopup={true}
                    item={data}
                    isMarker={true}
                    isInfo={true}
                    isMarkerClass={"markerClass"}
                    openLoginCb={this.props.togglePopUp}
                    gotoDetailPage={this.props.gotoDetailPage}
                    isLogin={this.props.isLogin}
                  />,
                  infoBox
                );
                let a = document.getElementsByClassName(
                  "mapboxgl-popup-content"
                );
                if (a.length > 1) {
                  a[1].remove();
                }
              } else {
                let loader = document.getElementById("marker-loader" + id);
                loader.remove();
                let infoBox = document.getElementById("marker-info" + id);
                ReactDOM.render(
                  <h5>Not Found !</h5>,
                  document.getElementById("marker-info" + id)
                );
              }
            } else {
              let loader = document.getElementById("marker-loader" + id);
              loader.remove();
              let infoBox = document.getElementById("marker-info" + id);
              ReactDOM.render(
                <h5>Not Found !</h5>,
                document.getElementById("marker-info" + id)
              );
            }
            // infoBox.insertAdjacentHTML("beforeend", this.renderItem);
          })
          .catch((e) => {});
      });
      map.on("touchstart", "unclustered-point", (t) => {
        let mobileBox = document.getElementById("markerInMobile");
        mobileBox.style.display = "block";

        let infoWindo = document.getElementById("markerMobileInfo");

        ReactDOM.render(
          <div className="loading_data">Loading...</div>,
          infoWindo
        );
        let latlang = t.features[0].geometry.coordinates.slice();
        // return;

        let r = t.features[0].properties.description;
        // for (var e = t.features[0].geometry.coordinates.slice(), r = t.features[0].properties.description; Math.abs(t.lngLat.lng - e[0]) > 180;) e[0] += t.lngLat.lng > e[0] ? 360 : -360;
        let listingId = t.features[0].properties.id;

        // (new mapboxgl.Popup({ offset: 0 })).setLngLat(latlang).setHTML(r).addTo(map)
        // this.map.flyTo({
        //     center: latlang,
        //     essential: true,
        // });

        const { soldStatus, status } = this.props.propertySearchFilter;
        let data = {
          id: t.features[0].properties.id,
          soldStatus: soldStatus,
          status: status,
        };

        let urls = markerApi;
        API.jsonApiCall(urls, data, "POST", null, {
          "Content-Type": "application/json",
        })
          .then((res) => {
            let html = "";
            let item = res.data;
            let data = item;
            if (res.status == 200) {
              if (data) {
                // let loader = document.getElementById('marker-loader' + data.ListingId);
                // loader.remove();
                // let infoBox = document.getElementById('marker-info' + data.ListingId);
                ReactDOM.render(
                  <MapCard
                    showIsFav={true}
                    openUserPopup={true}
                    item={data}
                    isMarker={true}
                    isInfo={true}
                    isMarkerClass={"markerClass"}
                    openLoginCb={this.props.togglePopUp}
                    gotoDetailPage={this.props.gotoDetailPage}
                    isLogin={this.props.isLogin}
                  />,
                  infoWindo
                );
              } else {
              }
            } else {
              let loader = document.getElementById("marker-loader" + id);
              loader.remove();
              let infoBox = document.getElementById("marker-info" + id);
              ReactDOM.render(
                <h5>Not Found !</h5>,
                document.getElementById("marker-info" + id)
              );
            }
            // infoBox.insertAdjacentHTML("beforeend", this.renderItem);
          })
          .catch((e) => {});
      });
      this.setState({
        markerArr: tempArr,
      });
      if (!this.props.isDraged) {
        this.map.flyTo({
          center: this.props.center,
          essential: true,
          zoom: this.props.mapData.length<150?12:9,
        });
      }
    } else {
    }
  }

  closeInfoWindow() {
    document.getElementById("markerInMobile").style.display = "none";
  }

  render() {
    const { isDrawBtnEnabled} = this.state;
    
    return (
      <>
        {/*this.props.mapLoading && <div className="position-relative">
          <MapLoader />
        </div>*/}
        <div id="mapDiv" ref={(e) => (this.mapDiv = e)} style={{'pointerEvents':`${this.props.mapLoading?'none':''}`}}>
          {isDrawBtnEnabled ? (
            <button
              className="vUlx btn "
              onClick={this.polygonClick}
              id="btn_draw"
              title="Draw On Map"
            >
              Draw
            </button>
          ) : (
            <button
              name=""
              className="styles___AppButton-sc-5pk18n-0 byjvbA styles___ClearMapBoundBtn-ebjn88-1 bfsJGf btn"
              onClick={this.updateTrash}
              id="clear_draw"
            >
              Clear Map Bounds
            </button>
          )}
        </div>
        <div id="markerInMobile">
          <span className="InfoWindowClose" onClick={this.closeInfoWindow}>
            <i className="fa fa-times"></i>
          </span>
          <div id="markerMobileInfo"></div>
        </div>
      </>
    );
  }
}

export default MapLoadv2;
