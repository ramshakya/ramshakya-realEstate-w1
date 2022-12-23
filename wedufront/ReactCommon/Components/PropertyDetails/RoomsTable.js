import React, { useState, useEffect, useRef } from "react";
export default (props) => {
  const roomdata = props.data ? props.data : {};
  const genlist = (e) => {
    if (roomdata && roomdata.length >= 0 && Array.isArray(roomdata)) {
      return roomdata.map((data, index) => {
        if (data.isHeading) {
          return (
            <thead key={index}>
              <tr className="headings">
                <th scope="col">{data.level}</th>
                <th scope="col">{data.name}</th>
                <th scope="col">{data.size}</th>
                <th scope="col">{data.features}</th>
              </tr>
            </thead>
          );
        }
        return (
          <tbody key={index}>
            <tr className={`${data.levels ? "trHead" : ""}`}>
              <td className="rooms-heading">{data.levels}</td>
              <td className="rooms-heading">{data.name}</td>
              {props.isShow ? (
                <td id="list_detroom_meter">{data.sizeInMt}</td>
              ) : (
                <td id="list_detroom_feet" className="">
                  {data.sizeInFt}
                </td>
              )}
              <td>{data.desc}</td>
            </tr>
          </tbody>
        );
      });
    }
  };
  return <table className="table">{genlist()}</table>;
};
