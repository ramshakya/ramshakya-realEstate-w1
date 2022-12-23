import React, { useState, useEffect, useRef } from "react";
import SelectWidget from '../../ReactCommon/Components/SelectWidget'
import Button from '../../ReactCommon/Components/SimpleButton';
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion"
import { useRouter } from 'next/router'

import Constants from '../../constants/GlobalConstants';
const SearchAreaWidget = (props) => {
    // console.log("props search", props);
    const router = useRouter()
    let propData = {
        title: "Property Type", className: "form-select",
        name: "propertyType",
        id: "propertyType"
    }
    let propData1 = {
        title: "Property Sub Type", className: "form-select",
        name: "propertySubType",
        id: "propertySubType"
    }
    let propPrice = {
        title: "Price", className: "form-select",
        id: "price",
        name: "price"
    }

    const [data, setData] = useState([]);
    useEffect(() => {
        // getFiltersData();
    }, []);
    function getFiltersData() {
        let payload = {
            is_search: true
        }
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        };

        fetch(Constants.filterDataApi,
            requestOptions)
            .then((response) =>
                response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                setData(json)
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
                cb({ allList: json })
            }).catch((err) => console.log({ err }));
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
        if (fieldName === "price") {
            filters.searchFilter.price = fieldValue
            filters.preField.price = { text: '', value: fieldValue }
        }
        if (fieldName === "propertyType") {
            filters.searchFilter.propertyType = fieldValue
            filters.preField.propertyType = { text: fieldValue, value: fieldValue }
        }
        if (fieldName === "propertySubType") {
            filters.searchFilter.propertySubType = fieldValue
            filters.preField.propertySubType = { text: fieldValue, value: fieldValue }
        }
        if (fieldName === "autoSuggestion") {
            filters.searchFilter.text_search = fieldValue.value
            filters.preField.text_search = fieldValue
        }
        localStorage.setItem("filters", JSON.stringify(filters));
    };
    const redirectToMap = async (e) => {
        router.push('/map');
    }
    return (
        <>
            {/*<!-- blackBox row  --> */}
            <div className="row">
                <div className="col-sm-12 col-md-10 col-lg-7">
                    <div className="box-block-wrapper mt-3">
                        <h1>Creating Modern Properties Is Our Speciality</h1>
                        <p className="my-5 fz-21">
                            With a lot of experience we will help you to create the modern
                            properties you want
                        </p>
                        {/*<!-- search box area --> */}
                        <form className="search-boxform">
                            <div className="search-box-wrapper">
                                <div className="row">
                                    {/*<!-- location area  --> */}
                                    <div className="col-lg-12 col-md-12 col-xs-12">
                                        <p className="dash-info">Search {props.resiCount} Residential for Sale and {props.condosCount} Condos for Sale with {props.soldCount} Sold/Rented Listings</p>
                                    </div>
                                    <div className="col-lg-10 col-md-6 col-xs-12">
                                        <div className=""><span className="search-title ml-1" hidden>Search by address, Neighbourhood, MLS #</span></div>
                                        <Autocomplete
                                            inputProps={{
                                                id: "autoSuggestion",
                                                name: "autoSuggestion",
                                                className: "auto form-control",
                                                placeholder: "Search",
                                                title: "Search @MLS , City , Community",
                                                readOnly: false,
                                                autocomplete: "off"

                                            }}
                                            allList={[]}
                                            autoCompleteCb={fetchPropertyData}
                                            cb={setValues}
                                            extraProps={{}}

                                        />
                                    </div>

                                    <div className="col-lg-1 col-md-3 search-btn-area col-xs-12">
                                        {/* <button className="common-btn search-btn">Search</button> */}
                                        <p></p>
                                        <Button extraProps={{
                                            size: "md",
                                            className: "common-btn search-btn h-75 py-2 mt-4 rounded",
                                            type: "button",
                                            value: "Search",
                                            text: "Search",
                                            onClick: redirectToMap
                                        }} />
                                        {/* <Button props={{className:"common-btn search-btn",type:"button",children:"Search",setShow:"searProp"}} /> */}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </>

    )
}

export default SearchAreaWidget;

