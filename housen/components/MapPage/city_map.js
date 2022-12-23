import MapLoad from "./MapLoad";
import React, {useState, useEffect} from 'react';
import {Container, Row, Col} from "react-bootstrap";
// import MapCard from "../../ReactCommon/Components/MapCard";
import CardRow3 from "../Cards/PropertyCard";
const CityMap =(props)=>{
    // console.log(props);
	const [center,setCenter] = useState([-79.39493841352393, 43.617343105067334]);
	const [zoom,setZoom] = useState(9);
	const mapRef = React.createRef();
	return(
		<>
			 <div className="p-3 mt-auto">
                        
                        <div className="row">
                            <div className="col-lg-6" style={{maxHeight: "100vh"}}>
                                <MapLoad
                                    center={center}
                                    zoom={zoom}
                                    handlePropertyCall={props.handlePropertyCall}
                                    mapData={props.mapData}
                                    ref={props.mapRef}
                                    mapLogin={props.mapLogin}
                                />
                            </div>
                <div className="col-md-6" style={{maxHeight: "100vh", overflowY: "scroll"}}>
                <Row>

                    {props.cityproperty.map((item) => {
                       
                        return (
                            <Col md={6}>
                                <CardRow3
                                    
                                    item={item}
                                />
                            </Col>
                        );
                    })}

                </Row>
            </div>
                        	
                        </div>
                    </div>
		</>
		)
}
export default CityMap;