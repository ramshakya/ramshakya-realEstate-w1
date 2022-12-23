// const front_url = "http://localhost:3000";
// const base_url = "http://3.96.220.206/"; // original url
const front_url = "https://housen.ca/";

const base_url = "https://admin.housen.ca/";
const image_base_url = base_url + "storage/";
// const base_url = "http://127.0.0.1:8000/";
// const image_base_url = "";
const propertySearchApi = base_url + "api/v1/services/search/propertiesSearch";
const mapSearchListApi = base_url + "api/v1/services/search/mapSearchList";
const mapBoundaryApi = base_url + "api/v1/services/search/mapBoundary";
const mapSearchTotalApi = base_url + "api/v1/services/search/mapSearchTotal";
const mapSearchMarkersApi = base_url + "api/v1/services/search/mapSearchMarkers";
const HomepropertyApi = base_url + "api/v1/services/home/propertyList";
const saveEventApi = base_url + "api/v1/agent/events/addSlots";
const slotsApi = base_url + "api/v1/agent/events/getSlots";
const debug_log = true; //print console log
const defaultImage = "/images/comingSoon.jpg";
const mapDefaultImage = "/images/comingSoon.jpg";
const detailPage = base_url + "api/v1/services/fetchPageViews/propertyDetails";
const yelpApi = base_url + "api/v1/services/search/getDataFromYelp";
const shareEmailApi = base_url + "api/v1/services/shareEmail";
const saveSearchApi = base_url + "api/v1/services/saveSearch";
const getWatchProperty = base_url + "api/v1/services/getWatchProperty";
const accessToken = "pk.eyJ1Ijoic2FnYXJ2ZXJtYWl0ZGV2ZWxvcGVyIiwiYSI6ImNraTFiOTA1NTB4anMyeXFoZ2hxZHhuazEifQ.gQOe35Xknut_JqBXHqOaMQ";
const mapStyle = "mapbox://styles/mapbox/streets-v9";
const markerApi = base_url + "api/v1/services/markerInfo";
const contactUsApi = base_url + "api/v1/services/ContactUsForm";
const feedbackApi = base_url + "api/v1/services/FeedbackForm";
const filterDataApi = base_url + "api/v1/services/bootstrap/filterData";
const autoSuggestionApi = base_url + "api/v1/services/search/suggestionSearch";
const extra_url = base_url + "api/v1/services/";
const featuredListApi = base_url + "api/v1/services/global/featuredMlsListing";
const featuredApi = base_url + "api/v1/services/global/featuredList";
const recentListApi = base_url + "api/v1/services/global/recentMlsListing";
const leadFormApi = base_url + "api/v1/services/leadForm";
const agentId = 2;  /// change by ram 
const favUrl = base_url + "api/v1/services/global/fav-property"
const getPropertiesList = base_url + "api/v1/services/global/getProperties";
const similarProperty = base_url + "api/v1/services/fetchPageViews/similarProperty";

const APP_NAME = "Housen.ca";
// const REACT_APP_GOOGLE_API_KEY = "AIzaSyDG5Md4sE3QWkq9bPOrpypRj0wsXnoa4ZY";
// const yelpKey = "X3ASKQfAYvSj1IkY_wx307yfoxqfaTpDHxH0xqUlRcD_fkXwj73-K9wWzCHUDjVxMLAiC_ho0qoBy0AJVg7q0vJ3-6KMExTzPPFCMf3JJE0l0lmArUEXisGP7eDhX3Yx";
// const googleClientId = "672088791066-battt8l4pa8bmosdk48u5368b45cvmkk.apps.googleusercontent.com"
// const fbAppId = "943838539830151";

