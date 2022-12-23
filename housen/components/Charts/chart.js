import React, { useState, useEffect, useRef } from "react";
import Link from "next/link";
import ReactECharts from 'echarts-for-react';
import detect from "./../../ReactCommon/utility/detect";
export default function CardBarChart(props) {
  const [graphConfig, setGraphConfig] = useState({});
  React.useEffect(() => {
    if (!props.chartData.date) {
      return;
    }
    let totalSoldPrice = props.chartData.price
    const colors = ['#0081a7', '#00bfdcbc', '#6fb2bd'];
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
      // grid: {
      //   right: '15%',
      //   left: '15%',
      // },
      toolbox: {
        feature: {
          dataView: { show: false, readOnly: false },
          restore: { show: false },
          saveAsImage: { show: false }
        }
      },
      legend: {
        data: ['Total Sold', 'Average Price']
      },
      xAxis: [
        {
          type: 'category',
          axisTick: {
            alignWithLabel: true
          },
          axisLabel: { rotate: 30 },
          data: props.chartData.date
        }
      ],
      yAxis: [
        {

          type: 'value',
          name: '',
          position: 'right',
          alignTicks: true,
          // offset: 5,
          axisLine: {
            show: true,
            lineStyle: {
              color: colors[0]
            }
          },

          axisLabel: {
            rotate: detect.isMobile() ? 65 : 0,
            formatter: ' {value}'
          }
        },
        {
          type: 'value',
          name: '',
          position: 'left',
          alignTicks: true,
          offset: detect.isMobile() ? -4 : 5,
          axisLine: {
            show: true,
            lineStyle: {
              color: colors[1]
            }
          },
          axisLabel: {
            rotate: detect.isMobile() ? -65 : 0,
            formatter: '${value}'
          }
        }
      ],
      series: [
        {
          name: 'Total Sold',
          type: 'bar',
          large: true,

          data: props.chartData.sold,
        },
        {
          name: 'Average Price',
          type: 'line',
          yAxisIndex: 1,
          large: false,
          data: totalSoldPrice,
        }
      ]
    }
    setGraphConfig(option);
  }, [props.chartData]);
  return (
    <>
      <div className="container-fluid">
        <div className="row">
          <div className="col-md-12"></div>
          <div className="col-md-12"></div>
          <div className="col-md-12"></div>
        </div>
        <div className="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
          <div className="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div className="flex flex-wrap items-center">
              <div className="relative w-full max-w-full flex-grow flex-1">

                <h3 className="text-blueGray-700 text-xl font-semibold" >
                  {/* Market Stats In The GTA (All Property Type)* */}
                  GTA Market Stats (All Property Types)*
                </h3>
              </div>
            </div>
          </div>
          <div className={`p-4 flex-auto pb-1 `}>
            {/* Chart */}
            <div className="position-relative h-350-px stats_cls">

              {
                props.statsLoader ? <>
                  <ReactECharts
                    option={graphConfig}
                    lazyUpdate={true}
                  />
                </> : <>

                </>
              }

            </div>
          </div>
          <div className="row">
            <div className="col-md-5">
            </div>
            <div className={`col-md-3 mb-4 text-center `}>
              <Link href="/marketStats" >
                <a className="stats-btn"><button className="common-btn search-btn btn  " style={{
                  'backgroundColor': 'var(--theme-color)',
                  'color': 'white'
                }}>View More Stats</button><br /></a>
              </Link>
            </div>
            <div className="col-md-12 text-center">
              <p className="stateNote"><b>Disclaimer:</b> *Based on sales records and analysis of GTA sold data since 2020 from TRREB.</p>
            </div>

          </div>
        </div>

      </div>
    </>
  );
}
// git check