import React, { useEffect, useState } from "react";
import Layout from '../../components/Layout/Layout';
import Head from "next/head";
import Link from "next/link";
import { useRouter } from 'next/router';
import API from "../../ReactCommon/utility/api";
import Constants from '../../constants/GlobalConstants';
import BlogCard from '../../components/Card/BlogCard';
import Image from "next/image";
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
const blogs = (props) => {
	console.log("blogs=======>>>>", props);
	const [records, setRecords] = useState([]);
	const [relatedRecords, setRelatedRecords] = useState([]);
	const [blogSearch, setAgentName] = useState('');
	const [category, setCategory] = useState('');
	const [currentPage, setCurrentPage] = useState(1);
	const router = useRouter();
	const blogUrl = router.query.slug;
	let post_date = '';

	useEffect(() => {
		if (blogUrl != undefined) {
			let body = JSON.stringify({
				agentId: Constants.agentId,
				blogUrl: blogUrl
			});

			API.jsonApiCall((extra_url + 'GetBlogs'), body, "POST", null, {
				"Content-Type": "application/json",
			}).then((res) => {
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
				// console.log("records.MainImg1", res.blogDetail);
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
				console.log("records.MainImg2", json.records.MainImg);
			});
		}
		post_date = new Date(records.created_at).toDateString();
	}, []);
	return (
		<>
		<Head>
          <title>{props.infoMeta.title}</title>
          <meta name="Description" content={props.infoMeta.description} />
          <meta name="og_title" property="og:title" content={props.infoMeta.title} />
          <meta name="og:description" content={props.infoMeta.description} />
          <meta name="og_image" property="og:image" content={props.infoMeta.imgUrl} />
          <meta name="og:image:alt" content={props.infoMeta.imgAlt} />
          <meta name="twitter:title" content={props.infoMeta.title} />
          <meta name="twitter:description" content={props.infoMeta.description} />
          <meta name="twitter:image" content={props.infoMeta.imgUrl} />
          <meta name="twitter:image:alt" content={props.infoMeta.imgAlt} />
        </Head>
			<section className="section-padding">
				<div className="container">
					<div className="row">
						<div className="col-md-6">
							<div className="blog-label1">Most Popular</div>
						</div>
						<div className="col-md-6">
							<div className="blog-detail-date">{post_date}</div>
						</div>
						<div className="col-md-12"><hr className="mt-1" /></div>
						<div className="col-md-12">
							<h3 className="pb-3">{records.Title}</h3>
							<hr className="mt-1" />
						</div>
					</div>
					<div className="row">
						<div className="col-md-8">

							<img src={records.MainImg} alt={records.Title} className="blog-detail-img" />
							<div className="blog-detail-content" dangerouslySetInnerHTML={{ __html: records.Content }}></div>
							<hr className="mt-3" />
							<div className="row">
								<div className="col-md-6">
									<Link href={'/map'}>
										<button className="common-btn blog-detail-page-btn">View listings</button>
									</Link>
								</div>
								{/* <div className="col-md-4">
									<button className="common-btn blog-detail-page-btn">View listings for rent</button>
								</div> */}
								<div className="col-md-6">
									<Link href={'/ContactUs'}>
										<button className="common-btn blog-detail-page-btn">Contact an agent</button>
									</Link>
								</div>
							</div>
						</div>
						<div className="col-md-4">
							<div className="row image2">
								{relatedRecords.map((item) => {
									let d = new Date(item.created_at).toDateString();
									return (
										<div className="col-md-12">
											<BlogCard
												image={item.MainImg}
												title={item.Title}
												label='Most Popular'
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
export async function getServerSideProps(context) {
	let hrefUrl = context.req.url;
	let slug = hrefUrl.replace("/blogs/", "");
	let body = JSON.stringify({
		agentId: Constants.agentId,
		blogUrl: slug
	});
	let infoMeta = {};
	await API.jsonApiCall((extra_url + 'GetBlogs'), body, "POST", null, {
		"Content-Type": "application/json",
	}).then((res) => {
		let blogDetail = res.blogDetail;
		infoMeta = {
			"keyword": blogDetail.BlogTags,
			"title": blogDetail.MetaTitle + " | Wedu",
			"description": blogDetail.MetaDesc,
			"imgAlt": "Home for Sale & Listing in Canada",
			"url": blogDetail.Url,
			"imgUrl": blogDetail.MainImg,
		};
	});
	return {
		props: {
			"infoMeta": infoMeta,
		},
	}
}

