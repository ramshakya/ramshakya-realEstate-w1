import React, { useEffect, useState } from "react";
import Layout from "../components/Layout/Layout";
import Link from "next/link";
import BlogCard from "../components/Card/BlogCard";
import BlogCard1 from "../components/Card/BlogCard1";
import API from "../ReactCommon/utility/api";
import Constants from "../constants/GlobalConstants";
import Autocomplete from "../ReactCommon/Components/AutoSuggestion";
import weduDefault from "../public/images/blogs.png"
import Head from "next/head";
const extra_url = Constants.extra_url;
const front_url = Constants.front_url;
const Blogs = (props) => {
  const [records, setRecords] = useState([]);
  const [total, setTotal] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPage, setTotalPage] = useState([]);
  const [blogSearch, setBlogSearch] = useState(false);
  const [category, setCategory] = useState(false);
  // const [dataFlag,steDataFlag] = useState(false);
  const [topPost, setTopPost] = useState([]);
  const [blogCategory, setBlogCategory] = useState("");
  const [blogSuggestion, setBlogSuggestion] = useState([]);
  const [search, setSearch] = useState(false);
  props.pageName("blogs");
  props.setMetaInfo(Constants.pageMeta.blogs);
  useEffect(() => {
    const getBlogData = () => {
      let body = JSON.stringify({
        currentPage: currentPage,
        agentId: Constants.agentId,
        blogSearch: blogSearch,
        category: category,
      });
      API.jsonApiCall(extra_url + "GetBlogs", body, "POST", null, {
        "Content-Type": "application/json",
      }).then((res) => {
        if (!res) {
          return;
        }
        setRecords(res.records);
        setTotal(res.total);
        setTotalPage(res.totalPages);
        setTopPost(res.topPost);
      });
    };

    getBlogData();
  }, [currentPage, category, blogSearch]);
  useEffect(() => {
    getCat();
  }, []);
  const getCat = async () => {
    const categoryList = await API.jsonApiCall(
      extra_url + "GetBlogCategory",
      {},
      "POST",
      null,
      {
        "Content-Type": "application/json",
      }
    );
    console.log("categoryList", categoryList);
    setBlogCategory(categoryList.suggesstionArr);
  };

  const getBlogAutoSuggest = async (fieldValue, fieldName, cb) => {
    let payload = {
      keyword_search: "",
      agentId: Constants.agentId,
    };
    if (fieldValue) {
      payload.keyword_search = fieldValue;
    }
    const requestOptions = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    };
    await fetch(extra_url + "GetBlogTitle", requestOptions)
      .then((response) => response.text())
      .then((res) => JSON.parse(res))
      .then((json) => {
        cb({ allList: json.suggesstionArr });
      })
      .catch((err) => console.log({ err }));
  };
  function actionAgainstKeyword(event) {
    // getBlogData(event.value,blogCategory);
    setBlogSearch(event.value);
    console.log(blogSearch);
    setSearch(true);
  }
  function actionAgainstCategory(event) {
    // getBlogData(blogSearch,event.value);
    setCategory(event.value);
    setSearch(true);
  }

  return (
    <>
      {/* <Head>
				<title>{props.title}</title>
				<meta name="og_title" property="og:title" content={props.title} />
				<meta name="og:description" content={props.description} />
				<meta name="og_image" property="og:image" content={"https://panel.wedu.ca//storage/banner_webp/62d1360d30497.webp"} />
				<meta name="og:image:alt" content={"BLogs Homes for Sale and Real Estate Get Listings in Canada|  IMAGE ALT"} />
			</Head> */}
      <section className="section-padding">
        <div className="container">
          <div className="container">
            <div className="row">
              <div className="col-md-12 col-lg-12 mb-4 mt-4">
                <div className="row">
                  <div className="col-md-4 col-lg-4">
                    <label>Blog Category</label>
                    <Autocomplete
                      inputProps={{
                        id: "autoSuggestion",
                        name: "purpose",
                        className: "auto form-control auto-suggestion-inp",
                        placeholder: "Blog Category",
                        title: "Blog Category",
                        readOnly: false,
                        required: true,
                        readOnly: true,
                      }}
                      allList={blogCategory}
                      autoCompleteCb={""}
                      cb={actionAgainstCategory}
                      selectedText={""}
                      extraProps={{}}
                    />
                  </div>
                  <div className="col-md-8 col-lg-8">
                    <label>Search Blog</label>
                    <Autocomplete
                      inputProps={{
                        id: "autoSuggestion",
                        name: "purpose",
                        className: "auto form-control auto-suggestion-inp",
                        placeholder: "search by keyword",
                        title: "search by keyword",
                        readOnly: false,
                        required: true,
                      }}
                      allList={[]}
                      autoCompleteCb={getBlogAutoSuggest}
                      cb={actionAgainstKeyword}
                      extraProps={{}}
                    />
                  </div>
                </div>
              </div>
              {!search && (
                <div className="col-md-12 col-lg-12">
                  <h4 className="text-bold">TOP POSTS</h4>
                </div>
              )}
              {!search &&
                topPost.map((item, index) => {
                  if (index === 0) {
                    let d = new Date(item.created_at).toDateString();
                    return (
                      <div
                        className="col-md-8 col-lg-8 image1"
                        key={index + "b"}
                      >
                        <BlogCard
                          image={item.MainImg?item.MainImg:weduDefault.src}
                          title={item.Title}
                          label="Most Popular"
                          date={d}
                          url={"/blogs/" + item.Url}
                          content={item.Content}
                        />
                      </div>
                    );
                  }
                })}

              {!search && (
                <div className="col-md-4 col-lg-4 image2">
                  <div className="row">
                    {topPost.map((item, index) => {
                      if (index !== 0) {
                        let d = new Date(item.created_at).toDateString();
                        return (
                          <div className="col-md-12" key={index + "c"}>
                            <BlogCard
                              image={item.MainImg?item.MainImg:weduDefault.src}
                              title={item.Title}
                              label="Most Popular"
                              date={d}
                              url={"/blogs/" + item.Url}
                              content={item.Content}
                              imageDivClass="w-100 position-relative vh-50"
                            />
                          </div>
                        );
                      }
                    })}
                  </div>
                </div>
              )}
            </div>
            <div className="row">
              <div className="col-md-12 col-lg-12">
                <h4 className="text-bold">THE LATEST</h4>
              </div>
              {records.map((item, index) => {
                let d = new Date(item.created_at).toDateString();
                return (
                  <div className="col-md-12 col-lg-12 image3" key={index + "d"}>
                    <BlogCard1
                      image={item.MainImg?item.MainImg:weduDefault.src}
                      title={item.Title}
                      label="Most Popular"
                      date={d}
                      url={"/blogs/" + item.Url}
                      content={item.Content}
                    />
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default Blogs;
