import { useState, useEffect, useRef, Component } from "react";
import Link from "next/link";
import Style from '../../styles/css/ReactCommon/popup.module.css'
// import Facebook from "./facebook";
import Google from "./google";
import Facebook from "./SocialLogin";
const Popup = (props) => {

	const [webSettingsFlag, setWebSettingsFlag] = useState(false);
	const [webSettings, setWebSettings] = useState(null);

	useEffect(() => {
		try {
			let websetting = localStorage.getItem("websetting");
			if (websetting !== null && websetting !== "undefined" && websetting !== undefined) {
				websetting = JSON.parse(websetting);
				setWebSettings(websetting);
				setWebSettingsFlag(true);
			}
		} catch (error) {
		}
	}, [webSettingsFlag]);
	function finished_rendering() {
		var spinner = document.getElementById("spinner");
		spinner.removeAttribute("style");
		spinner.removeChild(spinner.childNodes[0]);
	}
	return (
		<div className={`${props.parentcls ? props.parentcls : "", Style.commonPopup}  `}>
			<div className={Style.box}>
				<span className={`${Style.closeIcon} popcloseBtn`} id="closeBtn" onClick={props.handleClose}>x</span>
				{props.children}
				{
					webSettings !== null && <>
						<Google websetting={webSettings} />
						<Facebook {...webSettings} />
					</>
				}
			</div>
		</div>
	);
}
export default Popup;
