
import React from 'react'
import Link from "next/link";
import Styles from '../../../styles/css/ReactCommon/breadCrumbs.module.css'
const NavHeader = (props) => {
    const tempList = [
        { "text": "Home", "link": "/" }
        
    ];
    let listItems = props.listItems ? props.listItems : tempList
    const renderBreadCrumbs = () => {
        return listItems.map((data, index) => {
            if(data.link ==""){

                return (
                    <li key={index} >
                        <a>{data.text}</a>
                    </li>
                )
            }
            else{
                return (
                    <li key={index} >
                        <Link href={data.link}>{data.text}</Link>
                    </li>
                )
            }
        });
    }
    const handleClick =(e)=>{
        if(props.callBackHandle){
            callBackHandle();
        }
    }
    return (
        <ul className={props.mainClass ? props.mainClass : Styles.breadcrumbs}>
            { listItems && listItems.length > 0 && Array.isArray(listItems) &&
                renderBreadCrumbs()
            }
        </ul>
    );
};

export default NavHeader;