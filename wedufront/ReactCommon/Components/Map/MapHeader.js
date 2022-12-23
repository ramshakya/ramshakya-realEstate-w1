
import { useState, useEffect, useRef, Component } from "react";
import Styles from './css/index.module.css'
import Autocomplete from './../../Components/AutoSuggestion'
import Button from './../../Components/SimpleButton'
import Constants from './../../../constants/GlobalConstants'
const MapHeader = (props) => {
    // console.log("=====HOME PROPS===>>",props.homeData.price?props.homeData.price:"");
    const [open, setOpen] = useState(false);
    const [type, setType] = useState([]);
    const [beds, setBeds] = useState([]);
    const [baths, setBaths] = useState([]);
    const [status, setStatus] = useState([]);
    const [basement, setBasement] = useState([]);
    const [propType, setPropType] = useState([]);
    const [propSubType, setPropSubType] = useState([]);
    const [headersData, setHeaderData] = useState();
    const [priceData, setPriceData] = useState([]);
    const [searchtextData, setSearchData] = useState();
    const propSqft = [
        "0-99",
        "100-199",
        "200-299",
        "300-399",
        "400-499",
        "500-599",
        "600-699",
        "700-799",
        "800-899",
        "900-999",
        "1000-1199",
        "1200-1399",
        "1400-1599",
        "1600-1799",
        "1800-1999",
        "2000-2249",
        "2250-2499",
        "3000-3249",
        "3250-3299",
        "3300-3349",
        "3350-3399",
        "3400-3449",
        "3450-3499",
        "3500-3549",
        "3550-3599",
        "3600-3649",
        "3650-3699",
        "3700-3749",
        "3750-3799",
        "3800-3849",
        "3850-3899",
        "3900-3949",
        "3950-3999",
        "4000-4499",
    ];

    useEffect(() => {
        // getFiltersData();
        // headerData()
    }, []);
    function headerData() {
        let filters = JSON.parse(localStorage.getItem("filters"))
        // text_search
        if (filters) {
            //console.log("====>>>>>>filters map header", filters.text_search);
            setHeaderData(filters);
            setSearchData(filters.text_search);

        }
    }
    function getFiltersData() {
        fetch(Constants.filterDataApi,
            { "method": "GET", "headers": { 'Content-Type': 'application/json' } })
            .then((response) =>
                response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                setBasement(json.basement);
                setPropType(json.property_type);
                setPropSubType(json.subtype);
            }).catch((err) => console.log({ err }));
    }

    const fetchPropertyData = async (fieldValue, fieldName, cb) => {
        let payload = {
            "query": "default"
        }
        if (fieldValue) {
            payload.query = fieldValue;
        }
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        };
        await fetch(Constants.autoSuggestionApi, requestOptions).then((response) =>
            response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                //console.log("====response suggestion_search json==>", json);
                // setData(json)
                cb({ allList: json })
            }).catch((err) => console.log({ err }));
    }
    return (
        <>
            <div className=" p-0" style={{ 'padding': '0px !important' }}>
                <div className={'row mt-4  ml-2 ', Styles.gridView} >
                    <div className="col-2 form-group">
                        {/* <input className="input-box apply_search text_search form-control" name="text_search" type="search" placeholder="MLS# ,Address,Neighborhood,City" aria-label="Search" value="" /> */}
                        <Autocomplete
                            inputProps={{
                                id: "autoSuggestion",
                                name: "autoSuggestion",
                                className: "auto form-control auto-suggestion-inp",
                                placeholder: "MLS# ,Address,Neighborhood,City",
                                title: "Search @MLS , City , Neighborhood",
                                readOnly: false

                            }}
                            allList={[]}
                            autoCompleteCb={fetchPropertyData}
                            callBackMap={props.mapCallBack}
                            extraProps={{
                                value: searchtextData
                            }}
                        />
                    </div>
                    <div className="col-2 form-group">
                        <div className="row" style={{ 'padding': '0px !important' }}>
                            <div className="col-6 form-group">
                            <Autocomplete
                            inputProps={{
                                id: "autoSuggestion",
                                name: "autoSuggestion",
                                className: "auto form-control auto-suggestion-inp",
                                placeholder: "MLS# ,Address,Neighborhood,City",
                                title: "Search @MLS , City , Neighborhood",
                                readOnly: false

                            }}
                            allList={[]}
                            autoCompleteCb={fetchPropertyData}
                            callBackMap={props.statusCallBack}
                            extraProps={{
                                value: searchtextData
                            }}
                        />
                            </div>
                            <div className="col-6 form-group">
                                <select className="form-control on-focus-cls custom-form-control" title="Property Type" name="prop_type" onChange={props.PropertyType} style={{ 'borderRadius': '0px !important' }}>
                                    <option value="Sale">Any Price</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div className="col-2 form-group">
                        <div className="height">
                            <div className="row" style={{ 'padding': '0px !important' }}>
                                <div className="col-6 form-group">
                                    <select className="form-control on-focus-cls custom-form-control" title="Min Price" name="price_min" onChange={props.priceMin} style={{ 'borderRadius': '0px !important' }}>
                                        <option disabled>Min Price</option>
                                        <option value="">No min</option>
                                        <option value="25000">$25k</option>
                                        <option value="35000" >$35k</option>
                                        <option value="45000" >$45k</option>
                                        <option value="75000">$75k</option>
                                        <option value="100000" >$100k</option>
                                        <option value="150000" >$150k</option>
                                        <option value="200000" >$200k</option>
                                        <option value="250000" >$250k</option>
                                        <option value="300000" >$300k</option>
                                        <option value="350000" >$350k</option>
                                        <option value="400000" >$400k</option>
                                        <option value="450000" >$450k</option>
                                        <option value="500000" >$500k</option>
                                        <option value="550000" >$550k</option>
                                        <option value="600000" >$600k</option>
                                        <option value="650000" >$650k</option>
                                        <option value="700000" >$700k</option>
                                        <option value="750000" >$750k</option>
                                        <option value="800000" >$800k</option>
                                        <option value="850000" >$850k</option>
                                        <option value="900000" >$900k</option>
                                        <option value="950000" >$950k</option>
                                        <option value="1000000" >$1M</option>
                                        <option value="1500000" >$1.5M</option>
                                        <option value="2000000" >$2M</option>
                                        <option value="2500000" >$2.5M</option>
                                        <option value="3000000" >$3M</option>
                                        <option value="3500000" >$3.5M</option>
                                        <option value="4000000" >$4M</option>
                                        <option value="4500000" >$4.5M</option>
                                        <option value="5000000" >$5M</option>
                                        <option value="5500000" >$5.5M</option>
                                        <option value="6000000" >$6M</option>
                                        <option value="6500000" >$6.6M</option>
                                        <option value="7000000" >$7M</option>
                                        <option value="7500000" >$7.5M</option>
                                        <option value="8000000" >$8M</option>
                                        <option value="8500000" >$8.5M</option>
                                        <option value="9000000" >$9M</option>
                                        <option value="9500000" >$9.5M</option>
                                        <option value="10000000" >$10M</option>
                                        <option value="12000000" >$12M</option>
                                        <option value="13000000">$13M</option>
                                        <option value="15000000" >$15M</option>
                                        <option value="20000000" >$20M</option>
                                        <option value="25000000">$25M</option>
                                        <option value="30000000" >$30M</option>
                                        <option value="40000000" >$40M</option>
                                        <option value="50000000" >$50M</option>
                                    </select>
                                </div>
                                <div className="col-6 form-group">
                                    <select className="form-control on-focus-cls custom-form-control" name="price_max" title="Max Price" onChange={props.priceMax} style={{ 'borderRadius': '0px !important' }}>
                                        <option disabled>Max Price</option>
                                        <option value="">No max</option>
                                        <option value="25000" >$25k</option>
                                        <option value="35000" >$35k</option>
                                        <option value="45000" >$45k</option>
                                        <option value="75000" >$75k</option>
                                        <option value="100000" >$100k</option>
                                        <option value="150000" >$150k</option>
                                        <option value="200000" >$200k</option>
                                        <option value="250000" >$250k</option>
                                        <option value="300000" >$300k</option>
                                        <option value="350000" >$350k</option>
                                        <option value="400000" >$400k</option>
                                        <option value="450000" >$450k</option>
                                        <option value="500000" >$500k</option>
                                        <option value="550000" >$550k</option>
                                        <option value="600000" >$600k</option>
                                        <option value="650000" >$650k</option>
                                        <option value="700000" >$700k</option>
                                        <option value="750000" >$750k</option>
                                        <option value="800000" >$800k</option>
                                        <option value="850000" >$850k</option>
                                        <option value="900000" >$900k</option>
                                        <option value="950000" >$950k</option>
                                        <option value="1000000" >$1M</option>
                                        <option value="1500000" >$1.5M</option>
                                        <option value="2000000" >$2M</option>
                                        <option value="2500000" >$2.5M</option>
                                        <option value="3000000" >$3M</option>
                                        <option value="3500000" >$3.5M</option>
                                        <option value="4000000" >$4M</option>
                                        <option value="4500000" >$4.5M</option>
                                        <option value="5000000" >$5M</option>
                                        <option value="5500000" >$5.5M</option>
                                        <option value="6000000" >$6M</option>
                                        <option value="6500000" >$6.6M</option>
                                        <option value="7000000" >$7M</option>
                                        <option value="7500000" >$7.5M</option>
                                        <option value="8000000" >$8M</option>
                                        <option value="8500000" >$8.5M</option>
                                        <option value="9000000" >$9M</option>
                                        <option value="9500000" >$9.5M</option>
                                        <option value="10000000" >$10M</option>
                                        <option value="12000000" >$12M</option>
                                        <option value="13000000" >$13M</option>
                                        <option value="15000000" >$15M</option>
                                        <option value="20000000" >$20M</option>
                                        <option value="25000000" >$25M</option>
                                        <option value="30000000" >$30M</option>
                                        <option value="40000000" >$40M</option>
                                        <option value="50000000" >$50M</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-1 form-group">

                        <select className="form-control on-focus-cls custom-form-control" title="Beds" name="Br" id="beds" onChange={props.beds} style={{ 'borderRadius': '0px !important' }}>
                            <option value="">Beds</option>
                            {/* {beds.map((item) => {
                                return (<option value={item}>{item}</option>);
                            })} */}
                            <option value="1">1 +</option>
                            <option value="2">2 +</option>
                            <option value="3">3 +</option>
                            <option value="4">4 +</option>
                            <option value="5">5 +</option>
                            <option value="6">6 +</option>
                            <option value="7">7 +</option>
                        </select>
                    </div>
                    <div className="col-1 form-group">

                        <select className="form-control on-focus-cls custom-form-control" name="Bath_tot" title="Baths" id="baths" onChange={props.baths} style={{ 'borderRadius': '0px !important' }}>
                            <option value="" >Baths</option>
                            {/* {baths.map((item,key) => {
                                return (<option value={item} key={key}>{item}</option>);
                            })} */}
                            <option value="1">1 +</option>
                            <option value="2">2 +</option>
                            <option value="3">3 +</option>
                            <option value="4">4 +</option>
                            <option value="5">5 +</option>
                            <option value="6">6 +</option>
                            <option value="7">7 +</option>
                        </select>
                    </div>
                    <div className="col-1 form-group">

                        <select className=" status form-control on-focus-cls custom-form-control" title="Status" name="S_r[]" onChange={props.status} style={{ 'borderRadius': '0px !important' }}>
                            <option className="" value="" title="">Status</option>
                            <option className="" value="Sale" title="Status" >Sale</option>
                            <option className="" value="Lease" title="Status" >Rental</option>
                            <option className="" value="Sold" title="Status">Sold</option>
                            <option className="" value="Lease" title="Status">Rented</option>
                            {/* {status.map((item,key) => {
                                return (<option className="" value={item} title={item} key={key}>{item}</option>);
                            })} */}
                        </select>
                    </div>
                    <div className="col-1 form-group">
                        <button type="button" className="form-control on-focus-cls custom-form-control" style={{ 'borderRadius': '0px' }} onClick={() => setOpen(!open)}
                        >More</button>
                        {
                            open == true &&
                            <div id="example-collapse-text" style={morefilter}>
                                <span style={{ 'borderRadius': '0px', 'float': 'right', 'position': 'relative', 'zIndex': '99', 'cursor': 'pointer' }} onClick={() => setOpen(!open)}
                                >X</span>
                                <div className="container">
                                    <div className="row">
                                        <div className="col-4">
                                            <label><b>Size </b></label>
                                            <select className="form-control on-focus-cls custom-form-control" title="Size" name="size" onChange={props.sizeSqft} style={{ 'borderRadius': '0px !important' }}>
                                                <option >Select Size</option>

                                                {propSqft.map((item, key) => {
                                                    return (<option value={item} key={key}>{item} Sq.ft</option>);
                                                })}
                                                <option value="4500-7000"> Above 4500 + Sq.ft</option>

                                            </select>
                                        </div>
                                        <div className="col-4">
                                            <label></label>
                                            <div className="form-control on-focus-cls custom-form-control" style={{ 'borderRadius': '0px' }}>
                                                <div className="form-check" style={{ 'minHeight': '0px', 'marginBottom': '0px' }}>
                                                    <input type="checkbox" disabled className="form-check-input" id="house" onChange={props.openhouse} />
                                                    <label className="form-check-label" htmlFor="house">Open House</label>
                                                </div>
                                            </div>

                                        </div>
                                        <div className="col-4">
                                            <label></label>
                                            <select className="form-control on-focus-cls custom-form-control" name="day_on_market" title="" onChange={props.dom} style={{ 'borderRadius': '0px !important' }}>
                                                <option value="" disabled>Days on Market</option>
                                                <option value="3"> 3 days </option>
                                                <option value="7"> 7 days </option>
                                                <option value="14">  14 days </option>
                                                <option value="30">  30 days </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-12 pt-2">
                                            <label><strong>Property Type</strong></label>

                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Att/Row/Twnhouse" className="form-check-input" id="exampleCheck1" onChange={props.multiplePropType} />
                                                <label className="form-check-label" htmlFor="exampleCheck1">Att/Row/Twnhouse</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Condo Apt" className="form-check-input " onChange={props.multiplePropType} id="exampleCheck2" />
                                                <label className="form-check-label" htmlFor="exampleCheck2">Condo Apt</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Cottage" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck3" />
                                                <label className="form-check-label" htmlFor="exampleCheck3">Cottage</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Detached" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck4" />
                                                <label className="form-check-label" htmlFor="exampleCheck4">Detached</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Duplex" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck5" />
                                                <label className="form-check-label" htmlFor="exampleCheck5">Duplex</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Farm" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck6" />
                                                <label className="form-check-label" htmlFor="exampleCheck6">Farm</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Fourplex" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck7" />
                                                <label className="form-check-label" htmlFor="exampleCheck7">Fourplex</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Investment" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck8" />
                                                <label className="form-check-label" htmlFor="exampleCheck8">Investment</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Link" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck9" />
                                                <label className="form-check-label" htmlFor="exampleCheck9">Link</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Lower Level" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck10" />
                                                <label className="form-check-label" htmlFor="exampleCheck10">Lower Level</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Room" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck11" />
                                                <label className="form-check-label" htmlFor="exampleCheck11">Room</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Other" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck12" />
                                                <label className="form-check-label" htmlFor="exampleCheck12">Other</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Multiplex" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck13" />
                                                <label className="form-check-label" htmlFor="exampleCheck13">Multiplex</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Mobile/Trailer" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck14" />
                                                <label className="form-check-label" htmlFor="exampleCheck14">Mobile/Trailer</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Upper Level" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck15" />
                                                <label className="form-check-label" htmlFor="exampleCheck15">Upper Level</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Triplex" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck16" />
                                                <label className="form-check-label" htmlFor="exampleCheck16">Triplex</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Store W/Apt/Offc" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck17" />
                                                <label className="form-check-label" htmlFor="exampleCheck17">Store W/Apt/Offc</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Semi-Detached" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck18" />
                                                <label className="form-check-label" htmlFor="exampleCheck18">Semi-Detached</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Rural Resid" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck19" />
                                                <label className="form-check-label" htmlFor="exampleCheck19">Rural Resid</label>
                                            </div>
                                        </div>

                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="Vacant Land" className="form-check-input" onChange={props.multiplePropType} id="exampleCheck20" />
                                                <label className="form-check-label" htmlFor="exampleCheck20">Vacant Land</label>
                                            </div>
                                        </div>
                                        <div className="col-12"><hr /></div>
                                        <div className="col-12"><label><b>Features</b></label></div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature1" />
                                                <label className="form-check-label" htmlFor="feature1">Central A/C</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature2" />
                                                <label className="form-check-label" htmlFor="feature2">Central Vacuum</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature3" />
                                                <label className="form-check-label" htmlFor="feature3">Elevator</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature4" />
                                                <label className="form-check-label" htmlFor="feature4">Family Room</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature5" />
                                                <label className="form-check-label" htmlFor="feature5">Garage</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature6" />
                                                <label className="form-check-label" htmlFor="feature6">Gym</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature7" />
                                                <label className="form-check-label" htmlFor="feature7">Parking</label>
                                            </div>
                                        </div>
                                        <div className="col-4">
                                            <div className="form-check">
                                                <input type="checkbox" value="" className="form-check-input" id="feature8" />
                                                <label className="form-check-label" htmlFor="feature8">Pool</label>
                                            </div>
                                        </div>
                                        <div className="col-12"><hr /></div>
                                        <div className="col-12"><label><b>Basement</b></label></div>
                                        {basement.map((item, i) => {
                                            let id = 'basement' + i;
                                            return (<div className="col-4" key={i}>
                                                <div className="form-check">
                                                    <input type="checkbox" className="form-check-input" id={id} value={item} onChange={props.basement} />
                                                    <label className="form-check-label" htmlFor={id}>{item}</label>
                                                </div>
                                            </div>
                                            );
                                        })}
                                    </div>
                                </div>

                            </div>
                        }
                        {/* </Collapse> */}
                    </div>
                    <div className="col-1 form-group ">
                        <select className="form-control on-focus-cls custom-form-control sort advsearch_select2 selectpicker" title="Sort By" name="sort_by" title="Sort" onChange={props.sort_by} style={{ 'borderRadius': '0px' }}>
                            <option value="dom_high">Sort by</option>
                            <option value="price_low">Price (Lo-Hi)</option>
                            <option value="price_high">Price (Hi-Lo)</option>
                            <option value="dom_low">Dom (Lo-Hi)</option>
                            <option value="dom_high">Dom (Hi-Lo)</option>
                        </select>
                    </div>
                    <div className="col-md-1 col-sm-1 col-xs-12 col-lg-1">
                        <Button extraProps={{
                            size: "md",
                            className: "btn border   reset-btn",
                            type: "button",
                            value: "Reset",
                            text: "Clear",
                            onClick: props.resetFilter
                        }} />
                    </div>
                </div>
                {/* <div className="mb-2"></div> */}
                <div className={'row labelSection'} >
                    <div className="col-7">
                        <p><i>{props.searchLabel}</i></p>
                    </div>
                    <div className="col-5 gridMapViewBtn">
                        <Button extraProps={{
                            size: "md",
                            className: "gridMapView btn",
                            type: props.type ? props.type : "button",
                            value: props.value ? props.value : "",
                            text: props.savedSearch,
                            onClick: props.saveSearched
                        }} />
                        <Button extraProps={{
                            size: "md",
                            className: "gridMapView btn",
                            type: props.type ? props.type : "button",
                            value: props.value ? props.value : "",
                            text: props.text,
                            onClick: props.showGridMap
                        }} />
                    </div>
                </div>
            </div>
            <style jsx>
                {`
                     .row > * {
                        padding-right:0px !important;
                        padding-left:0px !important;
                    }
               `}
            </style>
        </>
    );
};
export default MapHeader;