const marketStatsFilterData = base_url + "api/v1/services/global/marketStatsFilterData";
const marketStatsCitiesData = base_url + "api/v1/services/global/marketStatsCitiesData";
const medianAvgDomApi = base_url + "api/v1/services/global/domAvgMedian";
const soldActive = base_url + "api/v1/services/global/soldActive";
const medianRentalApi = base_url + "api/v1/services/global/medianRental";
const propertyTyprDist = base_url + "api/v1/services/global/propertyTyprDist";
const absorptionData = base_url + "api/v1/services/global/absorptionData";
// const image_base_url = "";
const initialPropertySearchFilter = {
  text_search: "",
  propertySubType: [],
  basement: [],
  features: [],
  price_min: "",
  price_max: "",
  propertyType: "Residential",
  beds: "",
  baths: "",
  status: "",
  sort_by: "",
  curr_page: "",
  openhouse: "",
  Dom: "90",
  Sqft: "",
  shape: "",
  curr_path_query: "",
  City: "",
};
const minPriceConstant = [
  { text: "No min", value: "" },
  { value: "25000", text: "$25k" },
  { value: "35000", text: "$35k" },
  { value: "45000", text: "$45k" },
  { value: "75000", text: "$75k" },
  { value: "100000", text: "$100k" },
  { value: "150000", text: "$150k" },
  { value: "200000", text: "$200k" },
  { value: "250000", text: "$250k" },
  { value: "300000", text: "$300k" },
  { value: "350000", text: "$350k" },
  { value: "400000", text: "$400k" },
  { value: "450000", text: "$450k" },
  { value: "500000", text: "$500k" },
  { value: "550000", text: "$550k" },
  { value: "600000", text: "$600k" },
  { value: "650000", text: "$650k" },
  { value: "700000", text: "$700k" },
  { value: "750000", text: "$750k" },
  { value: "800000", text: "$800k" },
  { value: "850000", text: "$850k" },
  { value: "900000", text: "$900k" },
  { value: "950000", text: "$950k" },
  { value: "1000000", text: "$1M" },
  { value: "1500000", text: "$1.5M" },
  { value: "2000000", text: "$2M" },
  { value: "2500000", text: "$2.5M" },
  { value: "3000000", text: "$3M" },
  { value: "3500000", text: "$3.5M" },
  { value: "4000000", text: "$4M" },
  { value: "4500000", text: "$4.5M" },
  { value: "5000000", text: "$5M" },
  { value: "5500000", text: "$5.5M" },
  { value: "6000000", text: "$6M" },
  { value: "6500000", text: "$6.6M" },
  { value: "7000000", text: "$7M" },
  { value: "7500000", text: "$7.5M" },
  { value: "8000000", text: "$8M" },
  { value: "8500000", text: "$8.5M" },
  { value: "9000000", text: "$9M" },
  { value: "9500000", text: "$9.5M" },
  { value: "10000000", text: "$10M" },
  { value: "12000000", text: "$12M" },
  { value: "13000000", text: "$13M" },
  { value: "15000000", text: "$15M" },
  { value: "20000000", text: "$20M" },
  { value: "25000000", text: "$25M" },
  { value: "30000000", text: "$30M" },
  { value: "40000000", text: "$40M" },
  { value: "50000000", text: "$50M" },
];

const maxPrice = [
  { value: "", text: "No max" },
  { value: "25000", text: "$25k" },
  { value: "35000", text: "$35k" },
  { value: "45000", text: "$45k" },
  { value: "75000", text: "$75k" },
  { value: "100000", text: "$100k" },
  { value: "150000", text: "$150k" },
  { value: "200000", text: "$200k" },
  { value: "250000", text: "$250k" },
  { value: "300000", text: "$300k" },
  { value: "350000", text: "$350k" },
  { value: "400000", text: "$400k" },
  { value: "450000", text: "$450k" },
  { value: "500000", text: "$500k" },
  { value: "550000", text: "$550k" },
  { value: "600000", text: "$600k" },
  { value: "650000", text: "$650k" },
  { value: "700000", text: "$700k" },
  { value: "750000", text: "$750k" },
  { value: "800000", text: "$800k" },
  { value: "850000", text: "$850k" },
  { value: "900000", text: "$900k" },
  { value: "950000", text: "$950k" },
  { value: "1000000", text: "$1M" },
  { value: "1500000", text: "$1.5M" },
  { value: "2000000", text: "$2M" },
  { value: "2500000", text: "$2.5M" },
  { value: "3000000", text: "$3M" },
  { value: "3500000", text: "$3.5M" },
  { value: "4000000", text: "$4M" },
  { value: "4500000", text: "$4.5M" },
  { value: "5000000", text: "$5M" },
  { value: "5500000", text: "$5.5M" },
  { value: "6000000", text: "$6M" },
  { value: "6500000", text: "$6.6M" },
  { value: "7000000", text: "$7M" },
  { value: "7500000", text: "$7.5M" },
  { value: "8000000", text: "$8M" },
  { value: "8500000", text: "$8.5M" },
  { value: "9000000", text: "$9M" },
  { value: "9500000", text: "$9.5M" },
  { value: "10000000", text: "$10M" },
  { value: "12000000", text: "$12M" },
  { value: "13000000", text: "$13M" },
  { value: "15000000", text: "$15M" },
  { value: "20000000", text: "$20M" },
  { value: "25000000", text: "$25M" },
  { value: "30000000", text: "$30M" },
  { value: "40000000", text: "$40M" },
  { value: "50000000", text: "$50M" },
];

