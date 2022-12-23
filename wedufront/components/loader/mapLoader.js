import React from 'react';
import Styles from './loader.module.css'
const Loader =(props)=>{
    return(
        <>
       <div className={Styles.maploaderCls}>
       <img src="../loader.gif"  />
       </div>
        </>
    )
}
export default Loader;