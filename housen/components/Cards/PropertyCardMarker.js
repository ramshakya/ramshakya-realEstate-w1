import Link from "next/link";
import ReactCarousel from "./../../ReactCommon/Components/ReactCarousel";
import { useState, useEffect } from "react";
import Constants from "../../constants/Global";
import API from "../../ReactCommon/utility/api";
import Image from "next/image";
import trash from "./../../public/images/icon/trash.png";

const PropertyCard = (props) => {
  const {
    id,
    BedroomsTotal,
    BathroomsFull,
    Sqft,
    ListPrice,
    City,
    ImageUrl,
    PropertyStatus,
    PropertySubType,
    StandardAddress,
    SlugUrl,
    ListingId,
    isInfo,
    Dom,
    Community,
    Status,
    Park_spcs,
  } = props.item;
  let { emailIsVerified, isSold } = props;
  if (isSold !== undefined && !isSold) {
    emailIsVerified = true;
  }
  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });
  function gotoDetailPage(e) {}
  let img = [];
  if (props.imageData) {
    if (props.imageData[ListingId]) {
      img = props.imageData[ListingId];
    }
  }
  const data = props.item ? props.item : {};
  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });

  let LoginReq = "";
  if (props.LoginRequired !== "undefined") {
    if (props.LoginRequired && !props.isLogin) {
      LoginReq = "blury1";
    } else {
      LoginReq = "";
    }
  }
  // const [favIconState, setfavIconState] = useState(false);
  const [favIconImg, setfavIconImg] = useState("fa fa-heart-o");
  const [loginRequired, setLoginRequired] = useState(false);
  // const [favIconImg, setfavIconImg] = useState(emptyHeart);
  const getRound = (val) => {
    let res = Math.ceil(val);
    if (res) {
      return res;
    }
    return false;
  };
  function setUpDown(Orig_dol, ListPrice) {
    Orig_dol = Math.ceil(Orig_dol);
    ListPrice = Math.ceil(ListPrice);
    if (ListPrice > Orig_dol) {
      var diff_up = ListPrice - Orig_dol;
      var diff_up_per = Math.ceil((diff_up / ListPrice) * 100);
      return (
        <span
          className="iconsHolder diff_up"
          data-toggle="tooltip"
          data-placement="top"
          title={formatter.format(diff_up)}
          style={{ fontSize: "14px" }}
        >
          <img
            src="/images/icons/down-red-icon-svg.png"
            style={{ height: "7px", margin: "5px" }}
          />
          {diff_up_per + "%"}{" "}
        </span>
      );
    }
    if (Orig_dol > ListPrice) {
      var diff_dwn = Orig_dol - ListPrice;
      var diff_dwn_per = Math.ceil((diff_dwn / Orig_dol) * 100);
      return (
        <span
          className="iconsHolder diff_dwn"
          data-toggle="tooltip"
          data-placement="top"
          title={formatter.format(diff_dwn)}
          style={{ fontSize: "14px" }}
        >
          <img
            src="/images/icons/up-green-icon-svg.png"
            style={{ height: "8px", margin: "5px", color: "green !important" }}
          />{" "}
          {diff_dwn_per + "%"}{" "}
        </span>
      );
    }
  }
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
    const indexArr = userData.favourite_properties.indexOf(data.ListingId);
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
    API.jsonApiCall(Constants.favUrl, reqBody, "post", null, headers).then(
      (res) => {
        if (reqBody.Fav === 1) {
          userData.favourite_properties.push(data.ListingId);
          // setfavIconImg(fillHeart)
          setfavIconImg("fa fa-heart");
        } else {
          const favArr = userData.favourite_properties;
          favArr.splice(indexArr, 1);
          userData.favourite_properties = favArr;
          setfavIconImg("fa fa-heart-o");
          // setfavIconImg(emptyHeart)
        }
        localStorage.setItem("userDetail", JSON.stringify(userData));
        if (props.checkFavApiCall) {
          props.checkFavApiCall(reqBody);
        }
      }
    );
  };
  function getDom(dom, PropertyStatus = "") {
    let dm = getRound(dom);
    let txt = dm > 1 ? " Days" : " Day";
    if (Status == "A") {
      if (dm) {
        return "Listed " + dm + txt + " Ago";
      } else {
        return "Just Listed";
      }
    } else if (Status == "U") {
      if (dm) {
        if (dm && PropertyStatus == "Sale") {
          return "Sold " + dm + txt + " Ago";
        } else {
          return "Leased " + dm + txt + " Ago";
        }
        return "Sold " + dm + txt + " Ago";
      } else {
        if (PropertyStatus == "Sale") {
          return "Sold Today";
        } else {
          return "Leased Today";
        }
      }
    } else if (Status == "D") {
      if (dm) {
        return "De-listed " + dm + txt + " Ago";
      } else {
        return "De-listed Today";
      }
    }
  }
  useEffect(() => {
    if (data.Status === "U") {
      setLoginRequired(
        emailIsVerified || props.isLogin || data.Status !== "U" ? true : false
      );
    } else if (data.Status === "D") {
      setLoginRequired(
        emailIsVerified || props.isLogin || data.Status !== "D" ? true : false
      );
    } else {
      setLoginRequired(true);
    }
  }, [data.Status]);
  useEffect(() => {
    if (data.Status === "U") {
      setLoginRequired(
        emailIsVerified || props.isLogin || data.Status !== "U" ? true : false
      );
    } else if (data.Status === "D") {
      setLoginRequired(
        emailIsVerified || props.isLogin || data.Status !== "D" ? true : false
      );
    } else {
      setLoginRequired(true);
    }

    let userData = localStorage.getItem("userDetail");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    if (
      userData &&
      userData !== null &&
      userData !== "undefined" &&
      userData.favourite_properties.indexOf(data.ListingId) !== -1
    ) {
      // setfavIconImg(fillHeart)
      setfavIconImg("fa fa-heart");
    }
  }, [props.isLogin]);
  function signInToggled() {
    if (props.isLogin) {
      props.verifyEmail();
    } else {
      props.openLoginCb();
    }
  }
  return (
    <>
      {props.showTrash && (
        <div className="trash-btn-cls">
          <img {...trash} onClick={() => props.removeCb(data.ListingId)} />
        </div>
      )}
      <div
        data-pid={id}
        id={`propCard${ListingId}`}
        className={`property_div clearfix forsale propCard${ListingId} ${props.isMarkerClass}`}
      >
        <div
          className={`property_image ${
            loginRequired && props.isLogin ? "" : "   "
          }`}
        >
          {loginRequired ? (
            <></>
          ) : (
            <div className="sign-in-required  ">
              <div className=" sign-in-required-inner border bold-txt-cls text-center">
                {/* <span className=" " >
                            <u onClick={props.signInToggle} className={`join-signIn-toggle theme-color`}>
                                Join</u> or <u onClick={props.signInToggle} className={`join-signIn-toggle theme-color`}> Sign In</u>
                            • See photos & sold data
                        </span> */}
                {/* <p className=""> Real estate boards require a verified account. </p> */}
                <span className=" ">
                  <svg
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    className="xs-ml1"
                    aria-hidden="true"
                  >
                    <path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 116 0v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path>
                  </svg>{" "}
                  {!props.loginRequired && props.isLogin ? (
                    <>
                      <u
                        onClick={
                          props.signInToggle
                            ? props.signInToggle
                            : signInToggled
                        }
                        className={`join-signIn-toggle theme-color`}
                      >
                        {" "}
                        Verify account now
                      </u>
                    </>
                  ) : (
                    <>
                      <u
                        onClick={
                          props.signInToggle
                            ? props.signInToggle
                            : signInToggled
                        }
                        className={`join-signIn-toggle theme-color`}
                      >
                        Join
                      </u>{" "}
                      or{" "}
                      <u
                        onClick={
                          props.signInToggle
                            ? props.signInToggle
                            : signInToggled
                        }
                        className={`join-signIn-toggle red`}
                      >
                        {" "}
                        Sign In
                      </u>
                    </>
                  )}{" "}
                  • See All Photos & Sold Data - Real Estate Boards Require a
                  Verified Account (Free).
                </span>
              </div>
            </div>
          )}
          {props.isSold ? (
            <>
              {Status === "U" && (
                <>
                  <span className={`property-label forSold `}>{"Sold"}</span>
                  <span className="listingType"></span>
                </>
              )}
            </>
          ) : (
            <>
              {!data.Status && props.isHome && "For " + data.PropertyStatus}
              {data.Status === "U" && data.PropertyStatus == "Lease" && (
                <span className={`property-label forLease `}>Leased</span>
              )}

              {data.Status === "A" && data.PropertyStatus == "Lease" && (
                <span className={`property-label forLease `}>
                  For {data.PropertyStatus}
                </span>
              )}
              {data.Status === "U" && data.PropertyStatus == "Sale" && (
                <span className={`property-label forSold `}>Sold</span>
              )}
              {data.Status === "A" && data.PropertyStatus == "Sale" && (
                <span className={`property-label forSale `}>
                  For {data.PropertyStatus}
                </span>
              )}
              {data.Status === "D" && (
                <span className={`property-label forTer `}>De-listed</span>
              )}
              {/* {
                                PropertyStatus == 'Lease' && <>
                                    <span className={`property-label forLease `}>For {PropertyStatus}</span><span className="listingType"></span></>
                            }
                            {
                                PropertyStatus == 'Sale' && <>
                                    <span className={`property-label forSale `}>For {PropertyStatus}</span><span className="listingType"></span></>
                            } */}
            </>
          )}
          <span className={`heart-label ${LoginReq}`}>
            {props.showIsFav && (
              <i
                className={` ${favIconImg} faviconHover`}
                onClick={favorite}
                aria-hidden="true"
              ></i>
            )}
            {/*<i className="fa fa-heart-o faviconHover" aria-hidden="true"></i>*/}
          </span>

          {img.length > 0 ? (
            <ReactCarousel show={1}>
              {img.map((item) => {
                return (
                  <>
                    {loginRequired ? (
                      <a
                        href={"/propertydetails/" + SlugUrl}
                        className={`featured_multi_img_wrapper ${
                          loginRequired ? "" : "sign-in-required-container  "
                        }`}
                        alt=""
                        title=""
                        onMouseOver={props.highLightCb}
                        dataset={JSON.stringify({
                          id: ListingId,
                          ismap: false,
                        })}
                      >
                        <img
                          src={Constants.image_base_url + item["s3_image_url"]}
                          onMouseOver={props.highLightCb}
                          dataset={JSON.stringify({
                            id: ListingId,
                            ismap: false,
                          })}
                          className={`featured feature_image ${LoginReq}`}
                          alt=""
                          title=""
                          loading="lazy"
                        />
                      </a>
                    ) : (
                      <div
                        className={`${
                          loginRequired ? "" : "sign-in-required-container  "
                        }`}
                      >
                        <a
                          className={`featured_multi_img_wrapper ${
                            loginRequired ? "" : "sign-in-required-container  "
                          }`}
                          alt=""
                          title=""
                          onMouseOver={props.highLightCb}
                          dataset={JSON.stringify({
                            id: ListingId,
                            ismap: false,
                          })}
                        >
                          <img
                            src={
                              Constants.image_base_url + item["s3_image_url"]
                            }
                            onMouseOver={props.highLightCb}
                            dataset={JSON.stringify({
                              id: ListingId,
                              ismap: false,
                            })}
                            className={`featured feature_image ${LoginReq}`}
                            alt=""
                            title=""
                            loading="lazy"
                          />
                        </a>
                      </div>
                    )}
                  </>
                );
              })}
            </ReactCarousel>
          ) : (
            <>
              {loginRequired ? (
                <a
                  href={"/propertydetails/" + SlugUrl}
                  className={`featured_multi_img_wrapper ${
                    loginRequired ? "" : "sign-in-required-container  "
                  }`}
                  alt=""
                  title=""
                  onMouseOver={props.highLightCb}
                  dataset={JSON.stringify({ id: ListingId, ismap: false })}
                >
                  <img
                    src={
                      ImageUrl
                        ? Constants.image_base_url + ImageUrl
                        : Constants.defaultImage
                    }
                    onMouseOver={props.highLightCb}
                    dataset={JSON.stringify({ id: ListingId, ismap: false })}
                    className="featured feature_image"
                    alt=""
                    title=""
                  />
                </a>
              ) : (
                <div
                  className="none-logged-user"
                  onClick={
                    props.signInToggle ? props.signInToggle : signInToggled
                  }
                >
                  <a
                    className={`featured_multi_img_wrapper ${
                      loginRequired ? "" : "sign-in-required-container  "
                    }`}
                    alt=""
                    title=""
                    onMouseOver={props.highLightCb}
                    dataset={JSON.stringify({ id: ListingId, ismap: false })}
                  >
                    <img
                      src={
                        ImageUrl
                          ? Constants.image_base_url + ImageUrl
                          : Constants.defaultImage
                      }
                      onMouseOver={props.highLightCb}
                      dataset={JSON.stringify({ id: ListingId, ismap: false })}
                      className="featured feature_image"
                      alt=""
                      title=""
                    />
                  </a>
                </div>
              )}
            </>
          )}
        </div>

        <div className="wrapperFeature">
          <div className="item bookmark"></div>
          <div className="featuredListingAddress">
            <p
              className={`featuredListingCity ellipsis-cls ${LoginReq} ${
                loginRequired ? "" : "blury1"
              }`}
              title={StandardAddress}
            >
              {StandardAddress.substring(0, 30)}
            </p>
            <span title={Community} className="">
              {City} - {Community ? Community.substring(0, 30) : ""}
            </span>
          </div>
          <div className="price_bedroom_bathroom text-center">
            {getRound(BedroomsTotal) && (
              <span className={`featuredListingBedroom rightBorder`}>
                {getRound(BedroomsTotal) && (
                  <span>{getRound(BedroomsTotal)} BED</span>
                )}{" "}
              </span>
            )}
            {getRound(BathroomsFull) && (
              <span className={`featuredListingBedroom rightBorder`}>
                {getRound(BathroomsFull) && (
                  <span>{getRound(BathroomsFull)} BATH</span>
                )}
              </span>
            )}
            {Sqft ? (
              <span className={`featuredListingBathroom `} title="Area Sqft">
                {Sqft} sqft
              </span>
            ) : (
              <span className="featuredListingBathroom" title="Area Sqft">
                N/A sqft
              </span>
            )}
          </div>
          <div className="price_bedroom_bathroom">
            <ul className="listingTypeDom">
              <li className="first-list">
                <span
                  className={`featuredListingId ${LoginReq} ${
                    loginRequired ? "" : "blury1"
                  }`}
                >
                  MLS#: {ListingId}{" "}
                </span>
              </li>
              <li className="second-list text-center">
                <span className={`subtypeText`} style={{ marginLeft: "-2%" }}>
                  {PropertySubType}
                </span>
              </li>
              <li className="third-list text-right">
                <span className="dom">{getDom(Dom,PropertyStatus)}</span>
              </li>
            </ul>
            {/*<span className="featuredListingId">MLS#: {ListingId} </span>
                    <span className="subtypeText">{PropertySubType}</span>
                    <span className="dom">{getDom(Dom)}</span> */}
          </div>
          <div className="featuredListingPrice">
            <span
              className={`featuredPricePlaceholder ${LoginReq} ${
                loginRequired ? "" : "blury1"
              }`}
            >
              {formatter.format(ListPrice)}
            </span>
          </div>
        </div>
      </div>
      {LoginReq !== "" && (
        <div className="loginRequired">
          <button
            onClick={props.openLoginCb}
            className="custom-button-transparent round "
          >
            <i className="fa fa-lock" aria-hidden="true"></i> Login required
          </button>
        </div>
      )}
    </>
  );
};
export default PropertyCard;
