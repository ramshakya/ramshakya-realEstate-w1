import React from "react";
import { ToastContainer, toast } from 'react-toastify';
import * as echarts from 'echarts';
import {
    agentId,
    marketStatsFilterData,
    medianAvgDomApi, soldActive, medianRentalApi, propertyTyprDist, absorptionData
} from "./../constants/GlobalConstants";
import ReactECharts from 'echarts-for-react';
import API from "../ReactCommon/utility/api";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import { Row, Col, Modal, Form } from "react-bootstrap";
import Button from "../ReactCommon/Components/Button";
import SimpleButton from "../ReactCommon/Components/SimpleButton";
import Autocomplete from "../ReactCommon/Components/AutoSuggestion";
import Loader1 from './../components/loader/loader';
//Git check
class MarketStats extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            shimmer: true,
            currentPage: 1,
            modalShow: false,
            searchName: "",
            frequency: "",
            btnShow: true,
            loaderState: false,
            propertyType: [],
            cities: [],
            community: [],
            optionsMedianAvgDom: "",
            optionsSoldActive: "",
            marketsTemperature: "",
            investorDemands: "",
            medianRentals: "",
            soldPriceDist: "",
            propTypeDistribution: "",
            searchFilter: {
                propertyType: "",
                City: "",
                community: "",
                date: 6,
            },
            isActive: 2,
            medianAvgDomNodata: false,
            soldActiveNodata: false,
            medianRentalNodata: false,
            marketTemperatureNodata: false,
            propertyTypeDistributionNodata: false,
        };
        this.getFilterdata = this.getFilterdata.bind(this);
        this.handleTypeHead = this.handleTypeHead.bind(this);
        this.soldAndActiveList = this.soldAndActiveList.bind(this);
        this.medianAvgDom = this.medianAvgDom.bind(this);
        this.marketTemperature = this.marketTemperature.bind(this);
        this.investorDemand = this.investorDemand.bind(this);
        this.medianRental = this.medianRental.bind(this);
        this.dataFormatter = this.dataFormatter.bind(this);
        this.soldPriceDistribution = this.soldPriceDistribution.bind(this);
        this.propertyTypeDistribution = this.propertyTypeDistribution.bind(this);
        this.getStatsData = this.getStatsData.bind(this);
        this.toggleDate = this.toggleDate.bind(this);

        // this.dataFormatter = this.dataFormatter.bind(this);

    }
    componentDidMount() {
        this.getFilterdata();
        this.getStatsData();
    }
    handleTypeHead(obj = null, name = null, e) {
        const { searchFilter } = this.state;
        if (obj !== null && name !== null) {
            searchFilter[name] = obj.value;
        }
        this.getStatsData();
    }
    toggleDate(e) {
        const { searchFilter, isActive } = this.state;
        searchFilter.date = e.target.value;
        this.setState({
            isActive: e.target.dataset.set
        })
        this.getStatsData();
    }
    getStatsData() {
        this.setState({
            optionsMedianAvgDom:"",
            optionsSoldActive:"",
            marketsTemperature:"",
            medianRentals:"",
            propTypeDistribution:"",
        });
        this.medianAvgDom();
        this.soldAndActiveList();
        this.marketTemperature();
        this.investorDemand();
        this.medianRental();
        this.soldPriceDistribution();
        this.propertyTypeDistribution();
    }
    dataFormatter(obj) {
        // prettier-ignore
        var pList = ['demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo', 'demo'];
        var temp;
        for (var year = 2002; year <= 2011; year++) {
            var max = 0;
            var sum = 0;
            temp = obj[year];
            for (var i = 0, l = temp.length; i < l; i++) {
                max = Math.max(max, temp[i]);
                sum += temp[i];
                obj[year][i] = {
                    name: pList[i],
                    value: temp[i]
                };
            }
            obj[year + 'max'] = Math.floor(max / 100) * 100;
            obj[year + 'sum'] = sum;
        }
        return obj;
    }
    medianRental() {
        // medianRentalApi
        let propertySearchFilter = this.state.searchFilter
        API.jsonApiCall(medianRentalApi, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let dataList = ['20223-07', '2020-09', '2019-01', '2018-03', '20223-07', '2020-09', '2019-01', '2018-03', '2017-05', '2016-06', '2015-09', '2014-11', '2014-01', '2013-03', '2013-05', '2013-01'];
                const date = res.date.reverse();
                const median = res.median.reverse();
                const newList = res.newList.reverse();
                const totalLease = res.totalLease.reverse();
                dataList = date;
                if (date.length < 1 && median.length < 1 && newList.length < 1 && totalLease.length < 1) {
                    this.setState({
                        medianRentalNodata: true
                    });
                }
                var dataMap = {};

                let option = {
                    baseOption: {
                        timeline: {
                            axisType: 'category',
                            label: {
                                formatter: function (s) {
                                    return new Date(s).getFullYear();
                                }
                            }
                        },
                        title: {
                            subtext: ''
                        },
                        tooltip: {},
                        legend: {
                            left: 'right',
                            data: ['Median Rate Price', 'Total Lease', 'New Listings'],
                        },
                        // calculable: true,
                        grid: {
                            top: 80,
                            bottom: 100,
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: {
                                    type: 'shadow',
                                    label: {
                                        show: true,
                                        formatter: function (params) {
                                            return params.value.replace('\n', '');
                                        }
                                    }
                                }
                            }
                        },
                        xAxis: [
                            {
                                axisLabel: { interval: 0, rotate: 30 },
                                type: 'category',
                                // axisLabel: { interval: 0 },
                                data: dataList,
                                splitLine: { show: false }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value',
                                name: ''
                            }
                        ],
                        series: [
                            { name: 'Median Rate Price', type: 'line' },
                            { name: 'Total Lease', type: 'bar' },
                            { name: 'New Listings', type: 'bar' },
                        ]
                    },
                    options: [
                        {
                            title: { text: '' },
                            series: [
                                { data: median },
                                { data: newList },
                                { data: totalLease },
                            ]
                        },

                    ]
                };
                this.setState({
                    medianRentals: option
                });
            })
            .catch(() => {

            });
    }
    investorDemand() {
        let dataList = ['2021-07', '2020-09', '2019-01', '2018-03', '2017-05', '2016-06', '2015-09', '2014-11', '2014-01', '2013-03', '2013-05', '2013-01'];
        let option = {
            title: {
                text: ''
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                    label: {
                        show: true,
                        formatter: function (params) {
                            return params.value.replace('\n', '');
                        }
                    }
                }
            },
            legend: {
                data: ['Rent Ratio']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                axisLabel: { interval: 0, rotate: 30 },
                type: 'category',
                data: dataList
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Rent Ratio',
                    type: 'line',
                    step: 'start',
                    data: [120, 132, 101, 134, 90, 230, 210]
                }
            ]
        };
        this.setState({
            investorDemands: option
        });
    }
    marketTemperature() {
        let propertySearchFilter = this.state.searchFilter
        API.jsonApiCall(absorptionData, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let data = [2, 5, 9, 26, 28, 70, 175, 182, 48, 18, 6, 2];
                let dataList = ['2021-07', '2020-09', '2019-01', '2018-03', '2017-05', '2016-06', '2015-09', '2014-11', '2014-01', '2013-03', '2013-05', '2013-01'];
                dataList = res.date.reverse();
                data = res.absorptionData.reverse();
                if (data.length < 1) {
                    this.setState({
                        marketTemperatureNodata: true
                    });
                }
                let option = {
                    title: {
                        text: '',
                        left: '1%'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    // grid: {
                    //     left: '5%',
                    //     right: '5%',

                    // },
                    xAxis: {
                        axisLabel: { interval: 0, rotate: 25 },
                        data: dataList.map(function (item) {
                            return item;
                        })
                    },
                    yAxis: {},
                    toolbox: {
                        right: 10,
                        show: false,
                        feature: {
                            dataZoom: {
                                yAxisIndex: 'none'
                            },
                            restore: {},
                            saveAsImage: {}
                        }
                    },
                    visualMap: {
                        top: 50,
                        right: 10,
                        show: false,
                        pieces: [
                            {
                                lte: 0,
                                color: '#999'
                            },
                            {
                                gt: 0,
                                lte: 100,
                                color: '#93CE07'
                            },
                            {
                                gt: 100,
                                lte: 500,
                                color: '#FBDB0F'
                            },
                            {
                                gt: 500,
                                lte: 1000,
                                color: '#FF0000'
                            },

                            {
                                gt: 5000,
                                color: '#FF0000'
                            }
                        ],
                        outOfRange: {
                            color: '#FF0000'
                        }
                    },
                    series: {
                        name: 'Total Sold',
                        type: 'line',
                        data: data.map(function (item) {
                            return item;
                        }),
                        markLine: {
                            silent: true,
                            lineStyle: {
                                color: '#333'
                            },
                            data: [
                                {
                                    yAxis: 100
                                },
                                {
                                    yAxis: 500
                                },
                                {
                                    yAxis: 1000
                                },
                                {
                                    yAxis: 1500
                                },
                                {
                                    yAxis: 2000
                                },
                                {
                                    yAxis: 2500
                                }
                                , {
                                    yAxis: 3000
                                }, {
                                    yAxis: 3500
                                }, {
                                    yAxis: 4000
                                }
                                , {
                                    yAxis: 4500
                                }
                                , {
                                    yAxis: 5000
                                }
                            ]
                        }
                    }
                }
                this.setState({
                    marketsTemperature: option
                });
            })
            .catch(() => {

            });

    }
    medianAvgDom() {
        let propertySearchFilter = this.state.searchFilter
        API.jsonApiCall(medianAvgDomApi, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                const dates = res.date;
                const dom = res.dom;
                const median = res.median;
                const colors = ['#5470C6', '#db0814', '#EE6666'];
                let dataList = ['2021-07', '2020-09', '2019-01', '2018-03', '2017-05', '2016-06', '2015-09', '2014-11', '2014-01', '2013-03', '2013-05', '2013-01'];
                dataList = dates;
                if (dates.length < 1 && dom.length < 1 && median.length < 1) {
                    this.setState({
                        medianAvgDomNodata: true
                    });
                }
                let option = {
                    color: colors,
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow',
                            label: {
                                show: true,
                                formatter: function (params) {
                                    return params.value.replace('\n', '');
                                }
                            }
                        }
                    },
                    grid: {
                        right: '20%'
                    },
                    toolbox: {
                        feature: {
                            dataView: { show: false, readOnly: false },
                            restore: { show: false },
                            saveAsImage: { show: false }
                        }
                    },
                    legend: {
                        data: ['Median', 'Average Dom']
                    },
                    xAxis: [

                        {
                            type: 'category',
                            axisTick: {
                                alignWithLabel: true
                            },
                            axisLabel: { interval: 0, rotate: 25 },
                            // prettier-ignore
                            data: dataList
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value',
                            name: '',
                            position: 'left',
                            alignTicks: true,
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: colors[2]
                                }
                            },
                            axisLabel: {
                                formatter: '$ {value}'
                            }
                        },
                        {
                            type: 'value',
                            name: '',
                            position: 'right',
                            alignTicks: true,
                            offset: 80,
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: colors[1]
                                }
                            },
                            axisLabel: {
                                formatter: '{value}'
                            }
                        },

                    ],
                    series: [

                        {
                            name: 'Median',
                            type: 'bar',
                            large: true,
                            data: median
                        },
                        {
                            name: 'Average Dom',
                            type: 'line',
                            yAxisIndex: 1,
                            large: true,
                            data: dom
                        }
                    ]
                }
                this.setState({
                    optionsMedianAvgDom: option
                });
            })
            .catch(() => {

            });
    }
    soldAndActiveList() {

        let propertySearchFilter = this.state.searchFilter
        API.jsonApiCall(soldActive, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                let dates = res.dates
                let active = res.activeList
                let sold = res.soldList
                let newList = res.newList
                const colors = ['#5470C6', '#0083b4', '#db0814'];
                let dataList = ['2021-07', '2020-09', '2019-01', '2018-03', '2017-05', '2016-06', '2015-09', '2014-11', '2014-01', '2013-03', '2013-05', '2013-01'];
                dataList = dates;
                if (dates.length < 1 && active.length < 1 && sold.length < 1 && newList.length < 1) {
                    this.setState({
                        soldActiveNodata: true
                    });
                }
                let option = {
                    color: colors,
                    tooltip: {
                        trigger: 'axis',

                        axisPointer: {
                            type: 'shadow',
                            label: {
                                show: true,
                                formatter: function (params) {
                                    return params.value.replace('\n', '');
                                }
                            }
                        }
                    },
                    grid: {
                        right: '20%'
                    },
                    toolbox: {
                        feature: {
                            dataView: { show: false, readOnly: false },
                            restore: { show: false },
                            saveAsImage: { show: false }
                        }
                    },
                    legend: {
                        data: ['Active Listings', 'Total Sold', 'New Listings']
                    },
                    xAxis: [
                        {
                            axisLabel: { interval: 0, rotate: 25 },
                            type: 'category',
                            axisTick: {
                                alignWithLabel: true
                            },
                            // prettier-ignore
                            data: dataList
                        }
                    ],
                    yAxis: [
                        {

                            type: 'value',
                            name: '',
                            position: 'right',
                            alignTicks: true,
                            show: false,
                            offset: 80,
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: colors[1]
                                }
                            },
                            axisLabel: {
                                formatter: '{value}'
                            }
                        },
                        {
                            type: 'value',
                            name: '',
                            position: 'left',
                            alignTicks: true,
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: colors[2]
                                }
                            },
                            axisLabel: {
                                formatter: '$ {value}'
                            }
                        },
                        {
                            type: 'value',
                            name: '',
                            position: 'left',
                            alignTicks: true,
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: colors[3]
                                }
                            },
                            axisLabel: {
                                formatter: '$ {value}'
                            }
                        }
                    ],
                    series: [

                        {
                            name: 'Active Listings',
                            type: 'bar',
                            large: true,
                            data: active
                        },
                        {
                            name: 'Total Sold',
                            type: 'line',
                            yAxisIndex: 1,
                            large: true,
                            data: sold
                        },
                        {
                            name: 'New Listings',
                            type: 'line',
                            yAxisIndex: 1,
                            large: true,
                            data: newList
                        }
                    ]
                }
                this.setState({
                    optionsSoldActive: option
                });
            })
            .catch(() => {

            });
    }
    soldPriceDistribution() {
        // prettier-ignore
        let dataAxis = ['$100', '$165', '$152', '$212', '$215', '$120', '$121', '$140', '$40', '$251', '$21', '$201', '$215', '$120', '$121', '$140', '$40', '$251', '$21', '$201'];
        // prettier-ignore
        let data = [220, 182, 191, 234, 290, 330, 310, 123, 442, 321, 90, 149, 210, 122, 133, 334, 198, 123, 125, 220];
        let yMax = 500;
        let option = {
            title: {
                text: '',
                subtext: ''
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                    label: {
                        show: true,
                        formatter: function (params) {
                            return params.value.replace('\n', '');
                        }
                    }
                }
            },
            xAxis: {
                data: dataAxis,
                axisLabel: { interval: 0, rotate: 25 },
                axisTick: {
                    show: false
                },
                axisLine: {
                    show: false
                },
                z: 10
            },
            yAxis: {
                axisLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    color: '#999'
                }
            },
            series: [
                {
                    type: 'bar',
                    showBackground: false,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#db0810' },
                            { offset: 0.5, color: '#db0811' },
                            { offset: 1, color: '#db0812' }
                        ])
                    },
                    data: data
                }
            ]
        };
        this.setState({
            soldPriceDist: option
        });
    }
    propertyTypeDistribution() {
        let propertySearchFilter = this.state.searchFilter
        API.jsonApiCall(propertyTyprDist, propertySearchFilter, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                const attRowTwnHouse = res.attRowTwnHouse
                const condoApt = res.condoApt
                const condoTownhouse = res.condoTownhouse
                const detached = res.detached
                const others = res.others
                if (attRowTwnHouse.length < 1 && condoApt.length < 1 && condoTownhouse.length < 1 && detached.length < 1 && others.length < 1) {
                    this.setState({
                        propertyTypeDistributionNodata: true
                    });
                }
                let option = {
                    title: {
                        text: '',
                        subtext: '',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        orient: 'horizontal',
                    },
                    series: [
                        {
                            name: 'Access From',
                            type: 'pie',
                            radius: '50%',
                            data: [
                                { value: condoApt, name: 'Condo Apt' },
                                { value: detached, name: 'Detached' },
                                { value: condoTownhouse, name: 'Condo Townhouse' },
                                { value: attRowTwnHouse, name: 'Att/Row/Twnhouse' },
                                { value: others, name: 'Others' }
                            ],
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };
                this.setState({
                    propTypeDistribution: option
                });
            })
            .catch(() => {

            });
    }
    // marketStatsFilterData
    getFilterdata() {
        API.jsonApiCall(
            marketStatsFilterData,
            {},
            "GET",
            null,
            {
                "Content-Type": "application/json",
            }
        ).then((getFilterData) => {

            if (getFilterData) {
                try {
                    this.setState({
                        shimmer: false
                    });
                    // 
                    let temp = getFilterData.propertyType.map((item) => {
                        let obj = {
                            'value': item,
                            'text': item,
                        }
                        return obj;
                    });
                    this.setState({
                        propertyType: temp
                    });
                    temp = getFilterData.city.map((item) => {
                        let obj = {
                            'value': item,
                            'text': item,
                        }
                        return obj;
                    });
                    this.setState({
                        cities: temp
                    });
                    temp = getFilterData.community.map((item) => {
                        let obj = {
                            'value': item,
                            'text': item,
                        }
                        return obj;
                    });
                    this.setState({
                        community: temp
                    });
                } catch (error) {
                }
            } else {

            }
        });
    }
    render() {
        if (this.state.shimmer) {
            return <ShimmerEffect count={2} />;
        }
        return (
            <>
                <div className="p-4 shop-content">
                    <div className="container ">
                        <div className="row market-stats">
                            <div className="col-md-3">
                                <h3>Market Trend</h3>
                            </div>
                            <div className="col-md-3">
                                <Autocomplete
                                    inputProps={{
                                        id: "propertyType",
                                        name: "propertyType",
                                        className:
                                            "form-control bg-white",
                                        placeholder: "Property Type",
                                        title: "Property Type",
                                        readOnly: true,
                                    }}
                                    allList={this.state.propertyType}
                                    cb={this.handleTypeHead}
                                    extraProps={{}}
                                    selectedText={this.state.propertyType ? this.state.propertyType.text : ""}
                                />

                            </div>
                            <div className="col-md-3">
                                <Autocomplete
                                    inputProps={{
                                        id: "city",
                                        name: "City",
                                        className:
                                            "form-control bg-white",
                                        placeholder: "City",
                                        title: "City",
                                        readOnly: true,
                                    }}
                                    allList={this.state.cities}
                                    cb={this.handleTypeHead}
                                    extraProps={{}}
                                    selectedText={this.state.cities ? this.state.cities.text : ""}
                                />
                            </div>
                            <div className="col-md-3">
                                <Autocomplete
                                    inputProps={{
                                        id: "community",
                                        name: "community",
                                        className:
                                            "form-control bg-white",
                                        placeholder: "Community",
                                        title: "Community",
                                        readOnly: true,
                                    }}
                                    allList={this.state.community}
                                    cb={this.handleTypeHead}
                                    extraProps={{}}
                                    selectedText={this.state.community ? this.state.community.text : ""}
                                />
                            </div>
                        </div>
                        <div className="row">
                        <div className="col-md-12 mt-4 mb-4">
                                <div className="btn-containers">
                                    <div className="row">
                                        <div className="col-md-2 p-0">
                                            <button
                                                size="md"
                                                className={`gridMapView w-100 btn  ${this.state.isActive == 1 ? "rentBtn" : ""
                                                    }`}
                                                type="button"
                                                value="3"
                                                text="3 Months"
                                                data-set="1"
                                                onClick={this.toggleDate}
                                            >3 Months</button>
                                        </div>
                                    
                                    <div className="col-md-2 p-0">
                                        <button
                                            size="md"
                                            className={`gridMapView w-100 btn  ${this.state.isActive == 2 ? "rentBtn" : ""
                                                }`}
                                            type="button"
                                            value="6"
                                            text="6 Months"
                                            data-set="2"
                                            onClick={this.toggleDate}>
                                            6 Months
                                            </button>
                                    </div>
                                    <div className="col-md-2 p-0">
                                        <button
                                            size="md"
                                            className={`gridMapView w-100 btn  ${this.state.isActive == 3 ? "rentBtn" : ""
                                                }`}
                                            type="button"
                                            value="9"
                                            text="9 Months"
                                            data-set="3"
                                            onClick={this.toggleDate}
                                        >9 Months
                                    </button>
                                    </div>
                                    <div className="col-md-2 p-0">
                                        <button
                                            size="md"
                                            className={`gridMapView w-100 btn  ${this.state.isActive == 4 ? "rentBtn" : ""
                                                }`}
                                            type="button"
                                            value="12"
                                            text="1 Year"
                                            data-set="4"
                                            onClick={this.toggleDate}
                                        >1 Years
                                    </button>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div className="col-md-9">
                                <div className="row">
                                    <div className="col-md-12">
                                        <span>Median Price and Average Days On Market *</span>
                                        <p></p>
                                        {
                                            this.state.medianAvgDomNodata ? <div className="stats-data-not-found"> <h4>No Data Found !</h4> <p>We are Collecting Data for this City.</p></div> : <>
                                                {
                                                    this.state.optionsMedianAvgDom ? <>
                                                        <ReactECharts
                                                            option={this.state.optionsMedianAvgDom}
                                                            lazyUpdate={true}
                                                        />
                                                    </> : <>
                                                        <img src="/loader.gif" style={{ "width": "5%", "position": "relative", "left": "50%" }} />
                                                    </>
                                                }
                                            </>
                                        }
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12 mt-5">
                                        <span>Sold & Active Listings *</span>
                                        {
                                            this.state.soldActiveNodata ? <div className="stats-data-not-found"> <h4>No Data Found !</h4> <p>We are Collecting Data for this City.</p></div> : <>
                                                {
                                                    this.state.optionsSoldActive ? <>
                                                        <ReactECharts
                                                            option={this.state.optionsSoldActive}
                                                            lazyUpdate={true}
                                                        />
                                                    </> : <>
                                                    <img src="/loader.gif" style={{ "width": "5%", "position": "relative", "left": "50%" }} />
                                                    </>
                                                }
                                            </>
                                        }
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-12 mt-5">
                                        <span>Market Temperature (absorption rate) * </span>
                                        <br />
                                        <span className="contentsText">* Absorption rate indicates how fast homes are selling. It's calculated as homes sold divided by homes currently listed.</span>
                                        <p></p>
                                        {
                                            this.state.marketTemperatureNodata ? <div className="stats-data-not-found"> <h4>No Data Found !</h4> <p>We are Collecting Data for this City.</p></div> : <>
                                                {
                                                    this.state.marketsTemperature ? <>
                                                        <ReactECharts
                                                            option={this.state.marketsTemperature}
                                                            lazyUpdate={true}
                                                        />
                                                    </> : <>
                                                        <img src="/loader.gif" style={{ "width": "5%", "position": "relative", "left": "50%" }} />
                                                    </>
                                                }
                                            </>
                                        }
                                    </div>
                                </div>
                                {/*  */}
                                <div className="row">
                                    <div className="col-md-12 mt-5">
                                        <span>Median Rental Price * </span>
                                        <br />
                                        {/*  */}
                                        <span className="contentsText"></span>
                                        <p></p>
                                        {
                                            this.state.medianRentalNodata ? <div className="stats-data-not-found"> <h4>No Data Found !</h4> <p>We are Collecting Data for this City.</p></div> : <>
                                                {
                                                    this.state.medianRentals ? <>
                                                        <ReactECharts
                                                            option={this.state.medianRentals}
                                                            lazyUpdate={true}
                                                        />
                                                    </> : <>
                                                        <img src="/loader.gif" style={{ "width": "5%", "position": "relative", "left": "50%" }} />
                                                    </>
                                                }
                                            </>
                                        }
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12 mt-5">
                                        <span>Property Type Distribution * </span>
                                        <br />
                                        <span className="contentsText">Overview of sold price for All property types in All Communities, Vancouver in the last 6 months:</span>
                                        <p></p>
                                        {
                                            this.state.propertyTypeDistributionNodata ? <div className="stats-data-not-found"> <h4>No Data Found !</h4> <p>We are Collecting Data for this City.</p></div> : <>
                                                {
                                                    this.state.propTypeDistribution ? <>
                                                        <ReactECharts
                                                            option={this.state.propTypeDistribution}
                                                            lazyUpdate={true}
                                                        />
                                                    </> : <>
                                                        <img src="/loader.gif" style={{ "width": "5%", "position": "relative", "left": "50%" }} />
                                                    </>
                                                }
                                            </>
                                        }
                                    </div>
                                </div>
                            </div>

                            <div className="col-md-4">
                                lorem isum
                            </div>
                        </div>
                    </div>
                </div>
            </>
        );
    }
}
export default MarketStats;
