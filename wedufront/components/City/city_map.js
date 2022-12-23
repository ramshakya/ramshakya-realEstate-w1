import MapLoad from "../../ReactCommon/Components/MapLoadv2";
import React, { useState, useEffect } from 'react';
import MapCard from "../../ReactCommon/Components/MapCard";
const CityMap = (props) => {
	 
	const centerLat = props.mapData.length?props.mapData[props.mapData.length - 1].Latitude:-79.39493841352393;
	const centeralLong = props.mapData.length?props.mapData[props.mapData.length - 1].Longitude:43.617343105067334;
	const [center, setCenter] = useState([centeralLong, centerLat]);
	const [zoom, setZoom] = useState(9);
	const mapRef = React.createRef();
	return (
		<>
			<div className="p-3 mt-auto">

				<div className="row">
					<div className="col-lg-6" style={{ maxHeight: "100vh" }}>
						<MapLoad
							center={center}
							zoom={zoom}
							handlePropertyCall={props.handlePropertyCall}
							mapData={props.mapData}
							ref={mapRef}
							handleTypeHead={props.handleTypeHead}
							changeDrawState={props.changeDrawState}
							{...props}
						/>
					</div>
					<div className="col-lg-6" style={{ maxHeight: "100vh", overflowY: "scroll" }}>
						<div className="row">
							{props.cityproperty.map((item, index) => {
								return (
									<div className="col-lg-6 mb-2 p-1" key={index}>
										<MapCard
											item={item}
											key={index}
											showIsFav={true}
											openUserPopup={true}
											openLoginCb={props.togglePopUp}
											isLogin={props.isLogin} />
									</div>
								)
							})}
						</div>
					</div>
				</div>
			</div>
		</>
	)
}
export default CityMap;
// git check