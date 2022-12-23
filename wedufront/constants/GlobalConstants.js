const front_url = "https://www.wedu.ca";
const base_url = 'https://panel.wedu.ca/';
//const base_url = 'http://127.0.0.1:8000/';
const image_base_url = base_url + "storage/";
// const image_base_url = "";
// const base_url = 'http://127.0.0.1:8000/';
const propertySearchApi = base_url + "api/v1/services/search/propertiesSearch";
const mapSearchListApi = base_url + "api/v1/services/search/mapSearchList";
const mapBoundaryApi = base_url + "api/v1/services/search/mapBoundary";
const mapSearchTotalApi = base_url + "api/v1/services/search/mapSearchTotal";
const mapSearchMarkersApi = base_url + "api/v1/services/search/mapSearchMarkers";

const HomepropertyApi = base_url + "api/v1/services/home/propertyList";
const saveEventApi = base_url + "api/v1/agent/events/addSlots";
const slotsApi = base_url + "api/v1/agent/events/getSlots";
const defaultImage = "/images/comingSoon.webp";
const mapDefaultImage = "/images/comingSoon.webp";
const detailPage = base_url + "api/v1/services/fetchPageViews/propertyDetails";
const similarProperty = base_url + "api/v1/services/fetchPageViews/similarProperty";

const soldData = base_url + "api/v1/services/fetchPageViews/soldData";
const similarSaleProperty = base_url + "api/v1/services/fetchPageViews/similarSaleProperty";
const similarRentProperty = base_url + "api/v1/services/fetchPageViews/similarRentProperty";

const yelpApi = base_url + "api/v1/services/search/getDataFromYelp";
const shareEmailApi = base_url + "api/v1/services/shareEmail";
const saveSearchApi = base_url + "api/v1/services/saveSearch";
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
const agentId = 3;
const favUrl = base_url + "api/v1/services/global/fav-property"
const getPropertiesList = base_url + "api/v1/services/global/getProperties"
const APP_NAME = "Wedu.ca";
const REACT_APP_GOOGLE_API_KEY = "AIzaSyDG5Md4sE3QWkq9bPOrpypRj0wsXnoa4ZY";
const googleTestKey = "AIzaSyBrc6W-HZICQvpA_EYOefkoB66AG3ANAGQ";
const yelpKey = "X3ASKQfAYvSj1IkY_wx307yfoxqfaTpDHxH0xqUlRcD_fkXwj73-K9wWzCHUDjVxMLAiC_ho0qoBy0AJVg7q0vJ3-6KMExTzPPFCMf3JJE0l0lmArUEXisGP7eDhX3Yx";
const googleClientId = "672088791066-battt8l4pa8bmosdk48u5368b45cvmkk.apps.googleusercontent.com"
const fbAppId = "943838539830151";

const marketStatsFilterData = base_url + "api/v1/services/global/marketStatsFilterData";
const medianAvgDomApi = base_url + "api/v1/services/global/domAvgMedian";
const soldActive = base_url + "api/v1/services/global/soldActive";
const medianRentalApi = base_url + "api/v1/services/global/medianRental";
const propertyTyprDist = base_url + "api/v1/services/global/propertyTyprDist";
const absorptionData = base_url + "api/v1/services/global/absorptionData";

const initialPropertySearchFilter = {
  text_search: "",
  propertyType: "",
  propertySubType: [],
  basement: [],
  features: [],
  price_min: "",
  price_max: "",
  beds: "",
  baths: "",
  status: "",
  sort_by: "",
  curr_page: "",
  openhouse: "",
  Dom: "",
  Sqft: "",
  shape: "",
  curr_path_query: "",
  City: "",
};
const minPriceConstant = [
  { text: "Min Price", value: "" },
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
  { value: "", text: "Max Price" },
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
  { value: "", text: "Beds" },
  { value: "1", text: "1 +" },
  { value: "2", text: "2 +" },
  { value: "3", text: "3 +" },
  { value: "4", text: "4 +" },
  { value: "5", text: "5 +" },
  { value: "6", text: "6 +" },
  { value: "7", text: "7 +" },
];

const filterBaths = [
  { value: "", text: "Baths" },
  { value: "1", text: "1 +" },
  { value: "2", text: "2 +" },
  { value: "3", text: "3 +" },
  { value: "4", text: "4 +" },
  { value: "5", text: "5 +" },
  { value: "6", text: "6 +" },
  { value: "7", text: "7 +" },
];

