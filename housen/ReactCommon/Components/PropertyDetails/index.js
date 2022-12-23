import React from "react";
import {
  detailPage,
  yelpApi,
  agentId,
  REACT_APP_GOOGLE_API_KEY,
  base_url,
} from "../../../constants/Global";
import DetailSection from "./DetailSection";
import API from "../../utility/api";
import ShimmerEffect from "../../Components/ShimmerEffect";
class PropertyDetails extends React.Component {
  constructor(props) {
    super(props);
    this.scrollTop = React.createRef();
    this.state = {
      details: "",
      breadcrumb: {},
      shareLink: "",
      isLogin: false,
      checkDetailData: false,
      nearByType: "schools",
      amenities: {},
      schools: {},
      similarProperty: {},
      listingHistoryData: [],
      ShimmerState: false,
      apiCalled: false,
    };
    this.fetchProperty = this.fetchProperty.bind(this);
    this.getDataFromYelp = this.getDataFromYelp.bind(this);
    this.fetchSimilarProperty = this.fetchSimilarProperty.bind(this);
    this.listingHistory = this.listingHistory.bind(this);
    this.updateMap = this.updateMap.bind(this);
  }
  componentDidMount(e) {
    try {
      this.setState(
        {
          apiCalled: true,
        },
        () => {
          localStorage.removeItem("slUrl");
          localStorage.removeItem("mainUrl");
          this.fetchProperty();
          this.updateMap();
        }
      );
    } catch (error) {}
  }
  componentDidUpdate(prevProps, prevState, snapshot) {
    try {
      if (prevProps.Routers.asPath === "/propertydetails/[slug]") {
        return;
      }
      if (
        prevProps.Routers.asPath &&
        this.props.Routers.asPath !== prevProps.Routers.asPath
      ) {
        let mainUrl = localStorage.getItem("mainUrl");
        let links = this.props.Routers.asPath;
        if (links.includes(mainUrl)) {
          return;
        }
        this.fetchProperty();
        this.updateMap();
      }
      if (prevState.showSchedule) {
        this.setState({
          showSchedule: prevState.showSchedule,
        });
      }
      if (prevProps.isLoginRequired !== this.props.isLoginRequired) {
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
    } catch (error) {}
  }
  async updateMap() {
    try {
      if (!this.props.isLoginRequired || !this.props.isLogin) {
        if (!this.props.isOpen) {
          // this.props.togglePopUp()
        }
      } else {
      }
      let stats =
        !this.props.isLoginRequired || this.props.isLogin
          ? ""
          : this.props.togglePopUp();
      this.props.pageName("property details");
      let ApiKey = REACT_APP_GOOGLE_API_KEY;
      let webSettings = localStorage.getItem("websetting");
      if (webSettings) {
        webSettings = JSON.parse(webSettings);
        if (webSettings.GoogleMapApiKey != null) {
          ApiKey = webSettings.GoogleMapApiKey;
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
        script.onload = () => {
        };
      }
      
    } catch (error) {}
  }
  async listingHistory(details = {}) {
    details = details ? details : this.state.details.details;
    let payload = {
      Community: details.Community ? details.Community : "",
      Ml_num: details.Ml_num,
      Municipality: details.Municipality,
      PropertyStatus: details.S_r,
      PropertyType: details.PropertyType,
      Area: details.Area ? details.Area : "",
      Bath_tot: details.Bath_tot,
      Br: details.Br,
      Gar_spaces: details.Gar_spaces,
      price: details.Lp_dol,
      Type: details.Type_own1_out,
      Style: details.Style,
      Addr: details.Addr,
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
        if (
          (details.Lsc !== "Sld" && details.Lsc !== "Lsd") ||
          details.Status !== "U"
        ) {
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
  async fetchSimilarProperty(details = {}) {
    localStorage.setItem("similarList", true);
    details = details ? details : this.state.details.details;
    let payload = {
      Community: details.Community ? details.Community : "",
      Area: details.Area ? details.Area : "",
      Ml_num: details.Ml_num,
      Bath_tot: details.Bath_tot,
      Br: details.Br,
      Gar_spaces: details.Gar_spaces,
      Status: details.S_r,
      price: details.Lp_dol,
      Type: details.Type_own1_out,
      property_status: details.Status,
      Style: details.Style,
    };
    const requestOptions = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    };
    fetch(
      base_url + "api/v1/services/fetchPageViews/similarProperty",
      requestOptions
    )
      .then((response) => response.text())
      .then((res) => {
        setTimeout(() => {
          localStorage.removeItem("similarList");
        }, 1000);
        this.setState({
          similarProperty: JSON.parse(res),
        });
      })
      .catch((e) => {
        console.log("error", e);
      });
  }
  async getDataFromYelp(items = {}) {
    try {
      let details = this.state.details.details
        ? this.state.details.details
        : {};
      if (!details) {
        return;
      }
      let { Latitude, Longitude } = details;
      if (!Latitude && !Longitude) {
        return;
      }
      // Latitude=43.74447;
      // Longitude=-79.29238;
      let type = this.state.nearByType ? this.state.nearByType : "schools";
      let payload = {
        latitude: Latitude,
        longitude: Longitude,
        agentId: agentId,
        type: items.type ? items.type : type,
      };
      API.jsonApiCall(yelpApi, payload, "POST", null, {
        "Content-Type": "application/json",
      }).then((res) => {
        if (type === "schools") {
          let secondary = [];
          let elementory = [];
          if (res.businesses && res.businesses.length) {
            res.businesses.map((item, key) => {
              if (item.categories[0]) {
                if (item.categories[0].alias === "elementaryschools") {
                  elementory.push(item);
                } else {
                  secondary.push(item);
                }
              }
              //
            });
          }
          this.setState({
            schools: {
              secondary: secondary,
              elementory: elementory,
            },
          });
        }
        this.setState({
          amenities: res,
        });
      });
    } catch (error) {}
  }

  async fetchProperty() {
    
    this.scrollTop.current.scrollTo(0, 0);
    setTimeout(() => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    }, 200);
    // setTimeout(() => {
    //   this.setState({
    //     ShimmerState: true,
    //   });
    // }, 550);
    let hrefUrl = window.location.href;
    let slug1 = hrefUrl.replace(
      window.location.origin + "/propertydetails/",
      ""
    );
    localStorage.removeItem("similarList");
    // similarProperty
    const {slug,details,breadcrumb,shareLink,checkDetailData,isSold}=this.props.stateData
    this.setState({
      details: details,
      slug: slug,
      breadcrumb: breadcrumb,
      shareLink: shareLink,
      checkDetailData: checkDetailData,
      ShimmerState: false,
      isLogin: false,
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
        // this.props.togglePopUp();
      }
    }
    this.listingHistory(details.details);
    this.fetchSimilarProperty(details.details);
    this.getDataFromYelp();
    


    let payload = {};
    payload.SlugUrl = slug;
    let title = slug.replace("-", " ");
return;
    API.jsonApiCall(detailPage, payload, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        let link = [
          { text: "Home", link: "/" },
          { text: res.details ? res.details.Addr : "", link: "" },
        ];

        let meta = {
          title: "property details",
          slug: title,
          metaTitle: res.details ? res.details.Addr : "details",
          metaDesc: res.metaDesc,
          metaKeyword: "Housen For Sale , Housen Finder , Housen for Rent",
        };
        this.props.setMetaInfo(meta);
        if (this.props.isLoginRequired && !this.props.isLogin) {
          if (!this.props.isOpen) {
            try {
              let clsBtn = document.getElementsByClassName("popcloseBtn");
              if (clsBtn.length) {
                clsBtn[0].style.display = "none";
              }
              this.props.togglePopUp();
            } catch (error) {}
            // this.props.togglePopUp();
          }
        }
        if (res.details) {
          if (!localStorage.getItem("mainUrl")) {
            localStorage.setItem("mainUrl", slug);
          }
        }
        setTimeout(() => {
          localStorage.removeItem("mainUrl");
        }, 30000);
        this.setState({
          details: res,
          slug: slug,
          breadcrumb: link,
          shareLink: hrefUrl,
          checkDetailData: res.details ? false : true,
          ShimmerState: false,
          isLogin: false,
        });
        if (!this.props.isLoginRequired || this.props.isLogin) {
          this.getDataFromYelp();
        } else {
          // this.props.togglePopUp();
        }
        let fl = localStorage.getItem("similarList");
        if (!fl) {
          this.fetchSimilarProperty(res.details);
          this.listingHistory(res.details);
        }
      })
      .catch((err) => {
        this.setState({
          ShimmerState: false,
        });
      });
  }
  render() {
    
    return (
      <>
       
        <div
          ref={this.scrollTop}
          className={`propertiesCls ${
            !this.props.isLoginRequired || this.props.isLogin ? "" : "filter"
          }`}
          id="propertiesSection"
        >
          <>
            {this.state.details && !this.state.ShimmerState ? (
              <div id="propSection">
                <DetailSection
                  props={this.state}
                  schools={this.state.schools}
                  {...this.props}
                  checkDetailData={this.state.checkDetailData}
                  getYelpData={this.getDataFromYelp}
                  shareLink={this.state.shareLink}
                />
              </div>
            ) : (
              <>
                <ShimmerEffect count={4} />
              </>
            )}
          </>
          {this.state.ShimmerState && <ShimmerEffect count={4} />}
        </div>
      </>
    );
  }
}

export default PropertyDetails;


// git check