const filterBeds = [
  { value: "Any", text: "Any" },
  { value: "1", text: "1 +" },
  { value: "2", text: "2 +" },
  { value: "3", text: "3 +" },
  { value: "4", text: "4 +" },
  { value: "5", text: "5 +" },
  { value: "6", text: "6 +" },
  { value: "7", text: "7 +" },
];

const filterBaths = [
  { value: "Any", text: "Any" },
  { value: "1", text: "1 +" },
  { value: "2", text: "2 +" },
  { value: "3", text: "3 +" },
  { value: "4", text: "4 +" },
  { value: "5", text: "5 +" },
  { value: "6", text: "6 +" },
  { value: "7", text: "7 +" },
];

const propertyStatus = [
  { value: "", text: "Status" },
  { value: "Sale", text: "Sale" },
  { value: "Lease", text: "Lease" },
  { value: "Sold", text: "Sold" },
  { value: "Rented", text: "Rented" },
];

const sortStatus = [
  { value: 'price_low', text: 'Price (Lo-Hi)' },
  { value: 'price_high', text: 'Price (Hi-Lo)' },
  { value: 'dom_high',  text: 'DOM (New-Old)' },
  { value: 'dom_low', text: 'DOM (Old-New)' },
];

let blogsCat = [
  {
    text: "BLOG HOME",
    value: "BLOG HOME"
  },
  {
    text: "MARKET NEWS",
    value: "MARKET NEWS"
  },
  {
    text: "FOR BUYERS",
    value: "FOR BUYERS"
  },
  {
    text: "FOR SELLERS",
    value: "FOR SELLERS"
  },
  {
    text: "FOR RENTERS",
    value: "FOR RENTERS"
  },
  {
    text: "PRE-CONSTRUCTION",
    value: "PRE CONSTRUCTION"
  },
  {
    text: "FREE GUIDES",
    value: "FREE GUIDES"
  }
];

