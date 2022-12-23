"use strict";
import React from "react";
import {
  detailPage,
  similarProperty,
  yelpKey,
  defaultImage,
  image_base_url,
  yelpApi,
  agentId,
  REACT_APP_GOOGLE_API_KEY,
  soldData,
  similarSaleProperty,
  similarRentProperty,
  base_url
} from "../../../constants/GlobalConstants";
import NavHeader from "./NavHearder";
import DetailSection from "./DetailSection";
import API from "../../utility/api";
import ShimmerEffect from "../../Components/ShimmerEffect";
class PropertyDetails extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      details: "",
      breadcrumb: {},
      shareLink: "",
      isLogin: false,
      checkDetailData: false,
      nearByType: "schools",
      amenities: {},
      similarProperty: {},
      soldData: {},
      similarSaleProperty: {},
      loginFilter: false,
      similarRentProperty: {},
      listingHistoryData: [],
    };
    this.fetchProperty = this.fetchProperty.bind(this);
    this.getDataFromYelp = this.getDataFromYelp.bind(this);
    this.fetchSimilarProperty = this.fetchSimilarProperty.bind(this);
    this.getSoldData = this.getSoldData.bind(this);
    this.getSimilarSaleProperty = this.getSimilarSaleProperty.bind(this);
    this.getSimilarRentProperty = this.getSimilarRentProperty.bind(this);
    this.listingHistory = this.listingHistory.bind(this);
  }
  componentDidMount() {
    this.fetchProperty();
    try {
      let stats =
        !this.props.isLoginRequired || this.props.isLogin
          ? ""
          : this.props.togglePopUp();
      let ApiKey = REACT_APP_GOOGLE_API_KEY;
      if (localStorage.getItem("websetting")) {
        let websetting = JSON.parse(localStorage.getItem("websetting"));
        if (websetting.GoogleMapApiKey != null) {
          ApiKey = websetting.GoogleMapApiKey;
        }
      }
      let id = "googleMaps";
      if (document.getElementById(id) === null) {
        const script = document.createElement("script");
        script.setAttribute(
          "src",
          "https://maps.googleapis.com/maps/api/js?key=" + ApiKey
        );
        script.setAttribute("id", id);
        document.body.appendChild(script);
        script.onload = () => {};
      }
    } catch (error) {}
  }

  async fetchSimilarProperty(formData = {}) {
    if (localStorage.getItem("similarList")) {
      return;
    }
    // let details = this.state.details ? this.state.details.details : {};
    let details = this.props.stateData
    ? this.props.stateData.details.details
    : {};
    let payload = {
      Community: details ? details.Community : "",
      Area: details ? details.Area : "",
      PropertySubType: details.Type_own1_out,
      Ml_num: details.Ml_num,
    };
    let payloadForSold = {
      Community: details ? details.Community : "",
      PropertySubType: details.Type_own1_out,
      Ml_num: details.Ml_num,
    };
    this.getSimilarSaleProperty(payload);
    this.getSoldData(payloadForSold);
    this.getSimilarRentProperty(payload);
  }
  async getSoldData(payload) {
    API.jsonApiCall(soldData, payload, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      this.setState({
        soldData: res.soldData,
        apiLoaded: true,
      });
    });
  }
  async getSimilarSaleProperty(payload) {
    API.jsonApiCall(similarSaleProperty, payload, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      this.setState({
        similarSaleProperty: res.similarSale,
      });
    });
  }
  async getSimilarRentProperty(payload) {
    API.jsonApiCall(similarRentProperty, payload, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      this.setState({
        similarRentProperty: res.similarRent,
      });
    });
  }
  async getDataFromYelp(items = {}) {
    let details = this.state.details.details ? this.state.details.details : {};
    if (!details) {
      return;
    }
    let { Latitude, Longitude } = details;
    if (!Latitude && !Longitude) {
      return;
    }
    let type = this.state.nearByType ? this.state.nearByType : "schools";
    let payload = {
      latitude: Latitude,
      longitude: Longitude,
      agentId: agentId,
      type: items.type ? items.type : type,
    };
    try {
      API.jsonApiCall(yelpApi, payload, "POST", null, {
        "Content-Type": "application/json",
      }).then((res) => {
        this.setState({
          amenities: res,
        });
      });
    } catch (error) {}
  }
  async fetchProperty() {
    setTimeout(() => {
      window.scrollTo({
        top: 100,
        behavior: "smooth",
      });
    }, 100);
    this.setState(this.props.stateData);
    if (localStorage.getItem("propertyFetched")) {
      // return;
    }
    localStorage.setItem("propertyFetched", true);
    let meta = {
      title: "property details",
    };
    this.props.setMetaInfo(meta);
    localStorage.removeItem("similarList");
    this.fetchSimilarProperty();
    this.listingHistory({...this.props.stateData.details});
    if (!this.props.isLoginRequired || this.props.isLogin) {
      this.getDataFromYelp();
    }
  }
  componentDidUpdate(prevProps, prevState, snapshot) {
    if (prevProps.isLoginRequired !== this.props.isLoginRequired) {
      if (this.props.isLoginRequired && !this.props.isLogin) {
        if (!this.props.isOpen) {
          try {
            let clsBtn = document.getElementsByClassName("popcloseBtn");
            if (clsBtn.length) {
              clsBtn[0].style.display = "none";
            }
            this.props.togglePopUp();
            this.setState({
              loginFilter: true,
            });
          } catch (error) {}
          this.setState({
            isLogin: false,
          });
        }
      }
    }
    if (prevState.showSchedule) {
      this.setState({
        showSchedule: prevState.showSchedule,
      });
      if (this.props.isLoginRequired && !this.props.isLogin) {
        if (!this.props.isOpen) {
          try {
            let clsBtn = document.getElementsByClassName("popcloseBtn");
            if (clsBtn.length) {
              clsBtn[0].style.display = "none";
            }
            this.props.togglePopUp();
          } catch (error) {}
          this.setState({
            isLogin: false,
          });
        }
      }
    }
  }
  async listingHistory(details = {}) {
    details = details ? details.details : this.state.details.details;
    let payload = {
      Community: details.Community ? details.Community : "",
      Ml_num: details.Ml_num?details.Ml_num:"",
      Municipality: details.Municipality?details.Municipality:"",
      PropertyStatus: details.S_r?details.S_r:"",
      PropertyType: details.PropertyType?details.PropertyType:"",
      Area: details.Area ? details.Area : "",
      Bath_tot: details.Bath_tot?details.Bath_tot:"",
      Br: details.Br?details.Br:"",
      Gar_spaces: details.Gar_spaces?details.Gar_spaces:"",
      price: details.Lp_dol?details.Lp_dol:"",
      Type: details.Type_own1_out?details.Type_own1_out:"",
      Style: details.Style?details.Style:"",
      Addr: details.Addr?details.Addr:"",
    };
    API.jsonApiCall(
      base_url + "api/v1/services/fetchPageViews/listingHistory",
      payload,
      "POST",
      null,
      {
        "Content-Type": "application/json",
      }
    )
      .then((res) => {
        let temArr = [];
        let obj1 = {
          Lp_dol: details.Lp_dol,
          property_insert_time: details.property_insert_time,
          property_last_updated: details.sold_updated_time,
          Status: details.Status,
          S_r: details.S_r,
          Ml_num: details.Ml_num,
          Sp_dol: details.Sp_dol,
          Orig_dol: details.Orig_dol,
          Sp_date: details.Sp_date,
          Timestamp_sql: details.Timestamp_sql,
          LastStatus: details.Lsc,
        };
        if (details.Lsc !== "Sld" && details.Lsc !== "Lsd" || details.Status !== "U") {
          temArr.push(obj1);
        }

        if (res.listData) {
          res.listData.map((item, key) => {
            let ob = {
              Lp_dol: item.ListPrice,
              property_insert_time: item.inserted_time,
              property_last_updated: item.sold_updated_time,
              Status: item.Status,
              S_r: item.PropertyStatus,
              Ml_num: item.ListingId,
              Sp_dol: item.Sp_dol,
              Orig_dol: item.Orig_dol,
              Sp_date: item.Sp_date,
              Timestamp_sql: item.Timestamp_sql,
              LastStatus: item.LastStatus,
            };
            temArr.push(ob);
          });
        }
        this.setState({
          listingHistoryData: temArr,
        });
      })
      .catch((e) => {
        console.log("error", e);
      });
  }
  render() {
    return (
      <div
        className={`propertiesCls ${!this.state.loginFilter ? "" : "filter"}`}
        id="propertiesSection"
      >
        <>
          {this.state.checkDetailData ? (
            <></>
          ) : (
            <NavHeader
              props={this.state}
              shareLink={this.state.shareLink}
              loginPop={this.props.togglePopUp}
              isLogin={this.props.isLogin}
            />
          )}
          {this.state.details ? (
            <div id="propSection">
              <DetailSection
                props={this.state}
                similarProperty={this.state.similarProperty}
                {...this.props}
                checkDetailData={this.state.checkDetailData}
                getYelpData={this.getDataFromYelp}
              />
            </div>
          ) : (
            <ShimmerEffect count={4} />
          )}
        </>
      </div>
    );
  }
}
export default PropertyDetails;
