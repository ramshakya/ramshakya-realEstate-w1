import React, { Component } from "react";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import { accessToken } from "../../constants/GlobalConstants";
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
import Marker from "../../components/Marker/Marker";
import ReactDOM from 'react-dom';
mapboxgl.accessToken = accessToken;

class MapLoadv2 extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isDrawBtnEnabled: true,
            flag: true,
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
        this.polygonClick = this.polygonClick.bind(this)
        this.clearPrevMarkers = this.clearPrevMarkers.bind(this)

    }

    componentDidMount() {
        this.map = new mapboxgl.Map({
            container: this.mapDiv,
            style: "mapbox://styles/mapbox/streets-v11",
            center: this.props.center,
            zoom: this.props.zoom,
        });
        this.draw = new MapboxDraw({
            displayControlsDefault: false,
            controls: {
                polygon: true,
                trash: true,
            },
        });
        this.map.addControl(this.draw);
        this.map.addControl(new mapboxgl.NavigationControl(), "top-left");
        this.map.on("draw.create", this.createArea);
        this.map.on("draw.delete", this.deleteArea);
        this.map.on("draw.update", this.updateArea);
        this.createCluster();
        // if (this.props.mapData && this.props.mapData.length > 0) {
        //   this.createCluster();
        // }
    }
    clearPrevMarkers() {
        console.log("map==>>clearing");
        let map =this.map;
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
          if (map.getSource('properties_data')) {
            map.removeSource('properties_data');
          }
          if (map.getLayer('properties_data')) {
            map.removeLayer('properties_data');
          }
          
    }

    createCluster() {
        const map = this.map;
        console.log("map==>>2");
        map.on("load", () => {
            
            this.clearPrevMarkers();
            map.loadImage("../marker2.png", function (error, image) {
                if (error) throw error;
                map.addImage("pointer", image);
            });
            if (map.getSource('properties_data')) {
                map.removeSource('properties_data');
              }
              if (map.getLayer('properties_data')) {
                map.removeLayer('properties_data');
              }
            map.addSource("properties_data", {
                type: "geojson",
                data: this.clusterMapping(),
                cluster: false,
                //clusterMaxZoom: 14, // Max zoom to cluster points on
                //clusterRadius: 50, // Radius of each cluster when clustering points (defaults to 50)
            });




            // map.addLayer({
            //     id: "unclustered-point",
            //     type: "symbol",
            //     source: "properties",
            //     //filter: ["!", ["has", "point_count"]],
            //     paint: {
            //         "text-color": "#fff",
            //     },
            //     layout: {
            //         //"text-field": ["get", "price"],
            //         //"text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
            //         //"text-size": 14.5,
            //         "icon-image": "pointer",
            //         "icon-size": 1,
            //     },
            // });

            map.on("click", "unclustered-point", (e) => {
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

            map.on('moveend', async () => {
                // get center coordinates
                const { lng, lat } = map.getCenter();
                if (!this.props.mapData || !Array.isArray(this.props.mapData)) {
                    return [];
                } else {
                    console.log("In markers");
                    this.props.mapData.map((res) => {
                        const markerNode = document.createElement('div');
                        ReactDOM.render(<Marker id={res.id} />, markerNode);
                        // add marker to map
                        new mapboxgl.Marker(markerNode)
                            .setLngLat([res.Longitude, res.Latitude])
                            .addTo(map);
                    });

                }
            })

            this.props.mapData.map((res) => {
                const markerNode = document.createElement('div');
                ReactDOM.render(<Marker id={res.id} />, markerNode);
                // add marker to map
                new mapboxgl.Marker(markerNode)
                    .setLngLat([res.Longitude, res.Latitude])
                    .addTo(map);
            });

        });
        this.setState({
            flag: !this.state.flag,
        });
    }

    clusterMapping() {
        
        console.log("this.props.cityData===>>>", this.props.areaData);
        if (this.map.getLayer('area-new_adv')) {
            this.map.removeLayer('area-new_adv');
        }
        if (this.map.getSource('area_adv')) {
            this.map.removeSource('area_adv');
        }
        if (this.map.getLayer('city-new_adv')) {
            this.map.removeLayer('city-new_adv');
        }
        if (this.map.getSource('city_adv')) {
            this.map.removeSource('city_adv');
        }
        if (this.props.cityData) {
            let sourceData = [];
            try {
                console.log("try ===");
                sourceData = JSON.parse(this.props.cityData.cityPolygons)
            } catch (error) {
                console.log("catch ===");
                sourceData = this.props.cityData.cityPolygons;
            }
            this.map.addSource('city_adv', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'Polygon',
                        'coordinates': [sourceData]
                    }
                }
            });
            this.map.addLayer({
                'id': 'city-new_adv',
                'type': 'line',
                'source': 'city_adv',
                'layout': {},
                'paint': {
                    "line-width": 1.5,
                    //"line-color": "#d2d0d0"
                    "line-color": "#f20e15"
                }
            });
        } else {
            if (this.props.areaData) {
                let sourceData = [];
                let citysourceData = [];
                try {
                    console.log("try ===");
                let sourceData = [];
                    sourceData = JSON.parse(this.props.areaData.areasPolygons)
                    citysourceData = JSON.parse(this.props.areaData.cityPolygons)
                } catch (error) {
                    console.log("catch ===");
                    sourceData = this.props.areaData.areasPolygons;
                    citysourceData = this.props.areaData.cityPolygons;
                }
                this.map.addSource('city_adv', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'geometry': {
                            'type': 'Polygon',
                            'coordinates': [citysourceData]
                        }
                    }
                });
                this.map.addLayer({
                    'id': 'city-new_adv',
                    'type': 'line',
                    'source': 'city_adv',
                    'layout': {},
                    'paint': {
                        "line-width": 1.5,
                        "line-color": "#f20e15"
                    }
                });
                this.map.addSource('area_adv', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'geometry': {
                            'type': 'Polygon',
                            'coordinates': [sourceData]
                        }
                    }
                });
                this.map.addLayer({
                    'id': 'area-new_adv',
                    'type': 'line',
                    'source': 'area_adv',
                    'layout': {},
                    'paint': {
                        "line-width": 1.5,
                        "line-color": "#f20e15"
                    }
                });
            }

        }
        const clusterData = {
            type: "FeatureCollection",
            // crs: {
            //     type: "name",
            //     properties: {name: "urn:ogc:def:crs:OGC:1.3:CRS84"},
            // },
            features: !this.props.mapData || !Array.isArray(this.props.mapData) ? [] : this.props.mapData.map((res) => {
                return {
                    type: "Feature",
                    id: res.id,
                    geometry: {
                        type: "Point",
                        coordinates: [res.Longitude, res.Latitude],
                    },
                    properties: {
                        title: "tyu",
                        id: res.ListingId,
                        description: "fetching...",
                        price: res.ShortPrice,
                        "marker-size": "small",
                        "marker-color": "#ff5a5f",
                        "marker-symbol": "suitcase",
                    },
                };
            }),
        };
        return clusterData;
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
                'fill-color': '#ff5a5f',
                'fill-opacity': 0.5,
                'fill-outline-color': '#ff5a5f'
            },
        });
    }

    createArea(e) {
        let data = this.draw.getAll();
        const currentZoom = this.map.getZoom();
        let zoomLevel = currentZoom + 1;
        this.map.setZoom(zoomLevel);
        const polygonData = data.features[0].geometry.coordinates;
        this.drawPolygon(polygonData);
        this.apiCall(polygonData, data.features[0].geometry.type);
    }

    apiCall(data, type) {
        this.props.handlePropertyCall(data[0], type);
    }

    deleteArea(e) {
        let data = this.draw.getAll();
        //console.log("Deleting layer", data);
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
        var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
        btn[0].click();
        this.setState({ isDrawBtnEnabled: true });
        this.props.changeDrawState();
    }

    componentDidUpdate(prevProps) {
        console.log("map==>>1");
        this.clearPrevMarkers();
            if (JSON.stringify(this.props.mapData) !== JSON.stringify(prevProps.mapData)) {
                const clusterData = this.clusterMapping();
                if (this.map.getSource('properties_data')) {
                    this.map.removeSource('properties_data');
                  }
                  if (this.map.getLayer('properties_data')) {
                    this.map.removeLayer('properties_data');
                  }
                this.map.addSource("properties_data", {
                    type: "geojson",
                    data: clusterData,
                    cluster: false,
                    //clusterMaxZoom: 14, // Max zoom to cluster points on
                    //clusterRadius: 50, // Radius of each cluster when clustering points (defaults to 50)
                });
                if (this.map.getSource('properties_data')) {
                    // this.map.getSource("properties").setData(clusterData);  ////Commented By Ram :::::REASON Marker not clearing
                    this.map.flyTo({
                        center: this.props.center,
                        essential: true,
                    });
                }
            }
    }

    render() {
        const { isDrawBtnEnabled } = this.state;
        return (
            <>
                <div
                    id="mapDiv"
                    ref={(e) => (this.mapDiv = e)}
                    style={{ height: "100vh" }}
                >
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
            </>
        );
    }
}

export default MapLoadv2;
