import { useState, useEffect, useRef, Component } from "react";
import Link from "next/link";
// import emptyHeart from './../../public/images/icons/heart-fill.svg'
// import fillHeart from './../../public/images/icons/favourites.png'
import emptyHeart from './../../public/images/icons/empty_heart.svg'
import fillHeart from './../../public/images/icons/heartFill.svg'
import API from "../utility/api";
import {favUrl} from "../../constants/GlobalConstants";
const MapCard = (props) => {
    const [favIconImg, setfavIconImg] = useState(emptyHeart);
    const data = props.item;
    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    });
    const [favIconState, setfavIconState] = useState(false);
    const [favIcon, setfavIcon] = useState(emptyHeart);
    const getRound = (val) => {
        let res = Math.ceil(val);
        if (res) {
            return res;
        }
        return false;
    }
    // todo :: this is sagar function for favourite
    const favorite = (e) => {
        if (
            !localStorage.getItem("login_token") &&
            props.openUserPopup &&
            props.openLoginCb
        ) {
            props.openLoginCb();
            return true;
        }
        let userData =localStorage.getItem("userDetail");
        let token  = localStorage.getItem("login_token");
        userData = userData ?JSON.parse(localStorage.getItem("userDetail")):null;
        const indexArr =  userData.favourite_properties.indexOf(data.ListingId)
        const reqBody = {
            LeadId: userData.login_user_id,
            AgentId: 3,
            ListingId: data.ListingId,
            Fav: indexArr === -1 ? 1 : 0,
        };
        const headers = {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        };
        API.jsonApiCall(favUrl, reqBody, "post", null, headers).then((res) => {
            if (reqBody.Fav ===1 ){
                userData.favourite_properties.push(data.ListingId)
                setfavIconImg(fillHeart)
            } else {
                const favArr = userData.favourite_properties;
                const finalArr = favArr.splice(indexArr, 1);
                userData.favourite_properties = finalArr;
                setfavIconImg(emptyHeart)
            }
            localStorage.setItem("userDetail",JSON.stringify(userData))
        });
    }
    useEffect(() => {
        let userData =localStorage.getItem("userDetail");
        userData = userData ?JSON.parse(localStorage.getItem("userDetail")):null;
        if (userData && userData !==null && userData!=="undefined" && userData.favourite_properties.indexOf(data.ListingId) !==-1) {
            setfavIconImg(fillHeart)
        }
    }, [props.isLogin]);
    // todo :: this is the end sagar function for favourite
    return (
        <div className="card card-img-wrapper cardList" key={props.key}>
            <Link href={`/propertydetails/${data.SlugUrl}`} >
                <a>
                    <img src={data.ImageUrl ? data.ImageUrl : props.defaultImage} alt={'Property Image'} className="propertImage  img-fluid" />
                </a>
            </Link>
            <div className="dom-ribbon"> {data.PropertySubType}</div>
            {data.PropertyStatus ? <>
                {data.PropertyStatus == 'Sale' ? <div className="for-sale-ribbon for-lease-ribbon1 for-rent-ribbon">For Sale</div> : ""}
                {data.PropertyStatus == "Rent " ? <div className="for-rent-ribbon">For Rent</div> : ""}
                {data.PropertyStatus == "Lease" ? <div className="for-lease-ribbon for-lease-ribbon1">For Lease</div> : ""}
            </> : <>

                {data.MlsStatus == 'Sale' ? <div className="for-sale-ribbon for-lease-ribbon1 for-rent-ribbon">For Sale</div> : ""}
                {data.MlsStatus == "Lease" ? <div className="for-rent-ribbon">For Rent</div> : ""}
                {/* {data.MlsStatus == "Lease" ? <div className="for-lease-ribbon for-lease-ribbon1">For Lease</div> : ""} */}
            </>
            }
            <div className="card-body hill-body">
                <ul className="body-head priceDisp ">
                    <li className="priceSection"><h2 className="mt-0">{formatter.format(data.ListPrice)}</h2></li>
                    {props.showIsFav && (
                        <li className="favIcon">
                            <img
                                {...favIconImg}
                                height="33"
                                width="25"
                                onClick={favorite}
                                alt="fav-img"
                                // className={favIconState ? "fillHeart" : ""}
                                // data-gsv-fav={favIcon?"1":"0"}
                                // data-gsv-mls-id={data.ListingId}
                            />
                        </li>
                    )}
                </ul>
                <ul className="hill-inner mt-3">
                    {getRound(data.BedroomsTotal) && <li><span>{getRound(data.BedroomsTotal) ? getRound(data.BedroomsTotal) + ' Beds' : ""}  </span></li>}
                    {getRound(data.BathroomsFull) && <li><span>{getRound(data.BathroomsFull) ? getRound(data.BathroomsFull) + ' Baths' : ""} </span></li>}
                    {/* <li><span>{getRound(data.Gar) ? getRound(data.Gar) + " Garage" : ""}</span></li> */}
                    {getRound(data.Park_spcs) && <li><span className="landSize">{getRound(data.Park_spcs) ? " " + getRound(data.Park_spcs) + " Parking" : ""} </span></li>}
                    {getRound(data.Sqft) && <li><span className="landSize">{getRound(data.Sqft) ? getRound(data.Sqft) + " Sqft" : ""} </span></li>}
                </ul>
                {/* <p className="propAddress  mt-2">{data.StandardAddress},{data.City} </p> <span className="propAddress domCls ">{getRound(data.Dom) ? getRound(data.Dom) + " Days" : ""}</span> */}
                <ul className="AddressDisp ">
                    <li className="mt-1"> <Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
                        <a>
                            {data.StandardAddress},{data.City}
                        </a>
                    </Link>
                    </li>
                    <li className=""> {getRound(data.Dom) ? getRound(data.Dom) + " Days" : ""}</li>
                </ul>
                {/* , {data.City} */}

            </div>

        </div>
    );
}
export default MapCard;
