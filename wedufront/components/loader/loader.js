import React from 'react';
import Styles from './loader.module.css'
const Loader =(props)=>{
    return(
        <>
       <div className={Styles.loaderCls}>
       <img src="../loader.gif"  />
       </div>
        </>
    )
}
export default Loader;