const propertyStatus = [
  { value: "Sale", text: "Sale" },
  { value: "Lease", text: "Lease" },
  { value: "Sold", text: "Sold" },
  { value: "Rented", text: "Rented" },
];

const sortStatus = [
  { value: "", text: "Sort By" },
  { value: 'price_low', text: 'Price (Lo-Hi)' },
  { value: 'price_high', text: 'Price (Hi-Lo)' },
  { value: 'dom_low', text: 'Dom (Lo-Hi)' },
  { value: 'dom_high', text: 'Dom (Hi-Lo)' },
]
const pageMeta = {
  "home": {
    "title": "Homes for Sale & Real Estate Get Listings in Canada | Wedu",
    "keyword": "Homes for sale, real estate get listings",
    "description": "Wedu Is Your Best Choice for Real Estate Search in Canada. Find Homes for Sale, New Developments, Rental Homes, Real Estate Agents, and Property Insights",
    "imgAlt": "Homes for Sale and Real Estate Get Listings",
    "url": "Homes-for-sale-and-real-estate"
  }
  ,
  "contactUs": {
    "title": "Contact Us | Latest Real Estate & Property Insights | Canada",
    "keyword": "Homes for sale, real estate get listings",
    "description": "Our Mission Is to Give Everyone the Opportunity to Find Their Dream Home. Get the Latest Real Estate, Property Insights, Rental Homes, and Get Listings Real Estate.",
    "imgAlt": "Homes for Sale and Real Estate Get Listings in Canada",
    "url": "Homes-for-sale-and-real-estate"
  }
  ,
  "aboutUs": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "About Us | Homes for Sale & Real Estate Get Listings | Canada",
    "description": "Wedu Is a Premier Real Estate Portal, That Brings You the Latest in Homes for Sale and Real Estate. Our Website Is the Best Online Resource for Home Buyers and Sellers.",
    "imgAlt": "Homes for Sale and Real Estate Get Listings in Canada",
    "url": "homes-for-sale-and-real-estate"
  }
  ,
  "listingPage1": {
    "keyword": "Home for Sale & Listing in Canada",
    "title": "Homes for Sale & Real Estate Get Listings in Canada | Wedu",
    "description": "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!",
    "imgAlt": "Home for Sale & Listing in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada",
    "buy": true
  }
  ,
  "listingPage": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Home for Rent:Apartment & House for rent in Canada | Wedu",
    "description": "Wedu provides the latest listings of condos, apartments, and houses for Rent throughout Canada. View our comprehensive Rent listings today!",
    "imgAlt": "Home for Sale & Listing in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada",
    "rent": true
  }
  ,
  "advanceSearch": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Home for Rent:Apartment & House for rent in Canada | Wedu",
    "description": "Wedu provides the latest listings of condos, apartments, and houses for Rent/Buy throughout Canada. View our comprehensive Rent listings today!",
    "imgAlt": "Home for Sale & Listing in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada",
    "imgUrl": "/images/map.png"
  }
  ,
  "newHome": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Canada Condos & Condo Building Info | Wedu",
    "description": "Wedu.ca is your #1 resource for buying, selling, or renting a condo in Canada. Find amenities, strata restrictions, units for sale of each condo.",
    "imgAlt": "New home for sale in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada"
  }
  ,
  "buldings": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Canada Condos & Condo Building Info | Wedu",
    "description": "Wedu.ca is your #1 resource for buying, selling, or renting a condo in Canada. Find amenities, strata restrictions, units for sale of each condo.",
    "imgAlt": "New home for sale in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada"
  }
  ,
  "agents": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Canada Real Estate Agent Search | Wedu",
    "description": "Looking for a real estate agent to sell or buy a home? Wedu provides the most updated profiles of real estate agents across Canada. Start your agent search.",
    "imgAlt": "New home for sale in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada"
  }
  ,
  "mortgage": {
    "keyword": "Homes for sale, real estate get listings",
    "title": "Compare Today's Mortgage Rates in Canada | Wedu",
    "description": "Compare current mortgage interest rates from banks and lenders in Canada and find the best rate that works with your home purchasing plan!",
    "imgAlt": "New home for sale in Canada",
    "url": "Home-for-Sale-&-Listing-in-Canada"
  },
  "resetpassword": {
    "keyword": "Set-Password",
    "title": "Reset Password | Wedu",
    "description": "Reset your account password!",
    "imgAlt": "Reset",
    "url": "Reset",
  },
  "blogs": {
    "title": "Blog  Real Estate News, Home Buying &amp; Selling Tips",
    "ogtitle": " Blog: Real Estate News, Home Buying &amp; Selling Tips",
    "slug": "blogs",
    "page": "blogs",
    "description": "wedu.ca - Blog: Real Estate News, Home Buying &amp; Selling Tips",
    "keyword": "Real Estate News | Top Real Estate Blogs | Home Buying &amp; Selling Tips "
  },
  "profile": {
    "title": "Profile",
    "ogtitle": "Profile",
    "description": "wedu.ca - Blog: Real Estate News, Home Buying &amp; Selling Tips",
    "keyword": "Real Estate News | Top Real Estate Blogs | Home Buying &amp; Selling Tips "
  },

}
const metaContent = {
  title: "wedu",
  metaTitle: "Wedu For Sale , Wedu Finder , Wedu for Rent",
  metaDescs: "Are you searching for a home for sale? Wedu brings you the latest listings for Home with Interactive Map Search and Latest Listings Daily. ",
  keywords: "Wedu For Sale , Wedu Finder , Wedu for Rent",
  author: "wedu",
}
const dom = [ { "value": "", "text": "Dom" },{ "value": "3", "text": "3 Days" }, { "value": "7", "text": "7 Days" }, { "value": "14", "text": "14 Days" }, { "value": "30", "text": "30 Days" }];

