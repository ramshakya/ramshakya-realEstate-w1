import React from "react";
import HeadingSection from "../components/Home/HeadingSection";
import Listings from "../components/Home/Listings";
import SearchIn from "../components/Home/SearchIn";
import ArticleSection from "../components/Home/ArticleSection";
import Preference from "../components/Home/Preference";
import Constants from "../constants/Global";
import { getPropertiesList } from "../constants/Global";
import { useState, useEffect } from "react";
import API from "../ReactCommon/utility/api";
import Chart from "./../components/Charts/chart";
import Loader1 from "../components/loader/loader1.js";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import { useQuery } from "react-query";
import { useRouter } from "next/router";
import Homevalue from "../components/Home/Homevalue";
import detect from "./../ReactCommon/utility/detect";
import home_data from "../public/json/websetting.json";
function Home(props) {
  const router = useRouter();
  // let home_data = [];
  // const [home_data,setHome_data] = useState([]);
  const [featuredListing, setFeaturedListData] = useState([]);
  const [recentListData, setRecentListData] = useState([]);
  const [article, setBlogData] = useState([]);
  const [soldProperty, setSoldProperties] = useState([]);
  const [cityData, setCity] = useState(false);
  const [flag, setFlag] = useState(true);
  const [flag2, setFlag2] = useState(false);
  const [imageData, setImageData] = useState([]);
  const [featuredImageData, setFeaturedImage] = useState([]);
  const [soldImageData, setSoldImage] = useState([]);
  const [chartData, setChartData] = useState([]);

  const [pageSetting, setPageSetting] = useState([]);
  const [banner, setBanner] = useState("");
  const [arrangeSections, setArrangeSections] = useState([]);
  const [image, setImage] = useState(pageSetting.CommunityBanner);
  const [userId, setUserId] = useState("");
  const [sectionFlag, setSectionFlag] = useState(false);
  const [shimmer, setShimmer] = useState(true);
  const [userDetails, setUserDetails] = useState(props.userDetails);
  const [checkPage, setCheckPage] = useState(
    props.webSettingCheck ? props.webSettingCheck : false
  );
  const [arrangeSectionsState, setArrangeSectionsState] = useState(true);
  const [emailIsVerified, setEmailVerified] = useState(props.emailIsVerified);
  function getHomePage() {
    // API.jsonApiCall(Constants.base_url + "api/v1/services/home/getWebsettings",
    //         '', "POST", {}
    //     ).then((res)=>{
    // setHome_data(res);
    if (localStorage.getItem("home_page")) {
      let res = JSON.parse(localStorage.getItem("home_page"));
      setRecentListData(res.recentProperty);
      setBlogData(res.blog);
      setSoldProperties(res.soldProperty);
      setImageData(res.recentPropertyimages);
      setSoldImage(res.soldPropertyimages);
      setChartData(res.state);
      setPageSetting(res.pageSetting);
      setArrangeSections(res.arrangeSections);
      setBanner(res.pageSetting.TopBanner);
      setShimmer(false);
    }
    // });
  }
  useEffect(() => {
    // home_page
    getHomePage();
  }, [props.webSettingCheck]);
  useEffect(() => {
    if (props.emailIsVerified) {
      setEmailVerified(props.emailIsVerified);
    }
  }, [props.emailIsVerified]);
  // load this section after websettings call order no-2
  // useEffect(() => {
  //     if (props.webSettingCheck) {
  //         // if (localStorage.getItem('HomePageSetting')) {
  //         //     let setting = JSON.parse(localStorage.getItem('HomePageSetting'));
  //         //     setPageSetting(setting);
  //         //     setArrangeSections(JSON.parse(localStorage.getItem('arrangeSections')));
  //             setBanner(pageSetting.TopBanner);
  //             setImage(pageSetting.CommunityBanner);
  //         //     setShimmer(false);
  //         // }

  //         setArrangeSectionsState(true);
  //     }
  // }, [props.webSettingCheck]);
  useEffect(() => {
    let user = "";
    if (localStorage.getItem("userDetail")) {
      let user_detail = JSON.parse(localStorage.getItem("userDetail"));
      user = user_detail.login_user_id;
      setUserId(user);
    }

    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
    const body = { AgentId: Constants.agentId, housenProject: 1, userId: user };
    // const getBlogs = async () => {
    //     const blogs = await API.jsonApiCall(Constants.base_url + "api/v1/services/home/getBlogs",
    //         body, "POST", {}
    //     );
    //     setBlogData(blogs);
    // }
    // const getCity=async()=>{
    //     const body2 = {    limit:6,

    //                        agentId: Constants.agentId
    //                     };
    //     const cities = await API.jsonApiCall(Constants.base_url + "api/v1/services/home/getCities",
    //         body2, "POST" ,{}
    //     );
    //     let city="";
    //     if(cities.city){
    //         city=cities;
    //     }
    //     setCityData(city);
    // }
    const getSold = async () => {
      const sold_result = await API.jsonApiCall(
        Constants.base_url + "api/v1/services/global/soldListings",
        body,
        "POST",
        {}
      );
      let sold = "";
      if (sold_result.result) {
        sold = sold_result.result;
      }
      setSoldProperties(sold_result["property"]);
      setSoldImage(sold_result["images"]);
    };
    // const getTestimonial = async()=>{
    //     const testbody = { limit: 6, agentId: Constants.agentId };
    //     const test = await API.jsonApiCall(Constants.base_url + "api/v1/services/global/getTestimonials",
    //         testbody, "POST" ,{}
    //     );
    //     setTestimonial(test.result);
    // }
    const getData = async () => {
      const recentMlsListing = await API.jsonApiCall(
        Constants.base_url + "api/v1/services/global/recentMlsListing",
        body,
        "POST",
        {}
      );
      setRecentListData(recentMlsListing["property"]);
      setImageData(recentMlsListing["images"]);
    };

    if (props.isLogin && flag) {
      getData();
      getSold();
      setFlag(false);
      setFlag2(true);
    }
    
  }, [flag, props.isLogin]);
  useEffect(() => {
    arrangedSection();
    // console.clear();
  },);
  useEffect(() => {
    if (flag2) {
      getChart();
      setFlag2(false);
    }
  }, [flag2]);
  const getChart = async () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
    // let date = detect.isMobile() ? 6 : 12;
    // let date = 6;
    // let body = { AgentId: Constants.agentId, date: date }
    // let chartUrl = Constants.base_url;
    // const chartsData = await API.jsonApiCall(chartUrl + "api/v1/services/global/homeStats",
    //     body, "POST", {}
    // );
    // setChartData(chartsData);
  };
  const arrangedSection = () => {
    let items = "DefaultSection";
    arrangeSections.map((item) => {
      let previvousSection = document.getElementById(items);
      let currrentSection = document.getElementById(item);
      if (currrentSection !== null) {
        previvousSection.insertAdjacentElement("afterend", currrentSection);
        items = item;
      }
    });
  };

  function changeFlag() {
    setFlag(true);
  }
  function verifyEmail() {
    localStorage.setItem("verifyemail", true);
    router.push("/profile");
  }
  function signInToggle() {
    if (props.isLogin) {
      verifyEmail();
    } else {
      props.togglePopUp();
    }
  }
  return (
    <>
      {/* {shimmer &&
                <ShimmerEffect columnCls={"col-lg-12"} count={5} />
            } */}
      {pageSetting && pageSetting.topBannerSection == "hide" ? (
        <div className="paddingTop-50"></div>
      ) : (
        ""
      )}
      <div id="DefaultSection"></div>
      <div id="bannerSection">
        {pageSetting && pageSetting.topBannerSection == "show" ? (
          <div>
            <HeadingSection banner={Constants.base_url + banner} />
            <Preference changeFlag={changeFlag} userId={userId} {...props} />
          </div>
        ) : (
          ""
        )}
      </div>

      {!shimmer ? (
        <></>
      ) : (
        <>
          <ShimmerEffect columnCls={"col-lg-12"} type={"cardView"} count={6} />
        </>
      )}

      {recentListData && (
        <div
          id="recentSection"
          className={`${detect.isMobile() ? "mt-1" : ""}`}
        >
          {pageSetting && pageSetting.recentSection === "show" ? (
            <Listings
              {...props}
              heading="TODAY'S LISTINGS"
              propertyData={recentListData}
              imageData={imageData}
            />
          ) : (
            ""
          )}
        </div>
      )}

      {cityData && (
        <div id="citySection">
          {pageSetting && pageSetting.citySection === "show" ? (
            <SearchIn cityData={cityData} />
          ) : (
            ""
          )}
        </div>
      )}
      {soldProperty && (
        <div id="citySection">
          {pageSetting && pageSetting.citySection === "show" ? (
            <Listings
              {...props}
              heading="SOLD PROPERTIES"
              propertyData={soldProperty}
              imageData={soldImageData}
              isSold={true}
              LoginRequired={0}
              signInToggle={signInToggle}
            />
          ) : (
            ""
          )}
        </div>
      )}
      <div id="communitySection">
        {pageSetting && pageSetting.communityBannerSection == "show" ? (
          <div></div>
        ) : (
          ""
        )}
      </div>

      {article && (
        <div id="blogSection">
          {pageSetting && pageSetting.blogSection == "show" ? (
            <ArticleSection article={article} />
          ) : (
            ""
          )}
        </div>
      )}
      <div id="testimonialSection">
        {pageSetting && pageSetting.testimonialSection == "show" ? (
          <div></div>
        ) : (
          ""
        )}
      </div>
      <div id="contectFormSection">
        {pageSetting && pageSetting.contectFormSection == "show" ? (
          <div></div>
        ) : (
          ""
        )}
      </div>
      <div id="htmlContentSection">
        {pageSetting && pageSetting.contentSection == "show" ? (
          <div
            dangerouslySetInnerHTML={{ __html: pageSetting.htmlContent }}
          ></div>
        ) : (
          ""
        )}
      </div>
      <div id="gotoHomevaluation" className=""></div>
      <div className="mt-5">{arrangeSectionsState ? <Homevalue /> : ""}</div>
      <div className="mt-5">
        {chartData ? (
          <Chart
            statsLoader={!shimmer}
            chartData={chartData}
            emailIsVerified={emailIsVerified}
            signInToggle={signInToggle}
          />
        ) : (
          <>
            <Loader1 />
          </>
        )}
      </div>
    </>
  );
}

export default Home;
