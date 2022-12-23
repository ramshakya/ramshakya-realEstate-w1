import React, { useEffect, useState } from "react";
import Link from "next/link";
import { useRouter } from 'next/router';
import API from "../../ReactCommon/utility/api";
import Constants from '../../constants/Global';
import BlogCard from '../../components/Cards/ArticleCard';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import detect from "../../ReactCommon/utility/detect";
import { base_url,blogsCat } from "../../constants/Global";
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
const blogs = (props) => {
	const [records, setRecords] = useState([]);
	const [Categories, setCategories] = useState('Most Popular');
	const [PostDate, setPostDate] = useState('');
	const [labelCategory, setLabelCategory] = useState("Most Popular");
	const [activeCls, setActiveCls] = useState("");
	const [search, setSearch] = useState(false);
	const [isMobile, setIsMobile] = useState("");
	const [blogCategory, setBlogCategory] = useState('');
	const [relatedRecords, setRelatedRecords] = useState([]);
	const [blogSearch, setAgentName] = useState('');
	const [category, setCategory] = useState('');
	const [currentPage, setCurrentPage] = useState(1);
	const router = useRouter();
	const blogUrl = router.query.slug;
	let post_date = '';
	useEffect(() => {
		if (detect.isMobile()) {
			setIsMobile(true);
		}
		if (blogUrl != undefined) {
			let body = JSON.stringify({
				agentId: Constants.agentId,
				blogUrl: blogUrl
			});

			API.jsonApiCall((extra_url + 'GetBlogs'), body, "POST", null, {
				"Content-Type": "application/json",
			}).then((res) => {
				if (res.blogDetail) {
					setRecords(res.blogDetail);

					let blogDetail = res.blogDetail;
					let meta = {
						"title": blogDetail.MetaTitle,
						"slug": blogDetail.MetaTitle,
						"metaDesc": blogDetail.MetaDesc,
						"MetaTags": blogDetail.BlogTags,
						"metaKeyword": blogDetail.BlogTags
					}
					props.setMetaInfo(meta);
				}
			});
			let body1 = JSON.stringify({
				currentPage: currentPage,
				agentId: Constants.agentId,
				blogSearch: blogSearch,
				category: category,
				currentBlog: blogUrl
			});
			API.jsonApiCall((extra_url + 'GetBlogs'), body1, "POST", null, {
				"Content-Type": "application/json",
			}).then((json) => {
				setRelatedRecords(json.records);
				let blogCategories = localStorage.getItem('blogCategory');
				
				let categoryList=JSON.parse(blogCategories);
				// const catMap = new Map();
				// blogsCat.forEach(item => catMap.set(item.text, item));
				// categoryList.forEach(item => {
				// 	const exists = catMap.has(item.text);
				// 	if (!exists) {
				// 		catMap.set(item.text, item);
				// 	}
				// })
				// let ar = [];
				// catMap.forEach(item =>
				// 	ar.push(item)
				// )

				// setBlogCategory(categoryList); // for dynamic
				setBlogCategory(blogsCat); // for static
			});
		}

	}, [blogUrl, Categories]);
	useEffect(() => {
		// post_date = new Date(records.blogDetail).toDateString();
		if (records) {
			if (records.created_at) {
				setPostDate(new Date(records.created_at).toDateString());
			}
			try {
				let temCategories = JSON.parse(records.Categories);
				setCategories(temCategories[0]);
			} catch (error) {
			}
		}
	}, [records]);
	function goToMap() {
		let elm = document.getElementById('forSale');
		if (elm) {
			elm.click();
		}
		// forSale
		// forRent
	}
	function actionAgainstCategory(event) {
		if (event.target && event.target.dataset && event.target.dataset.set) {
			let index = event.target.getAttribute('data-value');
			let ob = {
				active: index
			}
			let  label="HOME"
			event = JSON.parse(event.target.dataset.set);
			if (event.text === "BLOG HOME") {
				ob.search = false;
				ob.category = "";
				ob.labelCategory = "";
			} else {
				ob.search = true;
				ob.category = event.value;
				label=event.value
				ob.labelCategory = event.value;
			}
			localStorage.setItem('pushtoblog', JSON.stringify(ob));
			router.push('/blog/'+label);
		}

	}
	return (
		<>
			<div className="section-padding-v2 section-padding">
				<Navbar className={`back-ground-color `} expand="lg">
					<div className={`container ${isMobile ? "mt-3" : ''}`} >
						<Navbar.Toggle aria-controls="basic-navbar-nav" className={`  ${isMobile ? "mt-4" : ''}`} />
						<Navbar.Collapse id="basic-navbar-nav" >
							<Nav className="me-auto">
								{
									blogCategory &&
									blogCategory.map((item, key) => {
										let l = blogCategory.length;
										return (
											<Nav.Link  key={key} data-value={key} className={`${activeCls === key ? "active-blog-nav" : ''}`} data-set={JSON.stringify(item)} onClick={actionAgainstCategory}>{item.text} {" "}{l - 1 > key ? "  | " : " "}</Nav.Link>
										)
									})
								}
							</Nav>
						</Navbar.Collapse>
					</div>
				</Navbar>
			</div>
			<section className=" blog-main-container">
				<div className="container">
					<div className="row">
						<div className="col-md-6">
							<div className="blog-label1">{Categories}</div>
						</div>
						<div className="col-md-6">

						</div>
						<div className="col-md-12"><hr className="mt-1" /></div>
						<div className="col-md-12">
							<h3 className="pb-3">{records.Title}</h3>
							<div className="blog-detail-date">{PostDate} </div>
							<hr className="mt-1" />
						</div>
					</div>
					<div className="row">
						<div className="col-md-8">

							<img src={records.MainImg} alt={records.Title} className="blog-detail-img" />
							<div className="blog-detail-content" dangerouslySetInnerHTML={{ __html: records.Content }}></div>
							<hr className="mt-3" />
							<div className="row mb-2">
								<div className="col-md-6">
									{/* <Link href={'/map'}> */}
									<button className="common-btn blog-detail-page-btn" onClick={goToMap}>View Listings</button>
									{/* </Link> */}
								</div>
								{/* <div className="col-md-4">
									<button className="common-btn blog-detail-page-btn">View listings for rent</button>
								</div> */}
								<div className="col-md-6">
									<Link href={'/ContactUs'}>
										<button className="common-btn blog-detail-page-btn"> Contact an Agent </button>
									</Link>
								</div>
							</div>
						</div>
						<div className="col-md-4">
							<div className="row image2">
								
								{relatedRecords.map((item) => {
									let d = new Date(item.created_at).toDateString();
									let cat =item.Categories && item.Categories.length?JSON.parse(item.Categories):[];
									let categories = "";
									if (cat.length) {
										cat.map((data, key) => {
											if (key == length) {
												categories = data;
												// categories=categories+data+"/";
											} else {
												// categories=categories+data;
												categories = data;
											}
										})
									}
									return (
										<div className="col-md-12">
											<BlogCard
												image={item.MainImg}
												title={item.Title}
												// label='Most Popular'
												label={category ? category : categories}
												date={d}
												url={'/blogs/' + item.Url}
												content={item.Content}
											/>
										</div>
									)
								})}

							</div>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
export default blogs;
