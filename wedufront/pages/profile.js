import React, {useEffect,useState} from "react";
import Profile from "../components/User/Profile";
import Layout from '../components/Layout/Layout';
const ProfilePage = (props) =>{
	const [checkLoginToken, setLoginToken] = useState(false);
	
	 useEffect(()=>{
	 	if(localStorage.getItem('login_token')){
			setLoginToken(localStorage.getItem('login_token'));
		}
		else
		{
			document.getElementById('togglebtn').click();

		}    

	  },[checkLoginToken]);
	return(
		<>	
			{checkLoginToken &&
				<Profile title={'Profile'}/>
			}
			{!checkLoginToken &&

				<div className="custom-padding text-center mb-5">
					<h4>First do login to access profile page</h4>
				</div>
			}
		</>
		);
	

}
export default ProfilePage;