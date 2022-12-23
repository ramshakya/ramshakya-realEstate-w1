import React, { useState } from "react";
import Link from "next/link";
import ReactECharts from 'echarts-for-react';
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
export default function CardBarChart(props) {
  const [graphConfig, setGraphConfig] = useState({});
  // console.log("propsprops",props);
  React.useEffect(() => {
    if(!props.chartData){
      return;
    }
    if (  !props.chartData && !props.chartData.date) {
      return;
    }
    const colors = ['#ff8286', '#e3342f', '#EE6666'];
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
        right: '25%',
        left: '25%',
      },
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
          axisLabel: { rotate: 25 },
          data: props.chartData.date
        }
      ],
      yAxis: [
        {
          type: 'value',
          name: '',
          position: 'right',
          alignTicks: true,
          offset: 5,
          axisLine: {
            show: true,
            lineStyle: {
              color: colors[1]
            }
          },
          axisLabel: {
            formatter: ' {value}'
          }
        },
        {
          type: 'value',
          name: '',
          position: 'left',
          alignTicks: true,
          offset: 5,
          axisLine: {
            show: true,
            lineStyle: {
              color: colors[2]
            }
          },
          axisLabel: {
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
          data: props.chartData.price,
        }
      ]
    }
    setGraphConfig(option);
  }, [props.chartData]);
  return (
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
              <h6 className="h2 text-blueGray-700 text-xl font-semibold">
                Sold History of Great Toronto Area
              </h6>
            </div>
          </div>
        </div>
        <div className="p-4 flex-auto ">
          <div className="relative h-350-px">
            {
              props.statsLoader ?
                <ReactECharts
                  option={graphConfig}
                  lazyUpdate={true}
                />
                :
                <ShimmerEffect type="featureElement" columnCls={"col-lg-12"} count={1} />
            }
          </div>
        </div>
        <div className="row">
          <div className="col-md-12">
            {
              props.statsLoader ?
                <Link href="/marketStats" >
                  <a className="stats-btn"><button className="common-btn search-btn h-75 pt-1 pb-2">View More Stats</button><br /></a>
                </Link>
                : <>
                </>
            }
          </div>
        </div>
      </div>
    </div>
  );
}