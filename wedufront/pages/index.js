import React, { useState, useEffect } from "react";
import PropertiesList from "./../components/HomeListSection/PropertiesList";
import API from "../ReactCommon/utility/api";
import SearchIn from "../components/HomeListSection/SearchIn";
import ServicesSection from "../components/ServicesSection/ServicesSection";
import Testimonials from "../components/Testimonials";
import Chart from "./../components/Charts/chart";
import detect from "./../ReactCommon/utility/detect";
import home_data from "../public/json/websetting.json";
import {
  getPropertiesList,
  agentId,
  base_url,
  front_url,
} from "../constants/GlobalConstants";
import Constants from "../constants/GlobalConstants";
import BannerSection from "../components/HomeListSection/BannerSection";
import Head from "next/head";
function Home(props) {
  const [recentListData, setRecentListData] = useState(home_data.recentListing);
  const [featuredListData, setFeaturedListing] = useState([]);
  const [blogs, setBlog] = useState([]);
  const [city, setCityData] = useState(false);
  const [pageSetting, setpageSetting] = useState(
    home_data.pageSetting ? home_data.pageSetting : {}
  );
  const [banner, setBanner] = useState(pageSetting.TopBanner);
  const [arrangeSections, setarrangeSections] = useState(
    home_data.arrangeSections
  );
  const [community, setCommunity] = useState(pageSetting.CommunityBanner);
  const [chartData, setchartData] = useState(home_data.state);
  const [statsLoader, setstatsLoader] = useState(true);
  const [flag, setflag] = useState(true);
  const [settingFlag, setSettingFlag] = useState(false);
  const [resiCount, setResiCount] = useState(home_data.resiCount);
  const [condosCount, setCondosCount] = useState(home_data.condosCount);
  const [soldCount, setSoldCount] = useState(home_data.soldCount);
  const [testimonial, setTestimonial] = useState(false);
  const [shimmer, setshimmer] = useState(true);
  const [propertyLoading, setPropertyLoading] = useState(false);
  const [getFlag, setgetFlag] = useState(true);
  const [checkPage, setCheckPage] = useState(true); //useState(props.webSettingCheck ? props.webSettingCheck : false);
  const [arrangeSectionsState, setArrangeSectionsState] = useState(false);
  const [testimonialLoaded, setTestimonialLoaded] = useState(false);
  // props.setMetaInfo(Constants.pageMeta.home);
  function arrangedSection() {
    let items = "DefaultSection";
    arrangeSections.map((item) => {
      let previvousSection = document.getElementById(items);
      let currrentSection = document.getElementById(item);
      if (currrentSection !== null) {
        previvousSection.insertAdjacentElement("afterend", currrentSection);
        items = item;
      }
    });
  }
  useEffect(() => {
    getTestmonial();
  }, []);
  async function getTestmonial() {
    if(pageSetting.testimonialSection=="show"){
        const body2 = { limit: 6, agentId: agentId };
        const testimonialData = await API.jsonApiCall(
          base_url + "api/v1/services/global/getTestimonials",
          body2,
          "POST",
          null,
          { "Content-Type": "application/json" }
        );
        // console.log("testimonialData",testimonialData.result);
        setTestimonial(testimonialData.result);
    }

    if(pageSetting.blogSection=="show"){
      const blogbody = { AgentId: Constants.agentId };
      const blogdata = await API.jsonApiCall(base_url + "api/v1/services/home/getBlogs",
        blogbody, "POST", null, { "Content-Type": "application/json" }
        );
        setBlog(blogdata);
    }
  }
  let meta = {
    title: "home",
  };
  // props.setMetaInfo(meta);
  return (
    <>
      {/* <Head>
        <meta
          name="og_title"
          property="og:title"
          content={
            "Homes for Sale & Real Estate Get Listings in Canada | Wedu "
          }
        />
        <meta
          name="og:description"
          content={
            "Homes for Sale & Real Estate Get Listings in Canada | Wedu | DESCRIPTION"
          }
        />
        <meta
          name="og_image"
          property="og:image"
          content={
            "https://panel.wedu.ca//storage/banner_webp/62d1360d30497.webp"
          }
        />
        <meta
          name="og:image:alt"
          content={
            "Homes for Sale and Real Estate Get Listings in Canada|  IMAGE ALT"
          }
        />
      </Head> */}
      <div id="DefaultSection"></div>
      <div
        id="bannerSection"
        className={
          pageSetting && pageSetting.topBannerSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <BannerSection
          banner={banner}
          resiCount={resiCount}
          condosCount={condosCount}
          soldCount={soldCount}
        />
      </div>
      <div
        id="recentSection"
        className={
          pageSetting && pageSetting.recentSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <div className="featureListing">
          <PropertiesList
            showIsFav={true}
            openUserPopup={true}
            data={recentListData}
            openLoginCb={props.togglePopUp}
            headerText={"Recent Listings"}
            isLogin={props.isLogin}
            isLoading={propertyLoading}
            isHome={true}
          />
        </div>
      </div>
      <div
        id="featuredSection"
        className={
          pageSetting && pageSetting.featuredSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <div className="featureListing">
          <PropertiesList
            data={featuredListData}
            showIsFav={true}
            openUserPopup={true}
            openLoginCb={props.togglePopUp}
            headerText={"Featured Listings"}
            isLogin={props.isLogin}
            isLoading={propertyLoading}
            isHome={true}
          />
        </div>
      </div>
      <div
        id="citySection"
        className={
          pageSetting && pageSetting.citySection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        {city && (
          <SearchIn
            cityData={city}
            headerText={"Search in City"}
            isLoading={propertyLoading}
          />
        )}
      </div>
      <div
        id="blogSection"
        className={
          pageSetting && pageSetting.blogSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <ServicesSection
          blogs={blogs}
          heading={"Real Estate Services"}
          isLoading={propertyLoading}
        />
      </div>
      <div
        id="testimonialSection"
        className={
          testimonial && pageSetting && pageSetting.testimonialSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <Testimonials Testimonials={testimonial} isLoading={propertyLoading} 
        />
      </div>
      <div
        id="htmlContentSection"
        className={
          pageSetting && pageSetting.contentSection == "show"
            ? "showContent"
            : "hidden"
        }
      >
        <div
          className="container"
          dangerouslySetInnerHTML={{ __html: pageSetting.htmlContent }}
        ></div>
      </div>
      <div id="stats">
        <Chart chartData={chartData} statsLoader={statsLoader} />
      </div>
    </>
  );
}
// export async function getStaticProps() {
//   const result = await API.jsonApiCall(
//     getPropertiesList,
//     {},
//     "GET",
//     null,
//     {
//       "Content-Type": "application/json",
//     },
//     { propertyList: 0, featuredListing: 1, recentListing: 1, agentId }
//   )
//   const body2 = { limit: 6, agentId: agentId };
//   const res = API.jsonApiCall(base_url + "api/v1/services/home/getCities",
//     body2, "POST", null, { "Content-Type": "application/json" }
//   )
//   let citydata = "";
//   if (res.city) {
//     citydata = res;
//   }
//   let date = detect.isMobile() ? 6 : 12;
//   let body4 = { AgentId: Constants.agentId, date: date }
//   let chartUrl = base_url;
//   const chartsData = await API.jsonApiCall(chartUrl + "api/v1/services/global/homeStats",
//     body4, "POST", {}
//   );
//   const body = { AgentId: Constants.agentId };
//   const blogdata = await API.jsonApiCall(base_url + "api/v1/services/home/getBlogs",
//     body, "POST", null, { "Content-Type": "application/json" }
//   );
//   const testimonial = await API.jsonApiCall(base_url + "api/v1/services/global/getTestimonials",
//     body2, "POST", null, { "Content-Type": "application/json" }
//   );
//   return {
//     props: {
//       recentListData: result.recentListing,
//       featuredListData: result.featuredListing,
//       ResiCount: result.resiCount,
//       CondosCount: result.condosCount,
//       SoldCount: result.soldCount,
//       CityData: citydata,
//       chartsData: chartsData,
//       blogdata: blogdata,
//       testData: testimonial.result
//     },
//     revalidate: 3600, //3600 = 1 hr , 2592000 = 30 days
//   };
// }
export default Home;
