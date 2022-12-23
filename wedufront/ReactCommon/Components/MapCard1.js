import {useState, useEffect} from "react";
import Image from 'next/image'
import Link from "next/link";
import emptyHeart from "./../../public/images/icons/empty_heart.svg";
import fillHeart from "./../../public/images/icons/heartFill.svg";
import {favUrl} from "../../constants/GlobalConstants";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/GlobalConstants"
const defaultImage = Constants.defaultImage
const MapCard = (props) => {
    const data = props.item;
    var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 0,
    });
    // const [favIconState, setfavIconState] = useState(false);
    const [favIconImg, setfavIconImg] = useState(emptyHeart);

    const getRound = (val) => {
        let res = Math.ceil(val);
        if (res) {
            return res;
        }
        return false;
    };
    const favorite = (e) => {
        if (
            !localStorage.getItem("login_token") &&
            props.openUserPopup &&
            props.openLoginCb
        ) {
            props.openLoginCb();
            return true;
        }
        let userData = localStorage.getItem("userDetail");
        let token = localStorage.getItem("login_token");
        userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
        const indexArr = userData.favourite_properties.indexOf(data.ListingId)
        const reqBody = {
            LeadId: userData.login_user_id,
            AgentId: Constants.agentId,
            ListingId: data.ListingId,
            Fav: indexArr === -1 ? 1 : 0,
        };
        const headers = {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        };
        API.jsonApiCall(favUrl, reqBody, "post", null, headers).then((res) => {
            if (reqBody.Fav === 1) {
                userData.favourite_properties.push(data.ListingId)
                setfavIconImg(fillHeart)
            } else {
                const favArr = userData.favourite_properties;
                favArr.splice(indexArr, 1);
                userData.favourite_properties = favArr;
                setfavIconImg(emptyHeart)
            }
            localStorage.setItem("userDetail", JSON.stringify(userData))
            if (props.checkFavApiCall){
                props.checkFavApiCall(reqBody);
            }
        });
    };
    useEffect(() => {
        let userData = localStorage.getItem("userDetail");
        userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
        if (userData && userData !== null && userData !== "undefined" && userData.favourite_properties.indexOf(data.ListingId) !== -1) {
            setfavIconImg(fillHeart)
        }
    }, [props.isLogin]);


    return (
        <div className="mapCardList">
            <div className="card card-img-wrapper cardSection">
                <Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
                    <a>
                    <Image
                    src={data.ImageUrl ? data.ImageUrl : defaultImage}
                    alt={"Property Image"}
                    // layout='fill'
                    width={400} 
                    height={240} 
                    layout="responsive"
                    objectFit='cover'
                    className="cardImages  img-fluid"
                    placeholder="blur"
                    blurDataURL = {data.ImageUrl ? data.ImageUrl : defaultImage}
                    quality='1'
                    />
                        {/* <img
                            src={data.ImageUrl ? data.ImageUrl : defaultImage}
                            alt={"Property Image"}
                            className="cardImages  img-fluid"
                        /> */}
                    </a>
                </Link>
                <div className="dom-ribbon"> {data.PropertySubType}</div>
                {data.PropertyStatus == "Sale" ? (
                    <div className="for-sale-ribbon for-lease-ribbon1 for-rent-ribbon">
                        For Sale
                    </div>
                ) : (
                    ""
                )}
                {data.PropertyStatus == "Rent " ? (
                    <div className="for-rent-ribbon">For Rent</div>
                ) : (
                    ""
                )}
                {data.PropertyStatus == "Lease" ? (
                    <div className="for-lease-ribbon for-lease-ribbon1">For Lease</div>
                ) : (
                    ""
                )}
                <div className="card-body hill-body">
                    <ul className="body-head priceDisp ">
                        <li className="priceSection">
                            <h2 className="mt-0">{formatter.format(data.ListPrice)}</h2>
                        </li>
                        {props.showIsFav && (
                            <li className="favIcon">
                                <img
                                    {...favIconImg}
                                    height="33"
                                    width="25"
                                    onClick={favorite}
                                    alt="fav-img"
                                />
                            </li>
                        )}
                    </ul>
                    <ul className="hill-inner mt-4">
                        <li>
              <span>
                {getRound(data.BedroomsTotal)
                    ? getRound(data.BedroomsTotal) + " Beds"
                    : ""}{" "}
              </span>
                        </li>
                        <li>
              <span>
                {getRound(data.BathroomsFull)
                    ? getRound(data.BathroomsFull) + " Baths"
                    : ""}{" "}
              </span>
                        </li>
                        <li>
              <span>
                {getRound(data.Gar) ? getRound(data.Gar) + " Garage" : ""}
              </span>
                        </li>
                        <li>
              <span className="landSize">
                {getRound(data.Sqft) ? getRound(data.Sqft) + " Sqft" : ""}{" "}
              </span>
                        </li>
                    </ul>
                    {/* <p className="propAddress  mt-2">
            <Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
              <a>
                {data.StandardAddress},{data.City}{" "}
              </a>
            </Link>
          </p>
           
          <p className="propAddress2 domCls ">
            {getRound(data.Dom) ? getRound(data.Dom) + " Days" : ""}
          </p> */}
                    <ul className="propAddrSection mt-2">
                        <li className="">
                            <Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
                                <a>
                                    {data.StandardAddress},{data.City}
                                </a>
                            </Link>
                        </li>
                        <li className="">
                            {" "}
                            {getRound(data.Dom) ? getRound(data.Dom) + " Days" : ""}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    );
};
export default MapCard;