const Dom = [
  { value: "1", text: "Last 1 days" },
  { value: "3", text: "Last 3 days" },
  { value: "7", text: "Last 7 days" },
  { value: "30", text: "Last 30 days" },
  { value: "60", text: "Last 60 days" },
  { value: "90", text: "Last 90 days" },
  { value: "", text: "Listing date-All" },
  { value: "15+", text: "More than 15 days" },
  { value: "30+", text: "More than 30 days" },
  { value: "60+", text: "More than 60 days" },
  { value: "90+", text: "More than 90 days" },

];
const SubType = [
  "Detached",
  "Semi-Detached",
  "Freehold Townhouse",
  "Condo Townhouse",
  "Condo Apt",
  "Link",
  "Duplex",
  "Vacant Land",
];
const metaContent = {
  title: "Housen",
  metaTitle: "Housen For Sale , Housen Finder , Housen for Rent",
  metaDescs: "Are you searching for a home for sale? Housen brings you the latest listings for Home with Interactive Map Search and Latest Listings Daily. ",
  keywords: "Housen For Sale , Housen Finder , Housen for Rent",
  author: "Housen",
}
const dom = Dom;
const propSqft = [{ "value": "0-99", "text": "0-99 Sq.ft" }, { "value": "100-199", "text": "100-199 Sq.ft" }, { "value": "200-299", "text": "200-299 Sq.ft" }, { "value": "300-399", "text": "300-399 Sq.ft" }, { "value": "400-499", "text": "400-499 Sq.ft" }, { "value": "500-599", "text": "500-599 Sq.ft" }, { "value": "600-699", "text": "600-699 Sq.ft" }, { "value": "700-799", "text": "700-799 Sq.ft" }, { "value": "800-899", "text": "800-899 Sq.ft" }, { "value": "900-999", "text": "900-999 Sq.ft" }, { "value": "1000-1199", "text": "1000-1199 Sq.ft" }, { "value": "1200-1399", "text": "1200-1399 Sq.ft" }, { "value": "1400-1599", "text": "1400-1599 Sq.ft" }, { "value": "1600-1799", "text": "1600-1799 Sq.ft" }, { "value": "1800-1999", "text": "1800-1999 Sq.ft" }, { "value": "2000-2249", "text": "2000-2249 Sq.ft" }, { "value": "2250-2499", "text": "2250-2499 Sq.ft" }, { "value": "3000-3249", "text": "3000-3249 Sq.ft" }, { "value": "3250-3299", "text": "3250-3299 Sq.ft" }, { "value": "3300-3349", "text": "3300-3349 Sq.ft" }, { "value": "3350-3399", "text": "3350-3399 Sq.ft" }, { "value": "3400-3449", "text": "3400-3449 Sq.ft" }, { "value": "3450-3499", "text": "3450-3499 Sq.ft" }, { "value": "3500-3549", "text": "3500-3549 Sq.ft" }, { "value": "3550-3599", "text": "3550-3599 Sq.ft" }, { "value": "3600-3649", "text": "3600-3649 Sq.ft" }, { "value": "3650-3699", "text": "3650-3699 Sq.ft" }, { "value": "3700-3749", "text": "3700-3749 Sq.ft" }, { "value": "3750-3799", "text": "3750-3799 Sq.ft" }, { "value": "3800-3849", "text": "3800-3849 Sq.ft" }, { "value": "3850-3899", "text": "3850-3899 Sq.ft" }, { "value": "3900-3949", "text": "3900-3949 Sq.ft" }, { "value": "3950-3999", "text": "3950-3999 Sq.ft" }, { "value": "4000-4499", "text": "4000-4499 Sq.ft" }, { "value": "4500-7000", "text": "Above 4500 + Sq.ft" }]
const agentInfo = {
  "name": "Agent Name",
  "title": "Real Estate Sales Representative",
  "officeName": "Housen.ca",
  "type": "Brokerage",
  "OfficeAddress": "25-81 Zenway Blvd. Woodbridge, ON. L4H 0S5",
  "city": "Brampton",
  "state": "Ontario",
  "brokerageEmail": "Info@Housen.ca",
  "mobileNumber": "+1 (647) 500-7777",
  "brokerageName": "Housen.ca",
  "brokerageAddress": "25-81 Zenway Blvd. Woodbridge, ON. L4H 0S5",
  "brokerageNumber": "+1 (647) 500-7777",
  "profile": ""
}
const pageMeta = {
  "home": {
    "title": "home",
    "slug": "home",
    "metaDesc": "",
    "MetaTags": "",
    "metaKeyword": ""
  },
  "blogs": {
    "title": "blogs",
    "slug": "blogs",
    "metaDesc": "Housen.ca blogs",
    "MetaTags": "top blogs",
    "metaKeyword": "top blogs"
  },
  "calculator": {
    "title": "calculator",
    "slug": "calculator",
    "metaDesc": "Housen.ca calculator",
    "MetaTags": "calculator",
    "metaKeyword": "calculator"
  },
  "buyinghomes": {
    "title": "buyinghomes",
    "slug": "buyinghomes",
  },
  "sellinghomes": {
    "title": "sellinghomes",
    "slug": "sellinghomes",
  },
  "homeValuation": {
    "title": "home valuation",
    "slug": "home valuation",
  }

}
let metaJson = [
  {
    "hostname": "https://housen.ca/",
    "twitterUsername": "@housen"
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "viewport" },
    "attr2": { "key": "content", "value": "width=device-width, initial-scale=1.0" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "http-equiv", "value": "Content-type" },
    "attr2": { "key": "content", "value": "text/html; charset=utf-8" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "robots" },
    "attr2": { "key": "content", "value": "noodp" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "msapplication-TileColor" },
    "attr2": { "key": "content", "value": "#0081a7" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "theme-color" },
    "attr2": { "key": "content", "value": "#0081a7" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "author" },
    "attr2": { "key": "content", "value": "Housen.ca" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "twitter:card" },
    "attr2": { "key": "content", "value": "website" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "twitter:site" },
    "attr2": { "key": "content", "value": "@housen.ca" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "twitter:creator" },
    "attr2": { "key": "content", "value": "@housen.ca" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "twitter:app:country" },
    "attr2": { "key": "content", "value": "ca" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "og_site_name" },
    "attr2": { "key": "content", "value": "housen.ca" },
    "attr3": { "key": "property", "value": "og:site_name" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "property", "value": "og:type" },
    "attr2": { "key": "content", "value": "website" },
  },
  {
    "tag": "link",
    "attr1": { "key": "rel", "value": "canonical" },
    "attr2": { "key": "href", "value": "https://housen.ca/" },
  },
  {
    "tag": "link",
    "attr1": { "key": "rel", "value": "alternate" },
    "attr2": { "key": "href", "value": "https://housen.ca/" },
  },
  {
    "tag": "link",
    "attr1": { "key": "rel", "value": "preconnect" },
    "attr2": { "key": "href", "value": "https://housen.ca/" },
  },
  {
    "tag": "link",
    "attr1": { "key": "href", "value": "/images/icon/favicon.jpg" },
    "attr2": { "key": "rel", "value": "shortcut icon" },
  },

  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "title" },
    "attr2": { "key": "content", "value": "housen.ca !" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "description" },
    "attr2": { "key": "content", "value": "test desc" },
  },
  {
    "tag": "meta",
    "attr1": { "key": "name", "value": "keywords" },
    "attr2": { "key": "content", "value": "test keywords" },
  },
  {
    "tag": "title",
    "value": "Housen.ca test tag !"
  }
];

