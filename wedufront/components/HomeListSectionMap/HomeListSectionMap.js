import React, {useState, useEffect, useRef} from "react";
import dynamic from "next/dynamic";
import ReactPaginate from 'react-paginate';
import Loader from './../loader/loader'
// import Pagination from "./Pagination";
import Constants from '../../constants/GlobalConstants'
// import MapV2 from "../MapV2";
import MapCard from './../../ReactCommon/Components/MapCard'
import MapHeader from "./MapHeader"


const urls = Constants.propertySearchApi;
const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/greggs.json?access_token=${Constants.accessToken}&bbox=-0.227654%2C51.464102%2C0.060737%2C51.553421&limit=10`;
const defaultImage = Constants.defaultImage;
let propertySubTpes = [];
let basements = [];
let payload = {
    "PropertyType": '',
    "PropertySubType": '',
    "price_min": '',
    "price_max": '',
    "beds": '',
    "baths": '',
    "status": '',
    "sort_by": '',
    "curr_page": '',
    "openhouse": '',
    "Dom": '',
    "multiplePropType": '',
    "basement": '',
    //"Sqft": '',
    //"Sqft": ''
}
const HomeListSectionMap = () => {
    const mapRef = useRef();
    const [showForm, setShowForm] = useState(true);
    const [changeViewCard, setChangeViewCard] = useState("Grid View");
    const [changeMapView, setChangeMapView] = useState(false);
    const [changeCard, setChangeCard] = useState("map_view");
    const [locations, setLocations] = useState([]);
    const [propertyData, setPropertyData] = useState([]);
    const [mapData, setMapData] = useState();
    const [pagintionData, setPagintion] = useState();
    const [totalCount, setTotal] = useState();
    const [searchLabel, setsearchLabel] = useState("");
    const [dataFlag, setFlag] = useState(false);

    const MapV2 = dynamic(() => import("../MapV2"), {
        loading: () => "Loading...",
        ssr: false
    });
    let props = {
        PropertyType,
        priceMin,
        propSubType,
        priceMax,
        beds,
        baths,
        status,
        sort_by,
        openhouse,
        dom,
        multiplePropType,
        basement,
        sizeSqft,
        resetFilter,
        mapCallBack,
        showGridMap,
        saveSearched,
        "text": changeViewCard,
        "savedSearch": "Saved Search",
        "searchLabel": searchLabel

    }
    useEffect(() => {
        setFiltersData()
        fetchLocations();
        fetchProperty();

    }, []);
    const fetchLocations = async () => {
        await fetch(url).then((response) =>
            response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                setLocations(json.features);
            }).catch((err) => console.log("Error", {err}));
    };
    const fetchProperty = async () => {
        if (localStorage.getItem("filters")) {
            let filters = JSON.parse(localStorage.getItem("filters"))
            payload.PropertyType = filters.propertyType
            payload.PropertySubType = filters.propertySubType
            let prices = filters.price ? filters.price.split('-') : [];
            payload.price_min = prices[0] ? prices[0] : "";
            payload.price_max = prices[1];
        }
        getPropertisList();
        return;
        const requestOptions = {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        };
        await fetch(urls, requestOptions).then((response) =>
            response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                setPropertyData(json.alldata);
                setMapData(json.mapdata);
                setPagintion(json.pagination);
                setTotal(Math.round(json.total / 10));
            }).catch((err) => console.log({err}));
    };
    const setFiltersData = async () => {
        let filters = JSON.parse(localStorage.getItem("filters"))
    }

    function sort_by(e) {
        payload.sort_by = e.target.value;
        payload.curr_page = '';
        getPropertisList();
    }

    function status(e) {
        payload.status = e.target.value;
        payload.curr_page = '';

        getPropertisList();
    }

    function beds(e) {

        payload.beds = e.target.value;
        payload.curr_page = '';
        getPropertisList();
    }

    function baths(e) {
        payload.curr_page = '';
        payload.baths = e.target.value;
        getPropertisList();
    }

    function priceMin(e) {
        payload.curr_page = '';
        payload.price_min = e.target.value;
        getPropertisList();
    }

    function priceMax(e) {
        payload.curr_page = '';
        payload.price_max = e.target.value;
        getPropertisList();
    }

    function PropertyType(e) {
        payload.curr_page = '';
        payload.PropertyType = e.target.value;
        getPropertisList();
    }

    function propSubType(e) {
        payload.curr_page = '';
        payload.PropertySubType = e.target.value;
        getPropertisList();
    }

    function openhouse(e) {
        payload.curr_page = '';
        payload.openhouse = e.target.value;
        getPropertisList();
    }

    function dom(e) {
        payload.curr_page = '';
        payload.Dom = e.target.value;
        getPropertisList();
    }

    function multiplePropType(e) {
        payload.curr_page = '';
        //console.log("====>>>eeeeeee", e);
        if (propertySubTpes.includes(e.target.value)) {
            let index = propertySubTpes.indexOf(e.target.value)
            // //console.log("====>>>>slice",propertySubTpes.slice(0, index));
            propertySubTpes = removeElementAt(propertySubTpes, index)
        } else {
            propertySubTpes.push(e.target.value);
        }
        payload.multiplePropType = (propertySubTpes);
        getPropertisList();
    }

    function sizeSqft(e) {
        payload.curr_page = '';
        payload.Sqft = e.target.value;
        getPropertisList();
    }

    function basement(e) {
        payload.curr_page = '';
        //console.log("====>>>eeeeeee", e);
        if (basements.includes(e.target.value)) {
            let index = basements.indexOf(e.target.value)
            basements = removeElementAt(basements, index)
        } else {
            basements.push(e.target.value);
        }
        payload.basement = (basements);
        getPropertisList();
    }

    function removeElementAt(arr, index) {
        let frontPart = arr.slice(0, index);
        let lastPart = arr.slice(index + 1); // index to end of array 
        return [...frontPart, ...lastPart];
    }

    function mapCallBack(fieldValue, fieldName = "") {
        payload.text_search = fieldValue
        getPropertisList();
    };

    function resetFilter() {
        payload.PropertyType = '';
        payload.PropertySubType = '';
        payload.price_min = '';
        payload.price_max = '';
        payload.beds = '';
        payload.baths = '';
        payload.status = '';
        payload.sort_by = '';
        payload.curr_page = '';
        payload.openhouse = '';
        payload.Dom = '';
        payload.multiplePropType = '';
        payload.basement = '';
        payload.Sqft = '';
        payload.text_search = '';
        payload.shape = '';
        payload.radius = '';
        payload.curr_path = '';
        payload.curr_path_query = '';
        payload.curr_bounds = '';
        payload.center_lat = '';
        payload.center_lng = '';
        payload.curr_page = '';
        if (localStorage.getItem("filters")) {
            localStorage.removeItem("filters")
        }
        getPropertisList();
    }

    function mapDraw(params) {
        //console.log("draw map params", params);
        let e = params.event;
        // let map = params.map;
        let data = params.data;
        if (e.type === "draw.create") {
            //console.log("=====>>draw data", data);
            var cordinates;
            var pathstr_query;
            var pathstr = "";
            if (data.features.length > 0) {
                var area = turf.area(data);
                var rounded_area = Math.round(area * 100) / 100;
                pathstr = "[ ";
                for (var i = 0; i < data.features.length; i++) {
                    if (data.features[i].geometry.type === 'Polygon') {
                        cordinates = data.features[i].geometry.coordinates;
                        for (var j = i; j < cordinates.length; j++) {
                            for (var k = 0; k < cordinates[j].length; k++) {
                                if ((k + 1) == cordinates[j].length) {
                                    pathstr_query += "" + cordinates[j][k][1] + " " + cordinates[j][k][0];
                                } else {
                                    pathstr_query += cordinates[j][k][1] + " " + cordinates[j][k][0] + ",";
                                }
                            }
                        }
                    }
                    pathstr_query = pathstr_query.replace("undefined", "");
                }
                //end
                //console.log("pathstr_query  pathstr", pathstr_query, pathstr);
                payload.shape = "polygon";

                payload.curr_path_query = pathstr_query;

                getPropertisList();
            }
        }
    }

    function mapDragend(params) {
        //console.log("draged map params", params.map);
        let map = params.map;

        var coordinates = map.getCenter();
        var bounds = map.getBounds();
        var sw = bounds.getSouthWest().wrap().toArray();
        var ne = bounds.getNorthEast().wrap().toArray();
        let bndstr = "" + bounds.getNorthEast().wrap().toArray() + "###" + bounds.getSouthWest().wrap().toArray() + "";
        payload.shape = "rectangle";
        payload.curr_bounds = bndstr;
        getPropertisList();
        //console.log("=>>mapDragend area", params);
    }

    function getPropertisList() {
        //console.log("payload====>>>", payload);
        let txt = "";
        if (payload.beds) {
            txt += payload.beds + " Beds "
        }
        if (payload.baths) {
            txt += payload.baths + " Baths "
        }
        if (payload.status) {
            txt += payload.status + " "
        }
        if (payload.PropertyType) {
            txt += payload.PropertyType + " properties"
        }
        let search = txt ? "Search :- " : "";
        txt = search ? search + txt + " in" : "";
        setsearchLabel(txt)
        const requestOptions = {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        };
        const fetchProperty = async () => {
            await fetch(urls, requestOptions).then((response) =>
                response.text()).then((res) => JSON.parse(res))
                .then((json) => {
                    setPropertyData([]);
                    setMapData([]);
                    // setPropertyData(json);
                    setPagintion(json.pagination);
                    //console.log("=======json.total", json);
                    setTotal(Math.round(json.total / 10));
                    setPropertyData(json.alldata);
                    setMapData(json.mapdata);
                    if (json.mapdata) {
                        setFlag(true);
                    } else {
                        setFlag(false)
                    }

                }).catch((err) => console.log({err}));
        };
        fetchProperty();
    }

    function paginte(e) {
        //console.log("===>>>", e.selected);
        payload.curr_page = e.selected + 1
        getPropertisList();
    }

    function mapCardCallBack(e) {

    }

    function favoriteHandler(e) {
        //console.log("====>>>e in homemap", e);
    }

    function showGridMap(e) {
        //console.log("view changed", e);
        if (changeMapView) {
            setChangeMapView(false)
            setChangeViewCard("Grid View")
            setChangeCard("map_view")
        } else {
            setChangeMapView(true)
            setChangeViewCard("Map View")
            setChangeCard("grid_view")
        }
    }

    function saveSearched() {

    }

    return (
        <>
            <div className="hero-section">
                <div className="p-4">
                    <div className="row mr-0">
                        <MapHeader {...props} />
                        {changeCard === "map_view" &&
                        <>
                            <div className="col-md-6 col-sm-6 col-xs-12  border">
                                <div className="" id="mapContain">
                                    {
                                        mapData ? <MapV2 mapData={mapData}
                                                         mapDrawCallBack={mapDraw}
                                                         mapDragendCallBack={mapDragend}
                                                         mapCardCallBack={mapCardCallBack}
                                        /> : dataFlag == true ? <Loader/> : <p>No Data Found</p>
                                    }
                                </div>
                            </div>
                            <div className={"col-md-6 col-sm-6 col-xs-12 border "}
                                 style={{'height': '615px', 'overflow': 'auto'}}>
                                <div className={"row mb-2 mt-2"}>
                                    {propertyData &&
                                    propertyData.map((item, key) => {
                                        return (
                                            <div className="col-6 mt-3 card card-row mapCardList ">
                                                <MapCard item={item} defaultImage={defaultImage} key={key}
                                                         favoriteCallBack={favoriteHandler}/>
                                            </div>
                                        );
                                    })
                                    }
                                    {/* Card Fluid */}
                                    <div className="col-md-12">
                                        <div className="paginationContain">
                                            <ReactPaginate
                                                previousLabel={'<'}
                                                nextLabel={'>'}
                                                pageCount={totalCount}
                                                onPageChange={paginte}
                                                containerClassName={'pagination'}
                                                activeClassName={'active'}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </>
                        }
                        {changeCard === "grid_view" &&
                        <>
                            <div className="col-12 ">
                                <div className={"row mt-2"}>
                                    {
                                        propertyData.map((item, key) => {
                                            return (
                                                <div
                                                    className="col-md-4  col-lg-4 col-sm-4 card card-row mapCardList grid_view">
                                                    <MapCard item={item} defaultImage={defaultImage} key={key}/>
                                                </div>
                                            );
                                        })
                                    }
                                </div>
                                <div className="col-12 mb-2 ">
                                    <div className="paginationContain">

                                        <ReactPaginate
                                            previousLabel={'<'}
                                            nextLabel={'>'}
                                            pageCount={totalCount}
                                            onPageChange={paginte}
                                            containerClassName={'pagination'}
                                            activeClassName={'active'}
                                        />
                                    </div>
                                </div>
                            </div>
                        </>
                        }
                    </div>
                </div>
            </div>
            <style jsx>
                {`
                 .card-fluid{border:none!important;background-color:#f3f3f3!important}.card-body-fluid{background-color:#f3f3f3!important;border-radius:0!important;border-top:3px solid var(--red)!important;font-family:Helvetica,sans-serif}.card-img-fluid{border-radius:0!important;width:100%;height:100%}.card-img-wrapper{position:relative;overflow:hidden;max-height:15rem}.card-title-fluid{color:var(--red);font-size:1em!important;font-weight:lighter}.card-text-fluid{color:#777;font-weight:700;font-size:.8em}.card-desc-fluid{color:#777;font-size:.8em}.badge.bg-secondary{background-color:#bbb!important}.price{color:#fff;position:absolute;bottom:0;left:0;right:0;padding:33px 0 10px 10px;background:linear-gradient(to bottom,rgba(44,44,44,0) 0,rgba(43,43,43,.21) 22%,rgba(0,0,0,.45) 58%,rgba(19,19,19,.7) 100%);font-size:1.3rem;font-family:Helvetica,sans-serif;transition:all .3s linear}.for-lease,.for-rent{font-size:.7em}.property-type{font-size:.7em;font-weight:700;color:#777}.open-house{position:absolute;left:10px;bottom:60px;overflow:hidden}.open-house .badge{border-radius:0!important;transition:all .3s linear}.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{color:#fff;position:absolute;background-color:var(--red);top:0;right:10px;padding:1.75rem .375rem .375rem .375rem;font-family:Helvetica,sans-serif;font-weight:700;opacity:.8;transition:all .3s linear}.for-rent-ribbon{background-color:#777}.card-img-wrapper:hover .price{bottom:-60px}.card-img-wrapper:hover .for-lease-ribbon,.card-img-wrapper:hover .for-rent-ribbon,.card-img-wrapper:hover .for-sale-ribbon{top:-50px;height:0%}.card-img-wrapper:hover .open-house .badge{transform:translate(0,25px)}@media (min-width:992px){.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{font-size:.8em}}@media (max-width:991.98px){.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{font-size:.7em}}@media (max-width:767.98px){.card-body-fluid{text-align:center}}
                 .card-row{border:none!important;height:22rem}.card-body-row{background-color:#f3f3f3!important;border-radius:0!important;border-top:3px solid var(--red)!important;font-family:Helvetica,sans-serif}.card-img-row{border-radius:0!important}.card-img-rows{height:inherit}.card-img-wrapper{position:relative;overflow:hidden;height:15rem}.card-title-row{color:var(--red);font-size:1em!important;font-weight:lighter}.card-text-row{color:#777;font-weight:700;font-size:.8em}.badge.bg-secondary{background-color:#bbb!important}.price{color:#fff;position:absolute;bottom:0;left:0;right:0;padding:33px 0 10px 10px;background:linear-gradient(to bottom,rgba(44,44,44,0) 0,rgba(43,43,43,.21) 22%,rgba(0,0,0,.45) 58%,rgba(19,19,19,.7) 100%);font-size:1.3rem;font-family:Helvetica,sans-serif;transition:all .3s linear}.for-lease,.for-rent{font-size:.7em}.property-type{font-size:.7em;font-weight:700;color:#777}.open-house{position:absolute;left:10px;bottom:60px;overflow:hidden}.open-house .badge{border-radius:0!important;transition:all .3s linear}.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{color:#fff;position:absolute;background-color:var(--red);top:0;right:10px;padding:1.75rem .375rem .375rem .375rem;font-family:Helvetica,sans-serif;font-weight:700;opacity:.8;transition:top .3s linear;overflow:hidden}.for-rent-ribbon{background-color:#777}.card-img-wrapper:hover .price{bottom:-60px}.card-img-wrapper:hover .for-lease-ribbon,.card-img-wrapper:hover .for-rent-ribbon,.card-img-wrapper:hover .for-sale-ribbon{top:-50px;height:0%}.card-img-wrapper:hover .open-house .badge{transform:translate(0,25px)}@media (min-width:992px){.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{font-size:.8em}}@media (max-width:991.98px){.for-lease-ribbon,.for-rent-ribbon,.for-sale-ribbon{font-size:.7em}}@media (max-width:767.98px){.card-body-row{text-align:center}}.me-1{margin-right:.25rem!important}.card{position:relative;display:-webkit-flex;display:flex;-webkit-flex-direction:column;flex-direction:column;min-width:0;word-wrap:break-word;background-color:#fff;background-clip:border-box;border:1px solid rgba(0,0,0,.125);border-radius:.25rem}.card-body{-webkit-flex:1 1 auto;flex:1 1 auto;padding:1rem 1rem;padding-top:1rem}
               `}
            </style>
        </>
    );
};
export default HomeListSectionMap;