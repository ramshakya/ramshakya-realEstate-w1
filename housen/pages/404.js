import React, {useEffect,useState} from "react";
import Layout from '../components/Layout/Layout';
import Link from "next/link";
const ErrorPage = (props) =>{
	
	return(
			<>
				<section className="error-404 section-padding">
				    <div className="container">
				        <div className="row justify-content-center align-items-center">
				            <div className="col-lg-12">
				                <div className="error-block">
				                    <div className="throw-code">
				                        <h2>
				                            404
				                        </h2>
				                    </div>
				                    <div className="error-info">
				                        <h2 className="mb-2">Look like you are lost!</h2>
				                        <p className="mb-5">The page you are looking for is not available.</p>
										<Link href="/">
											<a href="/">back to Home <i className="fa fa-angle-double-right ml-2"></i>
											</a>
										</Link>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>
				</section>
			</>
		);
	

}
export default ErrorPage;