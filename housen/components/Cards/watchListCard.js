import Link from "next/link";
import { useState, useEffect } from "react";
import { Modal, Tabs, Tab, Form } from "react-bootstrap";
import Constants from "../../constants/Global";

const watchListCard = (props) => {
    const ListPrice = 56412;
    const ImageUrl = '';
    const PropertySubType = 'test';
    const StandardAddress = 'addr';
    const [loaderState, setLoaderState] = useState(false);
    let { watchListings, createdAt, ListingId } = props.item;
    let { emailIsVerified, isSold, key } = props;
    if (isSold !== undefined && !isSold) {
        emailIsVerified = true;
    }
    useEffect(() => {
        let {AlertsOn}=watchListings;
        if(AlertsOn){
            try {
                if(AlertsOn.NewListings){
                    let el1=document.getElementById("NewListings"+ListingId);
                    if(el1){
                     el1.checked = true;
                    }
                }
                if(AlertsOn.SoldListings){
                    let el2=document.getElementById("SoldListings"+ListingId);
                    if(el2){
                     el2.checked = true;
                    }
                }
                if(AlertsOn.DelistedListings){
                    let el3=document.getElementById("DelistedListings"+ListingId);
                    if(el3){
                     el3.checked = true;
                    }
                }
            } catch (error) {
                
            }
        }
      },[]);

    
    
    let img = [];
    if (props.imageData) {
        if (props.imageData[ListingId]) {
            img = props.imageData[ListingId];
        }
    }
    const data = props.item ? props.item : {};
    
    const getRound = (val) => {
        let res = Math.ceil(val);
        if (res) {
            return res;
        }
        return false;
    };
  
    function deleteListings(ListingId) {
        setLoaderState(true);
        props.removeCb(ListingId);
    }
    return (
        <div data-pid={key} id={`propCard${ListingId}`} className={`property_div clearfix forsale propCard${ListingId} ${props.isMarkerClass} alertCard mb-4`}>
            <div className={`property_image `}>
                <div className="d-flex alertCardImage">
                    <img src={ImageUrl ? Constants.image_base_url + ImageUrl : Constants.defaultImage}
                        onMouseOver={props.highLightCb} dataset={JSON.stringify({ id: ListingId, ismap: false })}
                        className="featured feature_image feature_image2 " alt="" title="" />
                    <img src={ImageUrl ? Constants.image_base_url + ImageUrl : Constants.defaultImage}
                        onMouseOver={props.highLightCb} dataset={JSON.stringify({ id: ListingId, ismap: false })}
                        className="featured feature_image feature_image" alt="" title="" />
                    <img src={ImageUrl ? Constants.image_base_url + ImageUrl : Constants.defaultImage}
                        onMouseOver={props.highLightCb} dataset={JSON.stringify({ id: ListingId, ismap: false })}
                        className="featured feature_image feature_image" alt="" title="" />
                </div>
            </div>
            <div className="wrapperFeature">
                <div className="item bookmark"></div>
                <div className="featuredListingAddress">
                    <span className="subtypeText">{watchListings.PropertySubType}</span>
                    <p className={`featuredListingCity ellipsis-cls `} title={watchListings.Community}>{watchListings.City} - {watchListings.Community}</p>
                    {/*<span title={Community} className="">{City} - {Community ? Community.substring(0, 30) : ''}</span>*/}
                    <button className="btn btn-remove " onClick={() => deleteListings(ListingId)} ><i className="fa fa-trash icon" aria-hidden="true"></i> Remove  {loaderState?"":""}</button>

                    <div className="alertCardPrice">
                        {/* Median Price: <span className={`featuredPricePlaceholder `}>{formatter.format(ListPrice)}</span> */}
                        {/* Median Price: <span className={`featuredPricePlaceholder `}>{'---'}</span> */}
                    </div>
                    <p className="dateTime">{createdAt}</p>
                    <div className="d-flex pt-2">
                        <div className="w-30"><h6 className="text-center pt-1">Recieve<br/> Updates on:</h6></div>
                        <div className="checkboxes community-checkboxed">
                            <Form.Check type="checkbox" name={"NewListings"} data-value={JSON.stringify(watchListings)} onClick={props.handleInputChanges} id={`${"NewListings"+ListingId}`} className="ms-3 me-1"  label={'New Listings'}/>
                            <Form.Check type="checkbox" name={"SoldListings"} data-value={JSON.stringify(watchListings)} onClick={props.handleInputChanges} id={`${"SoldListings"+ListingId}`} className="ms-3 me-1"  label={'Sold Listings'}/>
                            <Form.Check type="checkbox" name={"DelistedListings"} data-value={JSON.stringify(watchListings)} onClick={props.handleInputChanges} id={`${"DelistedListings"+ListingId}`} className="ms-3 me-1"  label={'Delisted Listings'}/>
                        </div>
                    </div>
                </div>
                <hr className="mb-0" />
                <div className="d-flex">
                    <div className="card-footer-label"><i className="fa fa-building" aria-hidden="true"></i> New:{watchListings.countActive}</div>
                    <div className="card-footer-label"><i className="fa fa-building" aria-hidden="true"></i> Sold:{watchListings.countSold}</div>
                    <div className="card-footer-label">Market Trends</div>
                </div>
            </div>
        </div>
    )
}
export default watchListCard;
