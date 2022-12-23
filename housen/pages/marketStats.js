import React from "react";
import * as echarts from "echarts";
import {
  agentId,
  marketStatsFilterData,
  marketStatsCitiesData,
  medianAvgDomApi,
  soldActive,
  medianRentalApi,
  propertyTyprDist,
  absorptionData,
  SubType,
} from "./../constants/Global";
import ReactECharts from "echarts-for-react";
import API from "../ReactCommon/utility/api";
import ShimmerEffect from "../ReactCommon/Components/ShimmerEffect";
import detect from "./../ReactCommon/utility/detect";
import Loader1 from "../components/loader/loader1";
import Autocomplete from "../ReactCommon/Components/AutoSuggestion";
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
      selectedCommunity: {},
      optionsMedianAvgDom: "",
      optionsSoldActive: [],
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
      provinces: [
        { text: "Ontario", value: "ON" },
        { text: "British Columbia", value: "BC" },
      ],
      selectProvinces: { text: "Ontario", value: "ON" },
      municipality: [],
      municipalityHeading: [],
      activeCls: "heading-0",
      activeCityCls: "heading-0",
      medianAvgDomNodata: false,
      soldActiveNodata: false,
      medianRentalNodata: false,
      marketTemperatureNodata: false,
      propertyTypeDistributionNodata: false,
      statsLabel: "",
      cityStateLabel: "",
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
    this.showmenu = this.showmenu.bind(this);
    this.hidemenu = this.hidemenu.bind(this);
    this.changeProvince = this.changeProvince.bind(this);
    this.getCitiesdata = this.getCitiesdata.bind(this);
    this.changeMunicipality = this.changeMunicipality.bind(this);
    this.changeCities = this.changeCities.bind(this);

    // this.dataFormatter = this.dataFormatter.bind(this);
  }
  componentDidMount() {
    this.getFilterdata();
    this.getStatsData();
  }
  handleTypeHead(obj = null, name = null, e) {
    const { searchFilter } = this.state;
    if (obj !== null && name !== null) {
      if (obj.value === "All Communities" || obj.value === "All GTA") {
        searchFilter[name] = "";
      } else {
        searchFilter[name] = obj.value;
      }
      if (name === "community") {
        this.setState({
          selectedCommunity: obj,
        });
      }
    }
    this.getStatsData();
  }
  changeProvince(e) {
    this.setState({
      selectProvinces: e,
    });
    this.getFilterdata(e);
  }
  toggleDate(e) {
    const { searchFilter, isActive } = this.state;
    searchFilter.date = e.target.value;
    this.setState({
      isActive: e.target.dataset.set,
    });
    this.getStatsData();
  }
  getStatsData() {
    let propertySearchFilter = this.state.searchFilter;
    this.setState({
      optionsMedianAvgDom: "",
      optionsSoldActive: "",
      marketsTemperature: "",
      medianRentals: "",
      propTypeDistribution: "",
      cityStateLabel:propertySearchFilter.City ? propertySearchFilter.City : "GTA-ALL",
      statsLabel: propertySearchFilter.City
        ? propertySearchFilter.City
        : "Cities",
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
          value: temp[i],
        };
      }
      obj[year + "max"] = Math.floor(max / 100) * 100;
      obj[year + "sum"] = sum;
    }
    return obj;
  }
  medianRental() {
    // medianRentalApi
    let propertySearchFilter = this.state.searchFilter;
    API.jsonApiCall(medianRentalApi, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        let dataList = [
          "20223-07",
          "2020-09",
          "2019-01",
          "2018-03",
          "20223-07",
          "2020-09",
          "2019-01",
          "2018-03",
          "2017-05",
          "2016-06",
          "2015-09",
          "2014-11",
          "2014-01",
          "2013-03",
          "2013-05",
          "2013-01",
        ];
        const date = res.date.reverse();
        const median = res.median.reverse();
        const newList = res.newList.reverse();
        const totalLease = res.totalLease.reverse();
        if (
          date.length < 1 &&
          median.length < 1 &&
          newList.length < 1 &&
          totalLease.length < 1
        ) {
          this.setState({
            medianRentalNodata: true,
          });
        }

        dataList = date;
        var dataMap = {};

        let option = {
          baseOption: {
            timeline: {
              axisType: "category",
              label: {
                formatter: function (s) {
                  return new Date(s).getFullYear();
                },
              },
            },
            title: {
              subtext: "",
            },
            tooltip: {},
            legend: {
              left: "right",
              data: ["Median Rate Price", "Total Lease", "New Listings"],
            },
            // calculable: true,
            grid: {
              top: 80,
              bottom: 100,
              tooltip: {
                trigger: "axis",
                axisPointer: {
                  type: "shadow",
                  label: {
                    show: true,
                    formatter: function (params) {
                      return params.value.replace("\n", "");
                    },
                  },
                },
              },
            },
            xAxis: [
              {
                axisLabel: { interval: 0, rotate: 30 },
                type: "category",
                // axisLabel: { interval: 0 },
                data: dataList,
                splitLine: { show: false },
              },
            ],
            yAxis: [
              {
                axisLabel: { rotate: detect.isMobile() ? -55 : 0 },
                type: "value",
              },
            ],
            series: [
              { name: "Median Rate Price", type: "line" },
              { name: "Total Lease", type: "bar" },
              { name: "New Listings", type: "bar" },
            ],
          },
          options: [
            {
              title: { text: "" },
              series: [
                { data: median },
                { data: newList },
                { data: totalLease },
              ],
            },
          ],
        };
        this.setState({
          medianRentals: option,
        });
      })
      .catch(() => {});
  }
  //doto delete
  investorDemand() {
    let dataList = [
      "2021-07",
      "2020-09",
      "2019-01",
      "2018-03",
      "2017-05",
      "2016-06",
      "2015-09",
      "2014-11",
      "2014-01",
      "2013-03",
      "2013-05",
      "2013-01",
    ];
    let option = {
      title: {
        text: "",
      },
      tooltip: {
        trigger: "axis",
        axisPointer: {
          type: "shadow",
          label: {
            show: true,
            formatter: function (params) {
              return params.value.replace("\n", "");
            },
          },
        },
      },
      legend: {
        data: ["Rent Ratio"],
      },
      grid: {
        left: "3%",
        right: "4%",
        bottom: "3%",
        containLabel: true,
      },
      toolbox: {
        feature: {
          saveAsImage: {},
        },
      },
      xAxis: {
        axisLabel: { interval: 0, rotate: 30 },
        type: "category",
        data: dataList,
      },
      yAxis: {
        type: "value",
      },
      series: [
        {
          name: "Rent Ratio",
          type: "line",
          step: "start",
          data: [120, 132, 101, 134, 90, 230, 210],
        },
      ],
    };
    this.setState({
      investorDemands: option,
    });
  }
  marketTemperature() {
    let propertySearchFilter = this.state.searchFilter;
    API.jsonApiCall(absorptionData, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        let data = [2, 5, 9, 26, 28, 70, 175, 182, 48, 18, 6, 2];
        let dataList = [
          "2021-07",
          "2020-09",
          "2019-01",
          "2018-03",
          "2017-05",
          "2016-06",
          "2015-09",
          "2014-11",
          "2014-01",
          "2013-03",
          "2013-05",
          "2013-01",
        ];
        dataList = res.date.reverse();
        data = res.absorptionData.reverse();
        if (data.length < 1) {
          this.setState({
            marketTemperatureNodata: true,
          });
        }
        let option = {
          title: {
            text: "",
            left: "1%",
          },
          tooltip: {
            trigger: "axis",
          },
          // grid: {
          //     left: '5%',
          //     right: '5%',

          // },
          xAxis: {
            axisLabel: { interval: 0, rotate: 25 },
            data: dataList.map(function (item) {
              return item;
            }),
          },
          yAxis: {
            axisLabel: { rotate: detect.isMobile() ? -55 : 0 },
          },
          toolbox: {
            right: 10,
            show: false,
            feature: {
              dataZoom: {
                yAxisIndex: "none",
              },
              restore: {},
              saveAsImage: {},
            },
          },
          visualMap: {
            top: 50,
            right: 10,
            show: false,
            pieces: [
              {
                lte: 0,
                color: "#999",
              },
              {
                gt: 0,
                lte: 100,
                color: "#93CE07",
              },
              {
                gt: 100,
                lte: 500,
                color: "#FBDB0F",
              },
              {
                gt: 500,
                lte: 1000,
                color: "#FF0000",
              },

              {
                gt: 5000,
                color: "#FF0000",
              },
            ],
            outOfRange: {
              color: "#FF0000",
            },
          },
          series: {
            name: "Total Sold",
            type: "line",
            data: data.map(function (item) {
              return item;
            }),
            markLine: {
              silent: true,
              lineStyle: {
                color: "#333",
              },
              data: [
                {
                  yAxis: 100,
                },
                {
                  yAxis: 500,
                },
                {
                  yAxis: 1000,
                },
                {
                  yAxis: 1500,
                },
                {
                  yAxis: 2000,
                },
                {
                  yAxis: 2500,
                },
                {
                  yAxis: 3000,
                },
                {
                  yAxis: 3500,
                },
                {
                  yAxis: 4000,
                },
                {
                  yAxis: 4500,
                },
                {
                  yAxis: 5000,
                },
              ],
            },
          },
        };
        this.setState({
          marketsTemperature: option,
        });
      })
      .catch(() => {});
  }
  medianAvgDom() {
    let propertySearchFilter = this.state.searchFilter;
    API.jsonApiCall(medianAvgDomApi, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        const dates = res.date;
        const dom = res.dom;
        const median = res.median;
        if (dates.length < 1 && dom.length < 1 && median.length < 1) {
          this.setState({
            medianAvgDomNodata: true,
          });
        }
        const colors = ["#5470C6", "#db0814", "#EE6666"];
        let dataList = [
          "2021-07",
          "2020-09",
          "2019-01",
          "2018-03",
          "2017-05",
          "2016-06",
          "2015-09",
          "2014-11",
          "2014-01",
          "2013-03",
          "2013-05",
          "2013-01",
        ];
        dataList = dates;
        let option = {
          color: colors,
          tooltip: {
            trigger: "axis",
            axisPointer: {
              type: "shadow",
              label: {
                show: true,
                formatter: function (params) {
                  return params.value.replace("\n", "");
                },
              },
            },
          },
          grid: {
            right: "20%",
          },
          toolbox: {
            feature: {
              dataView: { show: false, readOnly: false },
              restore: { show: false },
              saveAsImage: { show: false },
            },
          },
          legend: {
            data: ["Median", "Average Dom"],
          },
          xAxis: [
            {
              type: "category",
              axisTick: {
                alignWithLabel: true,
              },
              axisLabel: { interval: 0, rotate: 25 },
              // prettier-ignore
              data: dataList,
            },
          ],
          yAxis: [
            {
              type: "value",
              name: "",
              position: "left",
              alignTicks: true,
              axisLine: {
                show: true,
                lineStyle: {
                  color: colors[2],
                },
              },
              axisLabel: {
                formatter: "$ {value}",
              },
            },
            {
              type: "value",
              name: "",
              position: "right",
              alignTicks: true,
              offset: 80,
              axisLine: {
                show: true,
                lineStyle: {
                  color: colors[1],
                },
              },
              axisLabel: {
                formatter: "{value}",
              },
            },
          ],
          series: [
            {
              name: "Median",
              type: "bar",
              large: true,
              data: median,
            },
            {
              name: "Average Dom",
              type: "line",
              yAxisIndex: 1,
              large: true,
              data: dom,
            },
          ],
        };
        this.setState({
          optionsMedianAvgDom: option,
        });
      })
      .catch(() => {});
  }
  soldAndActiveList() {
    let propertySearchFilter = this.state.searchFilter;
    API.jsonApiCall(soldActive, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        let dates = res.dates;
        let active = res.activeList;
        let sold = res.soldList;
        let newList = res.newList;
        if (
          dates.length < 1 &&
          active.length < 1 &&
          sold.length < 1 &&
          newList.length < 1
        ) {
          this.setState({
            soldActiveNodata: true,
          });
        }
        const colors = ["#5470C6", "#0083b4", "#db0814"];
        let dataList = [
          "2021-07",
          "2020-09",
          "2019-01",
          "2018-03",
          "2017-05",
          "2016-06",
          "2015-09",
          "2014-11",
          "2014-01",
          "2013-03",
          "2013-05",
          "2013-01",
        ];
        dataList = dates;
        let option = {
          color: colors,
          tooltip: {
            trigger: "axis",

            axisPointer: {
              type: "shadow",
              label: {
                show: true,
                formatter: function (params) {
                  return params.value.replace("\n", "");
                },
              },
            },
          },
          grid: {
            right: "20%",
          },
          toolbox: {
            feature: {
              dataView: { show: false, readOnly: false },
              restore: { show: false },
              saveAsImage: { show: false },
            },
          },
          legend: {
            data: ["Active Listings", "Total Sold", "New Listings"],
          },
          xAxis: [
            {
              axisLabel: { interval: 0, rotate: 25 },
              type: "category",
              axisTick: {
                alignWithLabel: true,
              },
              // prettier-ignore
              data: dataList,
            },
          ],
          yAxis: [
            {
              type: "value",
              name: "",
              position: "right",
              alignTicks: true,
              show: false,
              // offset: 80,
              axisLine: {
                show: true,
                lineStyle: {
                  color: colors[1],
                },
              },
              axisLabel: {
                rotate: detect.isMobile() ? -35 : 0,
                formatter: "{value}",
              },
            },
            {
              type: "value",
              name: "",
              position: "left",
              alignTicks: true,
              offset: detect.isMobile() ? -7 : 0,
              axisLine: {
                show: detect.isMobile() ? false : true,
                lineStyle: {
                  color: colors[2],
                },
              },
              axisLabel: {
                rotate: detect.isMobile() ? -55 : 0,
                formatter: "$ {value}",
              },
            },
            {
              type: "value",
              name: "",
              position: "left",
              alignTicks: true,
              axisLine: {
                show: true,
                lineStyle: {
                  color: colors[3],
                },
              },
              axisLabel: {
                rotate: detect.isMobile() ? -35 : 0,
                formatter: "$ {value}",
              },
            },
          ],
          series: [
            {
              name: "Active Listings",
              type: "bar",
              large: true,
              data: active,
            },
            {
              name: "Total Sold",
              type: "line",
              yAxisIndex: 1,
              large: true,
              data: sold,
            },
            {
              name: "New Listings",
              type: "line",
              yAxisIndex: 1,
              large: true,
              data: newList,
            },
          ],
        };
        this.setState({
          optionsSoldActive: option,
        });
      })
      .catch(() => {});
  }
  soldPriceDistribution() {
    // prettier-ignore
    let dataAxis = ['$100', '$165', '$152', '$212', '$215', '$120', '$121', '$140', '$40', '$251', '$21', '$201', '$215', '$120', '$121', '$140', '$40', '$251', '$21', '$201'];
    // prettier-ignore
    let data = [220, 182, 191, 234, 290, 330, 310, 123, 442, 321, 90, 149, 210, 122, 133, 334, 198, 123, 125, 220];
    let yMax = 500;
    let option = {
      title: {
        text: "",
        subtext: "",
      },
      tooltip: {
        trigger: "axis",
        axisPointer: {
          type: "shadow",
          label: {
            show: true,
            formatter: function (params) {
              return params.value.replace("\n", "");
            },
          },
        },
      },
      xAxis: {
        data: dataAxis,
        axisLabel: { interval: 0, rotate: 25 },
        axisTick: {
          show: false,
        },
        axisLine: {
          show: false,
        },
        z: 10,
      },
      yAxis: {
        axisLine: {
          show: false,
        },
        axisTick: {
          show: false,
        },
        axisLabel: {
          color: "#999",
        },
      },
      series: [
        {
          type: "bar",
          showBackground: false,
          itemStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              { offset: 0, color: "#db0810" },
              { offset: 0.5, color: "#db0811" },
              { offset: 1, color: "#db0812" },
            ]),
          },
          data: data,
        },
      ],
    };
    this.setState({
      soldPriceDist: option,
    });
  }
  propertyTypeDistribution() {
    let propertySearchFilter = this.state.searchFilter;
    API.jsonApiCall(propertyTyprDist, propertySearchFilter, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        const attRowTwnHouse = res.attRowTwnHouse;
        const condoApt = res.condoApt;
        const condoTownhouse = res.condoTownhouse;
        const detached = res.detached;
        const others = res.others;

        if (
          attRowTwnHouse.length < 1 &&
          condoApt.length < 1 &&
          condoTownhouse.length < 1 &&
          detached.length < 1 &&
          others.length < 1
        ) {
          this.setState({
            propertyTypeDistributionNodata: true,
          });
        }
        let option = {
          title: {
            text: "",
            subtext: "",
            left: "center",
          },
          tooltip: {
            trigger: "item",
          },
          legend: {
            orient: "horizontal",
          },
          series: [
            {
              name: "Access From",
              type: "pie",
              radius: "50%",
              data: [
                { value: condoApt, name: "Condo Apt" },
                { value: detached, name: "Detached" },
                { value: condoTownhouse, name: "Condo Townhouse" },
                { value: attRowTwnHouse, name: "Freehold Townhouse" },
                { value: others, name: "Others" },
              ],
              emphasis: {
                itemStyle: {
                  shadowBlur: 10,
                  shadowOffsetX: 0,
                  shadowColor: "rgba(0, 0, 0, 0.5)",
                },
              },
            },
          ],
        };
        this.setState({
          propTypeDistribution: option,
        });
      })
      .catch(() => {});
  }

  getFilterdata(e = null) {
    let body = {};
    if (e) {
      body = {
        province: e.value,
      };
    } else {
      body = {
        province: this.state.selectProvinces.value,
      };
    }
    API.jsonApiCall(marketStatsFilterData, body, "POST", null, {
      "Content-Type": "application/json",
    }).then((getFilterData) => {
      if (getFilterData) {
        try {
          this.setState({
            shimmer: false,
          });
          // getFilterData.propertyType
          let temp = SubType.map((item) => {
            let obj = {
              value: item,
              text: item,
            };
            return obj;
          });
          this.setState({
            propertyType: temp,
          });
          temp = getFilterData.municipality.map((item, key) => {
            let obj = {
              value: item.MunicipalityHeading,
              text: item.MunicipalityHeading,
              key: key,
            };
            return obj;
          });

          this.setState({
            municipalityHeading: temp,
          });
          temp = [];
 
        } catch (error) {}
      } else {
      }
    });
  }
  getCitiesdata(e = null) {
    let body = {};
    body = {
      municipality: e.value,
      province: this.state.selectProvinces.value,
    };
    API.jsonApiCall(marketStatsCitiesData, body, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      if (res) {
        try {
          this.setState({
            shimmer: false,
          });
          let temp = res.city.map((item) => {
            let obj = {
              value: item.Municipality,
              text: item.Municipality,
              Community: item.Community,
            };
            return obj;
          });
          this.setState({
            cities: temp,
          });
          if (res.city[0]) {
            let obj = res.city[0];
            const { searchFilter } = this.state;
            let name = "City";
            if (obj.Municipality === "All GTA") {
              searchFilter[name] = "";
            } else {
              searchFilter[name] = obj.Municipality;
            }
            let temp = JSON.parse(obj.Community).map((item) => {
              let obj = {
                value: item,
                text: item,
              };
              return obj;
            });
            this.setState({
              community: temp,
            });
            this.getStatsData();
          }
        } catch (error) {}
      } else {
      }
    });
  }
  changeMunicipality(e) {
    try {
      let obj = JSON.parse(e.target.attributes.dataset.value);
      this.setState({
        activeCls: "heading-" + obj.key,
      });
      this.getCitiesdata(obj.item);
    } catch (error) {
      console.error(error);
    }
  }
  changeCities(e) {
    try {
      let obj = JSON.parse(e.target.attributes.dataset.value);
      this.setState({
        activeCityCls: "heading-" + obj.key,
      });
      let temp = JSON.parse(obj.item.Community).map((item) => {
        let obj = {
          value: item,
          text: item,
        };
        return obj;
      });
      this.setState({
        selectedCommunity: {},
      });
      this.setState({
        community: temp,
      });

      const { searchFilter } = this.state;
      let name = "City";
      let community = "community";
      if (obj.item.value === "All GTA") {
        searchFilter[name] = "";
      } else {
        searchFilter[name] = obj.item.value;
      }
      searchFilter[community] = "";

      this.hidemenu();
      this.getStatsData();
    } catch (error) {
      console.error(error);
    }
    // heading-
  }
  showmenu(e) {
    if (e.target.classList[0] == "theme-text") {
      let panel = e.target.nextElementSibling;
      panel.classList.add("checkbox-opt-showing");
    }
  }
  hidemenu(e = null) {
    let cl = document.getElementsByClassName("checkbox-opt-container");
    for (var i = 0; i < cl.length; i++) {
      cl[i].classList.remove("checkbox-opt-showing");
    }
  }
  render() {
    if (this.state.shimmer) {
      return <ShimmerEffect count={2} />;
    }
    return (
      <>
        <div className="p-4 shop-content market-stats-nav">
          <div className="container ">
            <div className="row market-stats">
              <div className="col-md-4">
                <h5>{this.state.cityStateLabel} Statistics</h5>
              </div>
              <div className="col-md-4">
                <div
                  className="filter-div desktop-content filter-flex-opt"
                  onMouseLeave={this.hidemenu}
                >
                  <div className="filter-option-container checkbox-opt">
                    <label
                      for="Type"
                      className="theme-text gray-text cities-color"
                      onClick={this.showmenu}
                      data-suffix=" Property Types"
                      data-default="All Property Type"
                    >
                      {this.state.statsLabel}
                      <i className="fa fa-sort-down"></i>
                    </label>
                    <div className="checkbox-opt-container">
                      <div className="checkbox-title theme-text list-containers">
                        {this.state.municipalityHeading &&
                          Array.isArray(this.state.municipalityHeading) &&
                          this.state.municipalityHeading.length > 0 &&
                          this.state.municipalityHeading.map((item, key) => {
                            let obj = {
                              item: item,
                              key: key,
                            };
                            return (
                              <p
                                dataset={JSON.stringify(obj)}
                                key={key}
                                className={`list-items ${key ? "" : "mt-1"} ${
                                  this.state.activeCls === "heading-" + key
                                    ? "activeCls"
                                    : ""
                                }`}
                                onClick={this.changeMunicipality}
                              >
                                {item.text} »
                              </p>
                            );
                          })}
                      </div>
                      <div className="checkbox-title theme-text list-containers">
                        {this.state.cities &&
                        Array.isArray(this.state.cities) &&
                        this.state.cities.length > 0 ? (
                          this.state.cities.map((item, key) => {
                            let obj = {
                              item: item,
                              key: key,
                            };
                            return (
                              <p
                                dataset={JSON.stringify(obj)}
                                key={key}
                                className={`list-items ${key ? "" : "mt-1"} ${
                                  this.state.activeCityCls === "heading-" + key
                                    ? "activeCls"
                                    : ""
                                }`}
                                onClick={this.changeCities}
                              >
                                {item.text}
                              </p>
                            );
                          })
                        ) : (
                          <p
                            dataset={{ text: "all", value: "all" }}
                            key={0}
                            className={`list-items mt-1 activeCls`}
                          >
                            All
                          </p>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="col-md-2">
                <div>
                  <Autocomplete
                    inputProps={{
                      id: "community",
                      name: "community",
                      className: "form-control bg-white",
                      placeholder: "Community",
                      title: "Community",
                      readOnly: true,
                    }}
                    showIcon={true}
                    allList={this.state.community}
                    cb={this.handleTypeHead}
                    extraProps={{}}
                    selectedText={
                      this.state.selectedCommunity
                        ? this.state.selectedCommunity.text
                        : ""
                    }
                  />
                </div>
              </div>
              <div className="col-md-2">
                <Autocomplete
                  inputProps={{
                    id: "propertyType",
                    name: "propertyType",
                    className: "form-control bg-white",
                    placeholder: "Property Sub Type",
                    title: "Property Type",
                    readOnly: true,
                  }}
                  showIcon={true}
                  allList={this.state.propertyType}
                  cb={this.handleTypeHead}
                  extraProps={{}}
                  selectedText={
                    this.state.propertyType ? this.state.propertyType.text : ""
                  }
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
                        className={`gridMapView w-100 btn  ${
                          this.state.isActive == 1 ? "rentBtn" : ""
                        }`}
                        type="button"
                        value="3"
                        text="3 Months"
                        data-set="1"
                        onClick={this.toggleDate}
                      >
                        3 Months
                      </button>
                    </div>

                    <div className="col-md-2 p-0">
                      <button
                        size="md"
                        className={`gridMapView w-100 btn  ${
                          this.state.isActive == 2 ? "rentBtn" : ""
                        }`}
                        type="button"
                        value="6"
                        text="6 Months"
                        data-set="2"
                        onClick={this.toggleDate}
                      >
                        6 Months
                      </button>
                    </div>
                    <div className="col-md-2 p-0">
                      <button
                        size="md"
                        className={`gridMapView w-100 btn  ${
                          this.state.isActive == 3 ? "rentBtn" : ""
                        }`}
                        type="button"
                        value="9"
                        text="9 Months"
                        data-set="3"
                        onClick={this.toggleDate}
                      >
                        9 Months
                      </button>
                    </div>
                    <div className="col-md-2 p-0">
                      <button
                        size="md"
                        className={`gridMapView w-100 btn  ${
                          this.state.isActive == 4 ? "rentBtn" : ""
                        }`}
                        type="button"
                        value="12"
                        text="1 Year"
                        data-set="4"
                        onClick={this.toggleDate}
                      >
                        1 Years
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              {/* <div className="col-md-9">
                                <div className="row"> */}
              <div className="col-md-12">
                <span>Median Price and Average Days On Market *</span>
                <p></p>
                {this.state.medianAvgDomNodata ? (
                  <div className="stats-data-not-found">
                    {" "}
                    <h4>No Data Found !</h4>{" "}
                    <p>We are Collecting Data for this City.</p>
                  </div>
                ) : (
                  <>
                    {this.state.optionsMedianAvgDom ? (
                      <>
                        <ReactECharts
                          option={this.state.optionsMedianAvgDom}
                          lazyUpdate={true}
                        />
                      </>
                    ) : (
                      <>
                        <Loader1 />
                      </>
                    )}
                  </>
                )}
              </div>
            </div>
            <div className="row">
              <div className="col-md-12 mt-5 ml-5">
                <span className="mt-5">Sold & Active Listings *</span>
                {this.state.soldActiveNodata ? (
                  <div className="stats-data-not-found">
                    {" "}
                    <h4>No Data Found !</h4>{" "}
                    <p>We are Collecting Data for this City.</p>
                  </div>
                ) : (
                  <>
                    {this.state.optionsSoldActive ? (
                      <>
                        <ReactECharts
                          option={this.state.optionsSoldActive}
                          lazyUpdate={true}
                        />
                      </>
                    ) : (
                      <>
                        <Loader1 />
                      </>
                    )}
                  </>
                )}
              </div>
            </div>

            <div className="row">
              <div className="col-md-12 mt-5">
                <span>Market Temperature (absorption rate) * </span>
                <br />
                <span className="contentsText">
                  * Absorption rate indicates how fast homes are selling. It's
                  calculated as homes sold divided by homes currently listed.
                </span>
                <p></p>
                {this.state.marketTemperatureNodata ? (
                  <div className="stats-data-not-found">
                    {" "}
                    <h4>No Data Found !</h4>{" "}
                    <p>We are Collecting Data for this City.</p>
                  </div>
                ) : (
                  <>
                    {this.state.marketsTemperature ? (
                      <>
                        <ReactECharts
                          option={this.state.marketsTemperature}
                          lazyUpdate={true}
                        />
                      </>
                    ) : (
                      <>
                        <Loader1 />
                      </>
                    )}
                  </>
                )}
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
                {this.state.medianRentalNodata ? (
                  <div className="stats-data-not-found">
                    {" "}
                    <h4>No Data Found !</h4>{" "}
                    <p>We are Collecting Data for this City.</p>
                  </div>
                ) : (
                  <>
                    {this.state.medianRentals ? (
                      <>
                        <ReactECharts
                          option={this.state.medianRentals}
                          lazyUpdate={true}
                        />
                      </>
                    ) : (
                      <>
                        <Loader1 />
                      </>
                    )}
                  </>
                )}
              </div>
            </div>
            <div className="row">
              <div className="col-md-12 mt-5">
                <span>Property Type Distribution * </span>
                <br />
                <span className="contentsText">
                  Overview of sold price for All property types in All
                  Communities, Vancouver in the last 6 months:
                </span>
                <p></p>
                {this.state.propertyTypeDistributionNodata ? (
                  <div className="stats-data-not-found">
                    {" "}
                    <h4>No Data Found !</h4>{" "}
                    <p>We are Collecting Data for this City.</p>
                  </div>
                ) : (
                  <>
                    {this.state.propTypeDistribution ? (
                      <>
                        <ReactECharts
                          option={this.state.propTypeDistribution}
                          lazyUpdate={true}
                        />
                      </>
                    ) : (
                      <>
                        <Loader1 />
                      </>
                    )}
                  </>
                )}
              </div>
            </div>
          </div>

          {/* <div className="col-md-3"> */}
          {/*lorem isum*/}
          {/* </div> */}
          {/* </div> */}
          {/* </div> */}
        </div>
      </>
    );
  }
}
export default MarketStats;
