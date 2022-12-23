import React, { useEffect } from "react";
import { Col, Row, Container } from "react-bootstrap";
import Card from "./../ReactCommon/Components/MapCard";
import Autocomplete from './../ReactCommon/Components/AutoSuggestion';
import ShimmerEffect from './../ReactCommon/Components/ShimmerEffect';
import { getPropertiesList, sortStatus, defaultImage, featuredApi, agentId, autoSuggestionApi, initialPropertySearchFilter } from "../constants/GlobalConstants";
import MapLoad from "../ReactCommon/Components/MapLoad";
import API from "../ReactCommon/utility/api";
import Pagination from "../ReactCommon/Components/Pagination";
class FeaturedListings extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            shimmer: true,
            currentPage: 1,
            propertySearchFilter: {
                curr_page: 1
            }
        };
        this.handlePropertyCall = this.handlePropertyCall.bind(this);
        this.fetchAutoSuggestion = this.fetchAutoSuggestion.bind(this);
        this.handleTypeHead = this.handleTypeHead.bind(this);
        this.resetBtn = this.resetBtn.bind(this);
        this.mapRef = React.createRef();
        this.pageChange = this.pageChange.bind(this)
    }
    componentDidMount() {
        this.handleTypeHead();
    }
    handlePropertyCall(coordinates, geometryType) {
    }
    fetchAutoSuggestion(fieldValue, fieldName, cb) {
        let payload = {
            query: "default",
        };
        if (fieldValue) {
            payload.query = fieldValue;
        }
        API.jsonApiCall(autoSuggestionApi, payload, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
            cb({ allList: res });
        });
    }
    handleTypeHead(obj = null, name = null) {
        console.log("featuredApi",featuredApi);
        let testfeaturedApi = "http://127.0.0.1:8000/api/v1/services/global/featuredList";
        setTimeout(() => {
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        }, 0);
        const { propertySearchFilter } = this.state;
        propertySearchFilter.AgentId = 2;
        const stateData = {
            mapData: [],
            allData: [],
            apiCall: false,
        };
        if (obj !== null && name !== null) {
            propertySearchFilter[name] = obj.value;
            this.setState({
                [name]: obj,
            });
        }
        // this.setState({ apiCall: true });
        API.jsonApiCall(featuredApi, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let center = this.state.center;
                stateData.shimmer = false;
                if (res.alldata) {
                    stateData.allData = res.alldata;
                    stateData.totalData = res.total;
                    stateData.offset = res.offset;
                    stateData.limitCount = res.limit;
                }
                this.setState(stateData);
            })
            .catch(() => {
                this.setState(stateData);
            });
    }

    resetBtn() {
        const stateData = { apiCall: false };
        this.setState(
            {
                propertySearchFilter: JSON.parse(
                    JSON.stringify(initialPropertySearchFilter)
                ),
                text_search: "",
                propertyType: "",
                propertySubType: "",
                price_min: "",
                price_max: "",
                baths: "",
                beds: "",
                status: "",
                sort_by: "",
                apiCall: true,
            },
            () => {
                this.handleTypeHead();
            }
        );
    }
    pageChange(e) {
        let propertyPayLoad = this.state.propertySearchFilter;
        propertyPayLoad.curr_page = e
        this.setState({
            currentPage: e,
            propertySearchFilter: propertyPayLoad
        }, () => {
            this.handleTypeHead()
        })
    }
    render() {
        if (this.state.shimmer) {

            return <ShimmerEffect count={2} />;
        }
        return (
            <>
                <Container fluid className="feedback-section similarProperties">
                    <Row >
                        <Col md={12} className="">
                            <div className="title-wrapper">
                                <h3 className="listing-title">Featured MLS Listings</h3>
                                <hr />
                            </div>
                        </Col>
                    </Row>
                    <Row className="mt-0">
                        <Col md={6} className="">
                            <div className="">
                                <Autocomplete
                                    inputProps={{
                                        id: "autoSuggestion",
                                        name: "text_search",
                                        className: "auto form-control auto-suggestion-inp inp",
                                        placeholder: "MLS# ,Address,Neighborhood,City",
                                        title: "Search @MLS , City , Neighborhood",
                                        readOnly: false,
                                    }}
                                    allList={[]}
                                    autoCompleteCb={this.fetchAutoSuggestion}
                                    cb={this.handleTypeHead}
                                    selectedText={this.props.text_search ? this.props.text_search.text : ''}
                                    // callBackMap={props.mapCallBack}
                                    extraProps={{}}
                                />
                            </div>
                        </Col>
                        <Col md={2}>

                        </Col>
                        <Col md={2}>
                            <div className="">
                                <Autocomplete
                                    inputProps={{
                                        id: "sort_by",
                                        name: "sort_by",
                                        className:
                                            "form-control on-focus-cls custom-form-control bg-white",
                                        placeholder: "Sort By",
                                        title: "Sort By",
                                        readOnly: true,
                                    }}
                                    allList={sortStatus}
                                    cb={this.handleTypeHead}
                                    selectedText={this.props.sort_by ? this.props.sort_by : ""}
                                    extraProps={{}}

                                />
                            </div>
                        </Col>
                        <Col md={2}>
                        </Col>
                    </Row>
                    <Row className="mt-4  mt-3">
                        {this.state.allData.map((item, key) => {
                            return (
                               <Col md={3} className="mt-4 detailCards">
                                    <Card
                                        showIsFav={true}
                                        openUserPopup={true}
                                        openLoginCb={this.props.togglePopUp}
                                        isLogin={this.props.isLogin}
                                        item={item}
                                        key={key}
                                        defaultImage={defaultImage}
                                    />
                                </Col>
                            );
                        })}
                    </Row>
                    <Row className="mt-4">
                        <Col md={12}>
                            <div className="d-flex justify-content-center" >
                                {this.state.totalData &&
                                    <Pagination
                                        itemsCount={this.state.totalData}
                                        itemsPerPage={this.state.limitCount}
                                        currentPage={this.state.currentPage}
                                        setCurrentPage={this.pageChange}
                                        alwaysShown={false}
                                    />
                                }
                            </div>
                        </Col>
                    </Row>
                </Container>
            </>
        );
    }
}
export default FeaturedListings;