const yearData = [
  {
    text: "5 Years",
    value: "5",
  },
  {
    text: "10 Years",
    value: "10",
  },
  {
    text: "15 Years",
    value: "15",
  },
  {
    text: "20 Years",
    value: "20",
  },
  {
    text: "25 Years",
    value: "25",
  },
  {
    text: "30 Years",
    value: "30",
  },
];

module.exports = {
  propSqft,
  dom,
  slotsApi,
  base_url,
  debug_log,
  propertySearchApi,
  defaultImage,
  accessToken,
  mapStyle,
  HomepropertyApi,
  filterDataApi,
  autoSuggestionApi,
  mapDefaultImage,
  detailPage,
  extra_url,
  featuredListApi,
  recentListApi,
  initialPropertySearchFilter,
  minPriceConstant,
  maxPrice,
  filterBeds,
  filterBaths,
  propertyStatus,
  sortStatus,
  leadFormApi,
  front_url,
  agentId,
  favUrl,
  getPropertiesList,
  metaContent,
  APP_NAME,
  contactUsApi,
  feedbackApi,
  featuredApi,
  saveEventApi,
  shareEmailApi,
  saveSearchApi,
  markerApi,
  marketStatsFilterData,
  agentInfo,
  Dom,
  SubType,
  medianAvgDomApi,
  soldActive,
  medianRentalApi,
  propertyTyprDist,
  absorptionData,
  mapSearchListApi,
  mapBoundaryApi,
  mapSearchTotalApi,
  mapSearchMarkersApi,
  similarProperty,
  pageMeta,
  marketStatsCitiesData,
  yelpApi,
  image_base_url,
  metaJson,
  getWatchProperty,
  yearData,
  blogsCat
};
