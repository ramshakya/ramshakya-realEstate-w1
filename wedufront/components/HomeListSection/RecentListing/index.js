import React, {useState, useEffect, useRef} from "react";
import Constants from '../../../constants/GlobalConstants';
import Card from "../../../ReactCommon/Components/MapCard";

const defaultImage = Constants.defaultImage
import Button from '../../../ReactCommon/Components/SimpleButton';

const RecentListing = (props) => {
    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    });
    const [data, setData] = useState(props.data ? props.data : []);
    useEffect(() => {
        // propListData();
    }, [])
    return (
        <>
            {/*<!-- property section area start  --> */}
            <section className="property-section">
                <div className="container-fluid">
                    {/*<!-- title row --> */}
                    <div className="row justify-content-center">
                        <div className="col-12 col-lg-7">
                            <div className="title text-center">
                                <h1 className="px-5 mt-5">
                                    Recent Listings
                                </h1>
                            </div>
                        </div>
                    </div>
                    {/*<!-- card row --> */}
                    <div className="row">
                        {
                            data && Array.isArray(data) && data.length > 0 && data.map((item, key) => {
                                return (
                                    <div className="col-md-3 mt-4" key={key}>
                                        <Card
                                            item={item}
                                            defaultImage={defaultImage}
                                        />
                                    </div>
                                )
                            })
                        }
                    </div>
                    <div className="row mt-4" style={{"text-align": "center"}}>
                        <Button extraProps={{
                            size: "md",
                            className: "common-btn search-btn",
                            type: "button",
                            value: "Search",
                            text: "View All Recent Listings",
                        }}/>
                    </div>
                </div>
            </section>
        </>
    )
}
export default RecentListing;