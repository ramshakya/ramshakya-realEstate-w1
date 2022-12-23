import React, { Component, useRef } from "react";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import mapboxgl from "!mapbox-gl"; // eslint-disable-line import/no-webpack-loader-syntax;
import "mapbox-gl/dist/mapbox-gl.css";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
import Marker from "../../components/Marker/Marker";
import emptyHeart from "./../../public/images/icons/empty_heart.svg";
import fillHeart from "./../../public/images/icons/heartFill.svg";
import { accessToken, defaultImage, markerApi } from "../../constants/GlobalConstants";
import API from "./../utility/api";
import MapCard from './../Components/MapCard';
import ReactDOM from 'react-dom';

mapboxgl.accessToken = accessToken;

var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
});
class MapLoadv2 extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isDrawBtnEnabled: true,
            isDraged: false,
            flag: true,
            markerArr: [],
            favIconImg: "/images/icons/empty_heart.svg",
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
    }

    componentDidMount() {
        this.map = new mapboxgl.Map({
            container: this.mapDiv,
            style: "mapbox://styles/mapbox/streets-v11",
            center: this.props.center,
            zoom: this.props.zoom,
            optimize: true
        });
        const modes = MapboxDraw.modes;
        this.draw = new MapboxDraw({
            displayControlsDefault: false,
            controls: {
                polygon: true,
                trash: true,
            },
            styles: [
                // ACTIVE (being drawn)
                // polygon fill
                {
                    "id": "gl-draw-polygon-fill",
                    "type": "fill",
                    "filter": ["all", ["==", "$type", "Polygon"], ["!=", "mode", "static"]],
                    "paint": {
                        "fill-color": "#ff5a5f",
                        "fill-outline-color": "#ff5a5f",
                        "fill-opacity": 0.5
                    }
                },
                // polygon outline stroke
                // This doesn't style the first edge of the polygon, which uses the line stroke styling instead
                //*** HERE YOU DEFINE POINT STYLE *** //
                {
                    "id": "gl-draw-point",
                    "type": "circle",
                    "paint": {
                        "circle-radius": 5,
                        "circle-color": "#ff5b60"
                    }
                },
                {
                    "id": "gl-draw-polygon-stroke-active",
                    "type": "line",
                    "filter": ["all", ["==", "$type", "Polygon"], ["!=", "mode", "static"]],
                    "layout": {
                        "line-cap": "round",
                        "line-join": "round"
                    },
                    "paint": {
                        "line-color": "#ff5b60",
                        "line-dasharray": [0.3, 2],
                        "line-width": 3
                    }
                },
                {
                    'id': 'gl-draw-polygon-stroke-inactive',
                    'type': 'line',
                    'filter': ['all', ['==', 'active', 'false'],
                        ['==', '$type', 'Polygon'],
                        ['!=', 'mode', 'static']
                    ],
                    'layout': {
                        'line-cap': 'round',
                        'line-join': 'round'
                    },
                    'paint': {
                        'line-color': '#ff5b60',
                        'line-width': 2
                    }
                },
                // vertex points
                {
                    "id": "gl-draw-polygon-and-line-vertex-active",
                    "type": "circle",
                    "filter": ["all", ["==", "meta", "vertex"], ["==", "$type", "Point"], ["!=", "mode", "static"]],
                    "paint": {
                        "circle-radius": 2,
                        "circle-color": "#ff5b60",
                    }
                }
            ],
            zooming: true
        });
        modes.simple_select.onDrag = (e) => {
            this.updateTrash()
        };

        this.map.addControl(this.draw);
        this.map.addControl(new mapboxgl.NavigationControl(), "bottom-left");
        this.map.on("draw.create", this.createArea);
        this.map.on("draw.delete", this.deleteArea);
        // this.map.on("draw.update", this.updateArea);
        this.createCluster();
    }
    clearPrevMarkers() {
        this.state.markerArr.map((item, index) => {
            const element = document.getElementById('markers-' + item);
            if (element) {
                if (index == 1)
                    console.log("markers", element);
                element.remove();
            }
        });
    }


    popUpHtml(item) {
        let id = item.id;
        let data = item;
        return ('<div className="marker-info" id="marker-info' + item.ListingId + '"><p id="marker-loader' + item.ListingId + '" >loading......</p></div>');
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
                if (e.originalEvent.target) {
                    if (e.originalEvent.target.attributes) {
                        if (e.originalEvent.target.attributes.dataset) {
                            if (e.originalEvent.target.attributes.dataset.value) {
                                let id = e.originalEvent.target.attributes.dataset.value;
                                try {
                                    id = JSON.parse(id);
                                    id = id.id;
                                } catch (error) {

                                }
                                let urls = markerApi;
                                let uri = window.location.origin;
                                let data = {
                                    id: id
                                }
                                API.jsonApiCall(urls, data, "POST", null, {
                                    "Content-Type": "application/json",
                                }).then((res) => {
                                    let html = "";
                                    let item = res.data;
                                    let data = item;
                                    if (res.status == 200) {
                                        if (data) {
                                            let loader = document.getElementById('marker-loader' + data.ListingId);
                                            loader.remove();
                                            let infoBox = document.getElementById('marker-info' + data.ListingId);
                                            ReactDOM.render(<MapCard showIsFav={true}
                                                openUserPopup={true}
                                                item={data}
                                                isMarker={true}
                                                isMarkerClass={"markerClass"}
                                                openLoginCb={this.props.togglePopUp}
                                                isLogin={this.props.isLogin} />, document.getElementById('marker-info' + data.ListingId));
                                        } else {
                                            let loader = document.getElementById('marker-loader' + id);
                                            loader.remove();
                                            let infoBox = document.getElementById('marker-info' + id);
                                            ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
                                        }
                                    }
                                    else {
                                        let loader = document.getElementById('marker-loader' + id);
                                        loader.remove();
                                        let infoBox = document.getElementById('marker-info' + id);
                                        ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
                                    }
                                }).catch((e) => {
                                });
                            }
                        }
                    }
                }
            });
            map.on('dragend', (event) => {
                this.setState({
                    isDraged: true
                })
                var bounds = map.getBounds();
                let bndstr = "" + bounds.getNorthEast().wrap().toArray() + "###" + bounds.getSouthWest().wrap().toArray() + "";
                this.props.mapdragenCb({ bndstr });
            });
            this.setMarkers();
        });
        this.setState({
            flag: !this.state.flag,
        });
    }
    clusterMapping() {
        let isBoundSet = false;
        if (this.map.getLayer('city-new_adv')) {
            this.map.removeLayer('city-new_adv');
        }
        if (this.map.getSource('city_adv')) {
            this.map.removeSource('city_adv');
        }
        if (this.map.getLayer('areas-new_adv')) {
            this.map.removeLayer('areas-new_adv');
        }
        if (this.map.getSource('areas_adv')) {
            this.map.removeSource('areas_adv');
        }
        if (this.props.cityData) {
            let sourceData = [];
            try {
                sourceData = JSON.parse(this.props.cityData.cityPolygons)
            } catch (error) {
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
                    "line-width": 2,
                    "line-color": "#ff5b60"
                }
            });
            isBoundSet = true;
        }
        else {
            if (isBoundSet) {
                return;
            }
            let sourceData = [];
            try {
                sourceData = JSON.parse(this.props.areaData.areasPolygons)
            } catch (error) {
                sourceData = this.props.areaData.areasPolygons;
            }
            this.map.addSource('areas_adv', {
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
                'id': 'areas-new_adv',
                'type': 'line',
                'source': 'areas_adv',
                'layout': {},
                'paint': {
                    "line-width": 2,
                    "line-color": "#ff5b60"
                }
            });
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
                'fill-color': '#ff5a5f',
                'fill-opacity': 0.5,
                "line-width": 2,
                "line-color": "#ff5b60"
            },
        });
    }
    createArea(e) {
        let data = this.draw.getAll();
        const currentZoom = this.map.getZoom();
        let zoomLevel = currentZoom;
        this.map.setZoom(9);

        const polygonData = data.features[0].geometry.coordinates;
        let center = [];
        if (polygonData[0].length % 2) {
            let c = Math.ceil(polygonData[0].length / 2);
            center = polygonData[0][c];

        } else {
            let c = polygonData[0].length / 2;
            center = polygonData[0][c];
        }
        this.drawPolygon(polygonData);
        this.map.flyTo({
            center: center,
            zoom: 12,
            bearing: -1
        });
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
        this.clearPrevMarkers();
        let data = this.draw.getAll();
        this.apiCall(data);
        this.draw.deleteAll();
        this.map.removeLayer("maine").removeSource("maine");
        const polygonData = data.features[0].geometry.coordinates;
        this.drawPolygon(polygonData);
    }
    polygonClick() {
        var btn = document.getElementsByClassName("mapbox-gl-draw_polygon");
        btn[0].click();
        this.setState({ isDrawBtnEnabled: false });
    }

    updateTrash() {
        this.draw.deleteAll();
        let data = this.draw.delete();
        if (this.map.getLayer('maine')) {
            this.map.removeLayer('maine');
        }
        if (this.map.getSource('maine')) {
            this.map.removeSource('maine');
        }
        if (this.map.getSource('maine')) {
            this.map.removeSource('maine');
        }
        this.map.on('draw.delete', () => {
            setTimeout(() => {
                this.draw.deleteAll();
            }, 0)
        })
        var btn = document.getElementsByClassName('mapbox-gl-draw_trash');
        btn[0].click();
        this.setState({ isDrawBtnEnabled: true });
        this.props.changeDrawState();

    }
    setMarkers() {
        if (this.props.geojsonData && Array.isArray(this.props.geojsonData)) {
            let map = this.map;
            let tempArr = [];
            let markerGroup = this.props.geojsonData;
            // this.props.mapData.map((res, key) => {
            //     markerGroup.push({
            //         type: 'Feature',
            //         id: res.ListingId,
            //         geometry: {
            //             type: 'Point',
            //             coordinates: [
            //                 res.Longitude,
            //                 res.Latitude
            //             ]
            //         },
            //         properties: {
            //             title: 'circle',
            //             id: res.ListingId,
            //             class: "",
            //             description: this.popUpHtml(res),
            //             price: '<p>hi</p>',
            //             originalPrice: res.ListPrice,
            //             'marker-size': 'small',
            //             'marker-color': '#ff5b60',
            //             'marker-symbol': 'circle'
            //         }
            //     });

 

            // });
            console.log("markerGroup", markerGroup);
            if (map.getLayer('clusters')) {
                map.removeLayer('clusters');

            }

            if (map.getLayer('cluster-count')) {
                map.removeLayer('cluster-count');
            }
            if (map.getLayer('unclustered-point')) {
                map.removeLayer('unclustered-point');

            }

            if (map.getSource('properties_data')) {
                map.removeSource('properties_data');

            }

            map.addSource("properties_data", {
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
                clusterMaxZoom: 14, // Max zoom to cluster points on
                clusterRadius: 50,
            });
            map.addLayer({
                id: "unclustered-point",
                type: "symbol",
                source: "properties_data",
                filter: ["!", ["has", "point_count"]],
                paint: {
                    'text-color': "#ff5b60",


                },
                layout: {
                    'text-field': ['get', 'price'],
                    'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
                    'text-size': 12.5,
                    'icon-image': 'marker',
                    'icon-size': 0.90
                }

            });
            map.addLayer({
                id: "clusters",
                type: "circle",
                source: "properties_data",
                filter: ["has", "point_count"],
                paint: {
                    "circle-color": ["step", ["get", "point_count"], "#ff5b60", 10, "#ff5b60", 750, "#ff5b60"],

                    "circle-radius": ["step", ["get", "point_count"], 20, 25, 30, 70, 40]
                }
            });
            map.addLayer({
                id: "cluster-count",
                type: "symbol",
                source: "properties_data",
                filter: ["has", "point_count"],
                layout: {
                    "text-field": "{point_count_abbreviated}",
                    "text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
                    "text-size": 12
                },
                paint: {
                    "text-color": "white"
                }
            });
            map.on("click", "unclustered-point", (t) => {
                console.log("cluster points", t.features[0].geometry.coordinates.slice());
                let latlang = t.features[0].geometry.coordinates.slice();
                let r = t.features[0].properties.description;
                let listingId = t.features[0].properties.id;
                (new mapboxgl.Popup({ offset: 0 })).setLngLat(latlang).setHTML(r).addTo(map)
                this.map.flyTo({
                    center: latlang,
                    essential: true,
                });
                let data = {
                    id: t.features[0].properties.id
                }
                let urls = markerApi;
                API.jsonApiCall(urls, data, "POST", null, {
                    "Content-Type": "application/json",
                }).then((res) => {
                    let html = "";
                    let item = res.data;
                    let data = item;
                    if (res.status == 200) {
                        if (data) {
                            let loader = document.getElementById('marker-loader' + data.ListingId);
                            loader.remove();
                            let infoBox = document.getElementById('marker-info' + data.ListingId);
                            ReactDOM.render(<MapCard showIsFav={true}
                                openUserPopup={true}
                                item={data}
                                isMarker={true}
                                isInfo={true}
                                isMarkerClass={"markerClass"}
                                openLoginCb={this.props.togglePopUp}
                                gotoDetailPage={this.props.gotoDetailPage}
                                isLogin={this.props.isLogin} />, infoBox);
                            let a = document.getElementsByClassName("mapboxgl-popup-content");
                            if (a.length > 1) {
                                a[1].remove();
                            }
                        } else {
                            let loader = document.getElementById('marker-loader' + id);
                            loader.remove();
                            let infoBox = document.getElementById('marker-info' + id);
                            ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
                        }
                    }
                    else {
                        let loader = document.getElementById('marker-loader' + id);
                        loader.remove();
                        let infoBox = document.getElementById('marker-info' + id);
                        ReactDOM.render(<h5>Not Found !</h5>, document.getElementById('marker-info' + id));
                    }
                }).catch((e) => {
                });
            })
            this.setState({
                markerArr: tempArr
            });
            this.map.flyTo({
                center: this.props.center,
                essential: true,
                zoom: 10
            });
        } else {

        }
    }
    componentDidUpdate(prevProps) {
        if (JSON.stringify(this.props.geojsonData) !== JSON.stringify(prevProps.geojsonData)) {
            this.clearPrevMarkers();
            this.setMarkers();
            if (this.props.isReset) {
                this.map.flyTo({
                    center: this.props.center,
                    essential: true,
                    zoom: 10
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
                this.updateTrash;
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
                    className="mapInMbile"
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