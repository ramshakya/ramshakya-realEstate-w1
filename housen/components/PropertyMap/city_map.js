import MapLoad from "./MapLoad";
import React, { useState, useEffect } from 'react';
import { Container, Row, Col } from "react-bootstrap";
// import MapCard from "../../ReactCommon/Components/MapCard";
//
import CardRow3 from "../Cards/PropertyCard";
const CityMap = (props) => {
    let [center, setCenter] = useState([-79.39493841352393, 43.617343105067334]);
    const [zoom, setZoom] = useState(9);
    const mapRef = React.createRef();
    console.log("CityMap", props);
    useEffect(() => {
        if (props.mapData) {
            const centerLat = props.mapData[props.mapData.length - 1].Latitude;
            const centeralLong = props.mapData[props.mapData.length - 1].Longitude;
            setCenter([centeralLong, centerLat]);
        }
    }, [props.mapData]);

   
    return (
        <>
            <div className="">

                <div className="row">

                    <div className="col-md-6 mapScroll" style={{ maxHeight: "100vh", overflowY: "scroll" }}>
                        <Row>

                            {props.cityproperty.map((item) => {
                                const {
                                    id,
                                    PropertyStatus,
                                    isOpenHouse,
                                    PropertySubType,
                                    ListPrice,
                                    StandardAddress,
                                    City,
                                    ImageUrl,
                                    BedroomsTotal,
                                    BathroomsFull,
                                    Sqft,
                                    SlugUrl,
                                    ListingId

                                } = item;
                                return (
                                    <Col md={6} key={"key" + id} className="mb-4">
                                        <CardRow3
                                            key={id}
                                            forBadge={PropertyStatus}
                                            isOpenHouse={isOpenHouse}
                                            PropertySubType={PropertySubType}
                                            BedroomsTotal={BedroomsTotal}
                                            BathroomsFull={BathroomsFull}
                                            price={ListPrice}
                                            StandardAddress={StandardAddress}
                                            province={City}
                                            ImageUrl={ImageUrl}
                                            Sqft={Sqft}
                                            SlugUrl={SlugUrl}
                                            ListingId={ListingId}
                                            showIsFav={true}
                                            openUserPopup={true}
                                            openLoginCb={props.togglePopUp}
                                            isLogin={props.isLogin}
                                            item={item}
                                        />
                                    </Col>
                                );
                            })}

                        </Row>
                    </div>
                    <div className="col-lg-6 pl-0" style={{ maxHeight: "100vh" }}>
                        <MapLoad
                            center={center}
                            zoom={zoom}
                            handlePropertyCall={props.handlePropertyCall}
                            mapData={props.mapData}
                            ref={props.mapRef}
                            mapLogin={props.mapLogin}
                            mapdragenCb={props.mapdragenCb}
                        />
                    </div>
                </div>
            </div>
        </>
    )
}
export default CityMap;