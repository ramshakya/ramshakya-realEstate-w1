import { useState, useEffect } from "react";
import Image from 'next/image'
import Link from "next/link";
import emptyHeart from "./../../public/images/icons/empty_heart.svg";
import fillHeart from "./../../public/images/icons/heartFill.svg";
import { favUrl } from "../../constants/GlobalConstants";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/GlobalConstants"
const defaultImage = Constants.defaultImage
const MapCard = (props) => {
    const data = props.item ? props.item : {};
    const [favIconImg, setfavIconImg] = useState(emptyHeart);
    const getRound = (val) => {
        let res = Math.ceil(val);
        if (res > 0) {
            return res;
        }
        return "";
    };
    function setUpDown(Orig_dol, ListPrice) {
        Orig_dol = Math.ceil(Orig_dol);
        ListPrice = Math.ceil(ListPrice);
        if (ListPrice > Orig_dol) {
            var diff_up = ListPrice - Orig_dol;
            var diff_up_per = Math.ceil(diff_up / ListPrice * 100);
            return (<span className="iconsHolder diff_up font-14" data-toggle="tooltip" data-placement="top" title={Constants.formatter.format(diff_up)}  ><img src="/images/icons/down-red-icon-svg.png" alt="icon" className="down-red-icon " />{diff_up_per + '%'}  </span>)
        }
        if (Orig_dol > ListPrice) {
            var diff_dwn = Orig_dol - ListPrice;
            var diff_dwn_per = Math.ceil(diff_dwn / Orig_dol * 100);
            return (<span className="iconsHolder diff_dwn font-14" data-toggle="tooltip" data-placement="top" title={Constants.formatter.format(diff_dwn)} ><img src="/images/icons/up-green-icon-svg.png"  alt="icon" className="up-green-icon" /> {diff_dwn_per + '%'} </span>);
        }
    }
    function favorite(e) {
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
            if (props.checkFavApiCall) {
                props.checkFavApiCall(reqBody);
            }
        });
    };
    function getDom(dom) {
        let res = Math.round(dom);
        if (res === 1) {
          return res + " Day ";
        }
        if (res > 1) {
          return res + " Days ";
        }
        return "Today";
        // let dm = getRound(dom);
        // if (dm) {
        //     return dm + ' Days';
        // } else {
        //     return 'Today';
        // }
    }
    useEffect(() => {
        let userData = localStorage.getItem("userDetail");
        userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
        if (userData && userData !== null && userData !== "undefined" && userData.favourite_properties && userData.favourite_properties.indexOf(data.ListingId) !== -1) {
            setfavIconImg(fillHeart)
        }
    }, [props.isLogin]);
    return (
        <>{data &&
            <div id={`propCard${data.ListingId}`} className={`${data.isdetail ? "" : 'listing-card'} mb-2 propCard${data.ListingId}  ${props.isMarkerClass}  ${ data.Status === 'U' && !props.isLogin ? 'filter':''}`} >
                <div className="listing-image">
                    {/*<Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>*/}
                        <a className="property-link"  href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
                            <img 
                                src={data.ImageUrl ? Constants.image_base_url + data.ImageUrl : defaultImage}
                                title={data.ImageUrl ? data.ImageUrl : defaultImage}
                                alt={`For ${data.PropertyStatus} at ${data.StandardAddress} - MLS:${data.ListingId} `}
                                className="cardImages  img-fluid hover-image"
                                onMouseOver={props.highLightCb}
                                dataset={JSON.stringify({ id: data.ListingId, ismap: false })}
                                loading='lazy'
                             />
                            {/*<Image
                                src={data.ImageUrl ? Constants.image_base_url + data.ImageUrl : defaultImage}
                                // src={defaultImage}
                                alt={`For ${data.PropertyStatus} at ${data.StandardAddress} - MLS:${data.ListingId} `}
                                // layout='fill'
                                width={400}
                                height={260}
                                layout="responsive"
                                objectFit='cover'
                                className="cardImages  img-fluid hover-image"
                                placeholder="blur"
                                blurDataURL={data.ImageUrl ? Constants.image_base_url + data.ImageUrl : defaultImage}
                                // blurDataURL={defaultImage}
                                quality='100'
                                loading='lazy'
                                onMouseOver={props.highLightCb}
                                dataset={JSON.stringify({ id: data.ListingId, ismap: false })}
                            />*/}
                        </a>
                    {/*</Link>*/}
                </div>
                <div className="top-ribbon1">
                    {
                        !data.Status && props.isHome && ("For " + data.PropertyStatus)
                    }
                    {
                        data.Status === "U" && data.PropertyStatus == "Lease" && (" Rented ")
                    }
                    {
                        data.Status === "A" && data.PropertyStatus == "Lease" && ("For " + data.PropertyStatus)
                    }
                    {
                        data.Status === "U" && data.PropertyStatus == "Sale" && (" Sold ")
                    }
                    {
                        data.Status === "A" && data.PropertyStatus == "Sale" && ("For " + data.PropertyStatus)
                    }
                </div>
                <div className="top-ribbon2">
                    <span className="span1">{Constants.formatter.format(data.ListPrice)} {setUpDown(data.Orig_dol, data.ListPrice)}</span>
                </div>
                <div className="favourite-ribbon">
                    {props.showIsFav && ( 
                        <img
                            {...favIconImg}
                            height="28"
                            width="23"
                            title="fav-icon"
                            onClick={favorite}
                            alt="fav-img"
                            className="favicon"
                        />
                     
                    )
                    }
                </div>
                <div className="card-content">
                    <Link href={`/propertydetails/${data.SlugUrl}`} key={props.key}>
                        <a className="property-link">
                            <h6>{data.StandardAddress}</h6>
                        </a></Link>
                    <p className="description h-20" >{data.Ad_text ? data.Ad_text.slice(0, 40) + '..' : ""}</p>
                    <span className="spec" title="Bedrooms">
                        {getRound(data.BedroomsTotal) &&
                            <span>
                                {getRound(data.BedroomsTotal)} {' '}
                                <img title="bed-icon" src="../images/icons/bed.svg" height={20} width={20} alt="bath-icon" className="prop-icons opacity-cls" />
                            </span>
                        }
                    </span>
                    {getRound(data.BathroomsFull) &&
                        <span className="spec" title="Bathrooms">
                            {getRound(data.BathroomsFull)} {'  '}
                            <img  title="bath-icon" src="../images/icons/bath.svg" height={20} width={20} alt="bath-icon" className="prop-icons opacity-cls" />
                        </span>
                    }
                    {
                        data.isMarker ? "" : <>
                            {data.Sqft &&
                                <span className="spec area-sqft-cls" title="Area Sqft">
                                    {data.Sqft}{' '}
                                    <img title="sqft-icon" src="../images/icons/square.svg" height={20} width={20} alt="sqft-icon" className="prop-icons opacity0-5-cls" /><sup>2</sup>
                                </span>
                            }
                        </>
                    }
                    <span className="detail_btn">{getDom(data.Dom)}</span>
                </div>
            </div>
        }
        </>
    );
};
export default MapCard;
