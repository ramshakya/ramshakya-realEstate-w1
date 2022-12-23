import React from 'react'
import Constants from '../../constants/GlobalConstants'
const mapDefaultImage = Constants.mapDefaultImage;
var map;
var draw;
var markerGroup = [];
let mapdata = [];
var eventLngLat = [];
var selectedShape;
var zoomLevel = 6;
var mapdrawCb;
var mapdragenCb;
var mapdragen = false;
var mapdrawCb;
var mapdraw = false;
var formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
});
class Mapload extends React.Component {
  constructor(props) {
    super(props)
    mapdata = props.mapData;

    if (props.mapDragendCallBack) {
      mapdragenCb = props.mapDragendCallBack;
      mapdragen = true
    }
    if (props.mapDrawCallBack) {
      mapdrawCb = props.mapDrawCallBack;
      mapdraw = true
    }

    this.state = {
      lat: 43.617343105067334,
      lng: -79.39493841352393,
      zoom: 12,
    }
    this.drawPolygon = this.drawPolygon.bind(this);
    this.createArea = this.createArea.bind(this);
    this.updateArea = this.updateArea.bind(this);
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
  }
  componentDidMount() {
    mapboxgl.accessToken = Constants.accessToken;
    const { lat, lng, zoom } = this.state;
    map = new mapboxgl.Map({
      container: this.mapDiv,
      style: Constants.mapStyle,
      center: [lng, lat],
      zoom: zoom
    });
    
    
    draw = new MapboxDraw({
      displayControlsDefault: false,
      controls: {
        polygon: true,
        trash: true
        
      }
      
    });
    var geolocate = new mapboxgl.GeolocateControl({
      positionOptions: {
        enableHighAccuracy: true
      },
      trackUserLocation: true
    });
    map.addControl(geolocate, 'top-left');
    map.addControl(new mapboxgl.NavigationControl(), 'top-left');
    // Set an event listener that fires
    // when a geolocate event occurs.
    geolocate.on('geolocate', function (e) {
      eventLngLat = [e.coords.longitude, e.coords.latitude];
      
      var point = turf.point(eventLngLat);
      var searchRadius = turf.buffer(point, 1500, {
        units: 'kilometers'
      });
      //console.log("searchRadius", searchRadius);
      
    });
    
    map.addControl(draw);
    map.on('draw.create', this.createArea);
    map.on('draw.delete', this.deleteArea);
    map.on('draw.update', this.createArea);
    if (mapdata) {
      this.showMarkers();
    } else {

    }
    
    
  }
  
