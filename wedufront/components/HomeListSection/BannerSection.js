
import React, { useState, useEffect, useRef } from "react";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion"
import { useRouter } from 'next/router'
import Constants from '../../constants/GlobalConstants';
import data1 from "../../public/json/data.json";
const BannerSection = (props) => {
    const router = useRouter()
    const fetchPropertyData = async (fieldValue, fieldName, cb) => {
        let payload = {
            "query": "default",
            "type": "",
        }
        let dataList = [];
        if (fieldValue) {
           
            let matches = data1.filter(findValue => {
                const regex = new RegExp(`^${fieldValue}`, 'gi')
                return findValue.value.match(regex)
            })
            if (fieldValue.length === 0) {
                matches = [];
            }
            if (matches.length > 0) {
                // console.log("matches", matches[0].category);
                let temp_list = [];
                let temp_city = '';
                let temp_community = false;
                matches.map((item, key) => {
                    if (key == 0) {
                        if (item.category == 'Cities') {
                            let obj = {
                                "isHeading": true,
                                "text": "Cities",
                                "value": "Cities",
                                "category": "Cities",
                                "group": "City"
                            }
                            temp_list.push(obj);
                            temp_list.push(item);

                        }
                        if (item.category === 'Neighborhood') {
                            let obj = {
                                "isHeading": true,
                                "text": "Neighborhood",
                                "value": "Neighborhood",
                                "category": "Neighborhood",
                                "group": "Community"
                            }
                            temp_list.push(obj);
                            temp_list.push(item);
                        }
                    }
                    else {
                        if (item.category === 'Neighborhood') {
                            if (!temp_community) {
                                let obj = {
                                    "isHeading": true,
                                    "text": "Neighborhood",
                                    "value": "Neighborhood",
                                    "category": "Neighborhood",
                                    "group": "Community"
                                }
                                temp_list.push(obj);
                                temp_community = true
                            }
                        }
                        temp_list.push(item);
                    }

                })
                cb({ allList: temp_list });
            }
            else {
                // api calling
                let requestOptions = {};
                payload.query = fieldValue;
                payload.type = 'address';
                requestOptions = {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                };
                
                fetch(Constants.autoSuggestionApi, requestOptions).then((response) =>
                    response.text()).then((res) => JSON.parse(res))
                    .then((json) => {
                        if (json.length) {
                            dataList = dataList.concat(
                                json
                            );
                            cb({ allList: dataList });
                        }
                    }).catch((e) => {
                        console.log("error", e);
                    });
                // if whitespace then do not call listing id
                if(fieldValue.indexOf(' ') <= 0){
                    
                    payload.query = fieldValue;
                    payload.type = 'listingId';
                    requestOptions = {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    };
                    fetch(Constants.autoSuggestionApi, requestOptions).then((response) =>
                        response.text()).then((res) => JSON.parse(res))
                        .then((json) => {
                            if (json.length) {
                                dataList = dataList.concat(
                                    json
                                );
                                cb({ allList: dataList });
                            }
                        }).catch((e) => {
                            console.log("error", e);
                        });
                }
                
            }
        }
        else {
            localStorage.removeItem('suggestionList');
            cb({ allList: [] });
        }
    }
    const getValues = async (e) => {
        setValues(e.target.value, e.target.name);
    }
    const setValues = async (fieldValue, fieldName) => {

        let Prevfilters = JSON.parse(localStorage.getItem("filters"))
        let filters = {
            searchFilter: {},
            preField: {}
        }
        if (Prevfilters) {
            filters = Prevfilters
        }
        if (fieldName === "autoSuggestion") {
            filters.searchFilter.text_search = fieldValue.value
            filters.preField.text_search = fieldValue;
        }
        localStorage.setItem("filters", JSON.stringify(filters));
        redirectToMap();
    };
    const redirectToMap = async (e) => {
        let filters = JSON.parse(localStorage.getItem('filters'));
        let group = filters.preField.text_search.group;
        let params="/map";
        if(group=="StandardAddress"){
            let textSearch = filters.preField.text_search.ListingId;
            let StandardAddress = filters.preField.text_search.value;
            params+=`?text_search=${textSearch}&group=ListingId&StandardAddress=${StandardAddress}&status=Sale&propertyType=Residential`
        }
        else{
            let textSearch = filters.preField.text_search.value;
            params+=`?text_search=${textSearch}&group=${group}&status=Sale&propertyType=Residential`
        }
        // console.log(params,"filter on home");
        router.push(params);
    }
    function handleSubmitStop(e) {
        e.preventDefault();
    }
    return (
        <div className="BannerSection" style={{ 'backgroundImage': 'url(' + props.banner + ')' }}>
            <div className="container">
                <div className="row">
                    <div className="col-md-1"></div>
                    <div className="col-md-10 col-lg-10 bannerInner">
                        <div className="row">
                            <div className="col-md-4 col-lg-4 mb-2">
                                <div className="CounterCard">
                                    <h1>{props.resiCount}</h1>
                                    <p>Residential Properties</p>
                                </div>
                            </div>
                            <div className="col-md-4 col-lg-4 mb-2">
                                <div className="CounterCard">
                                    <h2>{props.condosCount}</h2>
                                    <p>Condos Properties</p>
                                </div>
                            </div>
                            <div className="col-md-4 col-lg-4 mb-2">
                                <div className="CounterCard">
                                    <h3>{props.soldCount}</h3>
                                    <p>Sold Properties</p>
                                </div>
                            </div>
                        </div>
                        <h4 className="text-heading pt-3 text-center searchHeadline">Creating Modern Properties Is Our Speciality</h4>
                        <h5 className="text-center search-tagline">With a lot of experience we will help you to create the modern properties you want</h5>
                        <form className="search-boxform pt-3" onSubmit={handleSubmitStop}>
                            <div className="">
                                <div className="row">
                                    {/*<!-- location area  --> */}
                                    <div className="col-lg-2 col-md-2 col-xs-2">

                                    </div>
                                    <div className="col-md-8 col-lg-8">
                                        <div className="row search-box-wrapper">
                                            <div className="col-lg-12 col-md-12">
                                                <div className=""><span className="search-title ml-1" hidden>Search by address, Neighbourhood, MLS #</span></div>
                                                <Autocomplete
                                                    inputProps={{
                                                        id: "autoSuggestion",
                                                        name: "autoSuggestion",
                                                        className: "auto form-control pb-2 border-square",
                                                        placeholder: "Search MLS# ,Address,Neighborhood,City",
                                                        title: "Search @MLS , City , Community",
                                                        readOnly: false,
                                                        autoComplete: "off"

                                                    }}
                                                    allList={[]}
                                                    autoCompleteCb={fetchPropertyData}
                                                    cb={setValues}
                                                    extraProps={{}}

                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-lg-2 col-md-2">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div className="col-md-1"></div>
                </div>
            </div>
        </div>
    )
}
export default BannerSection;