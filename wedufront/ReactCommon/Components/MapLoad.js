import React, {Component} from "react";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import {accessToken} from "../../constants/GlobalConstants";
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";

mapboxgl.accessToken = accessToken;

class MapLoad extends Component {
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

    createCluster() {
        const map = this.map;
        map.on("load", () => {
            map.loadImage("../markerBlack.png", function (error, image) {
                if (error) throw error;
                map.addImage("pointer", image);
            });
            map.addSource("properties", {
                type: "geojson",
                data: this.clusterMapping(),
                cluster: true,
                clusterMaxZoom: 14, // Max zoom to cluster points on
                clusterRadius: 50, // Radius of each cluster when clustering points (defaults to 50)
            });

            map.addLayer({
                id: "clusters",
                type: "circle",
                source: "properties",
                filter: ["has", "point_count"],
                paint: {
                    "circle-color": [
                        "step",
                        ["get", "point_count"],
                        "#51bbd6",
                        100,
                        "#f1f075",
                        750,
                        "#f28cb1",
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
                source: "properties",
                filter: ["has", "point_count"],
                layout: {
                    "text-field": "{point_count_abbreviated}",
                    "text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
                    "text-size": 14,
                },
            });

            map.addLayer({
                id: "unclustered-point",
                type: "symbol",
                source: "properties",
                filter: ["!", ["has", "point_count"]],
                paint: {
                    "text-color": "#fff",
                },
                layout: {
                    "text-field": ["get", "price"],
                    "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                    "text-size": 14.5,
                    "icon-image": "pointer",
                    "icon-size": 1,
                },
            });

            // inspect a cluster on click
            map.on("click", "clusters", (e) => {
                const features = map.queryRenderedFeatures(e.point, {
                    layers: ["clusters"],
                });
                const clusterId = features[0].properties.cluster_id;
                map
                    .getSource("properties")
                    .getClusterExpansionZoom(clusterId, (err, zoom) => {
                        if (err) return;

                        map.easeTo({
                            center: features[0].geometry.coordinates,
                            zoom: zoom,
                        });
                    });
            });
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

            map.on("mouseenter", "clusters", () => {
                map.getCanvas().style.cursor = "pointer";
            });
            map.on("mouseleave", "clusters", () => {
                map.getCanvas().style.cursor = "";
            });
        });
        this.setState({
            flag: !this.state.flag,
        });
    }

    clusterMapping() {
        const clusterData = {
            type: "FeatureCollection",
            crs: {
                type: "name",
                properties: {name: "urn:ogc:def:crs:OGC:1.3:CRS84"},
            },
            features: !this.props.mapData || !Array.isArray(this.props.mapData) ? [] : this.props.mapData.map((res) => {
                return {
                    type: "Feature",
                    id: res.id,
                    geometry: {
                        type: "Point",
                        coordinates: [+res.Longitude, +res.Latitude],
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
        this.setState({isDrawBtnEnabled: false});
    }

    updateTrash() {
        var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
        btn[0].click();
        this.setState({isDrawBtnEnabled: true});
        this.props.changeDrawState();
    }

    componentDidUpdate(prevProps) {
        if (JSON.stringify(this.props.mapData) !== JSON.stringify(prevProps.mapData)) {
            if (this.map.getSource('properties')) {
                const clusterData = this.clusterMapping();
                this.map.getSource("properties").setData(clusterData);
                this.map.flyTo({
                    center: this.props.center,
                    essential: true,
                });
            }

        }
    }

    render() {
        const {isDrawBtnEnabled} = this.state;
        return (
            <>
                <div
                    id="mapDiv"
                    ref={(e) => (this.mapDiv = e)}
                    style={{height: "100vh"}}
                >
                    {isDrawBtnEnabled ? (
                        <button
                            className="vUlx btn "
                            onClick={this.polygonClick}
                            id="btn_draw"
                            title="Draw On Map"
                            hidden
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

export default MapLoad;