  showMarkers() {
    this.clearSelection();
    // for map data
    var i;
    var uri = window.location.origin;
    var mapImageurl;
    var LASTLONGITUDE = 0;
    var LASTLATITUDE = 0;
    var lnth = mapdata.length;
    var cntr = 0;
    if (lnth) {
      cntr = Math.round(Math.random() * lnth)
    }
    //console.log("mapdatamapdata", mapdata);
    for (i = 0; i < mapdata.length; i++) {
      if (mapdata[i]) {
        var propertyId = mapdata[i].id;
        var shortPrice = mapdata[i].ShortPrice;
        var show_up_dwn = "";
        var Lp_dol = "";
        var Orig_dol = "";
        // Lp_dol = Math.round(mapdata[i].ListPrice.replace(",", ""));
        // Orig_dol = Math.round(mapdata[i].Orig_dol);
        var today = new Date();
        var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
        var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        // var dateTime = date + ' ' + time;
        var latitude = mapdata[i].Latitude;
        var longitude = mapdata[i].Longitude;
        // console.log("=====mapdata[i]>>>>", mapdata[i]);

        // var curr_marker_class = "";
        // var propertyAddr = '<a href="#"><p>' + mapdata[i].StandardAddress + " " + " " + mapdata[i].PostalCode + '</p></a>'
        // if (!mapdata[i].s3_image_url) {
        // } else {
        //   mapImageurl = mapdata[i].srcImg.s3_image_url
        // }
        // mapImageurl = mapDefaultImage;
        // var imageUrl = mapImageurl;
        // var updated_time = mapdata[i].updated_time;
        // var cal_hours = this.diff_hours(new Date(updated_time), new Date(dateTime));
        // cal_hours = this.timeSince(new Date(updated_time));
        //  let content = `<div className="row" key={${i}}>` +
        //     `<div className="col-lg-12 col-xs-6 col-sm-6 mynewclass mt-1" rel="${mapdata[i].id}" tabindex="${mapdata[i].id}" id="list_${mapdata[i].id}" data-id="${mapdata[i].Latitude},${mapdata[i].Longitude}">` +
        //     `<div className="hill-one">` +
        //     `<div className="hill-brand position-relative os">` +
        //     `<a href="#">` +
        //     `<img src="${imageUrl}" alt=""  style={"height":"150px !important" }  className="map-img"></a>` +
        //     `<ul className="rating justify-content-end">` +
        //     `</div>` +
        //     `<div className="hill-body">` +
        //     `<ul className="body-head">` +
        //     `<li>` +
        //     `<h4>$${mapdata[i].S_r == 'lease' || mapdata[i].S_r == "Lease" ? mapdata[i].ListPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "/m" : mapdata[i].ListPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")} ${show_up_dwn}</h4>` +
        //     `</li>` +
        //     `<li>${cal_hours} ago</li>` +
        //     `</ul>` +
        //     ` <ul className="hill-inner">` +
        //     `<li>${mapdata[i].BathroomsFull} BD</li>` +
        //     `<li>${mapdata[i].BedroomsTotal} BA</li>` +
        //     `<li>${mapdata[i].Gar} Garage</li>` +
        //     ` <li>Sq. Ft. ${mapdata[i].Sqft}</li>` +
        //     `</ul>` +
        //     `${propertyAddr}` +
        //     `</div>` +
        //     `</div>` +
        //     `</div>` +
        //     `</div>`;

        markerGroup.push({

          type: 'Feature',
          id: mapdata[i].id,
          geometry: {
            type: 'Point',
            coordinates: [
              longitude,
              latitude
            ]
          },
          properties: {
            title: 'tyu',
            id: propertyId,
            description: "fetching...",
            price: shortPrice,
            'marker-size': 'small',
            'marker-color': '#ff5a5f',
            'marker-symbol': 'suitcase'
          }
        });
        // varcord.push([parseFloat(latitude),parseFloat(longitude)]);
      }
    }
    this.clearLayers();
    map.on('load', function () {
      map.loadImage(
        '../markerBlack.png',
        function (error, image) {
          if (error) throw error;
          map.addImage('cat', image);
        });
      if (map.getLayer('clusters')) {
        map.removeLayer('clusters');
      }
      if (map.getLayer('cluster-count')) {
        map.removeLayer('cluster-count');
      }
      if (map.getLayer('unclustered-point')) {
        map.removeLayer('unclustered-point');
      }
      if (map.getLayer('draw.create')) {
        map.removeLayer('draw.create');
      }
      if (map.getLayer('draw.update')) {
        map.removeLayer('draw.update');
      }

      if (map.getSource('listings')) {
        map.removeSource('listings');
      }
      map.addSource("listings", {
        type: "geojson",
        data: {
          "type": "FeatureCollection",
          "crs": {
            "type": "name",
            "properties": {
              "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
            }
          },
          "features": markerGroup
        },
        cluster: true,
        clusterMaxZoom: 18, // Max zoom to cluster points on
        clusterRadius: 50
      });

      map.addLayer({
        id: "clusters",
        type: "circle",
        source: "listings",
        filter: ["has", "point_count"],
        paint: {
          "circle-color": ["step", ["get", "point_count"], "#ff5a5f", 100, "#ff5a5f", 750, "#ff5a5f"],
          //"circle-color": [ "step", ["get", "point_count"], "#787d79", 100, "#787d79", 750, "#787d79"],
          "circle-radius": ["step", ["get", "point_count"], 20, 100, 30, 750, 40]
        }
      });

      map.addLayer({
        id: "cluster-count",
        type: "symbol",
        source: "listings",
        filter: ["has", "point_count"],
        layout: {
          "text-field": "{point_count_abbreviated}",
          "text-font": ['Open Sans Semibold', 'Arial Unicode MS Bold'],
          "text-size": 16
        },
        paint: {
          "text-color": "white"
        }
      });

      map.addLayer({
        id: "unclustered-point",
        type: "symbol",
        source: "listings",
        filter: ["!", ["has", "point_count"]],
        paint: {
          /*"circle-color": "#000000",
          "circle-radius": 10,
          "circle-stroke-width": 2,
          "circle-stroke-color": "#fff"*/
          'text-color': "#fff"
        },
        layout: {
          'text-field': ['get', 'price'],
          'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
          'text-size': 12.5,
          'icon-image': 'cat',
          'icon-size': 0.90
        }

      });
      if (mapdata[cntr]) {
        if (mapdata[cntr].Latitude && mapdata[cntr].Longitude) {
          map.setCenter({
            lat: mapdata[cntr].Latitude,
            lng: mapdata[cntr].Longitude
          });
        }
      }
      // 
      var mapPop;
      map.setZoom(zoomLevel);
      map.on('click', 'unclustered-point', function (e) {
        var coordinates = e.features[0].geometry.coordinates.slice();
        var description = e.features[0].properties.description;
        map.getCanvas().style.cursor = 'pointer';
        while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
          coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
        }
        mapPop = new mapboxgl.Popup()
          .setLngLat(coordinates)
          .setHTML(description)
          .addTo(map);
      });
      //Zoom cluster
      map.on('click', 'cluster-count', function (e) {
        const cluster = map.queryRenderedFeatures(e.point, { layers: ["cluster-count"] });
        const coordinates = cluster[0].geometry.coordinates;
        const currentZoom = map.getZoom();
        zoomLevel = currentZoom + 2;
        map.flyTo({
          center: coordinates,
          zoom: zoomLevel,
          bearing: 0,
          speed: 1, // make the flying slow
          easing: function (t) {
            return t;
          }
        });
      })
      map.on('mouseleave', 'unclustered-point', function () {
        // map.getCanvas().style.cursor = '';
        // mapPop.remove();
      });
      map.on('dragend', function (event) {
        var bounds = map.getBounds();
        if (mapdragen) {
          mapdragenCb({ map, event });
        }
        else {

        }
        // bndstr = "" + bounds.getNorthEast().wrap().toArray() + "###" + bounds.getSouthWest().wrap().toArray() + "";
      });

    })
  }

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
    diff /= (60 * 60);
    return Math.abs(Math.round(diff));
  }
  drawPolygon(points) {
    if (map.getLayer('wedu_ca')) {
      map.removeLayer('wedu_ca').removeSource('wedu_ca');
    }
    map.addLayer({
      'id': 'wedu_ca',
      'type': 'fill',
      'source': {
        'type': 'geojson',
        'data': {
          'type': 'Feature',
          'geometry': {
            'type': 'Polygon',
            'coordinates': points
          }
        }
      },
      'layout': {},
      'paint': {
        'fill-color': '#ff5a5f',
        'fill-opacity': 0.5,
        'fill-outline-color': '#ff5a5f'
      }
    });
  }

  clearLayers() {

  }
  clearMarkers() {
    markerGroup.forEach(function (marker) {
      marker = []
    });
    markerGroup = [];
  }
  clearSelection() {
    this.clearMarkers();
    // this.deleteSelectedShape();
  }

  deleteSelectedShape() {
    if (selectedShape) {
      selectedShape.setMap(null);
    }
  }

  createArea(e) {
    let data = draw.getAll();
    const currentZoom = map.getZoom();
    let zoomLevel = currentZoom + 1;
    // var center = map.getCenter().wrap();
    const polygonData = e.features[0].geometry.coordinates;
    this.drawPolygon(polygonData);
    map.setZoom(zoomLevel);
    if (mapdraw) {
      let event = e;
      mapdrawCb({ map, event, data })
    }

    return;
    var polygon = data.features[0].geometry.coordinates;
    var fit = new L.Polygon(polygon).getBounds();

    var southWest = new mapboxgl.LngLat(fit['_southWest']['lat'], fit['_southWest']['lng']);
    var northEast = new mapboxgl.LngLat(fit['_northEast']['lat'], fit['_northEast']['lng']);
    var center = new mapboxgl.LngLatBounds(southWest, northEast).getCenter();

    let cnt = [
      center.lng,
      center.lat,
    ]
    map.flyTo({
      center: center,
      zoom: zoomLevel,
      bearing: 0,
      speed: 1, // make the flying slow
      curve: 1, // change the speed at which it zooms out
      easing: (t) => t,
      essential: true
    });

    // map.fitBounds(new mapboxgl.LngLatBounds(southWest, northEast));

  }


  deleteArea(e) {
    if (map.getLayer('wedu_ca')) {
      map.removeLayer('wedu_ca').removeSource('wedu_ca');
    }
  }
  updateArea(e) {
    if (map.getLayer('wedu_ca')) {
      map.removeLayer('wedu_ca').removeSource('wedu_ca');
    }
    const polygonData = e.features[0].geometry.coordinates;
    this.drawPolygon(polygonData);
  }
  polygonClick() {
    var btn = document.getElementsByClassName("mapbox-gl-draw_polygon");
    btn[0].click();
    document.getElementById("clear_draw").style.display = 'block';
    document.getElementById("btn_draw").style.display = 'none';

  }
  clearDrawArea() {
    var btn = document.getElementsByClassName("mapbox-gl-draw_trash");
    btn[0].click();
    document.getElementById("btn_draw").style.display = 'block';
    document.getElementById("clear_draw").style.display = 'none';

  }

  render() {
    return (
      <div>
        <div ref={e => this.mapDiv = e} className="map">
          <button className="vUlx btn " onClick={this.polygonClick} id="btn_draw" title="Draw On Map">
            <div className="styles___Flex-sc-1lfxfux-0 cSWkKV styles___DrawBtn-e3z4ed-1 hXsNXu">
              <span>Draw</span></div>
          </button>
          <button name="" className="styles___AppButton-sc-5pk18n-0 byjvbA styles___ClearMapBoundBtn-ebjn88-1 bfsJGf btn" onClick={this.clearDrawArea} id="clear_draw" style={{ "display": "none" }}>Clear Map Bounds</button>
        </div>
      </div>
    )
  }
}
export default Mapload


