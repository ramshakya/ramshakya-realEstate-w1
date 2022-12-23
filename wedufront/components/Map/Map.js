import ReactMapGL, {Marker, Popup, NavigationControl,FlyToInterpolator} from "react-map-gl";
import {useState, useEffect,useRef} from "react";
import markerImage from "../../public/markerBlack.png"
import Image from "next/image";


export default function Map({locations}) {
    const [selectedLocation, setSelectedLocation] = useState({})
    const [viewport, setViewport] = useState({
        width: "100%",
        height: "100vh",
        // The latitude and longitude of the center of London
        latitude: 51.5074,
        longitude: -0.1278,
        zoom: 10
    });
    const mapRef = useRef();
    return <ReactMapGL
        mapStyle="mapbox://styles/sagarvermaitdeveloper/ckkwt2tzs5bh017n4g1c1h9pg"
        mapboxApiAccessToken="pk.eyJ1Ijoic2FnYXJ2ZXJtYWl0ZGV2ZWxvcGVyIiwiYSI6ImNraTFiOTA1NTB4anMyeXFoZ2hxZHhuazEifQ.gQOe35Xknut_JqBXHqOaMQ"
        {...viewport}
        onViewportChange={(nextViewport) => setViewport(nextViewport)}
    >
        ref={mapRef}
        <NavigationControl/>
        {locations.map((location) => (
            <div key={location.id}>
                <Marker
                    latitude={location.center[1]}
                    longitude={location.center[0]}
                    offsetLeft={-20}
                    offsetTop={-10}
                    text={{
                        'text-field': ['get', 'text'],
                        "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                        "text-offset": [0, 0.6],
                        "text-anchor": "top"
                    }}
                >
                    <Image onClick={() => {
                        setSelectedLocation(location);
                    }} src={markerImage}
                           alt="awardImage1"
                           objectFit="contain">
                        {/*<span role="img" aria-label="push-pin">📌</span>*/}


                    </Image>
                </Marker>
                {selectedLocation.id === location.id ? (
                    <Popup
                        onClose={() => setSelectedLocation({})}
                        closeOnClick={true}
                        latitude={location.center[1]}
                        longitude={location.center[0]}>
                        {location.place_name}
                    </Popup>) : (false)}
            </div>
        ))}
    </ReactMapGL>
}