const propSqft = [
  { "value": "", "text": "Size" },
  { "value": "500", "text": "500+ Sq.ft" },
  { "value": "750", "text": "750+ Sq.ft" },
  { "value": "1000", "text": "1000+ Sq.ft" },
  { "value": "1250", "text": "1250+ Sq.ft" },
  { "value": "1500", "text": "1500+ Sq.ft" },
  { "value": "1750", "text": "1750+ Sq.ft" },
  { "value": "2000", "text": "2000+ Sq.ft" },
  { "value": "2250", "text": "2250+ Sq.ft" },
  { "value": "2500", "text": "2500+ Sq.ft" },
  { "value": "2750", "text": "2750+ Sq.ft" },
  { "value": "3000", "text": "3000+ Sq.ft" },
  { "value": "3250", "text": "3250+ Sq.ft" },
  { "value": "3500", "text": "3500+ Sq.ft" }
]
const agentInfo = {
  "name": "JUVAN MARIATHASAN",
  "title": "Broker",
  "officeName": "AIMHOME REALTY INC.",
  "type": "Brokerage",
  "OfficeAddress": "3601 HWY. 7 E UNIT 513",
  "city": "MARKHAM",
  "state": "Ontario",
  "brokerageEmail": "Info@Wedu.ca",
  "mobileNumber": "(416) 273 - 4114",
  "brokerageName": "AIMHOME REALTY INC.",
  "brokerageAddress": "AIMHOME REALTY INC. BROKERAGE, 3601 HWY. 7 E UNIT 513 MARKHAM,ONTARIO",
  "brokerageNumber": "+1 (905) 477-5900",
  "profile": "https://agentiwebs.com/assets/assist/propertydetailnew/images/avatar.png"
}

var formatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 0,
});
module.exports = {
  marketStatsFilterData,
  formatter,
  medianAvgDomApi,
  medianRentalApi,
  propertyTyprDist,
  soldActive,
  propSqft,
  similarProperty,
  googleClientId,
  fbAppId,
  googleTestKey,
  agentInfo,
  dom,
  yelpKey,
  slotsApi,
  base_url,
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
  REACT_APP_GOOGLE_API_KEY,
  contactUsApi,
  feedbackApi,
  featuredApi,
  yelpApi,
  saveEventApi,
  shareEmailApi,
  saveSearchApi,
  markerApi,
  absorptionData,
  mapSearchListApi,
  mapBoundaryApi,
  mapSearchTotalApi,
  mapSearchMarkersApi,
  pageMeta,
  image_base_url,
  soldData,
  similarSaleProperty,
  similarRentProperty
};
