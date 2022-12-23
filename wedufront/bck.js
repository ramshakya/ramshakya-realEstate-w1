import React from 'react'
import mapImage from "./../../../public/images/map-box.png"
const NavHeader = (props) => {
    const detailData={
        "addedOn":{
            "text":"Added On",
            "value":"3 Days ago"
        },
        "address":"180 Hillsview Dr, Richmond Hill.",
        "bagdes":{
            "bed":{
                "text":"Bed",
                "value":"5"
            },
            "bath":{
                "text":"Bath",
                "value":"2"
            },
            "garage":{
                "text":"Garage",
                "value":"3"
            }
        }
    }
    return (
        <>
            {/* slider here */}
            <div className={'row mt-3'}>
                <div className="col-md-8 col-lg-8 col-sm-8 col-xs-8">
                    <div className="row px-1">
                        <div className="col-md-6 details-head-left">
                            <span className="added-on-time"></span>
                            <h3></h3>
                            <ul className="room-details">
                                <li><span>{detailData.bagdes.bed.value} {detailData.bagdes.bed.text}</span></li>
                                <li><span>{detailData.bagdes.bath.value} {detailData.bagdes.bath.text}</span></li>
                                <li><span>{detailData.bagdes.garage.value} {detailData.bagdes.garage.text}</span></li>
                            </ul>
                        </div>
                        <div className="col-md-6 details-head-right">
                            <h3 className="property-price">$3,499,000 </h3>
                            <p>Estimated Mortgage $<span className="estimate_payment">18,968.75</span>/m</p>
                        </div>
                    </div>
                </div>
                <div className="col-sm-4 col-xs-4 col-md-4 col-lg-4 ">
                    <div className="row ">
                        <div className="col-md-12">
                            <div className="viewmap loadMap"><img src="./../../../images/map-box.png" alt="img" />
                                <a href="#" data-toggle="modal" data-target="#exampleModalLong" className="loadMap">View map &amp; nearby amenities</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default NavHeader;