import React, { useEffect, useState } from "react";
import About from "../components/ContactUs/AboutUs";
import Constants from "./../constants/Global";
import detect from "../ReactCommon/utility/detect";
const AboutUs = (props) => {
    useEffect(() => {
        const { metaJson } = Constants;
        // window.onload = function (e) {
            // metaJson.forEach((element, key) => {
            //     document.title = "dfsdfsdfsd";
            //     if (element.tag === "title") {
            //         let prev = document.getElementsByTagName('title');
            //         for (let index = 1; index < prev.length; index++) {
            //             const els = prev[index];
            //             // console.log("saasasasasasas",els.remove());
            //         }
            //         if (element.value) {
            //             var meta = document.createElement("title", "housen.ca");
            //             meta.innerText = element.value;
            //             meta.innerText = element.value;
            //             document.getElementsByTagName('head')[0].appendChild(meta);
            //             document.title = element.value;
            //         }
            //     } else {
            //         var meta = document.createElement(element.tag);
            //         if (element.attr1) {
            //             meta.setAttribute(element.attr1.key, element.attr1.value);
            //         }
            //         if (element.attr2) {
            //             meta.setAttribute(element.attr2.key, element.attr2.value);
            //         }
            //         if (element.attr3) {
            //             meta.setAttribute(element.attr3.key, element.attr3.value);
            //         }
            //         document.getElementsByTagName('head')[0].appendChild(meta);
            //     }

            // });
    });
    return (
        <>
            <About {...props} isMobile={detect.isMobile()} />
        </>
    );
};
export default AboutUs;
