import axios from "axios";
import Constants from  "../../constants/GlobalConstants"
const baseURL = Constants.extra_url;
export const requestToAPI = async (body, urlPath, method = 'GET',token='') => {

    // console.log(token);
    try {
        let resultData = null;
        let response = null;
        if (method === 'GET' || method === 'DELETE') {
            //console.log("url",`${baseURL}${urlPath}`);
            response = await fetch(`${baseURL}${urlPath}`, {
                headers: { 'Content-Type': 'application/json'},
                method,

            });
        } else {
            response = await fetch(`${baseURL}${urlPath}`, {
                method,
                headers: { 'Content-Type': 'application/json','Authorization': `Bearer ${token}`},
                body: body
            });
        }
        resultData = await response.json();
        if (!response.ok) {
            let msg = 'Some error occurred';
            if (resultData.message) {
                msg = resultData.message;
            }
            throw new Error(msg);
        }
        //console.log("result data",resultData);
        return resultData;
    } catch (err) {
        return Promise.reject(err);
    }
};





// export default axios.update({
//     baseURL: baseURL+''
// });