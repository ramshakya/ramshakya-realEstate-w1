import { useEffect, useState } from 'react';
import Axios from 'axios';
import Constants from '../../constants/GlobalConstants';

function StayTime() {
    const [flag, setFlag] = useState(false);

     useEffect(() => {
        if(flag){
          Axios.get('https://ipapi.co/json/').then((res) => {
          let StayTime = new Date().toLocaleString();
          let IpAddress = res.data.ip;
          let PageUrl = window.location.href;
          let body =""
          if(localStorage.getItem('userDetail'))
          {
            let localStorageData = JSON.parse(localStorage.getItem('userDetail'));
            let UserId = localStorageData.login_user_id;
            body = JSON.stringify({IpAddress:IpAddress,StayTime:StayTime,PageUrl:PageUrl,UserId:UserId});
          }
          else
          {
            body = JSON.stringify({IpAddress:IpAddress,StayTime:StayTime,PageUrl:PageUrl});
          }
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: body
        }; 
          let urls = Constants.base_url+'api/v1/frontend/userActivity/';
          fetch(urls, requestOptions).then((response) =>
                response.text()).then((res) => JSON.parse(res))
                .then((json) => {
                  // console response
                  // console.log(json);
                  }).catch((err) => console.log({ err }));
          // let response = requestToAPI(body,"frontend/userActivity/","POST");
          setFlag(false);
    })}},[flag])


    const [currentCount, setCount] = useState(0);
    const timer = () => setCount(currentCount + 1);

    useEffect(
        () => {
            if (currentCount >=5) {
                setFlag(true);
                setCount(0);
                return;
            }
            const id = setInterval(timer, 1000);
            return () => clearInterval(id);
        },
        [currentCount]
    );
return (
    <>
   
    </>
  );
}

export default StayTime;
