import React from "react";
import Constants from '../../constants/GlobalConstants';
import Card from "../../ReactCommon/Components/MapCard";
const defaultImage = Constants.defaultImage
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
const PropertiesList = (props) => {
    const { data } = props;
    return (
        <section className="property-section">
            <div className="container-fluid">
                <div className="row justify-content-center">
                    <div className="col-12 col-lg-7">
                        <div className="title-wrapper pt-5">
                            <h6 className="service-title">{props.headerText}</h6>
                            <hr />
                        </div>
                    </div>
                </div>
                <div className="row">
                    {props.isLoading &&
                        <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={4} />
                    }
                    {
                        data && Array.isArray(data) && data.length > 0 && data.map((item, key) => {
                            return (
                                <div className={`col-md-3 homeCard`} key={key}>
                                    {
                                        item.Vow_exclusive == 0 || props.isLogin ? <><p></p></> : <> <span className="vow-cls " >Login Required</span></>
                                    }
                                    <div className={`  ${item.Vow_exclusive == 0 || props.isLogin ? "" : "filter  mt-2"}`}>
                                        <Card
                                            item={item}
                                            defaultImage={defaultImage}
                                            showIsFav={props.showIsFav}
                                            openUserPopup={props.openUserPopup}
                                            openLoginCb={props.openLoginCb}
                                            isLogin={props.isLogin}
                                            isHome={props.isHome}
                                        />
                                    </div>
                                </div>
                            )
                        })
                    }
                </div>
            </div>
        </section>
    )
}
export default PropertiesList;