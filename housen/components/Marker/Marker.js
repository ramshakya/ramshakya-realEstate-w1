import React from 'react';
const Marker = ({ id,timeMicro,highLight,price }) => <div id={`marker-${id}`} onMouseOver={highLight} dataset={JSON.stringify({id:id,ismap:true})}><div id={`marker-${id}`} dataset={JSON.stringify({id:id,ismap:true})}   className={`marker prop_marker mapMarker${id}`}  >{price}</div></div>;
export default Marker;
//Check git check
